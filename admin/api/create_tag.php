<?php
/* ========================================================
    //ANCHOR [API_CREATE_TAG]
    FUNCTION: API - Create Tag
-----------------------------------------------------------
    Parameters: POST data (display_name, slug, category, description)
    Returns: JSON response
    Description: Creates a new tag in the tags_registry
    UniqueID: 793502
=========================================================== */

require_once __DIR__ . '/../includes/auth_check.php';

header('Content-Type: application/json');

try {
    // Verify CSRF token
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        throw new Exception('Invalid security token');
    }
    
    // Get form data
    $display_name = trim($_POST['display_name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $category = trim($_POST['category'] ?? 'custom');
    $description = trim($_POST['description'] ?? '');

    // Styling fields
    $text_color = trim($_POST['text_color'] ?? '#ffffff');
    $bg_color = trim($_POST['bg_color'] ?? '#333333');
    $border_color = trim($_POST['border_color'] ?? '#666666');
    $border_type = trim($_POST['border_type'] ?? 'solid');
    $shadow_color = trim($_POST['shadow_color'] ?? '') ?: null;

    // Validation
    if (empty($display_name)) {
        throw new Exception('Display name is required');
    }

    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $display_name), '-'));
    }

    // Validate border type
    $valid_border_types = ['solid', 'dashed', 'dotted', 'double', 'none'];
    if (!in_array($border_type, $valid_border_types)) {
        $border_type = 'solid';
    }

    // Check uniqueness
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM tags_registry WHERE slug = ? OR display_name = ?");
    $stmt->execute([$slug, $display_name]);
    if ($stmt->fetch()['count'] > 0) {
        throw new Exception('A tag with this name or slug already exists');
    }

    // Insert tag with styling
    $stmt = $db->prepare("
        INSERT INTO tags_registry (display_name, slug, category, description, text_color, bg_color, border_color, border_type, shadow_color, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([$display_name, $slug, $category, $description, $text_color, $bg_color, $border_color, $border_type, $shadow_color]);
    $tag_id = $db->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Tag created successfully',
        'tag_id' => $tag_id,
        'slug' => $slug
    ]);

} catch (Exception $e) {
    error_log("Create tag error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

