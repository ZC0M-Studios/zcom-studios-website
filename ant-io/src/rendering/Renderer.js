// =============================================================================
// ANCHOR:RENDERER
// Description: WebGL2 multi-pass renderer for the Ant-IO simulation.
//              Manages all GPU resources (shaders, VBOs, VAOs, textures)
//              and executes 6 render passes per frame in painter's order.
//
// Pass order (back to front):
//   1. Ground         — full-screen procedural dirt
//   2. AntHill ×2     — circular gradient mounds
//   3. Pheromone      — 4-channel fading signal overlay (additive blend)
//   4. Food           — instanced soft circles
//   5. Ants           — instanced rotated ellipses
//   6. Overlay        — corner fitness history panel (optional)
//
// Coordinate system: world [0,WORLD_W] × [0,WORLD_H] → NDC [-1,1]
//   u_worldToNDC = mat3 scale(2/W, 2/H) translate(-1,-1)
// =============================================================================

import { SimConfig }    from '../config/SimConfig.js';
import { ShaderProgram } from './ShaderProgram.js';

// Inline shader source loader from <script type="x-shader"> tags,
// or fetched from .glsl files if available.
async function loadShader(path) {
    const res = await fetch(path);
    if (!res.ok) throw new Error(`[Renderer] Failed to load shader: ${path}`);
    return res.text();
}

export class Renderer {
    constructor(canvas) {
        this.canvas = canvas;
        this.gl     = null;

        // Shader programs (initialized in init())
        this._prog = {};

        // WebGL resource handles
        this._vao  = {};
        this._vbo  = {};
        this._tex  = {};

        // Pheromone texture dimensions
        this._phW = SimConfig.PHEROMONE_GRID_W;
        this._phH = SimConfig.PHEROMONE_GRID_H;

        // Fitness history for overlay shader (ring buffer, last 64 generations)
        this._fitHistory0 = new Float32Array(64);
        this._fitHistory1 = new Float32Array(64);
        this._fitHistoryCount = 0;
        this._fitMax = 1;
    }

    // ANCHOR:FUNCTION_INIT
    // Initializes WebGL context, compiles shaders, creates GPU resources.
    // Must be awaited before first render() call.
    async init() {
        const gl = this.canvas.getContext('webgl2', {
            antialias: false,
            alpha:     false,
            depth:     false,
            stencil:   false,
        });
        if (!gl) throw new Error('[Renderer] WebGL2 not supported in this browser.');
        this.gl = gl;

        gl.clearColor(0.12, 0.09, 0.06, 1.0);
        gl.enable(gl.BLEND);

        // Load and compile all shader programs
        const BASE = '/ant-io/src/rendering/shaders/';
        const [
            groundVert, groundFrag,
            anthillFrag,
            pheromoneFrag,
            territoryFrag,
            foodVert, foodFrag,
            antVert, antFrag,
            overlayFrag,
        ] = await Promise.all([
            loadShader(BASE + 'ground.vert.glsl'),
            loadShader(BASE + 'ground.frag.glsl'),
            loadShader(BASE + 'anthill.frag.glsl'),
            loadShader(BASE + 'pheromone.frag.glsl'),
            loadShader(BASE + 'territory.frag.glsl'),
            loadShader(BASE + 'food.vert.glsl'),
            loadShader(BASE + 'food.frag.glsl'),
            loadShader(BASE + 'ant.vert.glsl'),
            loadShader(BASE + 'ant.frag.glsl'),
            loadShader(BASE + 'overlay.frag.glsl'),
        ]);

        // Shared passthrough vert for full-screen quad with world_pos out
        const passthroughVert = `#version 300 es
            in vec2 a_position;
            in vec2 a_texcoord;
            out vec2 v_uv;
            out vec2 v_world_pos;
            uniform mat3 u_worldToNDC;
            void main() {
                v_uv = a_texcoord;
                // world_pos: texcoord maps [0,1] → [0,WORLD_W/H]
                v_world_pos = vec2(a_texcoord.x * ${SimConfig.WORLD_W.toFixed(1)},
                                   a_texcoord.y * ${SimConfig.WORLD_H.toFixed(1)});
                gl_Position = vec4(a_position, 0.0, 1.0);
            }`;

        this._prog.ground    = new ShaderProgram(gl, groundVert,       groundFrag,    'ground');
        this._prog.anthill   = new ShaderProgram(gl, passthroughVert,  anthillFrag,   'anthill');
        this._prog.pheromone = new ShaderProgram(gl, passthroughVert,  pheromoneFrag, 'pheromone');
        this._prog.territory = new ShaderProgram(gl, passthroughVert,  territoryFrag, 'territory');
        this._prog.food      = new ShaderProgram(gl, foodVert,         foodFrag,      'food');
        this._prog.ant       = new ShaderProgram(gl, antVert,          antFrag,       'ant');
        this._prog.overlay   = new ShaderProgram(gl, passthroughVert,  overlayFrag,   'overlay');

        this._createFullscreenQuad();
        this._createAntHillQuads();
        this._createFoodBuffers();
        this._createAntBuffers();
        this._createPheromoneTexture();
        this._computeWorldToNDC();

        console.info('[Renderer] Initialized — WebGL2 ready');
    }

    // ANCHOR:FUNCTION_CREATE_FULLSCREEN_QUAD
    _createFullscreenQuad() {
        const gl = this.gl;
        // Positions (NDC), Texcoords
        const verts = new Float32Array([
            -1, -1,  0, 0,
             1, -1,  1, 0,
             1,  1,  1, 1,
            -1,  1,  0, 1,
        ]);
        const idx = new Uint16Array([0,1,2, 0,2,3]);

        const vao = gl.createVertexArray();
        gl.bindVertexArray(vao);

        const vbo = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, vbo);
        gl.bufferData(gl.ARRAY_BUFFER, verts, gl.STATIC_DRAW);

        const ebo = gl.createBuffer();
        gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, ebo);
        gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, idx, gl.STATIC_DRAW);

        // Attribute 0: a_position (xy), Attribute 1: a_texcoord (uv)
        gl.enableVertexAttribArray(0);
        gl.vertexAttribPointer(0, 2, gl.FLOAT, false, 16, 0);
        gl.enableVertexAttribArray(1);
        gl.vertexAttribPointer(1, 2, gl.FLOAT, false, 16, 8);

        gl.bindVertexArray(null);
        this._vao.quad = vao;
        this._vbo.quad = vbo;
        this._vbo.quadIdx = ebo;
    }

    // ANCHOR:FUNCTION_CREATE_ANTHILL_QUADS
    // Each hill gets a screen-aligned quad covering hill_center ± hill_radius*1.2
    _createAntHillQuads() {
        const gl = this.gl;
        this._vao.hills = [];
        this._vbo.hills = [];

        for (const colony of SimConfig.COLONY) {
            const r  = SimConfig.HILL_RADIUS * 1.2;
            const cx = colony.x, cy = colony.y;
            const W  = SimConfig.WORLD_W, H = SimConfig.WORLD_H;

            // World coords of quad corners
            const wx = [cx - r, cx + r, cx + r, cx - r];
            const wy = [cy - r, cy - r, cy + r, cy + r];

            // Convert to NDC: NDC = (world/worldSize)*2 - 1
            const verts = new Float32Array([
                (wx[0]/W)*2-1, (wy[0]/H)*2-1,  cx-r, cy-r,
                (wx[1]/W)*2-1, (wy[1]/H)*2-1,  cx+r, cy-r,
                (wx[2]/W)*2-1, (wy[2]/H)*2-1,  cx+r, cy+r,
                (wx[3]/W)*2-1, (wy[3]/H)*2-1,  cx-r, cy+r,
            ]);
            const idx = new Uint16Array([0,1,2,0,2,3]);

            const vao = gl.createVertexArray();
            gl.bindVertexArray(vao);

            const vbo = gl.createBuffer();
            gl.bindBuffer(gl.ARRAY_BUFFER, vbo);
            gl.bufferData(gl.ARRAY_BUFFER, verts, gl.STATIC_DRAW);

            const ebo = gl.createBuffer();
            gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, ebo);
            gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, idx, gl.STATIC_DRAW);

            // a_position (ndc x,y), a_texcoord (world x,y = world_pos)
            gl.enableVertexAttribArray(0);
            gl.vertexAttribPointer(0, 2, gl.FLOAT, false, 16, 0);
            gl.enableVertexAttribArray(1);
            gl.vertexAttribPointer(1, 2, gl.FLOAT, false, 16, 8);

            gl.bindVertexArray(null);
            this._vao.hills.push(vao);
            this._vbo.hills.push([vbo, ebo]);
        }
    }

    // ANCHOR:FUNCTION_CREATE_FOOD_BUFFERS
    _createFoodBuffers() {
        const gl = this.gl;

        // Unit circle approximation (16 segments)
        const segs = 16;
        const quadVerts = [];
        for (let i = 0; i <= segs; i++) {
            const a  = (i / segs) * Math.PI * 2;
            const bx = (i > 0) ? Math.cos(((i-1)/segs)*Math.PI*2) : 0;
            const by = (i > 0) ? Math.sin(((i-1)/segs)*Math.PI*2) : 0;
            if (i > 0) {
                quadVerts.push(0, 0, Math.cos(a), Math.sin(a), bx, by);
            }
        }
        // Fallback: simple square quad
        const unitVerts = new Float32Array([-1,-1, 1,-1, 1,1, -1,1]);
        const unitIdx   = new Uint16Array([0,1,2,0,2,3]);

        const vao = gl.createVertexArray();
        gl.bindVertexArray(vao);

        // Quad vertices
        const quadVbo = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, quadVbo);
        gl.bufferData(gl.ARRAY_BUFFER, unitVerts, gl.STATIC_DRAW);
        gl.enableVertexAttribArray(0); // a_quad_pos
        gl.vertexAttribPointer(0, 2, gl.FLOAT, false, 8, 0);

        const quadEbo = gl.createBuffer();
        gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, quadEbo);
        gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, unitIdx, gl.STATIC_DRAW);

        // Instance buffer: [x, y, size] per food item
        const maxFood = SimConfig.FOOD_COUNT_MAX * 3;
        const instVbo = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, instVbo);
        gl.bufferData(gl.ARRAY_BUFFER, maxFood * 3 * 4, gl.DYNAMIC_DRAW);

        gl.enableVertexAttribArray(1); // a_inst_pos (xy)
        gl.vertexAttribPointer(1, 2, gl.FLOAT, false, 12, 0);
        gl.vertexAttribDivisor(1, 1);

        gl.enableVertexAttribArray(2); // a_inst_size (z)
        gl.vertexAttribPointer(2, 1, gl.FLOAT, false, 12, 8);
        gl.vertexAttribDivisor(2, 1);

        gl.bindVertexArray(null);
        this._vao.food     = vao;
        this._vbo.foodQuad = quadVbo;
        this._vbo.foodInst = instVbo;
        this._vbo.foodIdx  = quadEbo;
    }

    // ANCHOR:FUNCTION_CREATE_ANT_BUFFERS
    _createAntBuffers() {
        const gl = this.gl;

        // Simple square quad for ant body (shader shapes it)
        const unitVerts = new Float32Array([-1,-1, 1,-1, 1,1, -1,1]);
        const unitIdx   = new Uint16Array([0,1,2, 0,2,3]);

        const vao = gl.createVertexArray();
        gl.bindVertexArray(vao);

        const quadVbo = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, quadVbo);
        gl.bufferData(gl.ARRAY_BUFFER, unitVerts, gl.STATIC_DRAW);
        gl.enableVertexAttribArray(0); // a_quad_pos
        gl.vertexAttribPointer(0, 2, gl.FLOAT, false, 8, 0);

        const quadEbo = gl.createBuffer();
        gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, quadEbo);
        gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, unitIdx, gl.STATIC_DRAW);

        // Instance buffer: stride=8 floats (32 bytes)
        // [x, y, angle, colony, state, carrying, energy_norm, nn_turn]
        const STRIDE = 32; // 8 floats × 4 bytes
        const maxAnts = SimConfig.POP_MAX_COLONY * 2;
        const instVbo = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, instVbo);
        gl.bufferData(gl.ARRAY_BUFFER, maxAnts * STRIDE, gl.DYNAMIC_DRAW);
        this._antVboCapacity = maxAnts * STRIDE;

        gl.enableVertexAttribArray(1); // a_inst_pos (xy)
        gl.vertexAttribPointer(1, 2, gl.FLOAT, false, STRIDE, 0);
        gl.vertexAttribDivisor(1, 1);

        gl.enableVertexAttribArray(2); // a_inst_angle
        gl.vertexAttribPointer(2, 1, gl.FLOAT, false, STRIDE, 8);
        gl.vertexAttribDivisor(2, 1);

        gl.enableVertexAttribArray(3); // a_inst_colony
        gl.vertexAttribPointer(3, 1, gl.FLOAT, false, STRIDE, 12);
        gl.vertexAttribDivisor(3, 1);

        gl.enableVertexAttribArray(4); // a_inst_state
        gl.vertexAttribPointer(4, 1, gl.FLOAT, false, STRIDE, 16);
        gl.vertexAttribDivisor(4, 1);

        gl.enableVertexAttribArray(5); // a_inst_carrying
        gl.vertexAttribPointer(5, 1, gl.FLOAT, false, STRIDE, 20);
        gl.vertexAttribDivisor(5, 1);

        gl.enableVertexAttribArray(6); // a_inst_energy
        gl.vertexAttribPointer(6, 1, gl.FLOAT, false, STRIDE, 24);
        gl.vertexAttribDivisor(6, 1);

        gl.enableVertexAttribArray(7); // a_inst_nn_turn
        gl.vertexAttribPointer(7, 1, gl.FLOAT, false, STRIDE, 28);
        gl.vertexAttribDivisor(7, 1);

        gl.bindVertexArray(null);
        this._vao.ant     = vao;
        this._vbo.antQuad = quadVbo;
        this._vbo.antInst = instVbo;
        this._vbo.antIdx  = quadEbo;
    }

    // ANCHOR:FUNCTION_CREATE_PHEROMONE_TEXTURE
    _createPheromoneTexture() {
        const gl  = this.gl;
        const ext = gl.getExtension('EXT_color_buffer_float');
        // EXT_color_buffer_float needed for RGBA32F render targets;
        // for sampling only we use OES_texture_float which is core in WebGL2.

        const tex = gl.createTexture();
        gl.bindTexture(gl.TEXTURE_2D, tex);
        gl.texImage2D(
            gl.TEXTURE_2D, 0, gl.RGBA32F,
            this._phW, this._phH, 0,
            gl.RGBA, gl.FLOAT, null
        );
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
        gl.bindTexture(gl.TEXTURE_2D, null);
        this._tex.pheromone = tex;
    }

    // ANCHOR:FUNCTION_COMPUTE_WORLD_TO_NDC
    // mat3 (column-major for WebGL) that maps world coords → NDC [-1,1]
    _computeWorldToNDC() {
        const sx = 2.0 / SimConfig.WORLD_W;
        const sy = 2.0 / SimConfig.WORLD_H;
        // Column-major mat3:
        // [ sx  0  -1 ]
        // [  0  sy -1 ]
        // [  0  0   1 ]
        this._worldToNDC = new Float32Array([
            sx, 0,  0,
            0,  sy, 0,
           -1, -1,  1,
        ]);
    }

    // ANCHOR:FUNCTION_RESIZE
    resize(w, h) {
        this.canvas.width  = w;
        this.canvas.height = h;
        this.gl.viewport(0, 0, w, h);
    }

    // ANCHOR:FUNCTION_RECORD_FITNESS
    // Call after each GA generation to feed the overlay shader history.
    recordFitness(fit0, fit1) {
        const N = this._fitHistoryCount;
        if (N < 64) {
            this._fitHistory0[N] = fit0;
            this._fitHistory1[N] = fit1;
            this._fitHistoryCount++;
        } else {
            // Shift left
            this._fitHistory0.copyWithin(0, 1);
            this._fitHistory1.copyWithin(0, 1);
            this._fitHistory0[63] = fit0;
            this._fitHistory1[63] = fit1;
        }
        this._fitMax = Math.max(this._fitMax, fit0, fit1);
    }

    // ANCHOR:FUNCTION_RENDER
    // Main render function. Called once per animation frame.
    // Parameters: state {object} — from World.getState()
    render(state) {
        const gl = this.gl;
        if (!gl) return;

        // Resize to match canvas display size
        const dpr = window.devicePixelRatio || 1;
        const w   = Math.floor(this.canvas.clientWidth  * dpr);
        const h   = Math.floor(this.canvas.clientHeight * dpr);
        if (this.canvas.width !== w || this.canvas.height !== h) {
            this.resize(w, h);
        }

        gl.clear(gl.COLOR_BUFFER_BIT);

        if (SimConfig.RENDER_LAYERS.GROUND)     this._renderGround();
        if (SimConfig.RENDER_LAYERS.ANTHILLS)   this._renderAnthills(state.hills);
        if (SimConfig.RENDER_LAYERS.TERRITORY)  this._renderTerritory(state.pheromoneGrid);
        if (SimConfig.RENDER_LAYERS.PHEROMONES) this._renderPheromones(state.pheromoneGrid);
        if (SimConfig.RENDER_LAYERS.FOOD)       this._renderFood(state);
        if (SimConfig.RENDER_LAYERS.ANTS)       this._renderAnts(state);
        if (SimConfig.RENDER_LAYERS.OVERLAY)    this._renderOverlay();
    }

    // ANCHOR:PASS_GROUND
    _renderGround() {
        const gl   = this.gl;
        const prog = this._prog.ground;
        gl.blendFunc(gl.ONE, gl.ZERO); // No blending for ground (opaque)
        prog.use();
        prog.set3f('u_color_a', 0.22, 0.16, 0.10);
        prog.set3f('u_color_b', 0.28, 0.20, 0.13);
        prog.set1f('u_noise_scale', 20.0);
        gl.bindVertexArray(this._vao.quad);
        gl.drawElements(gl.TRIANGLES, 6, gl.UNSIGNED_SHORT, 0);
        gl.bindVertexArray(null);
    }

    // ANCHOR:PASS_ANTHILLS
    _renderAnthills(hills) {
        const gl   = this.gl;
        const prog = this._prog.anthill;
        gl.blendFunc(gl.SRC_ALPHA, gl.ONE_MINUS_SRC_ALPHA);
        prog.use();
        hills.forEach((hill, i) => {
            prog.set2f('u_hill_center',   hill.x, hill.y);
            prog.set1f('u_hill_radius',   SimConfig.HILL_RADIUS);
            prog.set3fv('u_colony_color', hill.color);
            gl.bindVertexArray(this._vao.hills[i]);
            gl.drawElements(gl.TRIANGLES, 6, gl.UNSIGNED_SHORT, 0);
        });
        gl.bindVertexArray(null);
    }

    // ANCHOR:PASS_PHEROMONES
    _renderPheromones(pheromoneGrid) {
        const gl   = this.gl;
        const prog = this._prog.pheromone;

        // Upload pheromone grid to GPU texture only when dirty
        if (pheromoneGrid.isDirty) {
            gl.bindTexture(gl.TEXTURE_2D, this._tex.pheromone);
            gl.texSubImage2D(
                gl.TEXTURE_2D, 0, 0, 0,
                this._phW, this._phH,
                gl.RGBA, gl.FLOAT,
                pheromoneGrid.getBuffer()
            );
            gl.bindTexture(gl.TEXTURE_2D, null);
            pheromoneGrid.markClean();
        }

        gl.blendFunc(gl.SRC_ALPHA, gl.ONE); // Additive for glowing trails
        prog.use();
        gl.activeTexture(gl.TEXTURE0);
        gl.bindTexture(gl.TEXTURE_2D, this._tex.pheromone);
        prog.set1i('u_pheromone_tex', 0);
        prog.set1f('u_alpha_scale', SimConfig.PHEROMONE_ALPHA_SCALE);

        const colors = SimConfig.SIGNAL_COLORS;
        prog.set3fv('u_color_home',   colors[0]);
        prog.set3fv('u_color_food',   colors[1]);
        prog.set3fv('u_color_danger', colors[2]);

        // Colony tint colors for ownership-based marker coloring
        const col0 = SimConfig.COLONY[0].color;
        const col1 = SimConfig.COLONY[1].color;
        prog.set3fv('u_colony0_color', col0);
        prog.set3fv('u_colony1_color', col1);

        gl.bindVertexArray(this._vao.quad);
        gl.drawElements(gl.TRIANGLES, 6, gl.UNSIGNED_SHORT, 0);
        gl.bindVertexArray(null);
        gl.blendFunc(gl.SRC_ALPHA, gl.ONE_MINUS_SRC_ALPHA); // restore
    }

    // ANCHOR:PASS_TERRITORY
    _renderTerritory(pheromoneGrid) {
        const gl   = this.gl;
        const prog = this._prog.territory;

        // Upload pheromone data if dirty (shared with pheromone pass)
        if (pheromoneGrid.isDirty) {
            gl.bindTexture(gl.TEXTURE_2D, this._tex.pheromone);
            gl.texSubImage2D(
                gl.TEXTURE_2D, 0, 0, 0,
                this._phW, this._phH,
                gl.RGBA, gl.FLOAT,
                pheromoneGrid.getBuffer()
            );
            gl.bindTexture(gl.TEXTURE_2D, null);
            // Don't markClean here — pheromone pass may also need to upload
        }

        gl.blendFunc(gl.SRC_ALPHA, gl.ONE_MINUS_SRC_ALPHA);
        prog.use();
        gl.activeTexture(gl.TEXTURE0);
        gl.bindTexture(gl.TEXTURE_2D, this._tex.pheromone);
        prog.set1i('u_pheromone_tex', 0);
        prog.set3fv('u_colony0_color', SimConfig.COLONY[0].color);
        prog.set3fv('u_colony1_color', SimConfig.COLONY[1].color);

        gl.bindVertexArray(this._vao.quad);
        gl.drawElements(gl.TRIANGLES, 6, gl.UNSIGNED_SHORT, 0);
        gl.bindVertexArray(null);
    }

    // ANCHOR:PASS_FOOD
    _renderFood(state) {
        const gl   = this.gl;
        const prog = this._prog.food;
        if (state.foodCount === 0) return;

        gl.blendFunc(gl.SRC_ALPHA, gl.ONE_MINUS_SRC_ALPHA);
        prog.use();
        prog.set3f('u_food_color', 0.35, 0.85, 0.25);
        prog.setMat3('u_worldToNDC', this._worldToNDC);

        // Upload instance data for active food
        gl.bindBuffer(gl.ARRAY_BUFFER, this._vbo.foodInst);
        gl.bufferSubData(gl.ARRAY_BUFFER, 0,
            state.foodInstanceData.subarray(0, state.foodCount * 3));

        gl.bindVertexArray(this._vao.food);
        gl.drawElementsInstanced(gl.TRIANGLES, 6, gl.UNSIGNED_SHORT, 0, state.foodCount);
        gl.bindVertexArray(null);
    }

    // ANCHOR:PASS_ANTS
    _renderAnts(state) {
        const gl   = this.gl;
        const prog = this._prog.ant;
        if (state.antCount === 0) return;

        gl.blendFunc(gl.SRC_ALPHA, gl.ONE_MINUS_SRC_ALPHA);
        prog.use();
        prog.setMat3('u_worldToNDC', this._worldToNDC);
        prog.set1f('u_ant_size', SimConfig.ANT_SIZE);
        prog.set3fv('u_colony0_color', SimConfig.COLONY[0].color);
        prog.set3fv('u_colony1_color', SimConfig.COLONY[1].color);

        // Overlay mode: 0=normal, 1=energy, 2=state, 3=NN
        let mode = 0;
        if (SimConfig.RENDER_LAYERS.ENERGY_MAP)  mode = 1;
        if (SimConfig.RENDER_LAYERS.STATE_VIEW)  mode = 2;
        if (SimConfig.RENDER_LAYERS.NN_ACTIVITY) mode = 3;
        prog.set1i('u_overlay_mode', mode);

        gl.bindBuffer(gl.ARRAY_BUFFER, this._vbo.antInst);
        const antBytes = state.antCount * 8 * 4; // stride=8 floats
        if (antBytes > this._antVboCapacity) {
            this._antVboCapacity = antBytes * 2;
            gl.bufferData(gl.ARRAY_BUFFER, this._antVboCapacity, gl.DYNAMIC_DRAW);
        }
        gl.bufferSubData(gl.ARRAY_BUFFER, 0,
            state.antInstanceData.subarray(0, state.antCount * 8));

        gl.bindVertexArray(this._vao.ant);
        gl.drawElementsInstanced(gl.TRIANGLES, 6, gl.UNSIGNED_SHORT, 0, state.antCount);
        gl.bindVertexArray(null);
    }

    // ANCHOR:PASS_OVERLAY
    _renderOverlay() {
        const gl   = this.gl;
        const prog = this._prog.overlay;
        gl.blendFunc(gl.SRC_ALPHA, gl.ONE_MINUS_SRC_ALPHA);
        prog.use();
        prog.set1fv('u_gen_fitness_c0', this._fitHistory0);
        prog.set1fv('u_gen_fitness_c1', this._fitHistory1);
        prog.set1i('u_history_count', this._fitHistoryCount);
        prog.set1f('u_fitness_max', this._fitMax);
        prog.set3fv('u_colony0_color', SimConfig.COLONY[0].color);
        prog.set3fv('u_colony1_color', SimConfig.COLONY[1].color);

        gl.bindVertexArray(this._vao.quad);
        gl.drawElements(gl.TRIANGLES, 6, gl.UNSIGNED_SHORT, 0);
        gl.bindVertexArray(null);
    }
}
