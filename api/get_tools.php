<?php
/* ========================================================
    //ANCHOR [API_GET_TOOLS]
    FUNCTION: API endpoint to fetch tools from database
-----------------------------------------------------------
    Parameters: ?featured=1 (optional), ?category=X (optional), ?limit=N (optional)
    Returns: JSON array of tools
    Description: Returns tools from database in JSON format for AJAX requests
    UniqueID: 900003
=========================================================== */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../includes/db_config.php';

try {
    // Get query parameters
    $featured_only = isset($_GET['featured']) && $_GET['featured'] == '1';
    $category = isset($_GET['category']) ? $_GET['category'] : null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
    
    // Build query
    $sql = "
        SELECT 
            t.id,
            t.tool_id,
            t.name,
            t.slug,
            t.description,
            t.long_description,
            t.category,
            t.icon,
            t.page_url,
            t.instructions,
            t.benefits,
            t.use_cases,
            t.related_blog_post,
            t.views,
            t.uses,
            t.featured,
            t.display_order,
            GROUP_CONCAT(DISTINCT tt.tag ORDER BY tt.tag SEPARATOR ', ') as tags
        FROM tools t
        LEFT JOIN tool_tags tt ON t.id = tt.tool_id
        WHERE t.visibility = 'public'
    ";
    
    if ($featured_only) {
        $sql .= " AND t.featured = 1";
    }
    
    if ($category) {
        $sql .= " AND t.category = :category";
    }
    
    $sql .= " GROUP BY t.id ORDER BY t.display_order ASC, t.name ASC LIMIT :limit";
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    
    if ($category) {
        $stmt->bindValue(':category', $category, PDO::PARAM_STR);
    }
    
    $stmt->execute();
    
    $tools = $stmt->fetchAll();
    
    // Format data
    foreach ($tools as &$tool) {
        $tool['tags_array'] = $tool['tags'] ? explode(', ', $tool['tags']) : [];
        $tool['tool_url'] = $tool['page_url'] ?: "/tools/" . urlencode($tool['slug']) . ".php";
    }
    
    echo json_encode([
        'success' => true,
        'count' => count($tools),
        'tools' => $tools
    ]);
    
} catch (PDOException $e) {
    error_log("API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch tools'
    ]);
}
?>

