// =============================================================================
// ANCHOR:GENETIC_ALGORITHM
// Description: Evolves each colony's population independently at the end of
//              each generation. Writes elite genomes to Soul after evolving.
//
// Algorithm per colony:
//   1. Sort ants by fitnessScore descending
//   2. Carry top ELITE_FRACTION over unchanged (elitism)
//   3. Roulette-wheel selection to build mating pool
//   4. Crossover + mutate to fill remainder
//   5. Reset all ant positions to hill spawn
//   6. Async Soul.writeGeneration() (non-blocking)
// =============================================================================

import { SimConfig } from '../config/SimConfig.js';
import { Genome }    from '../core/Genome.js';
import { Ant }       from './Ant.js';

export class GeneticAlgorithm {
    constructor(soul) {
        this.soul       = soul;  // Soul instance for persistence
        this.generation = 0;

        // Running stats for the current generation (reset each gen)
        this._totalFoodFinds = 0;
        this._totalKills     = 0;
        this._totalDmg       = 0;
        this._ticksThisGen   = 0;

        // Steady-state replacement counter — increments generation every POP_PER_COLONY deaths
        this._replacements   = 0;
    }

    // ANCHOR:FUNCTION_RECORD_TICK_STATS
    // Called by World each tick to accumulate generation-level stats.
    recordTick(foodFinds, kills, dmg) {
        this._totalFoodFinds += foodFinds;
        this._totalKills     += kills;
        this._totalDmg       += dmg;
        this._ticksThisGen++;
    }

    // ANCHOR:FUNCTION_RUN_GENERATION
    // Parameters:
    //   colony0 {Ant[]} - all ants from colony 0
    //   colony1 {Ant[]} - all ants from colony 1
    // Returns: Promise<void>
    async runGeneration(colony0, colony1) {
        this.generation++;

        const ticks = this._ticksThisGen || 1;
        const stats = {
            colony0:      [...colony0].sort((a, b) => b.genome.fitnessScore - a.genome.fitnessScore),
            colony1:      [...colony1].sort((a, b) => b.genome.fitnessScore - a.genome.fitnessScore),
            find_rate:    this._totalFoodFinds / ticks,
            kill_rate:    this._totalKills     / ticks,
            dmg_recv_rate:this._totalDmg       / ticks,
        };

        // Evolve each colony
        await this._evolveColony(colony0, stats.colony0, 0);
        await this._evolveColony(colony1, stats.colony1, 1);

        // Persist to Soul (async, non-blocking)
        if (SimConfig.SOUL_WRITE_ON_GENERATION && this.soul) {
            this.soul.writeGeneration(this.generation, stats).catch(err => {
                console.warn('[GA] Soul write error:', err);
            });
        }

        // Reset generation stats
        this._totalFoodFinds = 0;
        this._totalKills     = 0;
        this._totalDmg       = 0;
        this._ticksThisGen   = 0;

        console.info(`[GA] Generation ${this.generation} complete`);
    }

    // ANCHOR:FUNCTION_EVOLVE_COLONY
    // In-place evolution: replaces genome references on existing Ant instances.
    // Parameters:
    //   colony     {Ant[]}  - original colony array (mutated in place)
    //   sorted     {Ant[]}  - colony sorted by fitness descending
    //   colonyId   {number} - 0 or 1
    async _evolveColony(colony, sorted, colonyId) {
        const pop       = colony.length;
        const eliteN    = Math.max(1, Math.floor(pop * SimConfig.GA_ELITE_FRACTION));
        const elites    = sorted.slice(0, eliteN);

        // Build new genome pool
        const newGenomes = [];

        // Keep elite genomes unchanged
        for (const e of elites) {
            newGenomes.push(e.genome);
        }

        // Fill remainder with crossover + mutation
        while (newGenomes.length < pop) {
            const parentA = this._rouletteSelect(sorted);
            const parentB = this._rouletteSelect(sorted);
            const child   = await Genome.crossover(parentA.genome, parentB.genome, this.generation);
            child.mutate();
            newGenomes.push(child);
        }

        // Assign new genomes and reset positions
        for (let i = 0; i < colony.length; i++) {
            colony[i].genome = newGenomes[i];
            colony[i].resetToHill();
        }
    }

    // ANCHOR:FUNCTION_REPLACE_ONE
    // Steady-state GA: replaces a single dead ant with a GA-bred offspring
    // selected from living colony members via tournament selection + crossover.
    // Parameters:
    //   colony   {Ant[]}  - full colony array
    //   index    {number} - index of the dead ant to replace
    //   colonyId {number} - 0 or 1
    //   hill     {AntHill}
    // Returns: Promise<Ant>
    async replaceOne(colony, index, colonyId, hill) {
        // Collect living ants for selection pool
        const living = colony.filter((a, i) => i !== index && a.health > 0);

        let newAnt;
        if (living.length >= 2) {
            // Tournament-select two parents and crossover
            const parentA = this._tournamentSelect(living);
            const parentB = this._tournamentSelect(living);
            const child   = await Genome.crossover(parentA.genome, parentB.genome, this.generation);
            child.mutate();
            newAnt = new Ant(child, hill, hill.x, hill.y);
        } else {
            // Not enough living ants — spawn random
            const g = await Genome.createRandom(colonyId, this.generation);
            newAnt = new Ant(g, hill, hill.x, hill.y);
        }
        newAnt.resetToHill();

        // Track replacements — increment generation every POP_PER_COLONY deaths
        this._replacements++;
        if (this._replacements >= SimConfig.POP_PER_COLONY) {
            this._replacements = 0;
            this.generation++;
            console.info(`[GA] Steady-state generation ${this.generation} (via ${SimConfig.POP_PER_COLONY} replacements)`);
        }

        return newAnt;
    }

    // ANCHOR:FUNCTION_TOURNAMENT_SELECT
    // Tournament selection: picks GA_TOURNAMENT_SIZE candidates and returns the fittest.
    _tournamentSelect(pool) {
        const k = Math.min(SimConfig.GA_TOURNAMENT_SIZE, pool.length);
        let best = null;
        for (let i = 0; i < k; i++) {
            const candidate = pool[Math.floor(Math.random() * pool.length)];
            if (!best || candidate.genome.fitnessScore > best.genome.fitnessScore) {
                best = candidate;
            }
        }
        return best;
    }

    // ANCHOR:FUNCTION_ROULETTE_SELECT
    // Fitness-proportionate (roulette wheel) selection.
    // Offsets all fitness values by |min| + 1 so all weights are positive.
    _rouletteSelect(sorted) {
        const scores  = sorted.map(a => a.genome.fitnessScore);
        const minScore = Math.min(...scores);
        const offset   = minScore < 0 ? -minScore + 1 : 1;
        const weights  = scores.map(s => s + offset);
        const total    = weights.reduce((a, b) => a + b, 0);

        let r = Math.random() * total;
        for (let i = 0; i < sorted.length; i++) {
            r -= weights[i];
            if (r <= 0) return sorted[i];
        }
        return sorted[sorted.length - 1]; // fallback
    }

    get currentGeneration() { return this.generation; }
}
