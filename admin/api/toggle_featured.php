<?php
/* ========================================================
    API - Toggle Featured Status
=========================================================== */

require_once __DIR__ . '/../../includes/db_config.php';
require_once __DIR__ . '/../includes/auth_check.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $type = $data['type'] ?? '';
    $id = $data['id'] ?? 0;
    
    if (!$id || !$type) {
        throw new Exception('Type and ID are required');
    }
    
    // Validate type
    $valid_types = ['article', 'project', 'prompt', 'tool'];
    if (!in_array($type, $valid_types)) {
        throw new Exception('Invalid type');
    }
    
    // Get table name
    $table = $type === 'article' ? 'articles' : $type . 's';
    
    // Toggle featured status
    $stmt = $db->prepare("UPDATE $table SET featured = NOT featured WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('Item not found');
    }
    
    // Get new status
    $stmt = $db->prepare("SELECT featured FROM $table WHERE id = ?");
    $stmt->execute([$id]);
    $featured = $stmt->fetch()['featured'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Featured status updated',
        'featured' => (bool)$featured
    ]);
    
} catch (Exception $e) {
    error_log("Toggle featured error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
