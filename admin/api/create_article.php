<?php
/* ========================================================
    API - Create Article
=========================================================== */

require_once __DIR__ . '/../../includes/db_config.php';
require_once __DIR__ . '/../includes/auth_check.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Verify CSRF token
if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

try {
    $db->beginTransaction();
    
    // Extract and validate data
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? '';
    $category = trim($_POST['category'] ?? '');
    
    // Author info
    $author_name = trim($_POST['author_name'] ?? '');
    $author_bio = trim($_POST['author_bio'] ?? '');
    $author_avatar_url = trim($_POST['author_avatar_url'] ?? '');
    $author_role = trim($_POST['author_role'] ?? '');
    
    // Metadata
    $meta_title = trim($_POST['meta_title'] ?? '');
    $meta_description = trim($_POST['meta_description'] ?? '');
    $og_title = trim($_POST['og_title'] ?? '');
    $og_description = trim($_POST['og_description'] ?? '');
    $og_image_url = trim($_POST['og_image_url'] ?? '');
    
    // Publishing
    $status = $_POST['status'] ?? 'draft';
    $visibility = $_POST['visibility'] ?? 'public';
    $published_date = $_POST['published_date'] ?? null;
    $featured = isset($_POST['featured']) ? 1 : 0;
    $sticky = isset($_POST['sticky']) ? 1 : 0;
    $allow_comments = isset($_POST['allow_comments']) ? 1 : 0;
    
    // Validation
    if (empty($title)) {
        throw new Exception('Title is required');
    }
    
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    }
    
    // Check if slug already exists
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM articles WHERE slug = ?");
    $stmt->execute([$slug]);
    if ($stmt->fetch()['count'] > 0) {
        $slug .= '-' . time();
    }
    
    // Calculate word count and reading time
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Average reading speed: 200 words/min
    
    // Insert article
    $stmt = $db->prepare("
        INSERT INTO articles (
            title, slug, excerpt, content, category,
            author_name, author_bio, author_avatar_url, author_role,
            meta_title, meta_description, og_title, og_description, og_image_url,
            status, visibility, published_date, featured, sticky, allow_comments,
            word_count, reading_time, views, created_at, updated_at
        ) VALUES (
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?,
            ?, ?, 0, NOW(), NOW()
        )
    ");
    
    $stmt->execute([
        $title, $slug, $excerpt, $content, $category,
        $author_name, $author_bio, $author_avatar_url, $author_role,
        $meta_title, $meta_description, $og_title, $og_description, $og_image_url,
        $status, $visibility, $published_date, $featured, $sticky, $allow_comments,
        $word_count, $reading_time
    ]);
    
    $article_id = $db->lastInsertId();
    
    // Handle tags
    $tags = $_POST['tags'] ?? [];
    if (!empty($tags)) {
        foreach ($tags as $tag_id) {
            $stmt = $db->prepare("
                INSERT INTO article_tags (article_id, tag_id)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE article_id = article_id
            ");
            $stmt->execute([$article_id, $tag_id]);
        }
    }
    
    // Handle new tag
    $new_tag = trim($_POST['new_tag'] ?? '');
    if (!empty($new_tag)) {
        $tag_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $new_tag), '-'));
        
        $stmt = $db->prepare("
            INSERT INTO tags_registry (display_name, slug, category)
            VALUES (?, ?, 'custom')
            ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)
        ");
        $stmt->execute([$new_tag, $tag_slug]);
        $new_tag_id = $db->lastInsertId();
        
        $stmt = $db->prepare("
            INSERT INTO article_tags (article_id, tag_id)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE article_id = article_id
        ");
        $stmt->execute([$article_id, $new_tag_id]);
    }
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Article created successfully',
        'article_id' => $article_id,
        'slug' => $slug
    ]);
    
} catch (Exception $e) {
    $db->rollBack();
    error_log("Create article error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
