<?php
/* ========================================================
    //ANCHOR [API_GET_PROJECTS]
    FUNCTION: API endpoint to fetch projects from database
-----------------------------------------------------------
    Parameters: ?featured=1 (optional), ?type=X (optional), ?limit=N (optional)
    Returns: JSON array of projects
    Description: Returns projects from database in JSON format for AJAX requests
    UniqueID: 900002
=========================================================== */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../includes/db_config.php';

try {
    // Get query parameters
    $featured_only = isset($_GET['featured']) && $_GET['featured'] == '1';
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;

    // Build query - matches actual schema columns
    $sql = "
        SELECT
            p.id,
            p.name,
            p.slug,
            p.status,
            p.tagline,
            p.description,
            p.featured,
            p.created_at,
            p.updated_at,
            GROUP_CONCAT(DISTINCT tr.display_name ORDER BY tr.display_name SEPARATOR ', ') as tags,
            GROUP_CONCAT(DISTINCT CONCAT(pts.category, ':', pts.technology) ORDER BY pts.category, pts.technology SEPARATOR '|||') as tech_stack
        FROM projects p
        LEFT JOIN project_tags pt ON p.id = pt.project_id
        LEFT JOIN tags_registry tr ON pt.tag_id = tr.id
        LEFT JOIN project_tech_stack pts ON p.id = pts.project_id
    ";

    $where_clauses = [];

    if ($featured_only) {
        $where_clauses[] = "p.featured = 1";
    }

    if ($status) {
        $where_clauses[] = "p.status = :status";
    }

    if (count($where_clauses) > 0) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }

    $sql .= " GROUP BY p.id ORDER BY p.featured DESC, p.created_at DESC LIMIT :limit";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

    if ($status) {
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    }

    $stmt->execute();

    $projects = $stmt->fetchAll();

    // Format data
    foreach ($projects as &$project) {
        $project['created_at_formatted'] = $project['created_at'] ? date('F Y', strtotime($project['created_at'])) : null;

        $project['tags_array'] = $project['tags'] ? explode(', ', $project['tags']) : [];

        // Parse tech stack by category
        $project['tech_stack_by_category'] = [];
        if ($project['tech_stack']) {
            $techPairs = explode('|||', $project['tech_stack']);
            foreach ($techPairs as $pair) {
                $parts = explode(':', $pair);
                if (count($parts) >= 2) {
                    $cat = $parts[0];
                    $tech = $parts[1];
                    if (!isset($project['tech_stack_by_category'][$cat])) {
                        $project['tech_stack_by_category'][$cat] = [];
                    }
                    $project['tech_stack_by_category'][$cat][] = $tech;
                }
            }
        }

        $project['project_url'] = "/portfolio/project.php?slug=" . urlencode($project['slug']);
    }
    
    echo json_encode([
        'success' => true,
        'count' => count($projects),
        'projects' => $projects
    ]);
    
} catch (PDOException $e) {
    error_log("API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch projects'
    ]);
}
?>

