<?php
/* ========================================================
    //ANCHOR [API_UPDATE_TAG]
    FUNCTION: API - Update Tag
-----------------------------------------------------------
    Parameters: POST data (tag_id, display_name, slug, category, description, styling)
    Returns: JSON response
    Description: Updates an existing tag in the tags_registry
    UniqueID: 793504
=========================================================== */

require_once __DIR__ . '/../includes/auth_check.php';

header('Content-Type: application/json');

try {
    // Verify CSRF token
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        throw new Exception('Invalid security token');
    }
    
    // Get tag ID
    $tag_id = (int)($_POST['tag_id'] ?? 0);
    if (!$tag_id) {
        throw new Exception('Tag ID is required');
    }
    
    // Verify tag exists
    $stmt = $db->prepare("SELECT id FROM tags_registry WHERE id = ?");
    $stmt->execute([$tag_id]);
    if (!$stmt->fetch()) {
        throw new Exception('Tag not found');
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

    // Check uniqueness (excluding current tag)
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM tags_registry WHERE (slug = ? OR display_name = ?) AND id != ?");
    $stmt->execute([$slug, $display_name, $tag_id]);
    if ($stmt->fetch()['count'] > 0) {
        throw new Exception('A tag with this name or slug already exists');
    }

    // Update tag
    $stmt = $db->prepare("
        UPDATE tags_registry 
        SET display_name = ?, slug = ?, category = ?, description = ?, 
            text_color = ?, bg_color = ?, border_color = ?, border_type = ?, shadow_color = ?
        WHERE id = ?
    ");

    $stmt->execute([$display_name, $slug, $category, $description, $text_color, $bg_color, $border_color, $border_type, $shadow_color, $tag_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Tag updated successfully',
        'tag_id' => $tag_id,
        'slug' => $slug
    ]);

} catch (Exception $e) {
    error_log("Update tag error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

