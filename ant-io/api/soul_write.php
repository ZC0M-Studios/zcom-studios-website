<?php
// =============================================================================
// ANCHOR:SOUL_WRITE
// Function: Append a new generation block + elite genomes to the Soul chain
// Method:   POST
// Body:     application/json
// Request:  { "block": {...}, "genomes": [...] }
// Returns:  { "success": bool, "block_index": int, "block_hash": string, "error": string }
// Rules:    - Validates hash integrity before any INSERT
//           - Wraps both INSERTs in a transaction (atomic)
//           - Never updates or deletes existing rows
// =============================================================================

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once '../../includes/db_config.php';

// ANCHOR:HELPER_COMPUTE_HASH
// Replicates the JS Hash.computeBlockHash() logic.
// Floats formatted to 6 decimal places so PHP and JS produce identical hashes.
// Parameters: string $prevHash, array $f (block fields)
// Returns: string lowercase hex SHA-256
function computeBlockHash(string $prevHash, array $f): string {
    $payload = $prevHash
        . (string)(int)$f['generation']
        . (string)(int)$f['colony_0_pop']
        . (string)(int)$f['colony_1_pop']
        . sprintf('%.6f', (float)$f['best_fitness_c0'])
        . sprintf('%.6f', (float)$f['best_fitness_c1'])
        . sprintf('%.6f', (float)$f['avg_fitness_c0'])
        . sprintf('%.6f', (float)$f['avg_fitness_c1'])
        . sprintf('%.6f', (float)$f['find_rate'])
        . sprintf('%.6f', (float)$f['kill_rate'])
        . sprintf('%.6f', (float)$f['dmg_recv_rate'])
        . (string)(int)$f['created_at_unix'];
    return hash('sha256', $payload);
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!$data || !isset($data['block'], $data['genomes'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request body']);
    exit;
}

$block   = $data['block'];
$genomes = $data['genomes'];

// Validate required block fields
$requiredBlockFields = [
    'generation', 'colony_0_pop', 'colony_1_pop',
    'best_fitness_c0', 'best_fitness_c1', 'avg_fitness_c0', 'avg_fitness_c1',
    'find_rate', 'kill_rate', 'dmg_recv_rate',
    'prev_hash', 'block_hash', 'created_at_unix'
];
foreach ($requiredBlockFields as $field) {
    if (!isset($block[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Missing block field: $field"]);
        exit;
    }
}

try {
    $db = getDBConnection();

    // ANCHOR:CHAIN_VERIFY
    // Fetch the last block_hash to verify chain continuity.
    // Genesis: no rows exist → prev_hash must be 64 zeros.
    $stmt = $db->query('SELECT block_index, block_hash FROM soul_index ORDER BY block_index DESC LIMIT 1');
    $last = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($last === false) {
        // Genesis block
        $expectedPrevHash = str_repeat('0', 64);
        $nextBlockIndex   = 0;
    } else {
        $expectedPrevHash = $last['block_hash'];
        $nextBlockIndex   = (int)$last['block_index'] + 1;
    }

    // Verify prev_hash matches chain tip
    if ($block['prev_hash'] !== $expectedPrevHash) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'error'   => 'Chain continuity violation: prev_hash does not match last block_hash'
        ]);
        exit;
    }

    // Verify block_hash integrity (server recomputes and checks)
    $serverHash = computeBlockHash($block['prev_hash'], $block);
    if ($block['block_hash'] !== $serverHash) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error'   => 'Hash integrity failure: block_hash does not match server computation'
        ]);
        exit;
    }

    // ANCHOR:TRANSACTION_INSERT
    $db->beginTransaction();

    // Insert soul_index block
    $insertBlock = $db->prepare('
        INSERT INTO soul_index
            (block_index, generation, colony_0_pop, colony_1_pop,
             best_fitness_c0, best_fitness_c1, avg_fitness_c0, avg_fitness_c1,
             find_rate, kill_rate, dmg_recv_rate, prev_hash, block_hash)
        VALUES
            (:block_index, :generation, :colony_0_pop, :colony_1_pop,
             :best_fitness_c0, :best_fitness_c1, :avg_fitness_c0, :avg_fitness_c1,
             :find_rate, :kill_rate, :dmg_recv_rate, :prev_hash, :block_hash)
    ');
    $insertBlock->execute([
        ':block_index'     => $nextBlockIndex,
        ':generation'      => (int)$block['generation'],
        ':colony_0_pop'    => (int)$block['colony_0_pop'],
        ':colony_1_pop'    => (int)$block['colony_1_pop'],
        ':best_fitness_c0' => (float)$block['best_fitness_c0'],
        ':best_fitness_c1' => (float)$block['best_fitness_c1'],
        ':avg_fitness_c0'  => (float)$block['avg_fitness_c0'],
        ':avg_fitness_c1'  => (float)$block['avg_fitness_c1'],
        ':find_rate'       => (float)$block['find_rate'],
        ':kill_rate'       => (float)$block['kill_rate'],
        ':dmg_recv_rate'   => (float)$block['dmg_recv_rate'],
        ':prev_hash'       => $block['prev_hash'],
        ':block_hash'      => $block['block_hash'],
    ]);

    // Insert elite genomes
    $insertGenome = $db->prepare('
        INSERT INTO ant_genomes
            (block_index, genome_id, colony_id, generation,
             parent_a_id, parent_b_id, nn_weights_b64, traits_json, fitness_score)
        VALUES
            (:block_index, :genome_id, :colony_id, :generation,
             :parent_a_id, :parent_b_id, :nn_weights_b64, :traits_json, :fitness_score)
    ');

    foreach ($genomes as $genome) {
        $insertGenome->execute([
            ':block_index'    => $nextBlockIndex,
            ':genome_id'      => $genome['genome_id'],
            ':colony_id'      => (int)$genome['colony_id'],
            ':generation'     => (int)$genome['generation'],
            ':parent_a_id'    => $genome['parent_a_id'] ?? null,
            ':parent_b_id'    => $genome['parent_b_id'] ?? null,
            ':nn_weights_b64' => $genome['nn_weights_b64'],
            ':traits_json'    => is_string($genome['traits_json'])
                                    ? $genome['traits_json']
                                    : json_encode($genome['traits_json']),
            ':fitness_score'  => (float)($genome['fitness_score'] ?? 0),
        ]);
    }

    $db->commit();

    echo json_encode([
        'success'     => true,
        'block_index' => $nextBlockIndex,
        'block_hash'  => $block['block_hash'],
    ]);

} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
