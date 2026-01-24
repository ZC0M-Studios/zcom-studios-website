<?php
/* ========================================================
    API - Delete Article
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
    $article_id = $data['id'] ?? 0;
    
    if (!$article_id) {
        throw new Exception('Article ID is required');
    }
    
    $db->beginTransaction();
    
    // Delete article tags
    $stmt = $db->prepare("DELETE FROM article_tags WHERE article_id = ?");
    $stmt->execute([$article_id]);
    
    // Delete article sections (if table exists)
    $stmt = $db->prepare("DELETE FROM article_sections WHERE article_id = ?");
    $stmt->execute([$article_id]);
    
    // Delete article
    $stmt = $db->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->execute([$article_id]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('Article not found');
    }
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Article deleted successfully'
    ]);
    
} catch (Exception $e) {
    $db->rollBack();
    error_log("Delete article error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
