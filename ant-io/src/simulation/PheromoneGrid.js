// =============================================================================
// ANCHOR:PHEROMONE_GRID
// Description: Colony-owned pheromone grid with marker competition.
//
//   Data layout per cell (RGBA float, uploaded to WebGL texture):
//     R = HOME_PATH  intensity (0–1)
//     G = FOOD_FOUND intensity (0–1)
//     B = DANGER     intensity (0–1)
//     A = colony ownership  (0.0=unclaimed, 0.25=colony 0, 0.75=colony 1)
//
//   Colony competition mechanic:
//     - Each cell belongs to one colony at a time.
//     - Ants only read/follow markers from their own colony.
//     - Stepping on an enemy cell subtracts MARKER_CONTEST_RATE from its signals.
//     - When total signal drops to 0, the cell flips to the stepping ant's colony.
//     - Dense traffic from one colony can overwrite and sever enemy trails.
// =============================================================================

import { SimConfig } from '../config/SimConfig.js';

// Colony ownership encoding in the A channel
const OWNER_NONE   = 0.0;
const OWNER_COL0   = 0.25;
const OWNER_COL1   = 0.75;
const OWNER_THRESH = 0.1; // threshold to distinguish unclaimed vs colony 0

export class PheromoneGrid {
    constructor(
        gridW = SimConfig.PHEROMONE_GRID_W,
        gridH = SimConfig.PHEROMONE_GRID_H,
        worldW = SimConfig.WORLD_W,
        worldH = SimConfig.WORLD_H
    ) {
        this.gridW  = gridW;
        this.gridH  = gridH;
        this.worldW = worldW;
        this.worldH = worldH;
        this.data   = new Float32Array(gridW * gridH * 4); // RGBA channels
        this._dirty = true;
    }

    // -------------------------------------------------------------------------
    // ANCHOR:FUNCTION_CELL_INDEX
    // Converts world coordinates to grid cell (cx, cy). Returns null if OOB.
    // -------------------------------------------------------------------------
    _toCell(wx, wy) {
        const cx = Math.floor((wx / this.worldW) * this.gridW);
        const cy = Math.floor((wy / this.worldH) * this.gridH);
        if (cx < 0 || cx >= this.gridW || cy < 0 || cy >= this.gridH) return null;
        return { cx, cy };
    }

    _idx(cx, cy) { return (cy * this.gridW + cx) * 4; }

    // -------------------------------------------------------------------------
    // ANCHOR:FUNCTION_GET_OWNER
    // Returns the colony ID that owns the cell at world coords, or -1 if unclaimed.
    // -------------------------------------------------------------------------
    getOwner(wx, wy) {
        const c = this._toCell(wx, wy);
        if (!c) return -1;
        return this._ownerFromA(this.data[this._idx(c.cx, c.cy) + 3]);
    }

    _ownerFromA(aVal) {
        if (aVal > 0.5)            return 1;   // colony 1
        if (aVal > OWNER_THRESH)   return 0;   // colony 0
        return -1;                              // unclaimed
    }

    _ownerToA(colonyId) {
        if (colonyId === 0) return OWNER_COL0;
        if (colonyId === 1) return OWNER_COL1;
        return OWNER_NONE;
    }

    // -------------------------------------------------------------------------
    // ANCHOR:FUNCTION_EMIT
    // Emits a signal into a 3×3 neighborhood. Only affects cells owned by the
    // emitting colony or unclaimed cells (which become claimed).
    // Parameters:
    //   wx, wy     {number} - world coords
    //   channel    {number} - signal channel (0=HOME, 1=FOOD, 2=DANGER)
    //   strength   {number} - emission amount
    //   colonyId   {number} - 0 or 1
    // -------------------------------------------------------------------------
    emit(wx, wy, channel, strength, colonyId) {
        const c = this._toCell(wx, wy);
        if (!c) return;

        for (let dy = -1; dy <= 1; dy++) {
            for (let dx = -1; dx <= 1; dx++) {
                const nx = c.cx + dx, ny = c.cy + dy;
                if (nx < 0 || nx >= this.gridW || ny < 0 || ny >= this.gridH) continue;

                const idx   = this._idx(nx, ny);
                const owner = this._ownerFromA(this.data[idx + 3]);

                // Only emit into own cells or unclaimed cells
                if (owner !== -1 && owner !== colonyId) continue;

                const falloff = (dx === 0 && dy === 0) ? 1.0 : 0.4;
                this.data[idx + channel] = Math.min(1.0, this.data[idx + channel] + strength * falloff);

                // Claim unclaimed cells
                if (owner === -1) {
                    this.data[idx + 3] = this._ownerToA(colonyId);
                }
            }
        }
        this._dirty = true;
    }

    // -------------------------------------------------------------------------
    // ANCHOR:FUNCTION_CONTEST
    // An ant steps on a cell. If the cell is owned by the enemy colony, subtract
    // MARKER_CONTEST_RATE from all signal channels. If total signal drops to ≈0,
    // flip ownership to the stepping ant's colony.
    // Parameters:
    //   wx, wy     {number} - world coords
    //   colonyId   {number} - the stepping ant's colony (0 or 1)
    //   rate       {number} - amount to subtract (default from SimConfig)
    // -------------------------------------------------------------------------
    contest(wx, wy, colonyId, rate = SimConfig.MARKER_CONTEST_RATE) {
        const c = this._toCell(wx, wy);
        if (!c) return;

        const idx   = this._idx(c.cx, c.cy);
        const owner = this._ownerFromA(this.data[idx + 3]);

        // Only contest enemy-owned cells
        if (owner === -1 || owner === colonyId) return;

        // Subtract from all signal channels
        for (let ch = 0; ch < 3; ch++) {
            this.data[idx + ch] = Math.max(0, this.data[idx + ch] - rate);
        }

        // Check if total signal is depleted → flip ownership
        const total = this.data[idx] + this.data[idx + 1] + this.data[idx + 2];
        if (total < 0.005) {
            // Flip ownership to the contesting ant's colony
            this.data[idx + 3] = this._ownerToA(colonyId);
        }

        this._dirty = true;
    }

    // -------------------------------------------------------------------------
    // ANCHOR:FUNCTION_SAMPLE_OWN
    // Samples a signal channel at world coords, but only if the cell is owned
    // by the specified colony. Returns 0 for enemy or unclaimed cells.
    // This is how ants are "blind" to enemy trails.
    // -------------------------------------------------------------------------
    sampleOwn(wx, wy, channel, colonyId) {
        const c = this._toCell(wx, wy);
        if (!c) return 0;
        const idx   = this._idx(c.cx, c.cy);
        const owner = this._ownerFromA(this.data[idx + 3]);
        if (owner !== colonyId) return 0;
        return this.data[idx + channel];
    }

    // -------------------------------------------------------------------------
    // ANCHOR:FUNCTION_SAMPLE (legacy — returns raw value regardless of ownership)
    // Used by systems that need to read any colony's markers (e.g. overlay).
    // -------------------------------------------------------------------------
    sample(wx, wy, signalType) {
        const fx  = (wx / this.worldW) * this.gridW - 0.5;
        const fy  = (wy / this.worldH) * this.gridH - 0.5;
        const x0  = Math.floor(fx), y0 = Math.floor(fy);
        const x1  = x0 + 1,        y1 = y0 + 1;
        const tx  = fx - x0,       ty = fy - y0;

        const _get = (cx, cy) => {
            if (cx < 0 || cx >= this.gridW || cy < 0 || cy >= this.gridH) return 0;
            return this.data[(cy * this.gridW + cx) * 4 + signalType];
        };

        const v00 = _get(x0, y0), v10 = _get(x1, y0);
        const v01 = _get(x0, y1), v11 = _get(x1, y1);

        return (1 - ty) * ((1 - tx) * v00 + tx * v10)
             +      ty  * ((1 - tx) * v01 + tx * v11);
    }

    // -------------------------------------------------------------------------
    // ANCHOR:FUNCTION_FADE
    // Fades signal intensities (R, G, B) but preserves colony ownership (A).
    // When all signals in a cell drop below threshold, ownership is released.
    // -------------------------------------------------------------------------
    fade() {
        const rate = SimConfig.PHEROMONE_FADE_RATE;
        const data = this.data;
        for (let i = 0; i < data.length; i += 4) {
            data[i]     *= rate; // R = home
            data[i + 1] *= rate; // G = food
            data[i + 2] *= rate; // B = danger
            // A = colony ownership — don't fade, but release if signals gone
            if (data[i] + data[i + 1] + data[i + 2] < 0.003) {
                data[i]     = 0;
                data[i + 1] = 0;
                data[i + 2] = 0;
                data[i + 3] = OWNER_NONE; // release ownership
            }
        }
        this._dirty = true;
    }

    // -------------------------------------------------------------------------
    // ANCHOR:FUNCTION_CLEAR
    // -------------------------------------------------------------------------
    clear() {
        this.data.fill(0);
        this._dirty = true;
    }

    getBuffer() { return this.data; }
    get isDirty()  { return this._dirty; }
    markClean()    { this._dirty = false; }
}
