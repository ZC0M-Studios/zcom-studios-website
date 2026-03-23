// =============================================================================
// ANCHOR:WORLD
// Description: Central simulation container with energy-based ant lifecycle,
//              resource-pool spawning, and proper dead ant cleanup.
// =============================================================================

import { SimConfig, simRandom }  from '../config/SimConfig.js';
import { AntHill }          from './AntHill.js';
import { Food, spawnFood }  from './Food.js';
import { PheromoneGrid }    from './PheromoneGrid.js';
import { Ant }              from './Ant.js';
import { Genome }           from '../core/Genome.js';
import { GeneticAlgorithm } from './GeneticAlgorithm.js';

const ANT_STRIDE = 8; // [x, y, angle, colony, state, carrying, energy_norm, nn_turn]

export class World {
    constructor(soul) {
        this.worldW = SimConfig.WORLD_W;
        this.worldH = SimConfig.WORLD_H;

        this.hills   = SimConfig.COLONY.map(cfg => new AntHill(cfg, SimConfig.HILL_RADIUS));
        this.colony0 = [];
        this.colony1 = [];
        this.foodItems     = [];
        this.pheromoneGrid = new PheromoneGrid();
        this.ga            = new GeneticAlgorithm(soul);

        this.tickCount  = 0;
        this._running   = false;
        this._soulSaveCounter  = 0;
        this._foodRespawnTimer = 0;

        // Pre-allocated typed arrays (sized to max capacity)
        const maxAnts = SimConfig.POP_MAX_COLONY * 2;
        this.antInstanceData  = new Float32Array(maxAnts * ANT_STRIDE);
        this.antCount         = 0;
        const maxFood = SimConfig.FOOD_COUNT_MAX * 3; // extra room for gradual respawn
        this.foodInstanceData = new Float32Array(maxFood * 3);
        this.foodCount        = 0;
    }

    async init() {
        const gen = 0;
        for (let i = 0; i < SimConfig.POP_PER_COLONY; i++) {
            const g0 = await Genome.createRandom(0, gen);
            const g1 = await Genome.createRandom(1, gen);
            this.colony0.push(new Ant(g0, this.hills[0], this.hills[0].x, this.hills[0].y));
            this.colony1.push(new Ant(g1, this.hills[1], this.hills[1].x, this.hills[1].y));
        }

        // Stagger initial energy so ants don't all die simultaneously
        for (const a of [...this.colony0, ...this.colony1]) {
            a.resetToHill();
            a.energy = SimConfig.ANT_ENERGY_MAX * (0.5 + simRandom() * 0.5);
        }

        this.foodItems = spawnFood(this.hills, this.worldW, this.worldH);
        this._updateInstanceBuffers();
        this._running = true;
        console.info('[World] Initialized — pop:', (this.colony0.length + this.colony1.length));
    }

    // ANCHOR:FUNCTION_TICK
    async tick(dt) {
        if (!this._running) return;

        // 1. Fade pheromones
        this.pheromoneGrid.fade();

        // 2. Step all living ants
        let tickFoodFinds = 0, tickKills = 0, tickDmg = 0;
        for (const colony of [this.colony0, this.colony1]) {
            for (const ant of colony) {
                if (!ant.alive) continue;
                const prevFood  = ant._foodFinds;
                const prevKills = ant._kills;
                const prevDmg   = ant._dmgReceived;
                ant.step(this, dt);
                tickFoodFinds += ant._foodFinds  - prevFood;
                tickKills     += ant._kills      - prevKills;
                tickDmg       += ant._dmgReceived - prevDmg;
            }
        }

        // 3. Remove dead ants — splice them out completely (no ghost objects)
        await this._cleanupDead();

        // 4. Spawn new ants from resource pool
        await this._spawnFromResources();

        // 5. Record stats
        this.ga.recordTick(tickFoodFinds, tickKills, tickDmg);
        this.tickCount++;

        // 6. Gradual food respawn
        this._foodRespawnTimer++;
        const activeFood = this.foodItems.filter(f => !f.isDepleted).length;
        if (this._foodRespawnTimer >= SimConfig.FOOD_RESPAWN_TICKS &&
            activeFood < SimConfig.FOOD_COUNT_MIN) {
            this._foodRespawnTimer = 0;
            const newChunks = 1 + Math.floor(simRandom() * 3);
            const margin = SimConfig.FOOD_MARGIN;
            for (let n = 0; n < newChunks; n++) {
                for (let a = 0; a < 20; a++) {
                    const x = margin + simRandom() * (this.worldW - 2 * margin);
                    const y = margin + simRandom() * (this.worldH - 2 * margin);
                    const tooClose = this.hills.some(h => {
                        const dx = x - h.x, dy = y - h.y;
                        return dx * dx + dy * dy < margin * margin;
                    });
                    if (!tooClose) { this.foodItems.push(new Food(x, y)); break; }
                }
            }
        }

        // 7. Periodic soul save
        if (SimConfig.SOUL_SAVE_INTERVAL > 0) {
            this._soulSaveCounter++;
            if (this._soulSaveCounter >= SimConfig.SOUL_SAVE_INTERVAL) {
                this._soulSaveCounter = 0;
                if (this.ga.soul) {
                    const stats = {
                        colony0: [...this.colony0].sort((a, b) => b.genome.fitnessScore - a.genome.fitnessScore),
                        colony1: [...this.colony1].sort((a, b) => b.genome.fitnessScore - a.genome.fitnessScore),
                        find_rate:     this.ga._totalFoodFinds / (this.ga._ticksThisGen || 1),
                        kill_rate:     this.ga._totalKills     / (this.ga._ticksThisGen || 1),
                        dmg_recv_rate: this.ga._totalDmg       / (this.ga._ticksThisGen || 1),
                    };
                    this.ga.soul.writeGeneration(this.ga.currentGeneration, stats).catch(() => {});
                }
            }
        }

        // 8. Update renderer buffers
        this._updateInstanceBuffers();
    }

    // ANCHOR:FUNCTION_CLEANUP_DEAD
    // Explicitly destroy dead ants by splicing them from the array.
    // This prevents invisible collision buildup from dead objects.
    async _cleanupDead() {
        for (const colony of [this.colony0, this.colony1]) {
            for (let i = colony.length - 1; i >= 0; i--) {
                if (!colony[i].alive) {
                    const dead = colony[i];
                    const colonyId = dead.hill.id;

                    // GA replacement: breed offspring from living ants
                    // (but don't add it here — spawning is resource-based now)
                    this.ga._replacements++;
                    if (this.ga._replacements >= SimConfig.POP_PER_COLONY) {
                        this.ga._replacements = 0;
                        this.ga.generation++;
                    }

                    // Splice out the dead ant — no ghost reference remains
                    colony.splice(i, 1);
                }
            }
        }
    }

    // ANCHOR:FUNCTION_SPAWN_FROM_RESOURCES
    // When a hill's resource pool has enough food, spawn a new ant.
    async _spawnFromResources() {
        for (let c = 0; c < 2; c++) {
            const colony = c === 0 ? this.colony0 : this.colony1;
            const hill   = this.hills[c];

            // Spawn while resources available and under population cap
            while (hill.canSpawn() && colony.length < SimConfig.POP_MAX_COLONY) {
                // Breed from living population if possible
                const living = colony.filter(a => a.alive);
                let newAnt;
                if (living.length >= 2) {
                    const pA = this.ga._tournamentSelect(living);
                    const pB = this.ga._tournamentSelect(living);
                    const child = await Genome.crossover(pA.genome, pB.genome, this.ga.generation);
                    child.mutate();
                    newAnt = new Ant(child, hill, hill.x, hill.y);
                } else {
                    const g = await Genome.createRandom(c, this.ga.generation);
                    newAnt = new Ant(g, hill, hill.x, hill.y);
                }
                newAnt.resetToHill();
                colony.push(newAnt);
            }
        }
    }

    _updateInstanceBuffers() {
        const allAnts = [...this.colony0, ...this.colony1];
        this.antCount = allAnts.length;

        // Resize buffer if needed
        if (this.antCount * ANT_STRIDE > this.antInstanceData.length) {
            this.antInstanceData = new Float32Array(this.antCount * ANT_STRIDE * 2);
        }

        for (let i = 0; i < allAnts.length; i++) {
            allAnts[i].writeToBuffer(this.antInstanceData, i * ANT_STRIDE);
        }

        const active = this.foodItems.filter(f => !f.isDepleted);
        this.foodCount = active.length;
        if (this.foodCount * 3 > this.foodInstanceData.length) {
            this.foodInstanceData = new Float32Array(this.foodCount * 3 * 2);
        }
        for (let i = 0; i < active.length; i++) {
            this.foodInstanceData[i * 3 + 0] = active[i].x;
            this.foodInstanceData[i * 3 + 1] = active[i].y;
            this.foodInstanceData[i * 3 + 2] = active[i].currentSize;
        }
    }

    getState() {
        return {
            antInstanceData:  this.antInstanceData,
            antCount:         this.antCount,
            foodInstanceData: this.foodInstanceData,
            foodCount:        this.foodCount,
            pheromoneGrid:    this.pheromoneGrid,
            hills:            this.hills,
            generation:       this.ga.currentGeneration,
            tick:             this.tickCount,
        };
    }

    getStats() {
        const sorted0 = [...this.colony0].sort((a, b) => b.genome.fitnessScore - a.genome.fitnessScore);
        const sorted1 = [...this.colony1].sort((a, b) => b.genome.fitnessScore - a.genome.fitnessScore);
        const activeFood = this.foodItems.filter(f => !f.isDepleted).length;
        return {
            generation:     this.ga.currentGeneration,
            tick:           this.tickCount,
            activeFood:     activeFood,
            pop0:           this.colony0.length,
            pop1:           this.colony1.length,
            bestFitness0:   sorted0[0]?.genome.fitnessScore.toFixed(1) ?? '—',
            bestFitness1:   sorted1[0]?.genome.fitnessScore.toFixed(1) ?? '—',
            foodStored0:    this.hills[0].foodStored,
            foodStored1:    this.hills[1].foodStored,
            resourcePool0:  this.hills[0].resourcePool,
            resourcePool1:  this.hills[1].resourcePool,
            soulBlock:      this.ga.soul?._lastBlockIndex ?? -1,
        };
    }
}
