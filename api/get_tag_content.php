<?php
/* ========================================================
    //ANCHOR [API_GET_TAG_CONTENT]
    FUNCTION: Get Tag Content API
-----------------------------------------------------------
    Parameters: ?tag=tag-name (URL parameter)
    Returns: JSON
    Description: Returns all content (blog, projects, prompts) with specified tag
    UniqueID: 900080
=========================================================== */

header('Content-Type: application/json');
require_once '../includes/db_config.php';

try {
    $tag = isset($_GET['tag']) ? $_GET['tag'] : '';
    
    if (empty($tag)) {
        echo json_encode(['success' => false, 'error' => 'Tag parameter required']);
        exit;
    }

    $content = [];

    // Get blog posts with this tag
    $blog_stmt = $db->prepare("
        SELECT 
            'blog' as type,
            p.id,
            p.title,
            p.slug,
            p.excerpt,
            p.thumbnail,
            p.category,
            DATE_FORMAT(p.published_date, '%b %d, %Y') as date
        FROM posts p
        INNER JOIN post_tags pt ON p.id = pt.post_id
        WHERE pt.tag = :tag AND p.status = 'published'
        ORDER BY p.published_date DESC
    ");
    $blog_stmt->execute(['tag' => $tag]);
    $blog_posts = $blog_stmt->fetchAll();
    
    foreach ($blog_posts as $post) {
        $content[] = $post;
    }

    // Get projects with this tag
    $project_stmt = $db->prepare("
        SELECT 
            'project' as type,
            p.id,
            p.name as title,
            p.slug,
            p.tagline as description,
            p.thumbnail,
            p.type as project_type,
            COALESCE(p.date_display, DATE_FORMAT(p.date_completed, '%b %d, %Y'), 'In Progress') as date
        FROM projects p
        INNER JOIN project_tags pt ON p.id = pt.project_id
        WHERE pt.tag = :tag AND p.visibility = 'public'
        ORDER BY p.date_completed DESC
    ");
    $project_stmt->execute(['tag' => $tag]);
    $projects = $project_stmt->fetchAll();
    
    foreach ($projects as $project) {
        $content[] = $project;
    }

    // Get prompts with this tag
    $prompt_stmt = $db->prepare("
        SELECT 
            'prompt' as type,
            p.id,
            p.title,
            p.slug,
            p.description,
            NULL as thumbnail,
            p.category,
            DATE_FORMAT(p.date_created, '%b %d, %Y') as date
        FROM prompts p
        INNER JOIN prompt_tags pt ON p.id = pt.prompt_id
        WHERE pt.tag = :tag AND p.visibility = 'public'
        ORDER BY p.date_created DESC
    ");
    $prompt_stmt->execute(['tag' => $tag]);
    $prompts = $prompt_stmt->fetchAll();
    
    foreach ($prompts as $prompt) {
        $content[] = $prompt;
    }

    // Sort all content by date (most recent first)
    usort($content, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });

    echo json_encode([
        'success' => true,
        'tag' => $tag,
        'count' => count($content),
        'content' => $content
    ]);

} catch (PDOException $e) {
    error_log("Tag Content API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
}
?>

