<?php
/* ========================================================
    //ANCHOR [API_GET_TAGS]
    FUNCTION: API endpoint to fetch content by tag
-----------------------------------------------------------
    Parameters: ?tag=X (required), ?type=articles|projects|prompts|tools (optional)
    Returns: JSON with all content tagged with specified tag
    Description: Returns aggregated content for tag pages
    UniqueID: 900004
=========================================================== */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../includes/db_config.php';

try {
    $tag = isset($_GET['tag']) ? $_GET['tag'] : null;
    $type = isset($_GET['type']) ? $_GET['type'] : 'all';
    
    if (!$tag) {
        throw new Exception('Tag parameter is required');
    }
    
    $result = [
        'success' => true,
        'tag' => $tag,
        'tag_info' => null,
        'articles' => [],
        'projects' => [],
        'prompts' => [],
        'tools' => []
    ];
    
    // Get tag info from registry
    $stmt = $db->prepare("SELECT * FROM tag_registry WHERE tag_id = :tag");
    $stmt->execute(['tag' => $tag]);
    $result['tag_info'] = $stmt->fetch();
    
    // Get articles with this tag
    if ($type === 'all' || $type === 'articles') {
        $stmt = $db->prepare("
            SELECT a.id, a.title, a.slug, a.excerpt, a.published_date, a.category, a.reading_time
            FROM articles a
            INNER JOIN article_tags at ON a.id = at.article_id
            WHERE at.tag = :tag AND a.status = 'published' AND a.visibility = 'public'
            ORDER BY a.published_date DESC
        ");
        $stmt->execute(['tag' => $tag]);
        $articles = $stmt->fetchAll();
        foreach ($articles as &$article) {
            $article['url'] = "/articles/article.php?slug=" . urlencode($article['slug']);
            $article['published_date_formatted'] = date('F j, Y', strtotime($article['published_date']));
        }
        $result['articles'] = $articles;
    }
    
    // Get projects with this tag
    if ($type === 'all' || $type === 'projects') {
        $stmt = $db->prepare("
            SELECT p.id, p.name, p.slug, p.tagline, p.description, p.thumbnail, p.type, p.status
            FROM projects p
            INNER JOIN project_tags pt ON p.id = pt.project_id
            WHERE pt.tag = :tag AND p.visibility = 'public'
            ORDER BY p.display_order ASC, p.date_completed DESC
        ");
        $stmt->execute(['tag' => $tag]);
        $projects = $stmt->fetchAll();
        foreach ($projects as &$project) {
            $project['url'] = "/portfolio/project.php?slug=" . urlencode($project['slug']);
        }
        $result['projects'] = $projects;
    }
    
    // Get prompts with this tag
    if ($type === 'all' || $type === 'prompts') {
        $stmt = $db->prepare("
            SELECT p.id, p.title, p.slug, p.description, p.ai_model, p.category, p.difficulty, p.success_rating
            FROM prompts p
            INNER JOIN prompt_tags pt ON p.id = pt.prompt_id
            WHERE pt.tag = :tag AND p.visibility = 'public'
            ORDER BY p.created_date DESC
        ");
        $stmt->execute(['tag' => $tag]);
        $prompts = $stmt->fetchAll();
        foreach ($prompts as &$prompt) {
            $prompt['url'] = "/prompts/prompt.php?slug=" . urlencode($prompt['slug']);
        }
        $result['prompts'] = $prompts;
    }
    
    // Get tools with this tag
    if ($type === 'all' || $type === 'tools') {
        $stmt = $db->prepare("
            SELECT t.id, t.name, t.slug, t.description, t.category, t.icon
            FROM tools t
            INNER JOIN tool_tags tt ON t.id = tt.tool_id
            WHERE tt.tag = :tag AND t.visibility = 'public'
            ORDER BY t.display_order ASC, t.name ASC
        ");
        $stmt->execute(['tag' => $tag]);
        $tools = $stmt->fetchAll();
        foreach ($tools as &$tool) {
            $tool['url'] = "/tools/" . urlencode($tool['slug']) . ".php";
        }
        $result['tools'] = $tools;
    }
    
    $result['total_count'] = count($result['articles']) + count($result['projects']) + count($result['prompts']) + count($result['tools']);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

