<?php
/* ========================================================
    //ANCHOR [API_CREATE_TOOL]
    FUNCTION: API - Create Tool
-----------------------------------------------------------
    Parameters: POST data (name, slug, description, page_url, featured, tags[])
    Returns: JSON response
    Description: Creates a new tool/utility in the database
    UniqueID: 793402
=========================================================== */

require_once __DIR__ . '/../includes/auth_check.php';

header('Content-Type: application/json');

try {
    // Verify CSRF token
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        throw new Exception('Invalid security token');
    }
    
    $db->beginTransaction();
    
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $page_url = trim($_POST['page_url'] ?? '');
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Validation
    if (empty($name)) {
        throw new Exception('Tool name is required');
    }
    
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    }
    
    // Check slug uniqueness
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM tools WHERE slug = ?");
    $stmt->execute([$slug]);
    if ($stmt->fetch()['count'] > 0) {
        $slug .= '-' . time();
    }
    
    // Insert tool
    $stmt = $db->prepare("
        INSERT INTO tools (name, slug, description, page_url, featured, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, NOW(), NOW())
    ");
    
    $stmt->execute([$name, $slug, $description, $page_url, $featured]);
    $tool_id = $db->lastInsertId();
    
    // Handle tags (check if tool_tags table exists)
    $tags = $_POST['tags'] ?? [];
    if (!empty($tags)) {
        $tableCheck = $db->query("SHOW TABLES LIKE 'tool_tags'")->fetch();
        if ($tableCheck) {
            foreach ($tags as $tag_id) {
                $stmt = $db->prepare("
                    INSERT INTO tool_tags (tool_id, tag_id)
                    VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE tool_id = tool_id
                ");
                $stmt->execute([$tool_id, $tag_id]);
            }
        }
    }
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Tool created successfully',
        'tool_id' => $tool_id,
        'slug' => $slug
    ]);

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    error_log("Create tool error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

