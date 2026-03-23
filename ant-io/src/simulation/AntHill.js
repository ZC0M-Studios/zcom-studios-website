// =============================================================================
// ANCHOR:ANTHILL
// Description: Ant colony home base with resource pool for spawning new ants.
//              Food deposited by ants accumulates in resourcePool.
//              When resourcePool >= ANT_SPAWN_COST, a new ant can be spawned.
// =============================================================================

import { SimConfig } from '../config/SimConfig.js';

export class AntHill {
    constructor(cfg, radius) {
        this.id     = cfg.id;
        this.x      = cfg.x;
        this.y      = cfg.y;
        this.color  = cfg.color;
        this.radius = radius;
        this.foodStored      = 0;
        this.totalDeliveries = 0;
        this.resourcePool    = 0;
    }

    isInside(x, y) {
        const dx = x - this.x;
        const dy = y - this.y;
        return (dx * dx + dy * dy) <= (this.radius * this.radius);
    }

    deposit(value) {
        this.foodStored += value;
        this.totalDeliveries++;
        this.resourcePool += value;
    }

    canSpawn() {
        if (this.resourcePool >= SimConfig.ANT_SPAWN_COST) {
            this.resourcePool -= SimConfig.ANT_SPAWN_COST;
            return true;
        }
        return false;
    }

    reset() {
        this.foodStored = 0;
        this.totalDeliveries = 0;
        this.resourcePool = 0;
    }
}
