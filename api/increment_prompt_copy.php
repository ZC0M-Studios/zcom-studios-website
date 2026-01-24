<?php
/* ========================================================
    //ANCHOR [API_INCREMENT_PROMPT_COPY]
    FUNCTION: Increment Prompt Copy Count API
-----------------------------------------------------------
    Parameters: ?id=prompt_id (URL parameter)
    Returns: JSON
    Description: Increments the copy count for a prompt
    UniqueID: 900090
=========================================================== */

header('Content-Type: application/json');
require_once '../includes/db_config.php';

try {
    $prompt_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($prompt_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid prompt ID']);
        exit;
    }

    $stmt = $db->prepare("UPDATE prompts SET times_copied = times_copied + 1 WHERE id = :id");
    $stmt->execute(['id' => $prompt_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Copy count incremented'
    ]);

} catch (PDOException $e) {
    error_log("Increment Prompt Copy API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
}
?>

