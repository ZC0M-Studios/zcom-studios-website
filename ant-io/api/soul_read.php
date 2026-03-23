<?php
// =============================================================================
// ANCHOR:SOUL_READ
// Function: Read the Soul chain from the database
// Method:   GET
// Params:   ?from_block=N    - return blocks N through latest (default 0)
//           ?verify=1        - include chain integrity verification result
//           ?genomes=1&block=N - include genomes for a specific block index
// Returns:  { "success": bool, "chain": [...], "chain_valid": bool|null,
//             "first_bad_block": int|null, "genomes": [...] }
// =============================================================================

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once '../../includes/db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$fromBlock    = isset($_GET['from_block']) ? max(0, (int)$_GET['from_block']) : 0;
$doVerify     = isset($_GET['verify']) && $_GET['verify'] === '1';
$includeGenomes = isset($_GET['genomes']) && $_GET['genomes'] === '1';
$genomeBlock  = isset($_GET['block']) ? (int)$_GET['block'] : null;

try {
    $db = getDBConnection();

    // ANCHOR:FETCH_CHAIN
    $stmt = $db->prepare('
        SELECT block_index, generation, colony_0_pop, colony_1_pop,
               best_fitness_c0, best_fitness_c1, avg_fitness_c0, avg_fitness_c1,
               find_rate, kill_rate, dmg_recv_rate, prev_hash, block_hash,
               UNIX_TIMESTAMP(created_at) AS created_at_unix
        FROM soul_index
        WHERE block_index >= :from_block
        ORDER BY block_index ASC
    ');
    $stmt->execute([':from_block' => $fromBlock]);
    $chain = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cast numeric fields to proper types
    foreach ($chain as &$block) {
        $block['block_index']     = (int)$block['block_index'];
        $block['generation']      = (int)$block['generation'];
        $block['colony_0_pop']    = (int)$block['colony_0_pop'];
        $block['colony_1_pop']    = (int)$block['colony_1_pop'];
        $block['best_fitness_c0'] = (float)$block['best_fitness_c0'];
        $block['best_fitness_c1'] = (float)$block['best_fitness_c1'];
        $block['avg_fitness_c0']  = (float)$block['avg_fitness_c0'];
        $block['avg_fitness_c1']  = (float)$block['avg_fitness_c1'];
        $block['find_rate']       = (float)$block['find_rate'];
        $block['kill_rate']       = (float)$block['kill_rate'];
        $block['dmg_recv_rate']   = (float)$block['dmg_recv_rate'];
        $block['created_at_unix'] = (int)$block['created_at_unix'];
    }
    unset($block);

    // ANCHOR:CHAIN_VERIFICATION
    // Walk the chain server-side and verify hash integrity.
    // The JS Soul.verifyChain() does the same computation client-side.
    $chainValid    = null;
    $firstBadBlock = null;

    if ($doVerify && !empty($chain)) {
        $chainValid = true;
        $prevHash   = str_repeat('0', 64); // genesis sentinel

        // If we fetched from mid-chain, get the previous block's hash
        if ($fromBlock > 0) {
            $prevStmt = $db->prepare('SELECT block_hash FROM soul_index WHERE block_index = :idx');
            $prevStmt->execute([':idx' => $fromBlock - 1]);
            $prevRow = $prevStmt->fetch(PDO::FETCH_ASSOC);
            if ($prevRow) {
                $prevHash = $prevRow['block_hash'];
            }
        }

        foreach ($chain as $block) {
            // Recompute expected hash
            $payload = $block['prev_hash']
                . (string)(int)$block['generation']
                . (string)(int)$block['colony_0_pop']
                . (string)(int)$block['colony_1_pop']
                . sprintf('%.6f', $block['best_fitness_c0'])
                . sprintf('%.6f', $block['best_fitness_c1'])
                . sprintf('%.6f', $block['avg_fitness_c0'])
                . sprintf('%.6f', $block['avg_fitness_c1'])
                . sprintf('%.6f', $block['find_rate'])
                . sprintf('%.6f', $block['kill_rate'])
                . sprintf('%.6f', $block['dmg_recv_rate'])
                . (string)(int)$block['created_at_unix'];
            $expectedHash = hash('sha256', $payload);

            if ($block['block_hash'] !== $expectedHash || $block['prev_hash'] !== $prevHash) {
                $chainValid    = false;
                $firstBadBlock = $block['block_index'];
                break;
            }
            $prevHash = $block['block_hash'];
        }
    }

    // ANCHOR:FETCH_GENOMES
    $genomes = [];
    if ($includeGenomes && $genomeBlock !== null) {
        $gStmt = $db->prepare('
            SELECT genome_id, colony_id, generation, parent_a_id, parent_b_id,
                   nn_weights_b64, traits_json, fitness_score,
                   UNIX_TIMESTAMP(created_at) AS created_at_unix
            FROM ant_genomes
            WHERE block_index = :block_index
            ORDER BY fitness_score DESC
        ');
        $gStmt->execute([':block_index' => $genomeBlock]);
        $genomes = $gStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($genomes as &$g) {
            $g['colony_id']      = (int)$g['colony_id'];
            $g['generation']     = (int)$g['generation'];
            $g['fitness_score']  = (float)$g['fitness_score'];
            $g['traits_json']    = json_decode($g['traits_json'], true);
            $g['created_at_unix'] = (int)$g['created_at_unix'];
        }
        unset($g);
    }

    $response = [
        'success' => true,
        'chain'   => $chain,
        'count'   => count($chain),
    ];
    if ($doVerify) {
        $response['chain_valid']     = $chainValid;
        $response['first_bad_block'] = $firstBadBlock;
    }
    if ($includeGenomes) {
        $response['genomes'] = $genomes;
    }

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
