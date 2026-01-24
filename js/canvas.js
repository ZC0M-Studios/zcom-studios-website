/* ========================================================
    //ANCHOR [CANVAS_PARTICLE_SYSTEM]
    FUNCTION: N/A
-----------------------------------------------------------
    Parameters: N/A
    Returns: N/A
    Description: This file contains the logic for a three-layer particle system with interactive cross-shaped particles.
    UniqueID: 123465
=========================================================== */

/*=========================================================
     Custom dynamic interactive canvas background
     for the index page as the header background
=========================================================*/

// Centralized configuration for the canvas and particle system.
// You can override any of these values by setting `window.CONFIG` before this script runs.
// All properties are documented inline so you know what each does.
const CONFIG = window.CONFIG || {
    // ID of the canvas element to draw into
    canvasId: 'headerCanvas',

    // Canvas sizing. Default uses the window viewport.
    // These can be either numbers or functions that return numbers.
    size: {
        // Function that returns canvas width
        width: () => window.innerWidth,
        // Function that returns canvas height
        height: () => window.innerHeight
    },

    // Mouse interactions
    mouse: {
        enabled: true // true to enable hover-based interactions, false to disable
    },

    // Default color settings. Each layer may optionally override color in `particles.layers[].color`.
    color: {
        base: [0, 204, 255], // default RGB color used when a layer doesn't specify its own color
        alphaBase: 0.1,     // base opacity for particles (0 = transparent, 1 = opaque)
        alphaLayerStep: 0.1,// added opacity per layer index (smaller step is less dramatic)
        alphaHoverBase: 0.1,// extra base opacity applied when particle is hovered
        alphaHoverExtra: 0.2 // additional hover opacity scaled by proximity
    },

    // Particle system parameters
    particles: {
        // Layers defines visual groups. Each entry is a layer config:
        // { size, spacing, color }
        // - size: half-length of the cross (in px)
        // - spacing: grid spacing between particles for that layer (in px)
        // - color: optional override for this layer's color (RGB array [r,g,b] or hex string like '#00ccff')
        layers: [
            { size: 12, spacing: 150, color: [0, 150, 200] },
            { size: 8, spacing: 100, color: [200, 200, 200] },
            { size: 4, spacing: 50, color: [0, 200, 255] }
        ],

        // Density controls a per-particle randomness factor. Not currently used to add/remove
        // particles automatically, but available for future tweaks.
        densityRange: [10, 30],

        // Distance (in px) from mouse at which hover effects start to apply
        maxDistance: 150,

        // Larger value = smaller size change on hover (divisor)
        sizeHoverFactor: 20,

        // Stroke width divider: particle line width = size / strokeWidthDivider
        strokeWidthDivider: 4
    },

    // Wave motion parameters that give particles a gentle floating effect
    wave: {
        amplitudeFactor: 4,       // multiplies oscillation amplitude per layer depth
        spatialDivisor: 20,       // divisor for spatial frequency (larger = slower spatial changes)
        timeDivisor: 1000         // divisor for time frequency (larger = slower motion)
    },

    // Click glitch effect configuration for a "cyber-hacker" aesthetic.
    glitch: {
        enabled: true,
        // Impulsive screen-tearing effect
        impulseDuration: 100,
        sliceCountRange: [30, 90],       // How long the screen tearing lasts (in ms). Short for an impulsive feel.
        sliceHeight: [2, 15],       // Min/max height of a slice.
        displacement: [15, 60],     // Min/max horizontal shift of a slice.
        
        // Text-based glitch artifacts
        textCount: 1,
        font: '16px Jura',
        color: [255, 100, 0],         // Glitch text color.
        textJitter: 1,              // Max pixel offset for fine-grained jitter.
        textJumpDistance: 10,       // Max distance text can jump from its spawn point.
        glitchChars: '█▓▒░',        // Characters to use for text corruption.
        codeStrings: [
            '0x7FF', 'ACCESS_DENIED', 'NULL_POINTER', '0xFA1', 'ERROR: 500', 
            '3F7B', 'z.com', 'REDACTED', 'CORRUPTED_DATA', '0010'
        ]
    }
};

// Expose CONFIG globally for runtime overrides
window.CONFIG = CONFIG;

// Backwards compatibility: accept older property names if present
if (CONFIG.particles && CONFIG.particles.strokeWidthRatio && !CONFIG.particles.strokeWidthDivider) {
    CONFIG.particles.strokeWidthDivider = CONFIG.particles.strokeWidthRatio;
}

const canvas = document.getElementById(CONFIG.canvasId);
if (!canvas) {
    console.error(`Canvas element with ID "${CONFIG.canvasId}" not found!`);
}
const ctx = canvas.getContext('2d');

function setCanvasSize() {
    canvas.width = CONFIG.size.width();
    canvas.height = CONFIG.size.height();
}

setCanvasSize();

let mouse = { x: null, y: null };

if (CONFIG.mouse.enabled) {
    window.addEventListener('mousemove', (event) => {
        const rect = canvas.getBoundingClientRect();
        mouse.x = event.clientX - rect.left;
        mouse.y = event.clientY - rect.top;
    });
}

window.addEventListener('resize', () => {
    setCanvasSize();
    init();
});

// Glitch effect system
let glitchArray = []; // active text artifacts
let glitchState = {   // for the impulsive screen-tearing effect
    isActive: false,
    endTime: 0,
    slices: []
};

// Ensure canvas element exists before adding event listeners
if (canvas && CONFIG.glitch.enabled) {
    // Set pointer cursor for feedback
    canvas.style.cursor = 'pointer';
    
    canvas.addEventListener('click', (event) => {
        const rect = canvas.getBoundingClientRect();
        const clickX = event.clientX - rect.left;
        const clickY = event.clientY - rect.top;
        console.log(`Glitch triggered at (${clickX}, ${clickY})`);
        // create a new glitch burst at click position
        createGlitchBurst(clickX, clickY);
    });
    
    console.log('Click listener attached to canvas');
}

// Helper: normalize a layer color definition to [r,g,b]
function _parseLayerColor(colorDef) {
    // Accept array [r,g,b]
    if (Array.isArray(colorDef) && colorDef.length === 3) return colorDef;
    // Accept hex strings like '#00ccff' or '00ccff'
    if (typeof colorDef === 'string') {
        const s = colorDef.replace('#', '');
        if (/^[0-9a-fA-F]{6}$/.test(s)) {
            return [parseInt(s.substring(0,2),16), parseInt(s.substring(2,4),16), parseInt(s.substring(4,6),16)];
        }
    }
    // Fallback to global base color
    return CONFIG.color.base;
}

// Helper: clamp a number between min and max
function clamp(v, a, b) {
    return Math.max(a, Math.min(b, v));
}

// --- CYBER GLITCH EFFECT SYSTEM ---

// Renders a piece of "hacker code" text that glitches, moves, and fades.
class GlitchText {
    constructor(x, y, text) {
        // Set an anchor point near the click, but with some initial variance.
        this.anchorX = x + (Math.random() - 0.5) * 50;
        this.anchorY = y + (Math.random() - 0.5) * 50;
        this.x = this.anchorX;
        this.y = this.anchorY;
        this.originalText = text;
        this.text = text;
        this.life = 1.0;
        this.decay = 0.01 + Math.random() * 0.02;
        this.color = CONFIG.glitch.color;
    }

    update() {
        // Instead of smooth velocity, jump to a new random position within a range of the anchor.
        const jump = CONFIG.glitch.textJumpDistance;
        const jitter = CONFIG.glitch.textJitter;
        
        // Main sporadic jump
        this.x = this.anchorX + (Math.random() - 0.5) * jump;
        this.y = this.anchorY + (Math.random() - 0.5) * jump;
        
        // Fine-grained jitter on top of the jump
        this.x += (Math.random() - 0.5) * jitter;
        this.y += (Math.random() - 0.5) * jitter;

        // Decay life
        this.life -= this.decay;

        // Randomly corrupt the text string
        if (Math.random() < this.life * 0.5) { // more corruption when new
            const chars = this.originalText.split('');
            const glitchChars = CONFIG.glitch.glitchChars;
            const i = Math.floor(Math.random() * chars.length);
            if (glitchChars.length > 0) {
                 chars[i] = glitchChars[Math.floor(Math.random() * glitchChars.length)];
            }
            this.text = chars.join('');
        } else {
            this.text = this.originalText;
        }

        return this.life > 0;
    }

    draw() {
        ctx.font = CONFIG.glitch.font;
        const alpha = Math.max(0, this.life);
        const shift = 3;

        // Draw RGB-shifted text for a chromatic aberration effect.
        ctx.fillStyle = `rgba(255, 0, 0, ${alpha * 0.7})`;
        ctx.fillText(this.text, this.x + shift, this.y);
        ctx.fillStyle = `rgba(0, 255, 255, ${alpha * 0.7})`;
        ctx.fillText(this.text, this.x - shift, this.y);
        
        // Draw the main text
        ctx.fillStyle = `rgba(${this.color[0]}, ${this.color[1]}, ${this.color[2]}, ${alpha})`;
        ctx.fillText(this.text, this.x, this.y);
    }
}

// Spawns a burst of glitch artifacts on click.
// This function now triggers the impulsive screen-slice effect and creates fading text particles.
function createGlitchBurst(x, y) {
    const cfg = CONFIG.glitch;

    // 1. Trigger the impulsive screen-slice effect
    glitchState.isActive = true;
    glitchState.endTime = performance.now() + cfg.impulseDuration;
    glitchState.slices = [];
    const sliceCount = Math.floor(Math.random() * (cfg.sliceCountRange[1] - cfg.sliceCountRange[0]) + cfg.sliceCountRange[0]);
    for (let i = 0; i < sliceCount; i++) {
        glitchState.slices.push({
            y: Math.random() * canvas.height,
            h: Math.random() * (cfg.sliceHeight[1] - cfg.sliceHeight[0]) + cfg.sliceHeight[0],
            offset: (Math.random() - 0.5) * 2 * (Math.random() * (cfg.displacement[1] - cfg.displacement[0]) + cfg.displacement[0])
        });
    }

    // 2. Create persistent text artifacts that fade out
    for (let i = 0; i < cfg.textCount; i++) {
        const text = cfg.codeStrings[Math.floor(Math.random() * cfg.codeStrings.length)];
        glitchArray.push(new GlitchText(x, y, text));
    }
}

class Particle {
    // x,y: position, size: visual size for this layer, layerIndex: 0-based index, layerCfg: the layer config object
    constructor(x, y, size, layerIndex, layerCfg) {
        this.baseX = x;
        this.baseY = y;
        this.x = x;
        this.y = y;
        this.size = size;
        this.baseSize = size;
        this.layerIndex = layerIndex; // 0-based layer index
        this.layerCfg = layerCfg || {};
        const [minD, maxD] = CONFIG.particles.densityRange;
        this.density = Math.random() * (maxD - minD) + minD;
        // determine rgb for this particle (per-layer override allowed)
        this.rgb = _parseLayerColor(this.layerCfg.color || CONFIG.color.base);
        // initial color with appropriate layer alpha
        const a = clamp(CONFIG.color.alphaBase + this.layerIndex * CONFIG.color.alphaLayerStep, 0, 1);
        this.color = this._rgba(a);
    }

    draw() {
        ctx.strokeStyle = this.color;
        ctx.lineWidth = Math.max(0.5, this.size / CONFIG.particles.strokeWidthDivider);
        ctx.beginPath();
        // Draw a + shape
        ctx.moveTo(this.x - this.size, this.y);
        ctx.lineTo(this.x + this.size, this.y);
        ctx.moveTo(this.x, this.y - this.size);
        ctx.lineTo(this.x, this.y + this.size);
        ctx.stroke();
    }

    update() {
        // Interaction / hover calculations
        const dx = (mouse.x || 0) - this.x;
        const dy = (mouse.y || 0) - this.y;
        const distance = Math.sqrt(dx * dx + dy * dy) || 0.0001;
        const forceDirectionX = dx / distance;
        const forceDirectionY = dy / distance;
        const maxDistance = CONFIG.particles.maxDistance;
        const force = (maxDistance - distance) / maxDistance;

        if (distance < maxDistance) {
            this.size = this.baseSize + (maxDistance - distance) / CONFIG.particles.sizeHoverFactor;
            // hover alpha uses configured hover base + a small per-layer increment + proximity scaling
            const hoverAlpha = clamp(CONFIG.color.alphaHoverBase + this.layerIndex * CONFIG.color.alphaLayerStep + (maxDistance - distance) / maxDistance * CONFIG.color.alphaHoverExtra, 0, 1);
            this.color = this._rgba(hoverAlpha);
        } else {
            this.size = this.baseSize;
            this.color = this._rgba(clamp(CONFIG.color.alphaBase + this.layerIndex * CONFIG.color.alphaLayerStep, 0, 1));
        }

        // Add a gentle wave motion
        const depth = this.layerIndex + 1; // avoid zero
        const amp = depth * CONFIG.wave.amplitudeFactor;
        const spatialDiv = CONFIG.wave.spatialDivisor || CONFIG.wave.divisorBase || 40;
        const timeDiv = CONFIG.wave.timeDivisor || CONFIG.wave.timeDivisorBase || 2000;
        this.x = this.baseX + Math.sin(this.baseY / (spatialDiv * depth) + performance.now() / (timeDiv * depth)) * amp;
        this.y = this.baseY + Math.cos(this.baseX / (spatialDiv * depth) + performance.now() / (timeDiv * depth)) * amp;
    }

    _rgba(alpha) {
        const [r, g, b] = this.rgb || CONFIG.color.base;
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }
}

let particlesArray;

function init() {
    particlesArray = [];
    const layers = CONFIG.particles.layers;

    layers.forEach((layer, layerIndex) => {
        for (let y = 0; y < canvas.height; y += layer.spacing) {
            for (let x = 0; x < canvas.width; x += layer.spacing) {
                // pass 0-based layerIndex and the layer config so particles can pick per-layer colors
                particlesArray.push(new Particle(x, y, layer.size, layerIndex, layer));
            }
        }
    });
}

function animate() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    for (let i = 0; i < particlesArray.length; i++) {
        particlesArray[i].update();
        particlesArray[i].draw();
    }

    // --- Apply impulsive screen-slice glitch effect ---
    if (glitchState.isActive && performance.now() < glitchState.endTime) {
        for (const slice of glitchState.slices) {
            try {
                if (slice.y + slice.h > canvas.height || slice.y < 0) continue;
                ctx.drawImage(canvas, 0, slice.y, canvas.width, slice.h, slice.offset, slice.y, canvas.width, slice.h);
            } catch (e) {}
        }
    } else if (glitchState.isActive) {
        glitchState.isActive = false; // Glitch duration ended
    }

    // Update and draw persistent glitch text artifacts
    for (let i = glitchArray.length - 1; i >= 0; i--) {
        if (glitchArray[i].update()) {
            glitchArray[i].draw();
        } else {
            glitchArray.splice(i, 1);
        }
    }
    requestAnimationFrame(animate);
}

init();
animate();