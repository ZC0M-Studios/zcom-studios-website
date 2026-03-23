<?php
// =============================================================================
// ANCHOR:SOUL_EXPORT
// Function: Export the full Soul chain + all genomes as a downloadable JSON file
// Method:   GET
// Returns:  JSON file download: { "exported_at", "block_count", "genome_count",
//                                 "chain": [...], "genomes": { block_index: [...] } }
// =============================================================================

require_once '../../includes/db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $db = getDBConnection();

    // Fetch full chain
    $chainStmt = $db->query('
        SELECT block_index, generation, colony_0_pop, colony_1_pop,
               best_fitness_c0, best_fitness_c1, avg_fitness_c0, avg_fitness_c1,
               find_rate, kill_rate, dmg_recv_rate, prev_hash, block_hash,
               UNIX_TIMESTAMP(created_at) AS created_at_unix
        FROM soul_index
        ORDER BY block_index ASC
    ');
    $chain = $chainStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all genomes, keyed by block_index
    $genomesStmt = $db->query('
        SELECT block_index, genome_id, colony_id, generation,
               parent_a_id, parent_b_id, nn_weights_b64, traits_json, fitness_score,
               UNIX_TIMESTAMP(created_at) AS created_at_unix
        FROM ant_genomes
        ORDER BY block_index ASC, fitness_score DESC
    ');
    $allGenomes = $genomesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Cast types for chain
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

    // Group genomes by block_index
    $genomesByBlock = [];
    foreach ($allGenomes as $g) {
        $idx = (int)$g['block_index'];
        if (!isset($genomesByBlock[$idx])) {
            $genomesByBlock[$idx] = [];
        }
        $g['colony_id']       = (int)$g['colony_id'];
        $g['generation']      = (int)$g['generation'];
        $g['fitness_score']   = (float)$g['fitness_score'];
        $g['traits_json']     = json_decode($g['traits_json'], true);
        $g['created_at_unix'] = (int)$g['created_at_unix'];
        unset($g['block_index']); // redundant — keyed by block_index
        $genomesByBlock[$idx][] = $g;
    }

    $export = [
        'exported_at'  => date('c'),
        'block_count'  => count($chain),
        'genome_count' => count($allGenomes),
        'chain'        => $chain,
        'genomes'      => $genomesByBlock,
    ];

    $filename = 'soul_export_' . date('Ymd_His') . '.json';
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('X-Content-Type-Options: nosniff');

    echo json_encode($export, JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
