// =============================================================================
// ANCHOR:GENOME
// Description: Genome creation, serialization, crossover, and mutation.
//              A genome encodes both neural network weights and scalar traits.
//              NN weights are stored as a Float32Array in row-major order:
//              [W1, b1, W2, b2] where the architecture is defined by SimConfig.NN_ARCH.
// =============================================================================

import { SimConfig } from '../config/SimConfig.js';
import { deriveGenomeId, randomSeedHex } from './Hash.js';

// ANCHOR:FUNCTION_COMPUTE_NN_SIZE
// Returns the total number of floats needed for the NN given an architecture array.
// Architecture: [INPUT_N, HIDDEN_N, OUTPUT_N]
// Layout: W1[HIDDEN×INPUT] + b1[HIDDEN] + W2[OUTPUT×HIDDEN] + b2[OUTPUT]
export function computeNNSize(arch) {
    const [inp, hid, out] = arch;
    return (hid * inp) + hid + (out * hid) + out;
}

// ANCHOR:FUNCTION_RANDOM_WEIGHTS
// Initializes NN weights from N(0, stddev).
// Parameters: arch {number[]} - [input, hidden, output]
// Returns: Float32Array
function randomWeights(arch) {
    const size    = computeNNSize(arch);
    const weights = new Float32Array(size);
    for (let i = 0; i < size; i++) {
        // Box-Muller transform for Gaussian sample
        const u1 = Math.random() || 1e-10;
        const u2 = Math.random();
        weights[i] = Math.sqrt(-2 * Math.log(u1)) * Math.cos(2 * Math.PI * u2) * 0.1;
    }
    return weights;
}

// ANCHOR:FUNCTION_DEFAULT_TRAITS
// Returns a fresh traits object with all values at 1.0 (neutral).
// Traits are multiplicative modifiers applied to SimConfig base values.
function defaultTraits() {
    return {
        speed:                1.0,
        vision_radius:        1.0,
        pheromone_sensitivity:1.0,
        attack_power:         1.0,
        carry_capacity:       1.0,
        turn_rate:            1.0,
        stamina:              1.0,
        pheromone_emit_rate:  1.0,
        mutation_rate:        SimConfig.GA_MUTATION_RATE,
    };
}

// ANCHOR:FUNCTION_CLAMP_TRAITS
// Clamps all trait values to a sane range to prevent runaway evolution.
function clampTraits(traits) {
    const MIN = 0.1, MAX = 3.0;
    for (const key in traits) {
        if (key !== 'mutation_rate') {
            traits[key] = Math.max(MIN, Math.min(MAX, traits[key]));
        }
    }
    traits.mutation_rate = Math.max(0.001, Math.min(0.5, traits.mutation_rate));
    return traits;
}

// =============================================================================
// ANCHOR:CLASS_GENOME
// =============================================================================
export class Genome {
    constructor({ genomeId, colonyId, generation, parentAId, parentBId, weights, traits }) {
        this.genomeId   = genomeId;    // string SHA-256 hex (64 chars)
        this.colonyId   = colonyId;    // 0 or 1
        this.generation = generation;  // integer
        this.parentAId  = parentAId;   // string | "NULL"
        this.parentBId  = parentBId;   // string | "NULL"
        this.weights    = weights;     // Float32Array
        this.traits     = traits;      // plain object
        this.fitnessScore = 0;
    }

    // ANCHOR:FUNCTION_CREATE_RANDOM
    // Parameters: colonyId {number}, generation {number}
    // Returns: Promise<Genome>
    static async createRandom(colonyId, generation) {
        const seed    = randomSeedHex();
        const id      = await deriveGenomeId('NULL', 'NULL', generation, seed);
        const weights = randomWeights(SimConfig.NN_ARCH);
        const traits  = defaultTraits();
        return new Genome({
            genomeId:   id,
            colonyId:   colonyId,
            generation: generation,
            parentAId:  'NULL',
            parentBId:  'NULL',
            weights:    weights,
            traits:     { ...traits },
        });
    }

    // ANCHOR:FUNCTION_CROSSOVER
    // Uniform crossover on both weight array and traits object.
    // Parameters: parentA {Genome}, parentB {Genome}, generation {number}
    // Returns: Promise<Genome>
    static async crossover(parentA, parentB, generation) {
        const seed      = randomSeedHex();
        const childId   = await deriveGenomeId(parentA.genomeId, parentB.genomeId, generation, seed);
        const size      = parentA.weights.length;
        const childW    = new Float32Array(size);

        if (SimConfig.GA_CROSSOVER_MODE === 'uniform') {
            // Uniform crossover: each gene independently from either parent
            for (let i = 0; i < size; i++) {
                childW[i] = Math.random() < 0.5 ? parentA.weights[i] : parentB.weights[i];
            }
        } else {
            // Single-point crossover
            const point = Math.floor(Math.random() * size);
            for (let i = 0; i < size; i++) {
                childW[i] = i < point ? parentA.weights[i] : parentB.weights[i];
            }
        }

        // Traits: uniform crossover on each key
        const childTraits = {};
        for (const key in parentA.traits) {
            childTraits[key] = Math.random() < 0.5 ? parentA.traits[key] : parentB.traits[key];
        }

        return new Genome({
            genomeId:   childId,
            colonyId:   parentA.colonyId,
            generation: generation,
            parentAId:  parentA.genomeId,
            parentBId:  parentB.genomeId,
            weights:    childW,
            traits:     clampTraits(childTraits),
        });
    }

    // ANCHOR:FUNCTION_MUTATE
    // Applies in-place Gaussian mutation to weights and perturbs traits.
    // Uses the genome's own mutation_rate trait if present.
    // Returns: this (mutated in place, for chaining)
    mutate() {
        const rate  = this.traits.mutation_rate ?? SimConfig.GA_MUTATION_RATE;
        const scale = SimConfig.GA_MUTATION_SCALE;

        for (let i = 0; i < this.weights.length; i++) {
            if (Math.random() < rate) {
                const u1 = Math.random() || 1e-10;
                const u2 = Math.random();
                const gaussian = Math.sqrt(-2 * Math.log(u1)) * Math.cos(2 * Math.PI * u2);
                this.weights[i] += gaussian * scale;
            }
        }

        // Perturb traits with small additive noise
        const traitNoiseScale = scale * 0.5;
        for (const key in this.traits) {
            if (Math.random() < rate) {
                const u1 = Math.random() || 1e-10;
                const u2 = Math.random();
                const gaussian = Math.sqrt(-2 * Math.log(u1)) * Math.cos(2 * Math.PI * u2);
                this.traits[key] += gaussian * traitNoiseScale;
            }
        }
        clampTraits(this.traits);
        return this;
    }

    // ANCHOR:FUNCTION_SERIALIZE
    // Produces a plain object suitable for JSON POST to soul_write.php.
    // nn_weights_b64: Base64-encoded raw bytes of the Float32Array.
    serialize() {
        const bytes  = new Uint8Array(this.weights.buffer);
        const chars  = Array.from(bytes, b => String.fromCharCode(b)).join('');
        const b64    = btoa(chars);

        return {
            genome_id:      this.genomeId,
            colony_id:      this.colonyId,
            generation:     this.generation,
            parent_a_id:    this.parentAId === 'NULL' ? null : this.parentAId,
            parent_b_id:    this.parentBId === 'NULL' ? null : this.parentBId,
            nn_weights_b64: b64,
            traits_json:    this.traits,
            fitness_score:  this.fitnessScore,
        };
    }

    // ANCHOR:FUNCTION_DESERIALIZE
    // Reconstructs a Genome from a soul_read.php genome row.
    // Parameters: row {object} - API genome object
    // Returns: Genome
    static deserialize(row) {
        const binary  = atob(row.nn_weights_b64);
        const bytes   = new Uint8Array(binary.length);
        for (let i = 0; i < binary.length; i++) bytes[i] = binary.charCodeAt(i);
        const weights = new Float32Array(bytes.buffer);

        return new Genome({
            genomeId:   row.genome_id,
            colonyId:   row.colony_id,
            generation: row.generation,
            parentAId:  row.parent_a_id ?? 'NULL',
            parentBId:  row.parent_b_id ?? 'NULL',
            weights:    weights,
            traits:     typeof row.traits_json === 'string'
                            ? JSON.parse(row.traits_json)
                            : row.traits_json,
        });
    }
}
