<?php
/* ========================================================
    //ANCHOR [PROJECT_DETAIL_PAGE]
    FUNCTION: Individual Project Detail Page
-----------------------------------------------------------
    Parameters: ?slug=project-slug (URL parameter)
    Returns: HTML
    Description: Displays full project details with related content
    UniqueID: 900020
=========================================================== */

require_once '../includes/db_config.php';

// Get project slug from URL
$project_slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($project_slug)) {
    header('Location: /portfolio.php');
    exit;
}

// Query project from database
try {
    $stmt = $db->prepare("
        SELECT
            p.*,
            GROUP_CONCAT(DISTINCT tr.display_name ORDER BY tr.display_name SEPARATOR ', ') as tags,
            GROUP_CONCAT(DISTINCT CONCAT(pts.category, ':', pts.technology) ORDER BY pts.category, pts.technology SEPARATOR '|||') as tech_stack,
            GROUP_CONCAT(DISTINCT pf.feature ORDER BY pf.feature_order SEPARATOR '|||') as features
        FROM projects p
        LEFT JOIN project_tags pt ON p.id = pt.project_id
        LEFT JOIN tags_registry tr ON pt.tag_id = tr.id
        LEFT JOIN project_tech_stack pts ON p.id = pts.project_id
        LEFT JOIN project_features pf ON p.id = pf.project_id
        WHERE p.slug = :slug
        GROUP BY p.id
    ");

    $stmt->execute(['slug' => $project_slug]);
    $project = $stmt->fetch();

    if (!$project) {
        header('Location: /portfolio.php');
        exit;
    }

    // Parse tags
    $tags_array = $project['tags'] ? explode(', ', $project['tags']) : [];

    // Parse tech stack
    $tech_stack_by_category = [];
    if ($project['tech_stack']) {
        $techPairs = explode('|||', $project['tech_stack']);
        foreach ($techPairs as $pair) {
            $parts = explode(':', $pair);
            if (count($parts) >= 2) {
                $cat = $parts[0];
                $tech = $parts[1];
                if (!isset($tech_stack_by_category[$cat])) {
                    $tech_stack_by_category[$cat] = [];
                }
                $tech_stack_by_category[$cat][] = $tech;
            }
        }
    }

    // Parse features
    $features_array = $project['features'] ? explode('|||', $project['features']) : [];

    // SEO Meta Variables
    $page_title = $project['name'];
    $page_description = $project['tagline'] ?? $project['description'] ?? 'Project by ZCOM Studios';
    $page_keywords = $project['tags'] ?? '';
    $page_type = 'website';
    $canonical_url = 'https://zcomstudios.com/portfolio/project.php?slug=' . urlencode($project['slug']);

    // JSON-LD for Project (CreativeWork)
    $json_ld = [
        "@context" => "https://schema.org",
        "@type" => "CreativeWork",
        "name" => $project['name'],
        "description" => $page_description,
        "author" => [
            "@type" => "Organization",
            "name" => "ZCOM Studios"
        ],
        "dateCreated" => $project['created_at'] ?? null,
        "url" => $canonical_url
    ];

} catch (PDOException $e) {
    error_log("Project Query Error: " . $e->getMessage());
    header('Location: /portfolio.php');
    exit;
}

include '../includes/header.php';
?>
<link rel="stylesheet" href="../css/style-blog.css">
<style>
.project-hero {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 2rem;
}
.project-links {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin: 2rem 0;
}
.tech-stack-section {
    margin: 2rem 0;
}
.tech-category {
    margin-bottom: 1rem;
}
.tech-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    margin: 0.25rem;
    background: rgba(100, 181, 246, 0.1);
    border: 1px solid #64b5f6;
    border-radius: 4px;
    font-size: 0.875rem;
    color: #64b5f6;
}
.feature-list {
    list-style: none;
    padding: 0;
}
.feature-list li {
    padding: 0.5rem 0;
    padding-left: 1.5rem;
    position: relative;
}
.feature-list li:before {
    content: "▹";
    position: absolute;
    left: 0;
    color: #64b5f6;
    font-size: 1.2rem;
}
</style>
</head>
<body>
<?php
include '../includes/navbar.php';
?>

<main class="container my-5" style="z-index: 3; pointer-events: auto;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="/portfolio.php">Portfolio</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($project['name']); ?></li>
        </ol>
    </nav>

    <!-- Project Header -->
    <article class="project-detail">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <span class="article-category font-nav">PROJECT</span>
                <?php if ($project['featured']): ?>
                    <span class="article-featured-badge font-mono">FEATURED</span>
                <?php endif; ?>
                <?php if ($project['status'] === 'live'): ?>
                    <span class="article-featured-badge font-mono" style="background: #4caf50;">LIVE</span>
                <?php endif; ?>
            </div>
            <span class="text-muted font-mono"><?php echo $project['created_at'] ? date('M Y', strtotime($project['created_at'])) : ''; ?></span>
        </div>

        <h1 class="page-title mb-3"><?php echo htmlspecialchars($project['name']); ?></h1>

        <?php if (!empty($project['tagline'])): ?>
            <p class="lead text-muted"><?php echo htmlspecialchars($project['tagline']); ?></p>
        <?php endif; ?>

        <!-- Description -->
        <section class="my-4">
            <h2 class="section-title mb-3">OVERVIEW</h2>
            <div class="article-content">
                <?php echo nl2br(htmlspecialchars($project['description'] ?? '')); ?>
            </div>
        </section>

        <!-- Tech Stack -->
        <?php if (!empty($tech_stack_by_category)): ?>
        <section class="tech-stack-section">
            <h2 class="section-title mb-3">TECH STACK</h2>
            <?php foreach ($tech_stack_by_category as $category => $technologies): ?>
                <div class="tech-category">
                    <h3 class="font-nav text-metallic" style="font-size: 1rem; margin-bottom: 0.5rem;">
                        <?php echo strtoupper($category); ?>
                    </h3>
                    <?php foreach ($technologies as $tech): ?>
                        <span class="tech-badge"><?php echo htmlspecialchars($tech); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </section>
        <?php endif; ?>

        <!-- Features -->
        <?php if (!empty($features_array)): ?>
        <section class="my-4">
            <h2 class="section-title mb-3">KEY FEATURES</h2>
            <ul class="feature-list">
                <?php foreach ($features_array as $feature): ?>
                    <li><?php echo htmlspecialchars($feature); ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php endif; ?>

        <!-- Tags -->
        <?php if (!empty($tags_array)): ?>
        <section class="my-4">
            <h2 class="section-title mb-3">TAGS</h2>
            <div class="article-tags">
                <?php foreach ($tags_array as $tag): ?>
                    <a href="/tags/tag.php?tag=<?php echo urlencode($tag); ?>" class="article-tag font-mono">
                        <?php echo htmlspecialchars($tag); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

    </article>
</main>

<?php
include '../includes/footer.php';
?>


