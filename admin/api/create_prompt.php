<?php
/* ========================================================
    //ANCHOR [API_CREATE_PROMPT]
    FUNCTION: API - Create Prompt
-----------------------------------------------------------
    Parameters: POST data (title, slug, description, prompt_text, visibility, featured, tags[])
    Returns: JSON response
    Description: Creates a new prompt in the database
    UniqueID: 793302
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
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prompt_text = trim($_POST['prompt_text'] ?? '');
    $visibility = $_POST['visibility'] ?? 'public';
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Validation
    if (empty($title)) {
        throw new Exception('Prompt title is required');
    }
    
    if (empty($prompt_text)) {
        throw new Exception('Prompt text is required');
    }
    
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    }
    
    // Check slug uniqueness
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM prompts WHERE slug = ?");
    $stmt->execute([$slug]);
    if ($stmt->fetch()['count'] > 0) {
        $slug .= '-' . time();
    }
    
    // Insert prompt
    $stmt = $db->prepare("
        INSERT INTO prompts (title, slug, description, prompt_text, visibility, featured, copies, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, 0, NOW(), NOW())
    ");
    
    $stmt->execute([$title, $slug, $description, $prompt_text, $visibility, $featured]);
    $prompt_id = $db->lastInsertId();
    
    // Handle tags (check if prompt_tags table exists)
    $tags = $_POST['tags'] ?? [];
    if (!empty($tags)) {
        $tableCheck = $db->query("SHOW TABLES LIKE 'prompt_tags'")->fetch();
        if ($tableCheck) {
            foreach ($tags as $tag_id) {
                $stmt = $db->prepare("
                    INSERT INTO prompt_tags (prompt_id, tag_id)
                    VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE prompt_id = prompt_id
                ");
                $stmt->execute([$prompt_id, $tag_id]);
            }
        }
    }
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Prompt created successfully',
        'prompt_id' => $prompt_id,
        'slug' => $slug
    ]);

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    error_log("Create prompt error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

