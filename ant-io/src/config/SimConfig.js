// =============================================================================
// ANCHOR:SIMCONFIG
// Description: Single source of truth for all simulation parameters.
// =============================================================================

const _config = {
    // -------------------------------------------------------------------------
    // WORLD
    // -------------------------------------------------------------------------
    WORLD_W: 100,
    WORLD_H: 75,
    METERS_PER_UNIT: 1.0,

    // -------------------------------------------------------------------------
    // COLONIES
    // -------------------------------------------------------------------------
    COLONY: [
        { id: 0, x: 15,  y: 37.5, color: [0.92, 0.38, 0.22] },  // Colony A — warm coral
        { id: 1, x: 85,  y: 37.5, color: [0.25, 0.55, 0.85] },  // Colony B — soft blue
    ],
    HILL_RADIUS: 3.0,

    // -------------------------------------------------------------------------
    // ANTS — energy-based lifecycle
    // -------------------------------------------------------------------------
    POP_PER_COLONY: 50,         // Initial population per colony
    POP_MAX_COLONY: 120,        // Hard cap on colony size
    ANT_SPEED_BASE: 4.0,
    ANT_SIZE: 0.35,
    ANT_VISION_BASE: 5.0,

    // Energy system (replaces health/age)
    ANT_ENERGY_MAX: 150.0,      // Starting energy reserve
    ANT_ENERGY_DRAIN: 0.05,     // Energy lost per tick (~3000 ticks = 50s lifespan)
    ANT_ENERGY_CARRY_MULT: 1.3, // Drain multiplier when carrying food
    ANT_ENERGY_EXHAUSTED: 0.15, // Fraction of max energy → exhausted (head home)

    // -------------------------------------------------------------------------
    // SPAWNING — food deposited → resource pool → new ants
    // -------------------------------------------------------------------------
    ANT_SPAWN_COST: 12,         // Resource pool cost to spawn one ant
    ANT_ENERGY_FROM_HOME: 60,   // Energy restored when reaching home

    // -------------------------------------------------------------------------
    // FOOD
    // -------------------------------------------------------------------------
    FOOD_COUNT_MIN: 20,
    FOOD_COUNT_MAX: 40,
    FOOD_SIZE: 0.8,
    FOOD_VALUE: 10,
    FOOD_MARGIN: 10,
    FOOD_RESPAWN_TICKS: 120,

    // -------------------------------------------------------------------------
    // PHEROMONE GRID
    // -------------------------------------------------------------------------
    PHEROMONE_GRID_W: 100,
    PHEROMONE_GRID_H: 75,
    PHEROMONE_FADE_RATE: 0.998,
    PHEROMONE_EMIT_STRENGTH: 0.3,
    PHEROMONE_ALPHA_SCALE: 1.5,

    SIGNAL_TYPES: Object.freeze({ HOME_PATH: 0, FOOD_FOUND: 1, DANGER: 2 }),
    SIGNAL_COLORS: [
        [0.2,  0.85, 0.45],  // HOME_PATH  → soft green
        [0.95, 0.82, 0.15],  // FOOD_FOUND → warm yellow
        [0.9,  0.25, 0.25],  // DANGER     → muted red
    ],

    // -------------------------------------------------------------------------
    // PHEROMONE COMPETITION
    // -------------------------------------------------------------------------
    MARKER_CONTEST_RATE: 0.03,
    EXPLORER_FRACTION: 0.08,
    EXPLORER_JITTER: 0.4,
    CONFUSED_JITTER: 1.2,       // High jitter when trail is severed (circles)
    CONFUSED_TICKS: 60,         // How long confusion lasts after trail loss

    // -------------------------------------------------------------------------
    // NEURAL NETWORK
    // -------------------------------------------------------------------------
    NN_ARCH: [12, 16, 6],

    // -------------------------------------------------------------------------
    // GENETIC ALGORITHM
    // -------------------------------------------------------------------------
    GA_TICKS_PER_GENERATION: 1200,
    GA_ELITE_FRACTION:  0.2,
    GA_MUTATION_RATE:   0.03,
    GA_MUTATION_SCALE:  0.1,
    GA_CROSSOVER_MODE: 'uniform',
    GA_TOURNAMENT_SIZE: 4,

    // -------------------------------------------------------------------------
    // SOUL / API
    // -------------------------------------------------------------------------
    API_BASE: '/ant-io/api',
    SOUL_WRITE_ON_GENERATION: true,
    SOUL_ELITE_COUNT: 10,
    SOUL_SAVE_INTERVAL: 600,

    // -------------------------------------------------------------------------
    // SIMULATION
    // -------------------------------------------------------------------------
    TARGET_SIM_TPS: 60,
    MAX_STEPS_PER_FRAME: 5,
    DETERMINISTIC: false,       // When true, uses seeded PRNG for reproducibility
    RNG_SEED: 42,               // Seed for deterministic mode

    // -------------------------------------------------------------------------
    // RENDERING
    // -------------------------------------------------------------------------
    RENDER_LAYERS: {
        GROUND:      true,
        ANTHILLS:    true,
        PHEROMONES:  true,
        TERRITORY:   false,
        FOOD:        true,
        ANTS:        true,
        ENERGY_MAP:  false,
        STATE_VIEW:  false,
        NN_ACTIVITY: false,
        OVERLAY:     false,
    },
};

_config.override = function(patches) { Object.assign(_config, patches); };
export const SimConfig = _config;

// =============================================================================
// ANCHOR:SEEDED_RNG — Deterministic PRNG (mulberry32) for reproducible sims
// =============================================================================

let _rngState = _config.RNG_SEED;

export function simRandom() {
    if (!_config.DETERMINISTIC) return Math.random();
    _rngState |= 0;
    _rngState = _rngState + 0x6D2B79F5 | 0;
    let t = Math.imul(_rngState ^ _rngState >>> 15, 1 | _rngState);
    t = t + Math.imul(t ^ t >>> 7, 61 | t) ^ t;
    return ((t ^ t >>> 14) >>> 0) / 4294967296;
}

export function resetRng(seed) { _rngState = seed ?? _config.RNG_SEED; }
