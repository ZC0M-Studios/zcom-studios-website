// =============================================================================
// ANCHOR:ANT
// Description: Individual ant agent with energy-based lifecycle, behavioral
//              steering biases, and NN modulation. Dies when energy reaches 0.
//
// States: FORAGING=1, RETURNING=2, ATTACKING=3, FOLLOWING_PHEROMONE=4,
//         EXHAUSTED=5, CONFUSED=6
// =============================================================================

import { SimConfig, simRandom } from '../config/SimConfig.js';

export const AntState = Object.freeze({
    IDLE:               0,
    FORAGING:           1,
    RETURNING:          2,
    ATTACKING:          3,
    FOLLOWING_PHEROMONE:4,
    EXHAUSTED:          5,
    CONFUSED:           6,
});

const TWO_PI = Math.PI * 2;

export class Ant {
    constructor(genome, hill, x, y) {
        this.genome    = genome;
        this.hill      = hill;
        this.x         = x;
        this.y         = y;
        this.angle     = simRandom() * TWO_PI;
        this.speed     = SimConfig.ANT_SPEED_BASE * genome.traits.speed;
        this.carrying  = false;
        this.carriedValue = 0;
        this.state     = AntState.FORAGING;

        // Energy system — depletes each tick, ant dies at 0
        this.energy    = SimConfig.ANT_ENERGY_MAX;
        this.alive     = true;

        // Role: ~8% explorers, rest are trail followers
        this.isExplorer = simRandom() < SimConfig.EXPLORER_FRACTION;

        // Confusion timer — set when trail is severed under this ant
        this._confusedTimer = 0;
        // Track whether we were on a trail last tick (for severing detection)
        this._wasOnTrail = false;

        // Fitness accumulators
        this.genome.fitnessScore = 0;
        this._foodFinds    = 0;
        this._kills        = 0;
        this._dmgReceived  = 0;

        // Pre-allocated NN buffers
        const [inp, hid, out] = SimConfig.NN_ARCH;
        this._input  = new Float32Array(inp);
        this._hidden = new Float32Array(hid);
        this._output = new Float32Array(out);
    }

    // ANCHOR:FUNCTION_STEP
    step(world, dt) {
        if (!this.alive) return;

        this._buildInputs(world);
        this._forwardPass();
        this._applyOutputs(world, dt);
        this._updateState(world);
        this._clampToBounds(world);

        // Drain energy
        const drainMult = this.carrying ? SimConfig.ANT_ENERGY_CARRY_MULT : 1.0;
        this.energy -= SimConfig.ANT_ENERGY_DRAIN * drainMult;

        // Check exhaustion threshold
        if (this.energy <= SimConfig.ANT_ENERGY_MAX * SimConfig.ANT_ENERGY_EXHAUSTED &&
            this.state !== AntState.EXHAUSTED && this.state !== AntState.RETURNING) {
            // Only enter exhausted state if NOT currently on a food trail
            // (prevents turning around right next to food)
            const ph  = world.pheromoneGrid;
            const cid = this.hill.id;
            const onFoodTrail = ph.sampleOwn(this.x, this.y, 1, cid) > 0.05;
            if (!onFoodTrail) {
                this.state = AntState.EXHAUSTED;
            }
        }

        // Death at zero energy
        if (this.energy <= 0) {
            this.energy = 0;
            this.alive  = false;
        }
    }

    // ANCHOR:FUNCTION_BUILD_INPUTS
    _buildInputs(world) {
        const inp = this._input;

        let nearestFood = null, nearestFoodDist2 = Infinity;
        if (!this.carrying) {
            for (const f of world.foodItems) {
                if (f.isDepleted) continue;
                const dx = f.x - this.x, dy = f.y - this.y;
                const d2 = dx * dx + dy * dy;
                if (d2 < nearestFoodDist2) { nearestFoodDist2 = d2; nearestFood = f; }
            }
        }
        const visionR2 = (SimConfig.ANT_VISION_BASE * this.genome.traits.vision_radius) ** 2;
        if (nearestFood && nearestFoodDist2 < visionR2) {
            const d = Math.sqrt(nearestFoodDist2);
            inp[0] = (nearestFood.x - this.x) / (d || 1);
            inp[1] = (nearestFood.y - this.y) / (d || 1);
        } else { inp[0] = 0; inp[1] = 0; }

        const hdx = this.hill.x - this.x, hdy = this.hill.y - this.y;
        const hd  = Math.sqrt(hdx * hdx + hdy * hdy) || 1;
        inp[2] = hdx / hd;
        inp[3] = hdy / hd;

        const ph  = world.pheromoneGrid;
        const cid = this.hill.id;
        const sens = this.genome.traits.pheromone_sensitivity;
        inp[4] = ph.sampleOwn(this.x, this.y, 0, cid) * sens;
        inp[5] = ph.sampleOwn(this.x, this.y, 1, cid) * sens;
        inp[6] = ph.sampleOwn(this.x, this.y, 2, cid) * sens;
        inp[7] = 0;

        const enemyColony = this.hill.id === 0 ? world.colony1 : world.colony0;
        let minEnemyD2 = Infinity;
        for (const e of enemyColony) {
            if (!e.alive) continue;
            const dx = e.x - this.x, dy = e.y - this.y;
            const d2 = dx * dx + dy * dy;
            if (d2 < minEnemyD2) minEnemyD2 = d2;
        }
        inp[8] = minEnemyD2 < visionR2 ? Math.sqrt(minEnemyD2) / Math.sqrt(visionR2) : 1.0;

        const allyColony = this.hill.id === 0 ? world.colony0 : world.colony1;
        let minAllyD2 = Infinity;
        for (const a of allyColony) {
            if (a === this || !a.alive) continue;
            const dx = a.x - this.x, dy = a.y - this.y;
            const d2 = dx * dx + dy * dy;
            if (d2 < minAllyD2) minAllyD2 = d2;
        }
        inp[9] = minAllyD2 < visionR2 ? Math.sqrt(minAllyD2) / Math.sqrt(visionR2) : 1.0;

        inp[10] = this.carrying ? 1.0 : 0.0;
        inp[11] = this.energy / SimConfig.ANT_ENERGY_MAX;
    }

    // ANCHOR:FUNCTION_FORWARD_PASS
    _forwardPass() {
        const [inp, hid, out] = SimConfig.NN_ARCH;
        const W = this.genome.weights;
        const input  = this._input;
        const hidden = this._hidden;
        const output = this._output;

        const W1offset = 0;
        const b1offset = hid * inp;
        for (let h = 0; h < hid; h++) {
            let sum = W[b1offset + h];
            for (let i = 0; i < inp; i++) sum += W[W1offset + h * inp + i] * input[i];
            hidden[h] = sum > 0 ? sum : 0;
        }

        const W2offset = b1offset + hid;
        const b2offset = W2offset + out * hid;
        for (let o = 0; o < out; o++) {
            let sum = W[b2offset + o];
            for (let h = 0; h < hid; h++) sum += W[W2offset + o * hid + h] * hidden[h];
            output[o] = Math.tanh(sum);
        }
    }

    // ANCHOR:FUNCTION_APPLY_OUTPUTS
    _applyOutputs(world, dt) {
        const out = this._output;
        const ph  = world.pheromoneGrid;
        const cid = this.hill.id;
        const maxTurn = Math.PI * this.genome.traits.turn_rate * dt * 3;

        // =================================================================
        // TRAIL SEVERING DETECTION
        // =================================================================
        const onTrailNow = ph.sampleOwn(this.x, this.y, 0, cid) > 0.02 ||
                           ph.sampleOwn(this.x, this.y, 1, cid) > 0.02;
        if (this._wasOnTrail && !onTrailNow && this.state === AntState.FOLLOWING_PHEROMONE) {
            // Trail was cut under us — enter confused state
            this._confusedTimer = SimConfig.CONFUSED_TICKS;
            this.state = AntState.CONFUSED;
        }
        this._wasOnTrail = onTrailNow;

        // Tick down confusion
        if (this._confusedTimer > 0) this._confusedTimer--;
        if (this._confusedTimer <= 0 && this.state === AntState.CONFUSED) {
            this.state = AntState.FORAGING;
        }

        // =================================================================
        // BEHAVIORAL STEERING
        // =================================================================
        let behaviorTurn = 0;

        if (this.state === AntState.CONFUSED) {
            // CONFUSED: spin in circles (trail was severed)
            behaviorTurn = (simRandom() - 0.5) * SimConfig.CONFUSED_JITTER;

        } else if (this.state === AntState.EXHAUSTED) {
            // EXHAUSTED: head home, become follower, follow home trail
            const hdx = this.hill.x - this.x;
            const hdy = this.hill.y - this.y;
            const homeAngle = Math.atan2(hdy, hdx);
            let diff = homeAngle - this.angle;
            diff = ((diff + Math.PI) % TWO_PI + TWO_PI) % TWO_PI - Math.PI;
            behaviorTurn = diff * 0.25;

        } else if (this.carrying) {
            // CARRYING: steer strongly toward home
            const hdx = this.hill.x - this.x;
            const hdy = this.hill.y - this.y;
            const homeAngle = Math.atan2(hdy, hdx);
            let diff = homeAngle - this.angle;
            diff = ((diff + Math.PI) % TWO_PI + TWO_PI) % TWO_PI - Math.PI;
            behaviorTurn = diff * 0.3;

            // Lay HOME_PATH trail (creates the highway)
            ph.emit(this.x, this.y, 0, SimConfig.PHEROMONE_EMIT_STRENGTH * 1.5, cid);

        } else if (this._input[0] !== 0 || this._input[1] !== 0) {
            // FOOD VISIBLE: steer toward it
            const foodAngle = Math.atan2(this._input[1], this._input[0]);
            let diff = foodAngle - this.angle;
            diff = ((diff + Math.PI) % TWO_PI + TWO_PI) % TWO_PI - Math.PI;
            behaviorTurn = diff * 0.25;
            ph.emit(this.x, this.y, 1, SimConfig.PHEROMONE_EMIT_STRENGTH * 0.6, cid);

        } else {
            // SEARCH: follow pheromone gradient or wander
            const probeD = 3.0;
            const probeA = 0.45;
            const lx = this.x + Math.cos(this.angle - probeA) * probeD;
            const ly = this.y + Math.sin(this.angle - probeA) * probeD;
            const rx = this.x + Math.cos(this.angle + probeA) * probeD;
            const ry = this.y + Math.sin(this.angle + probeA) * probeD;
            const fx = this.x + Math.cos(this.angle) * probeD;
            const fy = this.y + Math.sin(this.angle) * probeD;

            const leftFood  = ph.sampleOwn(lx, ly, 1, cid);
            const rightFood = ph.sampleOwn(rx, ry, 1, cid);
            const fwdFood   = ph.sampleOwn(fx, fy, 1, cid);
            const leftHome  = ph.sampleOwn(lx, ly, 0, cid);
            const rightHome = ph.sampleOwn(rx, ry, 0, cid);

            if (leftFood > 0.01 || rightFood > 0.01 || fwdFood > 0.01) {
                behaviorTurn = (rightFood - leftFood) * 3.5;
                this.state = AntState.FOLLOWING_PHEROMONE;
            } else if (leftHome > 0.01 || rightHome > 0.01) {
                behaviorTurn = (rightHome - leftHome) * 1.5;
            } else if (this.isExplorer) {
                behaviorTurn = (simRandom() - 0.5) * SimConfig.EXPLORER_JITTER;
            } else {
                behaviorTurn = (simRandom() - 0.5) * 0.15;
            }
        }

        // APPLY TURN
        this.angle += behaviorTurn + out[0] * maxTurn * 0.25;
        this.angle  = ((this.angle % TWO_PI) + TWO_PI) % TWO_PI;

        // SPEED
        const speedMod = 0.5 + (out[1] + 1) * 0.5;
        const effectiveSpeed = SimConfig.ANT_SPEED_BASE * this.genome.traits.speed * speedMod;
        this.x += Math.cos(this.angle) * effectiveSpeed * dt;
        this.y += Math.sin(this.angle) * effectiveSpeed * dt;

        // PHEROMONE EMISSION (NN-modulated)
        const emitRate = this.genome.traits.pheromone_emit_rate;
        if ((out[2] + 1) * 0.5 > 0.3) ph.emit(this.x, this.y, 0, SimConfig.PHEROMONE_EMIT_STRENGTH * emitRate, cid);
        if ((out[3] + 1) * 0.5 > 0.3) ph.emit(this.x, this.y, 1, SimConfig.PHEROMONE_EMIT_STRENGTH * emitRate, cid);
        if ((out[4] + 1) * 0.5 > 0.3) ph.emit(this.x, this.y, 2, SimConfig.PHEROMONE_EMIT_STRENGTH * emitRate, cid);

        // CONTEST enemy markers
        ph.contest(this.x, this.y, cid);

        // ATTACK
        if ((out[5] + 1) * 0.5 > 0.5) this._tryAttack(world);
    }

    // ANCHOR:FUNCTION_TRY_ATTACK
    _tryAttack(world) {
        const enemies = this.hill.id === 0 ? world.colony1 : world.colony0;
        const atkRange2 = (SimConfig.ANT_SIZE * 2) ** 2;
        for (const e of enemies) {
            if (!e.alive) continue;
            const dx = e.x - this.x, dy = e.y - this.y;
            if (dx * dx + dy * dy < atkRange2) {
                const dmg = this.genome.traits.attack_power * 10;
                e.energy -= dmg;
                e._dmgReceived += dmg;
                this._kills += (e.energy <= 0 ? 1 : 0);
                if (e.energy <= 0) e.alive = false;
                this.state = AntState.ATTACKING;
                return;
            }
        }
    }

    // ANCHOR:FUNCTION_UPDATE_STATE
    _updateState(world) {
        if (this.state === AntState.ATTACKING) this.state = AntState.FORAGING;

        if (!this.carrying) {
            for (const f of world.foodItems) {
                if (f.isDepleted) continue;
                const dx = f.x - this.x, dy = f.y - this.y;
                if (dx * dx + dy * dy < (SimConfig.ANT_SIZE + SimConfig.FOOD_SIZE) ** 2) {
                    const taken = f.consume(1);
                    if (taken > 0) {
                        this.carrying = true;
                        this.carriedValue = taken * SimConfig.FOOD_VALUE;
                        this._foodFinds++;
                        this.state = AntState.RETURNING;
                        world.pheromoneGrid.emit(this.x, this.y, 1,
                            SimConfig.PHEROMONE_EMIT_STRENGTH * 5.0, this.hill.id);
                    }
                    break;
                }
            }
        } else {
            if (this.hill.isInside(this.x, this.y)) {
                this.hill.deposit(this.carriedValue);
                this.genome.fitnessScore += this.carriedValue;
                this.carrying     = false;
                this.carriedValue = 0;
                this.state        = AntState.FORAGING;

                // Refuel partial energy when depositing
                this.energy = Math.min(SimConfig.ANT_ENERGY_MAX,
                    this.energy + SimConfig.ANT_ENERGY_FROM_HOME);
            }
        }

        // Exhausted ants reaching home get refueled
        if (this.state === AntState.EXHAUSTED && this.hill.isInside(this.x, this.y)) {
            this.energy = Math.min(SimConfig.ANT_ENERGY_MAX,
                this.energy + SimConfig.ANT_ENERGY_FROM_HOME);
            if (this.energy > SimConfig.ANT_ENERGY_MAX * SimConfig.ANT_ENERGY_EXHAUSTED) {
                this.state = AntState.FORAGING;
            }
        }

        this.genome.fitnessScore += this._kills * 2 - this._dmgReceived * 0.1;
        this._kills       = 0;
        this._dmgReceived = 0;
    }

    // ANCHOR:FUNCTION_CLAMP_TO_BOUNDS
    _clampToBounds(world) {
        this.x = Math.max(0, Math.min(world.worldW, this.x));
        this.y = Math.max(0, Math.min(world.worldH, this.y));
        if (this.x <= 0 || this.x >= world.worldW) this.angle = Math.PI - this.angle;
        if (this.y <= 0 || this.y >= world.worldH) this.angle = -this.angle;
        this.angle = ((this.angle % TWO_PI) + TWO_PI) % TWO_PI;
    }

    // ANCHOR:FUNCTION_RESET_POSITION
    resetToHill() {
        const jitter = this.hill.radius * 0.8;
        this.x = this.hill.x + (simRandom() - 0.5) * jitter;
        this.y = this.hill.y + (simRandom() - 0.5) * jitter;
        this.angle    = simRandom() * Math.PI * 2;
        this.energy   = SimConfig.ANT_ENERGY_MAX;
        this.alive    = true;
        this.carrying = false;
        this.carriedValue = 0;
        this.state    = AntState.FORAGING;
        this.isExplorer = simRandom() < SimConfig.EXPLORER_FRACTION;
        this._confusedTimer = 0;
        this._wasOnTrail    = false;
        this.genome.fitnessScore = 0;
        this._foodFinds    = 0;
        this._kills        = 0;
        this._dmgReceived  = 0;
    }

    // Stride = 8 floats: [x, y, angle, colony_id, state, carrying, energy_norm, nn_turn]
    writeToBuffer(buf, offset) {
        buf[offset + 0] = this.x;
        buf[offset + 1] = this.y;
        buf[offset + 2] = this.angle;
        buf[offset + 3] = this.hill.id;
        buf[offset + 4] = this.state;
        buf[offset + 5] = this.carrying ? 1.0 : 0.0;
        buf[offset + 6] = this.energy / SimConfig.ANT_ENERGY_MAX;
        buf[offset + 7] = this._output ? this._output[0] : 0.0; // NN turn output [-1,1]
    }
}
