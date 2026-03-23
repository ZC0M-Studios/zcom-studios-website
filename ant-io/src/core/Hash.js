// =============================================================================
// ANCHOR:HASH
// Description: SHA-256 hashing via the Web Crypto API.
//              All functions produce lowercase hex strings that match the
//              PHP hash('sha256', ...) output for the same payload string.
//
//              IMPORTANT: Float fields must be formatted with .toFixed(6)
//              to match PHP's sprintf('%.6f', ...) — both sides must agree.
// =============================================================================

// ANCHOR:FUNCTION_HEX_FROM_BUFFER
// Parameters: ArrayBuffer buf
// Returns:    string lowercase hex
function hexFromBuffer(buf) {
    return Array.from(new Uint8Array(buf))
        .map(b => b.toString(16).padStart(2, '0'))
        .join('');
}

// ANCHOR:FUNCTION_SHA256
// Parameters: string payload
// Returns:    Promise<string> lowercase hex SHA-256 digest
export async function sha256(payload) {
    const encoded = new TextEncoder().encode(payload);
    const buf     = await crypto.subtle.digest('SHA-256', encoded);
    return hexFromBuffer(buf);
}

// ANCHOR:FUNCTION_COMPUTE_BLOCK_HASH
// Description: Replicates soul_write.php::computeBlockHash().
//              Field order and float formatting MUST remain in sync with PHP.
// Parameters:
//   prevHash {string}  - hex hash of previous block (64 zeros for genesis)
//   fields   {object}  - block data fields (see soul_write.php for list)
// Returns: Promise<string> lowercase hex SHA-256
export async function computeBlockHash(prevHash, fields) {
    const payload = prevHash
        + String(Math.round(fields.generation))
        + String(Math.round(fields.colony_0_pop))
        + String(Math.round(fields.colony_1_pop))
        + Number(fields.best_fitness_c0).toFixed(6)
        + Number(fields.best_fitness_c1).toFixed(6)
        + Number(fields.avg_fitness_c0).toFixed(6)
        + Number(fields.avg_fitness_c1).toFixed(6)
        + Number(fields.find_rate).toFixed(6)
        + Number(fields.kill_rate).toFixed(6)
        + Number(fields.dmg_recv_rate).toFixed(6)
        + String(Math.round(fields.created_at_unix));
    return sha256(payload);
}

// ANCHOR:FUNCTION_DERIVE_GENOME_ID
// Description: Derives a deterministic genome_id from lineage + generation seed.
//              Genesis ants use the literal string "NULL" for both parent slots,
//              which must match the PHP schema comment and soul_write expectations.
// Parameters:
//   parentAId    {string} - genome_id of parent A, or "NULL" for genesis
//   parentBId    {string} - genome_id of parent B, or "NULL" for genesis
//   generation   {number} - integer generation number
//   seedHex      {string} - random 8-char hex string (Math.random based)
// Returns: Promise<string> lowercase hex SHA-256 (64 chars)
export async function deriveGenomeId(parentAId, parentBId, generation, seedHex) {
    const payload = String(parentAId) + String(parentBId) + String(Math.round(generation)) + String(seedHex);
    return sha256(payload);
}

// ANCHOR:FUNCTION_RANDOM_SEED_HEX
// Returns a random 8-character hex string for use in genome ID derivation
export function randomSeedHex() {
    return Math.floor(Math.random() * 0xFFFFFFFF).toString(16).padStart(8, '0');
}
