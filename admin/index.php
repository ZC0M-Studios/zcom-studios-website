<?php
/* ========================================================
    ADMIN DASHBOARD - Overview Page
=========================================================== */

require_once __DIR__ . '/includes/auth_check.php';

$page_title = 'Dashboard';
$current_page = 'dashboard';

// Fetch statistics
try {
    // Total counts
    $stmt = $db->query("SELECT COUNT(*) as count FROM articles");
    $total_articles = $stmt->fetch()['count'];
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM projects");
    $total_projects = $stmt->fetch()['count'];
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM prompts");
    $total_prompts = $stmt->fetch()['count'];
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM tools");
    $total_tools = $stmt->fetch()['count'];
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM tags_registry");
    $total_tags = $stmt->fetch()['count'];
    
    // Draft counts
    $stmt = $db->query("SELECT COUNT(*) as count FROM articles WHERE status = 'draft'");
    $draft_articles = $stmt->fetch()['count'];
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM projects WHERE status IN ('concept', 'in_development')");
    $draft_projects = $stmt->fetch()['count'];
    
    // Recent activity
    $recent_articles = $db->query("
        SELECT 'article' as type, title as name, created_at, slug 
        FROM articles 
        ORDER BY created_at DESC 
        LIMIT 5
    ")->fetchAll();
    
    $recent_projects = $db->query("
        SELECT 'project' as type, name, created_at, slug 
        FROM projects 
        ORDER BY created_at DESC 
        LIMIT 5
    ")->fetchAll();
    
    $recent_prompts = $db->query("
        SELECT 'prompt' as type, title as name, created_at, slug 
        FROM prompts 
        ORDER BY created_at DESC 
        LIMIT 5
    ")->fetchAll();
    
    // Combine and sort recent activity
    $recent_activity = array_merge($recent_articles, $recent_projects, $recent_prompts);
    usort($recent_activity, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    $recent_activity = array_slice($recent_activity, 0, 10);
    
    // Top performing content
    $top_articles = $db->query("
        SELECT title, slug, views 
        FROM articles 
        WHERE status = 'published' 
        ORDER BY views DESC 
        LIMIT 5
    ")->fetchAll();
    
    $top_prompts = $db->query("
        SELECT title, slug, copies 
        FROM prompts 
        WHERE visibility = 'public' 
        ORDER BY copies DESC 
        LIMIT 5
    ")->fetchAll();
    
} catch (PDOException $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    $total_articles = $total_projects = $total_prompts = $total_tools = $total_tags = 0;
    $draft_articles = $draft_projects = 0;
    $recent_activity = $top_articles = $top_prompts = [];
}

include __DIR__ . '/includes/admin_header.php';
include __DIR__ . '/includes/admin_sidebar.php';
?>

<main class="admin-main">
    <div class="page-header">
        <h2>SYSTEM OVERVIEW</h2>
        <p>OPERATOR: <?php echo strtoupper(htmlspecialchars($_SESSION['admin_username'])); ?> // SESSION ACTIVE</p>
    </div>

    <!-- BATCOMPUTER Statistics Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <div class="stat-info">
                <h3>ARTICLES</h3>
                <p><?php echo $total_articles; ?></p>
                <?php if ($draft_articles > 0): ?>
                    <small class="text-muted"><?php echo $draft_articles; ?> DRAFTS</small>
                <?php endif; ?>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-folder"></i>
            </div>
            <div class="stat-info">
                <h3>PROJECTS</h3>
                <p><?php echo $total_projects; ?></p>
                <?php if ($draft_projects > 0): ?>
                    <small class="text-muted"><?php echo $draft_projects; ?> IN DEV</small>
                <?php endif; ?>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-chat-square-quote"></i>
            </div>
            <div class="stat-info">
                <h3>PROMPTS</h3>
                <p><?php echo $total_prompts; ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-tools"></i>
            </div>
            <div class="stat-info">
                <h3>TOOLS</h3>
                <p><?php echo $total_tools; ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-tags"></i>
            </div>
            <div class="stat-info">
                <h3>TAGS</h3>
                <p><?php echo $total_tags; ?></p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Recent Activity -->
        <div class="col-md-6">
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">ACTIVITY LOG</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_activity)): ?>
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <h3>NO ACTIVITY DETECTED</h3>
                            <p>CREATE CONTENT TO POPULATE LOG</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($recent_activity as $item): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center mb-1">
                                    <div>
                                        <span class="badge badge-info me-2"><?php echo strtoupper($item['type']); ?></span>
                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                    </div>
                                    <small class="text-muted">
                                        <?php
                                        $time_ago = time() - strtotime($item['created_at']);
                                        if ($time_ago < 3600) {
                                            echo floor($time_ago / 60) . 'M AGO';
                                        } elseif ($time_ago < 86400) {
                                            echo floor($time_ago / 3600) . 'H AGO';
                                        } else {
                                            echo floor($time_ago / 86400) . 'D AGO';
                                        }
                                        ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Top Performing Content -->
        <div class="col-md-6">
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">PERFORMANCE METRICS</h3>
                </div>
                <div class="card-body">
                    <h4 style="font-family: 'Orbitron', sans-serif; font-size: 10px; color: var(--bat-red); margin-bottom: 8px; letter-spacing: 0.1em;">TOP ARTICLES</h4>
                    <?php if (empty($top_articles)): ?>
                        <p class="text-muted">NO PUBLISHED ARTICLES</p>
                    <?php else: ?>
                        <ul class="list-unstyled">
                            <?php foreach ($top_articles as $article): ?>
                                <li class="mb-1" style="padding: 4px 8px; background: var(--bat-panel); border: 1px solid var(--bat-border);">
                                    <a href="/articles/article.php?slug=<?php echo urlencode($article['slug']); ?>" target="_blank" style="color: var(--bat-text-data); text-decoration: none; font-size: 11px;">
                                        <?php echo htmlspecialchars($article['title']); ?>
                                    </a>
                                    <span class="badge badge-success ms-2"><?php echo $article['views']; ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <h4 style="font-family: 'Orbitron', sans-serif; font-size: 10px; color: var(--bat-red); margin: 12px 0 8px; letter-spacing: 0.1em;">TOP PROMPTS</h4>
                    <?php if (empty($top_prompts)): ?>
                        <p class="text-muted">NO PUBLIC PROMPTS</p>
                    <?php else: ?>
                        <ul class="list-unstyled">
                            <?php foreach ($top_prompts as $prompt): ?>
                                <li class="mb-1" style="padding: 4px 8px; background: var(--bat-panel); border: 1px solid var(--bat-border);">
                                    <a href="/prompts/prompt.php?slug=<?php echo urlencode($prompt['slug']); ?>" target="_blank" style="color: var(--bat-text-data); text-decoration: none; font-size: 11px;">
                                        <?php echo htmlspecialchars($prompt['title']); ?>
                                    </a>
                                    <span class="badge badge-info ms-2"><?php echo $prompt['copies']; ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="admin-card">
        <div class="card-header">
            <h3 class="card-title">QUICK ACTIONS</h3>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2 flex-wrap">
                <a href="/admin/articles/create.php" class="btn-scifi">
                    <i class="bi bi-plus-circle"></i> NEW ARTICLE
                </a>
                <a href="/admin/projects/create.php" class="btn-scifi">
                    <i class="bi bi-plus-circle"></i> NEW PROJECT
                </a>
                <a href="/admin/prompts/create.php" class="btn-scifi">
                    <i class="bi bi-plus-circle"></i> NEW PROMPT
                </a>
                <a href="/admin/tools/create.php" class="btn-scifi">
                    <i class="bi bi-plus-circle"></i> NEW TOOL
                </a>
                <a href="/admin/tags/create.php" class="btn-scifi btn-outline">
                    <i class="bi bi-plus-circle"></i> NEW TAG
                </a>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
