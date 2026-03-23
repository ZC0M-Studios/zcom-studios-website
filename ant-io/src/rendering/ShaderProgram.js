// =============================================================================
// ANCHOR:SHADER_PROGRAM
// Description: Lightweight WebGL2 shader compilation and uniform management.
//              Caches uniform locations to avoid repeated gl.getUniformLocation calls.
// =============================================================================

export class ShaderProgram {
    // Parameters:
    //   gl       {WebGL2RenderingContext}
    //   vertSrc  {string} - GLSL vertex shader source
    //   fragSrc  {string} - GLSL fragment shader source
    //   label    {string} - debug label
    constructor(gl, vertSrc, fragSrc, label = 'unnamed') {
        this.gl    = gl;
        this.label = label;
        this._locs = {};

        const vert = this._compile(gl.VERTEX_SHADER,   vertSrc, label + ':vert');
        const frag = this._compile(gl.FRAGMENT_SHADER, fragSrc, label + ':frag');
        this.program = this._link(vert, frag, label);

        gl.deleteShader(vert);
        gl.deleteShader(frag);
    }

    _compile(type, src, label) {
        const shader = this.gl.createShader(type);
        this.gl.shaderSource(shader, src);
        this.gl.compileShader(shader);
        if (!this.gl.getShaderParameter(shader, this.gl.COMPILE_STATUS)) {
            const log = this.gl.getShaderInfoLog(shader);
            this.gl.deleteShader(shader);
            throw new Error(`[ShaderProgram] Compile error in ${label}:\n${log}`);
        }
        return shader;
    }

    _link(vert, frag, label) {
        const prog = this.gl.createProgram();
        this.gl.attachShader(prog, vert);
        this.gl.attachShader(prog, frag);
        this.gl.linkProgram(prog);
        if (!this.gl.getProgramParameter(prog, this.gl.LINK_STATUS)) {
            const log = this.gl.getProgramInfoLog(prog);
            this.gl.deleteProgram(prog);
            throw new Error(`[ShaderProgram] Link error in ${label}:\n${log}`);
        }
        return prog;
    }

    use() { this.gl.useProgram(this.program); }

    // ANCHOR:UNIFORM_HELPERS
    _loc(name) {
        if (!(name in this._locs)) {
            this._locs[name] = this.gl.getUniformLocation(this.program, name);
        }
        return this._locs[name];
    }

    set1f(name, v)          { this.gl.uniform1f(this._loc(name), v); }
    set1i(name, v)          { this.gl.uniform1i(this._loc(name), v); }
    set2f(name, x, y)       { this.gl.uniform2f(this._loc(name), x, y); }
    set3f(name, x, y, z)    { this.gl.uniform3f(this._loc(name), x, y, z); }
    set3fv(name, arr)       { this.gl.uniform3fv(this._loc(name), arr); }
    set1fv(name, arr)       { this.gl.uniform1fv(this._loc(name), arr); }
    setMat3(name, m)        { this.gl.uniformMatrix3fv(this._loc(name), false, m); }

    dispose() {
        this.gl.deleteProgram(this.program);
    }
}
