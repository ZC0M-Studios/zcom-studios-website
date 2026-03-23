// =============================================================================
// ANCHOR:ANTIO
// Description: Top-level module. Wires World, Renderer, Soul, and the HUD.
//              Runs the fixed-timestep simulation loop with decoupled rendering.
//
// Loop structure:
//   accumulator += dt × speedMultiplier
//   while accumulator >= tickDuration && steps < MAX_STEPS:
//       world.tick(tickDuration)
//       accumulator -= tickDuration
//   renderer.render(world.getState())
//
// Usage:
//   const app = new AntIO(canvas, hudEl, controlsEl);
//   await app.init();
//   app.start();
// =============================================================================

import { SimConfig }  from './config/SimConfig.js';
import { Soul }       from './core/Soul.js';
import { World }      from './simulation/World.js';
import { Renderer }   from './rendering/Renderer.js';

export class AntIO {
    // Parameters:
    //   canvas   {HTMLCanvasElement}
    //   hudEl    {HTMLElement} - HUD container
    constructor(canvas, hudEl) {
        this.canvas  = canvas;
        this.hudEl   = hudEl;

        this.soul     = new Soul();
        this.world    = new World(this.soul);
        this.renderer = new Renderer(canvas);

        this._running       = false;
        this._rafId         = null;
        this._lastTimestamp = 0;
        this._accumulator   = 0;
        this._speedMult     = 1.0;
        this._frameCount    = 0;
        this._tickDuration  = 1.0 / SimConfig.TARGET_SIM_TPS;
        this._lastRecordedGen = -1;

        // Bind loop to preserve 'this'
        this._loop = this._loop.bind(this);
    }

    // ANCHOR:FUNCTION_INIT
    // Must be awaited. Initializes renderer (compiles shaders) and world (genesis ants).
    async init() {
        console.info('[AntIO] Initializing...');

        // Sync Soul chain tip (non-fatal if DB is empty or unreachable)
        await this.soul.syncChainTip().catch(() => {});

        // Initialize renderer (WebGL2 setup, shader compilation)
        await this.renderer.init();

        // Initialize world (async genome creation for genesis population)
        await this.world.init();

        console.info('[AntIO] Ready.');
    }

    // ANCHOR:FUNCTION_START
    start() {
        if (this._running) return;
        this._running      = true;
        this._lastTimestamp = performance.now();
        this._rafId        = requestAnimationFrame(this._loop);
        console.info('[AntIO] Running');
    }

    // ANCHOR:FUNCTION_PAUSE
    pause() {
        if (!this._running) return;
        this._running = false;
        if (this._rafId !== null) {
            cancelAnimationFrame(this._rafId);
            this._rafId = null;
        }
    }

    // ANCHOR:FUNCTION_TOGGLE
    toggle() {
        this._running ? this.pause() : this.start();
        return this._running;
    }

    // ANCHOR:FUNCTION_SET_SPEED
    // Parameters: mult {number} - speed multiplier (1.0 = realtime, 4.0 = 4× speed)
    setSpeed(mult) {
        this._speedMult = Math.max(0.1, Math.min(10.0, mult));
    }

    // ANCHOR:FUNCTION_TOGGLE_LAYER
    toggleLayer(name) {
        if (name in SimConfig.RENDER_LAYERS) {
            SimConfig.RENDER_LAYERS[name] = !SimConfig.RENDER_LAYERS[name];
        }
    }

    // ANCHOR:FUNCTION_APPLY_CONFIG_PATCH
    // Applies a single runtime config change to SimConfig and updates any
    // derived internal state that depends on it.
    // Parameters: key {string}, value {any}
    applyConfigPatch(key, value) {
        SimConfig[key] = value;
        // TARGET_SIM_TPS controls the fixed-step tick duration used in the loop
        if (key === 'TARGET_SIM_TPS') {
            this._tickDuration = 1.0 / value;
        }
    }

    // Returns a snapshot of current SimConfig values for the settings UI
    getConfigSnapshot() {
        return SimConfig;
    }

    // ANCHOR:FUNCTION_LOOP
    // Fixed-timestep loop with uncapped render rate.
    async _loop(timestamp) {
        if (!this._running) return;

        const dt = Math.min((timestamp - this._lastTimestamp) / 1000, 0.1); // cap at 100ms
        this._lastTimestamp = timestamp;
        this._accumulator  += dt * this._speedMult;

        let steps = 0;
        while (this._accumulator >= this._tickDuration &&
               steps < SimConfig.MAX_STEPS_PER_FRAME) {
            await this.world.tick(this._tickDuration);
            this._accumulator -= this._tickDuration;
            steps++;
        }

        // Render latest state (always once per animation frame)
        this.renderer.render(this.world.getState());

        // Update HUD every 10 frames
        this._frameCount++;
        if (this._frameCount % 10 === 0) {
            this._updateHUD(this.world.getStats());
        }

        // Feed fitness history to overlay shader when generation changes
        const state = this.world.getState();
        if (state.generation !== this._lastRecordedGen && state.generation > 0) {
            this._lastRecordedGen = state.generation;
            const stats = this.world.getStats();
            this.renderer.recordFitness(
                parseFloat(stats.bestFitness0) || 0,
                parseFloat(stats.bestFitness1) || 0
            );
        }

        this._rafId = requestAnimationFrame(this._loop);
    }

    // ANCHOR:FUNCTION_UPDATE_HUD
    _updateHUD(stats) {
        if (!this.hudEl) return;
        this.hudEl.innerHTML = `
            <div class="hud-row">Gen <span class="val">${stats.generation}</span>
                &nbsp;Tick <span class="val">${stats.tick}</span></div>
            <div class="hud-row">Food left: <span class="val">${stats.activeFood}</span></div>
            <div class="hud-row colony0">Colony A &nbsp;Fit: <span class="val">${stats.bestFitness0}</span>
                &nbsp;Stored: <span class="val">${stats.foodStored0}</span></div>
            <div class="hud-row colony1">Colony B &nbsp;Fit: <span class="val">${stats.bestFitness1}</span>
                &nbsp;Stored: <span class="val">${stats.foodStored1}</span></div>
            <div class="hud-row soul">Soul block: <span class="val">${stats.soulBlock}</span></div>
        `;
    }

    get isRunning() { return this._running; }
    get generation() { return this.world?.ga?.currentGeneration ?? 0; }
}
