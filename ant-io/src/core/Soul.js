// =============================================================================
// ANCHOR:SOUL
// Description: Client-side Soul chain manager.
//              Handles writing new generation blocks to the PHP API,
//              reading and verifying the chain, and caching the chain tip.
// =============================================================================

import { SimConfig } from '../config/SimConfig.js';
import { computeBlockHash } from './Hash.js';

export class Soul {
    constructor() {
        this._lastBlockHash = '0'.repeat(64); // genesis sentinel
        this._lastBlockIndex = -1;
        this._writing = false; // prevent concurrent writes
    }

    // ANCHOR:FUNCTION_WRITE_GENERATION
    // Collects top-N ants from each colony, builds the Soul block, and POSTs
    // to soul_write.php. Non-blocking — errors are logged, not thrown.
    // Parameters:
    //   generation {number}   - current generation number
    //   stats      {object}   - { colony0, colony1, find_rate, kill_rate, dmg_recv_rate }
    //     colony0/colony1: arrays of Ant objects sorted by fitnessScore descending
    // Returns: Promise<void>
    async writeGeneration(generation, stats) {
        if (this._writing) {
            console.warn('[Soul] Write already in progress — skipping generation', generation);
            return;
        }
        this._writing = true;

        try {
            const now = Math.floor(Date.now() / 1000);

            const col0 = stats.colony0.slice(0, SimConfig.SOUL_ELITE_COUNT);
            const col1 = stats.colony1.slice(0, SimConfig.SOUL_ELITE_COUNT);

            const blockFields = {
                generation:      generation,
                colony_0_pop:    stats.colony0.length,
                colony_1_pop:    stats.colony1.length,
                best_fitness_c0: col0[0]?.genome?.fitnessScore ?? 0,
                best_fitness_c1: col1[0]?.genome?.fitnessScore ?? 0,
                avg_fitness_c0:  _avg(stats.colony0.map(a => a.genome.fitnessScore)),
                avg_fitness_c1:  _avg(stats.colony1.map(a => a.genome.fitnessScore)),
                find_rate:       stats.find_rate,
                kill_rate:       stats.kill_rate,
                dmg_recv_rate:   stats.dmg_recv_rate,
                created_at_unix: now,
            };

            const blockHash = await computeBlockHash(this._lastBlockHash, blockFields);

            const block = {
                ...blockFields,
                prev_hash:   this._lastBlockHash,
                block_hash:  blockHash,
            };

            // Serialize elite genomes from both colonies
            const genomes = [
                ...col0.map(a => a.genome.serialize()),
                ...col1.map(a => a.genome.serialize()),
            ];

            const res = await fetch(`${SimConfig.API_BASE}/soul_write.php`, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify({ block, genomes }),
            });

            const json = await res.json();

            if (json.success) {
                this._lastBlockHash  = json.block_hash;
                this._lastBlockIndex = json.block_index;
                console.info(`[Soul] Block #${json.block_index} written — gen ${generation}`);
            } else {
                console.error('[Soul] Write rejected:', json.error);
            }

        } catch (err) {
            console.error('[Soul] Write failed:', err);
        } finally {
            this._writing = false;
        }
    }

    // ANCHOR:FUNCTION_READ_CHAIN
    // Fetches the chain from soul_read.php.
    // Parameters:
    //   fromBlock  {number}  - start block index (default 0)
    //   verify     {boolean} - request server-side verification (default false)
    // Returns: Promise<{ chain, chain_valid, first_bad_block }>
    async readChain(fromBlock = 0, verify = false) {
        const url = `${SimConfig.API_BASE}/soul_read.php?from_block=${fromBlock}${verify ? '&verify=1' : ''}`;
        const res  = await fetch(url);
        return res.json();
    }

    // ANCHOR:FUNCTION_READ_GENOMES
    // Fetches genomes for a specific block.
    // Parameters: blockIndex {number}
    // Returns: Promise<Genome[]> - deserialized Genome instances
    async readGenomes(blockIndex) {
        const url = `${SimConfig.API_BASE}/soul_read.php?genomes=1&block=${blockIndex}`;
        const res  = await fetch(url);
        const json = await res.json();
        if (!json.success) throw new Error(json.error);

        const { Genome } = await import('./Genome.js');
        return json.genomes.map(row => Genome.deserialize(row));
    }

    // ANCHOR:FUNCTION_VERIFY_CHAIN
    // Client-side chain walk. Recomputes every block_hash and checks linkage.
    // Parameters: blocks {object[]} - chain array from readChain()
    // Returns: Promise<{ valid: boolean, firstBadBlock: number|null }>
    async verifyChain(blocks) {
        if (!blocks || blocks.length === 0) return { valid: true, firstBadBlock: null };

        let prevHash = '0'.repeat(64);

        // If blocks don't start from genesis, prevHash is unknown — skip genesis check
        if (blocks[0].block_index > 0) {
            prevHash = blocks[0].prev_hash;
        }

        for (const block of blocks) {
            const recomputed = await computeBlockHash(block.prev_hash, block);
            if (recomputed !== block.block_hash || block.prev_hash !== prevHash) {
                return { valid: false, firstBadBlock: block.block_index };
            }
            prevHash = block.block_hash;
        }
        return { valid: true, firstBadBlock: null };
    }

    // ANCHOR:FUNCTION_SYNC_CHAIN_TIP
    // Syncs the in-memory lastBlockHash to the actual DB tip.
    // Call this on startup if continuing a previous session.
    // Returns: Promise<void>
    async syncChainTip() {
        try {
            const data = await this.readChain(0, false);
            if (data.chain && data.chain.length > 0) {
                const last = data.chain[data.chain.length - 1];
                this._lastBlockHash  = last.block_hash;
                this._lastBlockIndex = last.block_index;
                console.info(`[Soul] Synced to chain tip: block #${this._lastBlockIndex}`);
            }
        } catch (err) {
            console.warn('[Soul] Could not sync chain tip:', err);
        }
    }

    get lastBlockIndex() { return this._lastBlockIndex; }
    get lastBlockHash()  { return this._lastBlockHash;  }
}

// ANCHOR:HELPER_AVG
function _avg(arr) {
    if (!arr.length) return 0;
    return arr.reduce((s, v) => s + v, 0) / arr.length;
}
