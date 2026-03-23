// =============================================================================
// ANCHOR:FOOD
// Description: Data class for a food chunk in the world.
//              Tracks remaining value and whether it has been fully consumed.
// =============================================================================

import { SimConfig } from '../config/SimConfig.js';

export class Food {
    // Parameters:
    //   x, y   {number} - world position
    //   value  {number} - total food value (default from SimConfig)
    constructor(x, y, value = SimConfig.FOOD_VALUE) {
        this.x         = x;
        this.y         = y;
        this.value     = value;
        this.remaining = value;
    }

    // ANCHOR:FUNCTION_CONSUME
    // Parameters: amount {number} - how much to consume (clamped to remaining)
    // Returns: number - actual amount consumed
    consume(amount = 1) {
        const actual = Math.min(amount, this.remaining);
        this.remaining -= actual;
        return actual;
    }

    get isDepleted() {
        return this.remaining <= 0;
    }

    // Visual size scales with remaining value for shader uniform
    get currentSize() {
        return SimConfig.FOOD_SIZE * Math.sqrt(this.remaining / this.value);
    }
}

// ANCHOR:FUNCTION_SPAWN_FOOD
// Generates a new batch of food chunks avoiding colony spawn areas.
// Parameters:
//   hills   {AntHill[]} - colony hills to avoid
//   worldW  {number}
//   worldH  {number}
// Returns: Food[]
export function spawnFood(hills, worldW = SimConfig.WORLD_W, worldH = SimConfig.WORLD_H) {
    const count  = SimConfig.FOOD_COUNT_MIN
                 + Math.floor(Math.random() * (SimConfig.FOOD_COUNT_MAX - SimConfig.FOOD_COUNT_MIN + 1));
    const margin = SimConfig.FOOD_MARGIN;
    const items  = [];
    let  attempts = 0;

    while (items.length < count && attempts < count * 10) {
        attempts++;
        const x = margin + Math.random() * (worldW - 2 * margin);
        const y = margin + Math.random() * (worldH - 2 * margin);

        // Reject positions too close to any hill
        const tooClose = hills.some(h => {
            const dx = x - h.x, dy = y - h.y;
            return (dx * dx + dy * dy) < (margin * margin);
        });

        if (!tooClose) items.push(new Food(x, y));
    }

    return items;
}
