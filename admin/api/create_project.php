<?php
/* ========================================================
    //ANCHOR [API_CREATE_PROJECT]
    FUNCTION: API - Create Project
-----------------------------------------------------------
    Parameters: POST data (name, slug, tagline, description, status, featured, tags[])
    Returns: JSON response
    Description: Creates a new project in the database
    UniqueID: 793202
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
    $tagline = trim($_POST['tagline'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'concept';
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Validation
    if (empty($name)) {
        throw new Exception('Project name is required');
    }
    
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    }
    
    // Check slug uniqueness
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM projects WHERE slug = ?");
    $stmt->execute([$slug]);
    if ($stmt->fetch()['count'] > 0) {
        $slug .= '-' . time();
    }
    
    // Insert project
    $stmt = $db->prepare("
        INSERT INTO projects (name, slug, tagline, description, status, featured, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    
    $stmt->execute([$name, $slug, $tagline, $description, $status, $featured]);
    $project_id = $db->lastInsertId();
    
    // Handle tags (check if project_tags table exists)
    $tags = $_POST['tags'] ?? [];
    if (!empty($tags)) {
        // Check if project_tags table exists
        $tableCheck = $db->query("SHOW TABLES LIKE 'project_tags'")->fetch();
        if ($tableCheck) {
            foreach ($tags as $tag_id) {
                $stmt = $db->prepare("
                    INSERT INTO project_tags (project_id, tag_id)
                    VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE project_id = project_id
                ");
                $stmt->execute([$project_id, $tag_id]);
            }
        }
    }
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Project created successfully',
        'project_id' => $project_id,
        'slug' => $slug
    ]);

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    error_log("Create project error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

