<?php
/* ========================================================
    //ANCHOR [DYNAMIC_ARTICLE_PAGE]
    FUNCTION: Dynamic article display from database
-----------------------------------------------------------
    Parameters: ?slug=article-slug (URL parameter)
    Returns: N/A
    Description: Displays a single article queried from the database by slug
    UniqueID: 789200
=========================================================== */

// Get article slug from URL parameter
$article_slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($article_slug)) {
    header('Location: /blog.php');
    exit;
}

// Include database connection
require_once '../includes/db_config.php';

// Query article from database
try {
    // Simplified query - just get the article first
    $stmt = $db->prepare("SELECT * FROM articles WHERE slug = :slug");
    $stmt->execute(['slug' => $article_slug]);
    $article = $stmt->fetch();

    if (!$article) {
        header('Location: /blog.php');
        exit;
    }

    // Get tags separately
    $tagStmt = $db->prepare("
        SELECT tr.display_name
        FROM article_tags at
        JOIN tags_registry tr ON at.tag_id = tr.id
        WHERE at.article_id = :article_id
        ORDER BY tr.display_name
    ");
    $tagStmt->execute(['article_id' => $article['id']]);
    $tagRows = $tagStmt->fetchAll(PDO::FETCH_COLUMN);
    $article['tags'] = implode(', ', $tagRows);

    // Increment view count
    $update_stmt = $db->prepare("UPDATE articles SET views = views + 1 WHERE id = :id");
    $update_stmt->execute(['id' => $article['id']]);

    // SEO Meta Variables
    $page_title = $article['title'];
    $page_description = $article['excerpt'] ?? $article['meta_description'] ?? substr(strip_tags($article['content']), 0, 160);
    $page_keywords = $article['tags'] ?? '';
    $page_type = 'article';
    $page_image = $article['og_image_url'] ?? null;
    $page_author = $article['author_name'] ?? 'ZCOM Studios';
    $publish_date = $article['published_date'] ?? null;
    $modified_date = $article['updated_at'] ?? null;
    $canonical_url = 'https://zcomstudios.com/articles/article.php?slug=' . urlencode($article['slug']);

    // JSON-LD for Article
    $json_ld = [
        "@context" => "https://schema.org",
        "@type" => "Article",
        "headline" => $article['title'],
        "description" => $page_description,
        "author" => [
            "@type" => "Person",
            "name" => $page_author
        ],
        "publisher" => [
            "@type" => "Organization",
            "name" => "ZCOM Studios",
            "logo" => [
                "@type" => "ImageObject",
                "url" => "https://zcomstudios.com/img/logo/logo_zcom-studios_1.png"
            ]
        ],
        "datePublished" => $publish_date,
        "dateModified" => $modified_date,
        "mainEntityOfPage" => $canonical_url
    ];
    if ($page_image) {
        $json_ld["image"] = $page_image;
    }

} catch (PDOException $e) {
    error_log("Article Query Error: " . $e->getMessage());
    header('Location: /blog.php');
    exit;
}

include '../includes/header.php';
?>
</head>
<body class="cyber-theme">
<?php
include '../includes/navbar.php';
?>

<main class="container my-5" style="z-index: 3; pointer-events: auto;">
    
    <!-- Article Header Panel -->
    <div class="cyber-panel mb-4">
        <div class="cyber-panel-header">
            <span class="panel-title">ARTICLE.DAT</span>
            <span class="panel-id"><?php echo strtoupper($article['category'] ?? 'BLOG'); ?></span>
        </div>
        <div class="cyber-panel-body">
            <h1 class="page-title mb-3"><?php echo htmlspecialchars($article['title']); ?></h1>
            <p class="lead" style="color: #7090a8;"><?php echo htmlspecialchars($article['excerpt']); ?></p>
            
            <!-- Article Meta -->
            <div style="display: flex; flex-wrap: wrap; gap: 16px; margin-top: 16px; padding-top: 16px; border-top: 1px solid #2a4050;">
                <div class="cyber-data-status">
                    <span class="status-label">AUTHOR</span>
                    <span style="color: #4a9ead;"><?php echo htmlspecialchars($article['author_name']); ?></span>
                </div>
                <div class="cyber-data-status">
                    <span class="status-label">DATE</span>
                    <span style="color: #c8d8e8;"><?php echo date('Y.m.d', strtotime($article['published_date'])); ?></span>
                </div>
                <div class="cyber-data-status">
                    <span class="status-label">READ TIME</span>
                    <span style="color: #c8d8e8;"><?php echo $article['reading_time']; ?> MIN</span>
                </div>
                <?php if ($article['category']): ?>
                <div class="cyber-data-status">
                    <span class="status-label">CATEGORY</span>
                    <span class="cyber-badge cyan"><?php echo htmlspecialchars(strtoupper($article['category'])); ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if ($article['tags']): ?>
            <div style="margin-top: 12px; display: flex; flex-wrap: wrap; gap: 8px;">
                <?php 
                $tags = explode(', ', $article['tags']);
                foreach ($tags as $tag): 
                ?>
                <span class="cyber-badge"><?php echo htmlspecialchars(strtoupper($tag)); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Article Content Panel -->
    <div class="cyber-panel">
        <div class="cyber-panel-header">
            <span class="panel-title">CONTENT.STREAM</span>
            <span class="panel-status">TX.ACTIVE</span>
        </div>
        <div class="cyber-panel-body">
            <article style="max-width: 100%;">
                <?php if (!empty($article['og_image_url'])): ?>
                <div class="featured-image mb-4" style="border: 1px solid #2a4050; padding: 4px;">
                    <img src="<?php echo htmlspecialchars($article['og_image_url']); ?>"
                         alt="<?php echo htmlspecialchars($article['title']); ?>"
                         class="img-fluid" style="width: 100%;">
                </div>
                <?php endif; ?>

                <div class="article-content" style="color: #c8d8e8; line-height: 1.8;">
                    <?php echo $article['content']; ?>
                </div>
            </article>
        </div>
    </div>

    <!-- Article Footer Panel -->
    <div class="cyber-panel mt-4">
        <div class="cyber-panel-header">
            <span class="panel-title">META.INFO</span>
        </div>
        <div class="cyber-panel-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <div class="cyber-data-row">
                        <span class="data-key">PUBLISHED</span>
                        <span class="data-value"><?php echo $article['published_date'] ? date('Y.m.d H:i', strtotime($article['published_date'])) : 'N/A'; ?></span>
                    </div>
                    <?php if (!empty($article['updated_at']) && $article['updated_at'] != $article['published_date']): ?>
                    <div class="cyber-data-row">
                        <span class="data-key">UPDATED</span>
                        <span class="data-value"><?php echo date('Y.m.d H:i', strtotime($article['updated_at'])); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="cyber-data-row">
                        <span class="data-key">VIEWS</span>
                        <span class="data-value" style="color: #4a9ead;"><?php echo number_format($article['views'] ?? 0); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4 pt-3" style="border-top: 1px solid #2a4050;">
                <a href="/blog.php" class="btn-scifi btn-scifi-primary">← RETURN TO BLOG.INDEX</a>
            </div>
        </div>
    </div>
    
</main>

<?php
include '../includes/footer.php';
?>
