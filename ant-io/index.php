<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ant-IO | Simulation</title>
    <link rel="stylesheet" href="/ant-io/style.css">
</head>
<body>

<!-- Primary WebGL canvas — fills viewport behind HUD -->
<canvas id="ant-io-canvas"></canvas>

<!-- HUD toggle — always visible, outside wrapper -->
<button id="btn-hud-toggle" class="hud-toggle-btn" title="Toggle HUD (H)">HUD</button>

<!-- ========================================================================
     CYBERPUNK HUD OVERLAY — 5-column layout with real-time simulation data
     ======================================================================== -->
<div class="hud-wrapper" id="hud-wrapper">
  <header class="hud-header">
    <div class="header-block">
      <span class="timer" id="main-timer">00:00:00:00</span>
    </div>
    <div class="header-block text-sec" style="text-align:center">
      ANT-IO &nbsp;|&nbsp; COLONY WAR SIMULATION
    </div>
    <div class="header-block text-sec align-right">
      GEN <span id="hdr-gen" class="hl">0</span> &nbsp;|&nbsp;
      TICK <span id="hdr-tick">0</span>
    </div>
  </header>

  <main class="hud-main">
    <!-- ===== LEFT: Event Log ===== -->
    <section class="panel log-panel">
      <div class="panel-title">EVENT LOG</div>
      <div class="log-content" id="log-output"></div>
      <div class="log-footer">
        <span class="text-sec">POPULATION</span>
        <span class="large-num" id="stat-deaths">0</span>
      </div>
    </section>

    <!-- ===== COLONY A Plot ===== -->
    <section class="panel plot-panel">
      <div class="panel-title space-between">
        <span><span class="dot-a">&#9679;</span> COLONY A</span>
        <span class="hl" id="colA-fit">FIT: 0.0</span>
      </div>
      <div class="plot-container" id="plot-colA">
        <canvas class="plot-canvas"></canvas>
        <div class="plot-label large" id="colA-stored">0</div>
        <div class="plot-label small" style="top:8px;right:8px" id="colA-pop">POP: 50</div>
      </div>
    </section>

    <!-- ===== CENTER: Territory + Speed ===== -->
    <section class="panel radar-panel">
      <div class="radar-module">
        <div class="radar-info left">
          <span id="rad-col0-cells" class="hl">0</span>
          <span class="text-sec">CELLS</span>
        </div>
        <div class="radar-target">
          <div class="circle outer"></div>
          <div class="circle inner"></div>
          <div class="crosshair x"></div>
          <div class="crosshair y"></div>
        </div>
        <div class="radar-info right">
          <span id="rad-col1-cells" class="hl">0</span>
          <span class="text-sec">CELLS</span>
        </div>
      </div>
      <div class="radar-divider">TERRITORY</div>
      <div class="territory-bar-wrap">
        <div class="territory-bar">
          <div class="territory-fill col0" id="terr-fill0" style="width:50%"></div>
        </div>
      </div>
      <div class="radar-divider">FOOD</div>
      <div class="radar-stat-row">
        <span>ACTIVE: <span class="hl" id="rad-food">0</span></span>
        <span>A: <span class="dot-a" id="rad-storeA">0</span></span>
        <span>B: <span class="dot-b" id="rad-storeB">0</span></span>
      </div>
      <div class="radar-divider">SPEED</div>
      <div class="speed-control">
        <button id="btn-playpause" class="hud-btn">&#9654;</button>
        <input id="range-speed" type="range" min="0.1" max="8" step="0.1" value="1">
        <span id="speed-val" class="hl">1.0x</span>
      </div>
    </section>

    <!-- ===== COLONY B Plot ===== -->
    <section class="panel plot-panel">
      <div class="panel-title space-between">
        <span class="hl" id="colB-fit">FIT: 0.0</span>
        <span>COLONY B <span class="dot-b">&#9679;</span></span>
      </div>
      <div class="plot-container" id="plot-colB">
        <canvas class="plot-canvas"></canvas>
        <div class="plot-label large" id="colB-stored">0</div>
        <div class="plot-label small" style="top:8px;left:8px" id="colB-pop">POP: 50</div>
      </div>
    </section>

    <!-- ===== RIGHT: Controls ===== -->
    <section class="panel controls-panel">
      <div class="panel-title">CONTROLS</div>
      <div class="control-group">
        <div class="ctrl-btn-row">
          <button class="hud-btn active layer-toggle" data-layer="PHEROMONES" title="1: Pheromone trails">PHERO</button>
          <button class="hud-btn layer-toggle" data-layer="TERRITORY" title="2: Territory map">TERR</button>
        </div>
        <div class="ctrl-btn-row">
          <button class="hud-btn layer-toggle" data-layer="ENERGY_MAP" title="3: Energy heatmap">ENRGY</button>
          <button class="hud-btn layer-toggle" data-layer="STATE_VIEW" title="4: Ant state view">STATE</button>
        </div>
        <div class="ctrl-btn-row">
          <button class="hud-btn layer-toggle" data-layer="NN_ACTIVITY" title="5: Neural network">NN</button>
          <button class="hud-btn layer-toggle" data-layer="OVERLAY" title="6: Fitness graph">GRAPH</button>
        </div>
        <div class="divider-line">LAYERS (1-6)</div>
      </div>
      <div class="control-group">
        <div class="ctrl-btn-row">
          <button id="btn-settings" class="hud-btn accent" title="Settings">SETTINGS</button>
          <button id="btn-export" class="hud-btn" title="Export Soul">EXPORT</button>
        </div>
        <div class="divider-line">TOOLS</div>
      </div>
      <div class="control-group">
        <div class="coords"><span>GEN</span><span id="ctrl-gen" class="hl">0</span></div>
        <div class="coords"><span>TPS</span><span id="ctrl-tps">60</span></div>
        <div class="coords"><span>FOOD</span><span id="ctrl-food">0</span></div>
        <div class="coords"><span>SOUL</span><span id="ctrl-soul">-1</span></div>
        <div class="divider-line">STATUS</div>
      </div>
      <div class="big-stats">
        <span class="sub">STORED</span>
        <div class="nums">
          <span id="ctrl-totalA" class="dot-a">0</span>
          <span id="ctrl-totalB" class="dot-b">0</span>
        </div>
        <div id="chain-status" class="chain-ok">&#9679; CHAIN OK</div>
      </div>
    </section>
  </main>

  <footer class="hud-footer">
    <!-- Left: Genome Grid -->
    <div class="matrix-block">
      <div class="matrix-label">G<br>E<br>N</div>
      <div class="grid-system" id="genome-grid"></div>
    </div>

    <!-- Center: Fitness Terrain -->
    <div class="center-graph-block">
      <div class="graph-header">
        <span class="animate-pulse-fast">FITNESS HISTORY</span>
        <span id="terrain-gen">GEN 0</span>
      </div>
      <div class="graph-line-container">
        <svg viewBox="0 0 100 20" preserveAspectRatio="none" class="vector-line">
          <polyline id="fitness-line-0" points="" fill="none" stroke="var(--col0)" stroke-width="0.5" opacity="0.8"></polyline>
          <polyline id="fitness-line-1" points="" fill="none" stroke="var(--col1)" stroke-width="0.5" opacity="0.8"></polyline>
        </svg>
      </div>
      <div class="graph-header bottom">
        <span>COLONY A vs COLONY B</span>
        <span>BEST FIT / GEN</span>
      </div>
    </div>

    <!-- Right: System Log -->
    <div class="matrix-block">
      <div class="grid-system" id="status-grid"></div>
      <div class="matrix-logs" id="sys-log">
        &gt; ANT-IO v1.0<br>
        &gt; STEADY-STATE GA
      </div>
    </div>
  </footer>
</div>

<!-- Settings modal (unchanged) -->
<div id="settings-overlay" role="dialog" aria-modal="true" aria-label="Simulation Settings">
    <div id="settings-panel">
        <div class="sp-header">
            <span class="sp-title">SIMULATION SETTINGS</span>
            <button id="btn-settings-close" title="Close">&#10005;</button>
        </div>
        <div class="sp-tabs" id="sp-tabs"></div>
        <div class="sp-body"  id="sp-body"></div>
        <div class="sp-footer">
            <button id="btn-settings-reset">Reset Tab</button>
            <button id="btn-settings-apply">Apply</button>
        </div>
    </div>
</div>

<script type="module">
// =============================================================================
// ANCHOR:MAIN_SCRIPT
// Description: Bootstrap — wires HUD, controls, settings modal, and AntIO.
// =============================================================================

import { AntIO }     from '/ant-io/src/AntIO.js';
import { SimConfig } from '/ant-io/src/config/SimConfig.js';

const canvas     = document.getElementById('ant-io-canvas');
const hudBody    = document.createElement('div'); // hidden — AntIO needs it but we use custom HUD
const btnPlay    = document.getElementById('btn-playpause');
const rangeSpeed = document.getElementById('range-speed');
const speedVal   = document.getElementById('speed-val');
const chainStatus = document.getElementById('chain-status');

const app = new AntIO(canvas, hudBody);

(async () => {
    try {
        await app.init();
        app.start();
        btnPlay.textContent = '\u25B6';
        logEvent('SYS_INIT colony_war started');
    } catch (err) {
        logEvent('INIT_FAIL ' + err.message);
        console.error('[Ant-IO] Init error:', err);
    }
})();

// --- Play / Pause -----------------------------------------------------------
btnPlay.addEventListener('click', () => {
    const running = app.toggle();
    btnPlay.textContent = running ? '\u25B6' : '\u23F8';
});

// --- Speed control ----------------------------------------------------------
rangeSpeed.addEventListener('input', () => {
    const v = parseFloat(rangeSpeed.value);
    app.setSpeed(v);
    speedVal.textContent = v.toFixed(1) + 'x';
});

// --- Layer toggles (generic for all data-layer buttons) ---------------------
document.querySelectorAll('.layer-toggle').forEach(btn => {
    btn.addEventListener('click', function() {
        app.toggleLayer(this.dataset.layer);
        this.classList.toggle('active');
    });
});

// Keyboard shortcuts 1-6 for layer toggles
const LAYER_KEYS = ['PHEROMONES', 'TERRITORY', 'ENERGY_MAP', 'STATE_VIEW', 'NN_ACTIVITY', 'OVERLAY'];
document.addEventListener('keydown', e => {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') return;
    const idx = parseInt(e.key) - 1;
    if (idx >= 0 && idx < LAYER_KEYS.length) {
        const layer = LAYER_KEYS[idx];
        app.toggleLayer(layer);
        const btn = document.querySelector(`.layer-toggle[data-layer="${layer}"]`);
        if (btn) btn.classList.toggle('active');
    }
});

// --- Soul export ------------------------------------------------------------
document.getElementById('btn-export').addEventListener('click', () => {
    window.open('/ant-io/api/soul_export.php', '_blank');
});

// --- HUD Toggle (button + H key) --------------------------------------------
const hudWrapper = document.getElementById('hud-wrapper');
const hudToggle  = document.getElementById('btn-hud-toggle');
hudToggle.addEventListener('click', () => {
    hudWrapper.classList.toggle('hud-hidden');
    hudToggle.classList.toggle('active');
});
document.addEventListener('keydown', e => {
    if (e.key === 'h' || e.key === 'H') {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') return;
        hudWrapper.classList.toggle('hud-hidden');
        hudToggle.classList.toggle('active');
    }
});

// --- Chain verification -----------------------------------------------------
app.soul.readChain(0, true).then(data => {
    if (data.chain_valid === false) {
        chainStatus.textContent = `\u26A0 TAMPERED #${data.first_bad_block}`;
        chainStatus.className = 'chain-bad';
    } else if (data.chain_valid === true) {
        chainStatus.textContent = `\u25CF CHAIN OK (${data.count})`;
    } else {
        chainStatus.textContent = '\u25CF NO BLOCKS';
    }
}).catch(() => {
    chainStatus.textContent = '? DB OFFLINE';
    chainStatus.className = '';
});

// =============================================================================
// ANCHOR:HUD_UPDATE — Polls simulation stats and updates all HUD panels
// =============================================================================

const logEl   = document.getElementById('log-output');
let _startTime = performance.now();
let _prevStats = null;
let _deathCount = 0;
let _fitnessHistory0 = [];
let _fitnessHistory1 = [];

function logEvent(msg) {
    const p = document.createElement('p');
    p.textContent = '> ' + msg;
    logEl.appendChild(p);
    if (logEl.childElementCount > 18) logEl.removeChild(logEl.firstChild);
    logEl.scrollTop = logEl.scrollHeight;
}

// Timer
setInterval(() => {
    const elapsed = (performance.now() - _startTime) / 1000;
    const h = Math.floor(elapsed / 3600);
    const m = Math.floor((elapsed % 3600) / 60);
    const s = Math.floor(elapsed % 60);
    const f = Math.floor((elapsed * 30) % 30);
    const fmt = n => n.toString().padStart(2, '0');
    document.getElementById('main-timer').textContent = `${fmt(h)}:${fmt(m)}:${fmt(s)}:${fmt(f)}`;
}, 33);

// Main HUD update (every 200ms)
setInterval(() => {
    if (!app.world) return;
    const s = app.world.getStats();

    // Header
    document.getElementById('hdr-gen').textContent = s.generation;
    document.getElementById('hdr-tick').textContent = s.tick;

    // Colony A
    document.getElementById('colA-fit').textContent = 'FIT: ' + s.bestFitness0;
    document.getElementById('colA-stored').textContent = s.foodStored0;
    document.getElementById('colA-pop').textContent = 'POP: ' + s.pop0;

    // Colony B
    document.getElementById('colB-fit').textContent = 'FIT: ' + s.bestFitness1;
    document.getElementById('colB-stored').textContent = s.foodStored1;
    document.getElementById('colB-pop').textContent = 'POP: ' + s.pop1;

    // Territory (pheromone ownership)
    const ph = app.world.pheromoneGrid;
    let c0 = 0, c1 = 0;
    for (let i = 3; i < ph.data.length; i += 4) {
        if (ph.data[i] > 0.5) c1++;
        else if (ph.data[i] > 0.1) c0++;
    }
    document.getElementById('rad-col0-cells').textContent = c0;
    document.getElementById('rad-col1-cells').textContent = c1;
    const total = c0 + c1 || 1;
    document.getElementById('terr-fill0').style.width = ((c0 / total) * 100).toFixed(1) + '%';

    // Food
    document.getElementById('rad-food').textContent = s.activeFood;
    document.getElementById('rad-storeA').textContent = s.foodStored0;
    document.getElementById('rad-storeB').textContent = s.foodStored1;

    // Right panel
    document.getElementById('ctrl-gen').textContent = s.generation;
    document.getElementById('ctrl-tps').textContent = SimConfig.TARGET_SIM_TPS;
    document.getElementById('ctrl-food').textContent = s.activeFood;
    document.getElementById('ctrl-soul').textContent = s.soulBlock;
    document.getElementById('ctrl-totalA').textContent = s.foodStored0;
    document.getElementById('ctrl-totalB').textContent = s.foodStored1;

    // Population as deaths stat
    document.getElementById('stat-deaths').textContent = s.pop0 + s.pop1;

    // Log events on stat changes
    if (_prevStats) {
        if (s.generation > _prevStats.generation) {
            logEvent(`GEN_UP generation ${s.generation}`);

            // Record per-generation genetic data for colony graphs
            const bestA = parseFloat(s.bestFitness0) || 0;
            const bestB = parseFloat(s.bestFitness1) || 0;
            const avgA  = app.world.colony0.reduce((s,a) => s + a.genome.fitnessScore, 0) / (app.world.colony0.length || 1);
            const avgB  = app.world.colony1.reduce((s,a) => s + a.genome.fitnessScore, 0) / (app.world.colony1.length || 1);
            _genDataA.best.push(bestA);
            _genDataA.avg.push(avgA);
            _genDataA.stored.push(s.foodStored0);
            _genDataB.best.push(bestB);
            _genDataB.avg.push(avgB);
            _genDataB.stored.push(s.foodStored1);

            // Footer fitness terrain graph
            _fitnessHistory0.push(bestA);
            _fitnessHistory1.push(bestB);
            updateFitnessGraph();
            document.getElementById('terrain-gen').textContent = 'GEN ' + s.generation;
        }
        if (s.foodStored0 > _prevStats.foodStored0) {
            logEvent(`FOOD_DEPOSIT colony:A +${s.foodStored0 - _prevStats.foodStored0}`);
        }
        if (s.foodStored1 > _prevStats.foodStored1) {
            logEvent(`FOOD_DEPOSIT colony:B +${s.foodStored1 - _prevStats.foodStored1}`);
        }
    }
    _prevStats = {...s};

}, 200);

// Periodic system log
setInterval(() => {
    if (!app.world) return;
    const hex = Math.floor(Math.random() * 16777215).toString(16).toUpperCase().padStart(6, '0');
    logEvent(`SYS_CHK 0x${hex} OK`);
}, 3000);

// =============================================================================
// ANCHOR:GENETIC_GRAPHS — Real-time evolution line graphs in colony panels
// =============================================================================

// Per-generation data arrays (pushed each time generation increments)
const _genDataA = { best: [], avg: [], stored: [] };
const _genDataB = { best: [], avg: [], stored: [] };

class GeneticGraph {
    constructor(containerId, data, rgbBright, rgbDim) {
        this.container = document.getElementById(containerId);
        if (!this.container) return;
        this.canvas = this.container.querySelector('canvas');
        this.ctx = this.canvas.getContext('2d');
        this.data = data;
        this.rgbBright = rgbBright;
        this.rgbDim = rgbDim;
        this.maxPts = 60;
        this._resize();
        this._draw = this._draw.bind(this);
        requestAnimationFrame(this._draw);
    }
    _resize() {
        const r = this.container.getBoundingClientRect();
        this.canvas.width = r.width;
        this.canvas.height = r.height;
    }
    _draw() {
        const c = this.ctx, w = this.canvas.width, h = this.canvas.height;
        c.clearRect(0, 0, w, h);

        const pad = { t: 8, b: 14, l: 4, r: 4 };
        const gw = w - pad.l - pad.r;
        const gh = h - pad.t - pad.b;

        // Grid lines
        c.strokeStyle = 'rgba(86,135,152,0.12)';
        c.lineWidth = 0.5;
        for (let i = 0; i <= 4; i++) {
            const y = pad.t + (gh / 4) * i;
            c.beginPath(); c.moveTo(pad.l, y); c.lineTo(w - pad.r, y); c.stroke();
        }

        const best = this.data.best.slice(-this.maxPts);
        const avg  = this.data.avg.slice(-this.maxPts);
        const stored = this.data.stored.slice(-this.maxPts);
        if (best.length < 2) { requestAnimationFrame(this._draw); return; }

        // Compute Y scale from best fitness
        const allVals = [...best, ...avg];
        const maxVal = Math.max(...allVals, 1);
        const minVal = Math.min(...allVals, 0);
        const range = maxVal - minVal || 1;

        const toX = i => pad.l + (i / (best.length - 1)) * gw;
        const toY = v => pad.t + gh - ((v - minVal) / range) * gh;

        // Draw stored food as filled area (secondary metric)
        if (stored.length >= 2) {
            const maxStored = Math.max(...stored, 1);
            c.fillStyle = `rgba(${this.rgbBright}, 0.06)`;
            c.beginPath();
            c.moveTo(toX(0), pad.t + gh);
            for (let i = 0; i < stored.length; i++) {
                c.lineTo(toX(i), pad.t + gh - (stored[i] / maxStored) * gh * 0.5);
            }
            c.lineTo(toX(stored.length - 1), pad.t + gh);
            c.closePath();
            c.fill();
        }

        // Draw avg fitness line (dim)
        if (avg.length >= 2) {
            c.strokeStyle = `rgba(${this.rgbDim}, 0.5)`;
            c.lineWidth = 1;
            c.beginPath();
            for (let i = 0; i < avg.length; i++) {
                const x = toX(i), y = toY(avg[i]);
                i === 0 ? c.moveTo(x, y) : c.lineTo(x, y);
            }
            c.stroke();
        }

        // Draw best fitness line (bright)
        c.strokeStyle = `rgba(${this.rgbBright}, 0.9)`;
        c.lineWidth = 1.5;
        c.shadowBlur = 4;
        c.shadowColor = `rgba(${this.rgbBright}, 0.5)`;
        c.beginPath();
        for (let i = 0; i < best.length; i++) {
            const x = toX(i), y = toY(best[i]);
            i === 0 ? c.moveTo(x, y) : c.lineTo(x, y);
        }
        c.stroke();
        c.shadowBlur = 0;

        // Latest value dot
        const lastX = toX(best.length - 1);
        const lastY = toY(best[best.length - 1]);
        c.beginPath();
        c.arc(lastX, lastY, 3, 0, Math.PI * 2);
        c.fillStyle = `rgba(${this.rgbBright}, 1)`;
        c.fill();

        // Axis labels
        c.fillStyle = 'rgba(86,135,152,0.5)';
        c.font = '7px JetBrains Mono, monospace';
        c.textAlign = 'left';
        c.fillText(maxVal.toFixed(0), pad.l + 2, pad.t + 8);
        c.fillText(minVal.toFixed(0), pad.l + 2, pad.t + gh - 2);
        c.textAlign = 'right';
        c.fillText('GEN ' + (this.data.best.length), w - pad.r - 2, h - 2);

        requestAnimationFrame(this._draw);
    }
}

const graphA = new GeneticGraph('plot-colA', _genDataA, '255, 72, 20',  '255, 120, 80');
const graphB = new GeneticGraph('plot-colB', _genDataB, '26, 122, 255', '100, 160, 255');

// =============================================================================
// ANCHOR:FITNESS_GRAPH — SVG terrain line in footer
// =============================================================================

function updateFitnessGraph() {
    const maxPts = 50;
    const draw = (arr, elId) => {
        const el = document.getElementById(elId);
        if (!el || arr.length < 2) return;
        const recent = arr.slice(-maxPts);
        const maxVal = Math.max(...recent, 1);
        let pts = '';
        for (let i = 0; i < recent.length; i++) {
            const x = (i / (recent.length - 1)) * 100;
            const y = 18 - (recent[i] / maxVal) * 16;
            pts += `${x.toFixed(1)},${y.toFixed(1)} `;
        }
        el.setAttribute('points', pts.trim());
    };
    draw(_fitnessHistory0, 'fitness-line-0');
    draw(_fitnessHistory1, 'fitness-line-1');
}

// =============================================================================
// ANCHOR:GENOME_GRID — Footer matrix boxes
// =============================================================================

function generateGrid(containerId, count) {
    const el = document.getElementById(containerId);
    if (!el) return;
    const chars = 'ACGT01-.XY*'.split('');
    for (let i = 0; i < count; i++) {
        const box = document.createElement('div');
        box.classList.add('grid-box');
        if (Math.random() > 0.4) box.textContent = chars[Math.floor(Math.random() * chars.length)];
        if (Math.random() > 0.82) box.classList.add('active');
        el.appendChild(box);
    }
}
generateGrid('genome-grid', 42);
generateGrid('status-grid', 42);

// =============================================================================
// ANCHOR:SETTINGS_MODAL (unchanged — data-driven settings panel)
// =============================================================================

const SETTINGS_SCHEMA = {
    'Simulation': [
        { key: 'TARGET_SIM_TPS',          label: 'Tick Rate',          type: 'range',  min: 10,   max: 120,  step: 1,      unit: 'tps' },
        { key: 'MAX_STEPS_PER_FRAME',     label: 'Max Steps / Frame',  type: 'range',  min: 1,    max: 20,   step: 1 },
    ],
    'Genetics': [
        { key: 'GA_MUTATION_RATE',    label: 'Mutation Rate',    type: 'range',  min: 0.001, max: 0.30, step: 0.001, pct: true },
        { key: 'GA_MUTATION_SCALE',   label: 'Mutation Scale',   type: 'range',  min: 0.01,  max: 1.0,  step: 0.01,  decimals: 2 },
        { key: 'GA_ELITE_FRACTION',   label: 'Elite Fraction',   type: 'range',  min: 0.05,  max: 0.5,  step: 0.05,  pct: true },
        { key: 'GA_TOURNAMENT_SIZE',  label: 'Tournament Size',  type: 'range',  min: 2,     max: 20,   step: 1 },
        { key: 'GA_CROSSOVER_MODE',   label: 'Crossover Mode',   type: 'select', options: ['uniform', 'single_point'] },
    ],
    'Ants': [
        { key: 'POP_PER_COLONY',       label: 'Initial Pop',    type: 'range', min: 5,   max: 200, step: 5 },
        { key: 'POP_MAX_COLONY',       label: 'Max Pop',        type: 'range', min: 20,  max: 300, step: 10 },
        { key: 'ANT_SPEED_BASE',       label: 'Base Speed',     type: 'range', min: 0.5, max: 15,  step: 0.5,  unit: 'm/s' },
        { key: 'ANT_VISION_BASE',      label: 'Vision Radius',  type: 'range', min: 1.0, max: 20,  step: 0.5,  unit: 'm' },
        { key: 'ANT_SIZE',             label: 'Ant Size',       type: 'range', min: 0.1, max: 1.5, step: 0.05, unit: 'm', decimals: 2 },
        { key: 'ANT_ENERGY_MAX',       label: 'Max Energy',     type: 'range', min: 20,  max: 300, step: 10 },
        { key: 'ANT_ENERGY_DRAIN',     label: 'Energy Drain',   type: 'range', min: 0.01, max: 0.5, step: 0.01, decimals: 2, note: 'Per tick' },
        { key: 'ANT_ENERGY_CARRY_MULT',label: 'Carry Drain x',  type: 'range', min: 1.0, max: 3.0, step: 0.1, decimals: 1 },
        { key: 'ANT_SPAWN_COST',       label: 'Spawn Cost',     type: 'range', min: 5,   max: 50,  step: 1,  note: 'Food to spawn 1 ant' },
        { key: 'HILL_RADIUS',          label: 'Hill Radius',    type: 'range', min: 1.0, max: 10,  step: 0.5, unit: 'm' },
    ],
    'Pheromones': [
        { key: 'PHEROMONE_FADE_RATE',     label: 'Fade Rate',     type: 'range', min: 0.900, max: 1.000, step: 0.0005, decimals: 4 },
        { key: 'PHEROMONE_EMIT_STRENGTH', label: 'Emit Strength', type: 'range', min: 0.01,  max: 0.50,  step: 0.005,  decimals: 3 },
        { key: 'PHEROMONE_ALPHA_SCALE',   label: 'Visual Alpha',  type: 'range', min: 0.0,   max: 1.0,   step: 0.05,   decimals: 2 },
        { key: 'MARKER_CONTEST_RATE',     label: 'Contest Rate',  type: 'range', min: 0.001, max: 0.2,   step: 0.001,  decimals: 3 },
        { key: 'EXPLORER_FRACTION',       label: 'Explorer %',    type: 'range', min: 0.0,   max: 0.5,   step: 0.01,   pct: true },
        { key: 'EXPLORER_JITTER',         label: 'Explorer Jitter', type: 'range', min: 0.0, max: 2.0,   step: 0.05,   decimals: 2, unit: 'rad' },
    ],
    'Food': [
        { key: 'FOOD_COUNT_MIN', label: 'Min Count',     type: 'range', min: 1,   max: 100, step: 1 },
        { key: 'FOOD_COUNT_MAX', label: 'Max Count',     type: 'range', min: 1,   max: 200, step: 1 },
        { key: 'FOOD_VALUE',     label: 'Value',         type: 'range', min: 1,   max: 100, step: 1 },
        { key: 'FOOD_MARGIN',    label: 'Spawn Margin',  type: 'range', min: 1,   max: 30,  step: 1,  unit: 'm' },
        { key: 'FOOD_SIZE',      label: 'Food Size',     type: 'range', min: 0.1, max: 3.0, step: 0.1, unit: 'm', decimals: 1 },
    ],
    'Soul': [
        { key: 'SOUL_ELITE_COUNT',         label: 'Elite Count',    type: 'range',    min: 1,  max: 50,   step: 1 },
        { key: 'SOUL_SAVE_INTERVAL',       label: 'Save Interval',  type: 'range',    min: 0,  max: 3000, step: 30, unit: 'ticks' },
        { key: 'SOUL_WRITE_ON_GENERATION', label: 'Write on GA',    type: 'checkbox' },
    ],
};

const DEFAULTS = {};
for (const fields of Object.values(SETTINGS_SCHEMA)) {
    for (const f of fields) DEFAULTS[f.key] = SimConfig[f.key];
}

const spTabs = document.getElementById('sp-tabs');
const spBody = document.getElementById('sp-body');

Object.keys(SETTINGS_SCHEMA).forEach((tab, i) => {
    const btn = document.createElement('button');
    btn.className = 'sp-tab' + (i === 0 ? ' active' : '');
    btn.textContent = tab;
    btn.dataset.tab = tab;
    spTabs.appendChild(btn);

    const panel = document.createElement('div');
    panel.className = 'sp-panel' + (i === 0 ? ' active' : '');
    panel.id = 'sp-panel-' + tab;
    for (const field of SETTINGS_SCHEMA[tab]) panel.appendChild(buildRow(field));
    spBody.appendChild(panel);
});

function formatVal(field, raw) {
    const v = parseFloat(raw);
    if (field.pct)      return (v * 100).toFixed(1) + '%';
    if (field.unit)     return (field.decimals != null ? v.toFixed(field.decimals) : v) + ' ' + field.unit;
    if (field.decimals != null) return v.toFixed(field.decimals);
    return String(raw);
}

function buildRow(field) {
    const wrap = document.createElement('div');
    wrap.className = 'sp-row';
    wrap.dataset.key = field.key;
    const label = document.createElement('span');
    label.className = 'sp-row-label';
    label.textContent = field.label;

    if (field.type === 'range') {
        const input = document.createElement('input');
        Object.assign(input, { type: 'range', min: field.min, max: field.max, step: field.step, value: SimConfig[field.key] });
        input.dataset.key = field.key;
        const valEl = document.createElement('span');
        valEl.className = 'sp-val';
        valEl.textContent = formatVal(field, SimConfig[field.key]);
        input.addEventListener('input', () => {
            const v = field.step % 1 === 0 ? parseInt(input.value) : parseFloat(input.value);
            valEl.textContent = formatVal(field, v);
        });
        wrap.append(label, input, valEl);
    } else if (field.type === 'select') {
        const sel = document.createElement('select');
        sel.dataset.key = field.key;
        for (const opt of field.options) {
            const o = document.createElement('option');
            o.value = opt; o.textContent = opt.replace('_', ' ');
            if (SimConfig[field.key] === opt) o.selected = true;
            sel.appendChild(o);
        }
        wrap.append(label, sel);
    } else if (field.type === 'checkbox') {
        const lbl = document.createElement('label');
        lbl.className = 'sp-toggle';
        const chk = document.createElement('input');
        chk.type = 'checkbox'; chk.checked = !!SimConfig[field.key]; chk.dataset.key = field.key;
        const track = document.createElement('span');
        track.className = 'sp-toggle-track';
        lbl.append(chk, track);
        wrap.append(label, lbl);
    }

    if (field.note) {
        const note = document.createElement('div');
        note.className = 'sp-note';
        note.textContent = field.note;
        const outer = document.createElement('div');
        outer.append(wrap, note);
        return outer;
    }
    return wrap;
}

// Settings tab switching
spTabs.addEventListener('click', e => {
    const btn = e.target.closest('.sp-tab');
    if (!btn) return;
    document.querySelectorAll('.sp-tab, .sp-panel').forEach(el => el.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('sp-panel-' + btn.dataset.tab).classList.add('active');
});

// Settings open/close
const overlay = document.getElementById('settings-overlay');
document.getElementById('btn-settings').addEventListener('click', () => overlay.classList.add('open'));
document.getElementById('btn-settings-close').addEventListener('click', () => overlay.classList.remove('open'));
overlay.addEventListener('click', e => { if (e.target === overlay) overlay.classList.remove('open'); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') overlay.classList.remove('open'); });

// Settings apply
document.getElementById('btn-settings-apply').addEventListener('click', () => {
    const activePanel = spBody.querySelector('.sp-panel.active');
    if (!activePanel) return;
    activePanel.querySelectorAll('[data-key]').forEach(el => {
        const key = el.dataset.key;
        const schema = Object.values(SETTINGS_SCHEMA).flat().find(f => f.key === key);
        if (!schema) return;
        let value;
        if (schema.type === 'range') value = schema.step % 1 === 0 ? parseInt(el.value) : parseFloat(el.value);
        else if (schema.type === 'select') value = el.value;
        else if (schema.type === 'checkbox') value = el.checked;
        else return;
        app.applyConfigPatch(key, value);
    });
    const btn = document.getElementById('btn-settings-apply');
    btn.textContent = '\u2713 Applied';
    setTimeout(() => { btn.textContent = 'Apply'; }, 800);
});

// Live-apply
spBody.addEventListener('input', e => {
    if (e.target.type !== 'range') return;
    const schema = Object.values(SETTINGS_SCHEMA).flat().find(f => f.key === e.target.dataset.key);
    if (!schema) return;
    app.applyConfigPatch(e.target.dataset.key, schema.step % 1 === 0 ? parseInt(e.target.value) : parseFloat(e.target.value));
});
spBody.addEventListener('change', e => {
    if (e.target.type === 'checkbox') app.applyConfigPatch(e.target.dataset.key, e.target.checked);
    if (e.target.tagName === 'SELECT') app.applyConfigPatch(e.target.dataset.key, e.target.value);
});

// Reset tab
document.getElementById('btn-settings-reset').addEventListener('click', () => {
    const activePanel = spBody.querySelector('.sp-panel.active');
    const activeTab = spTabs.querySelector('.sp-tab.active');
    if (!activePanel || !activeTab) return;
    for (const field of SETTINGS_SCHEMA[activeTab.dataset.tab]) {
        const dv = DEFAULTS[field.key];
        app.applyConfigPatch(field.key, dv);
        const el = activePanel.querySelector(`[data-key="${field.key}"]`);
        if (!el) continue;
        if (field.type === 'range') { el.value = dv; const v = el.nextElementSibling; if (v?.classList.contains('sp-val')) v.textContent = formatVal(field, dv); }
        else if (field.type === 'select') el.value = dv;
        else if (field.type === 'checkbox') el.checked = !!dv;
    }
});

window.antIO = app;
window.SimConfig = SimConfig;
</script>

</body>
</html>
