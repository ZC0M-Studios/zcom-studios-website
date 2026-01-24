<?php
/* ========================================================
    //ANCHOR [API_GET_ARTICLES]
    FUNCTION: API endpoint to fetch articles from database
-----------------------------------------------------------
    Parameters: ?featured=1 (optional), ?limit=N (optional)
    Returns: JSON array of articles
    Description: Returns articles from database in JSON format for AJAX requests
    UniqueID: 789300
=========================================================== */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../includes/db_config.php';

try {
    // Get query parameters
    $featured_only = isset($_GET['featured']) && $_GET['featured'] == '1';
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;

    // Build query - matches actual schema columns
    $sql = "
        SELECT
            a.id,
            a.article_id,
            a.title,
            a.slug,
            a.author_name,
            a.author_role,
            a.published_date,
            a.category,
            a.excerpt,
            a.og_image_url as featured_image,
            a.word_count,
            a.reading_time,
            a.views,
            a.featured,
            GROUP_CONCAT(DISTINCT tr.display_name ORDER BY tr.display_name SEPARATOR ', ') as tags
        FROM articles a
        LEFT JOIN article_tags at ON a.id = at.article_id
        LEFT JOIN tags_registry tr ON at.tag_id = tr.id
        WHERE a.status = 'published' AND a.visibility = 'public'
    ";

    if ($featured_only) {
        $sql .= " AND a.featured = 1";
    }

    $sql .= " GROUP BY a.id ORDER BY a.published_date DESC LIMIT :limit";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    $articles = $stmt->fetchAll();

    // Format dates and tags
    foreach ($articles as &$article) {
        $article['published_date_formatted'] = $article['published_date'] ? date('F j, Y', strtotime($article['published_date'])) : null;
        $article['tags_array'] = $article['tags'] ? explode(', ', $article['tags']) : [];
        $article['article_url'] = "/articles/article.php?slug=" . urlencode($article['slug']);
        $article['likes'] = 0; // Placeholder - column doesn't exist in schema
    }
    
    echo json_encode([
        'success' => true,
        'count' => count($articles),
        'articles' => $articles
    ]);
    
} catch (PDOException $e) {
    error_log("API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch articles'
    ]);
}
?>
