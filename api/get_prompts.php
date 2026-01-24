<?php
/* ========================================================
    //ANCHOR [API_GET_PROMPTS]
    FUNCTION: API endpoint to fetch prompts from database
-----------------------------------------------------------
    Parameters: ?featured=1 (optional), ?category=X (optional), ?limit=N (optional)
    Returns: JSON array of prompts
    Description: Returns prompts from database in JSON format for AJAX requests
    UniqueID: 900001
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
            p.id,
            p.title,
            p.slug,
            p.description,
            p.prompt_text,
            p.created_at,
            p.updated_at,
            p.copies,
            p.featured,
            p.visibility,
            GROUP_CONCAT(DISTINCT CONCAT(tr.display_name, ':', tr.category) ORDER BY tr.display_name SEPARATOR '|||') as tags
        FROM prompts p
        LEFT JOIN prompt_tags pt ON p.id = pt.prompt_id
        LEFT JOIN tags_registry tr ON pt.tag_id = tr.id
        WHERE p.visibility = 'public'
    ";

    if ($featured_only) {
        $sql .= " AND p.featured = 1";
    }

    $sql .= " GROUP BY p.id ORDER BY p.created_at DESC LIMIT :limit";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    $prompts = $stmt->fetchAll();

    // Format data
    foreach ($prompts as &$prompt) {
        $prompt['created_date_formatted'] = $prompt['created_at'] ? date('F j, Y', strtotime($prompt['created_at'])) : null;
        $prompt['last_updated_formatted'] = $prompt['updated_at'] ? date('F j, Y', strtotime($prompt['updated_at'])) : null;

        // Parse tags
        $prompt['tags_by_category'] = [];
        if ($prompt['tags']) {
            $tagPairs = explode('|||', $prompt['tags']);
            foreach ($tagPairs as $pair) {
                $parts = explode(':', $pair);
                if (count($parts) >= 2) {
                    $tag = $parts[0];
                    $cat = $parts[1];
                    if (!isset($prompt['tags_by_category'][$cat])) {
                        $prompt['tags_by_category'][$cat] = [];
                    }
                    $prompt['tags_by_category'][$cat][] = $tag;
                }
            }
        }

        $prompt['prompt_url'] = "/prompts/prompt.php?slug=" . urlencode($prompt['slug']);

        // Truncate prompt text for listing
        $promptText = $prompt['prompt_text'] ?? '';
        if (strlen($promptText) > 200) {
            $prompt['prompt_text_preview'] = substr($promptText, 0, 200) . '...';
        } else {
            $prompt['prompt_text_preview'] = $promptText;
        }

        // Placeholder values for missing columns
        $prompt['views'] = 0;
    }
    
    echo json_encode([
        'success' => true,
        'count' => count($prompts),
        'prompts' => $prompts
    ]);
    
} catch (PDOException $e) {
    error_log("API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch prompts'
    ]);
}
?>

