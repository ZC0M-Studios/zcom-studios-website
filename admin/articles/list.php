<?php
/* ========================================================
    //ANCHOR [ADMIN_ARTICLES_LIST]
    FUNCTION: Admin - Articles List Page
-----------------------------------------------------------
    Parameters: GET (page, search, category, status, visibility)
    Returns: HTML table output
    Description: Compact table view of all articles with filtering
    UniqueID: 793701
=========================================================== */

require_once __DIR__ . '/../includes/auth_check.php';

$page_title = 'ARTICLES';
$current_page = 'articles';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Filters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';
$visibility = $_GET['visibility'] ?? '';

// Build query
$where = [];
$params = [];

if ($search) {
    $where[] = "(title LIKE ? OR excerpt LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category) {
    $where[] = "category = ?";
    $params[] = $category;
}

if ($status) {
    $where[] = "status = ?";
    $params[] = $status;
}

if ($visibility) {
    $where[] = "visibility = ?";
    $params[] = $visibility;
}

$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

try {
    // Get total count
    $count_query = "SELECT COUNT(*) as total FROM articles $where_clause";
    $stmt = $db->prepare($count_query);
    $stmt->execute($params);
    $total_articles = $stmt->fetch()['total'];
    $total_pages = ceil($total_articles / $per_page);
    
    // Get articles
    $query = "
        SELECT 
            id, title, category, status, visibility, published_date, 
            views, featured, slug, created_at
        FROM articles 
        $where_clause
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ";
    $stmt = $db->prepare($query);
    $stmt->execute(array_merge($params, [$per_page, $offset]));
    $articles = $stmt->fetchAll();
    
    // Get unique categories for filter
    $categories = $db->query("SELECT DISTINCT category FROM articles WHERE category IS NOT NULL ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
    
} catch (PDOException $e) {
    error_log("Articles list error: " . $e->getMessage());
    $articles = [];
    $categories = [];
    $total_articles = 0;
    $total_pages = 0;
}

include __DIR__ . '/../includes/admin_header.php';
include __DIR__ . '/../includes/admin_sidebar.php';
?>

<main class="admin-main">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>ARTICLES // <?php echo $total_articles; ?> RECORDS</h2>
                <p>BLOG CONTENT MANAGEMENT</p>
            </div>
            <a href="/admin/articles/create.php" class="btn-scifi">
                <i class="bi bi-plus-circle"></i> NEW ARTICLE
            </a>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="admin-card">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search articles..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2">
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Published</option>
                    <option value="archived" <?php echo $status === 'archived' ? 'selected' : ''; ?>>Archived</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="visibility" class="form-control">
                    <option value="">All Visibility</option>
                    <option value="public" <?php echo $visibility === 'public' ? 'selected' : ''; ?>>Public</option>
                    <option value="private" <?php echo $visibility === 'private' ? 'selected' : ''; ?>>Private</option>
                    <option value="unlisted" <?php echo $visibility === 'unlisted' ? 'selected' : ''; ?>>Unlisted</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn-scifi btn-sm">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="/admin/articles/list.php" class="btn-outline btn-sm">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            </div>
        </form>
        
        <?php if ($search || $category || $status || $visibility): ?>
            <div class="mt-3">
                <small class="text-muted">
                    Showing <?php echo count($articles); ?> of <?php echo $total_articles; ?> articles
                </small>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Articles Table -->
    <div class="admin-card">
        <?php if (empty($articles)): ?>
            <div class="empty-state">
                <i class="bi bi-file-earmark-text"></i>
                <h3>No articles found</h3>
                <p>Start creating your first article or adjust your filters.</p>
                <a href="/admin/articles/create.php" class="btn-scifi mt-3">
                    <i class="bi bi-plus-circle"></i> Create Article
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table" id="articlesTable">
                    <thead>
                        <tr>
                            <th data-sortable data-column="title">Title</th>
                            <th data-sortable data-column="category">Category</th>
                            <th data-sortable data-column="status">Status</th>
                            <th data-sortable data-column="visibility">Visibility</th>
                            <th data-sortable data-column="published_date">Published</th>
                            <th data-sortable data-column="views">Views</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <td data-column="title">
                                    <strong><?php echo htmlspecialchars($article['title']); ?></strong>
                                </td>
                                <td data-column="category">
                                    <?php echo htmlspecialchars($article['category'] ?? 'N/A'); ?>
                                </td>
                                <td data-column="status">
                                    <?php
                                    $status_class = [
                                        'draft' => 'badge-warning',
                                        'published' => 'badge-success',
                                        'archived' => 'badge-danger'
                                    ];
                                    ?>
                                    <span class="badge <?php echo $status_class[$article['status']] ?? 'badge-info'; ?>">
                                        <?php echo ucfirst($article['status']); ?>
                                    </span>
                                </td>
                                <td data-column="visibility">
                                    <span class="badge badge-info">
                                        <?php echo ucfirst($article['visibility']); ?>
                                    </span>
                                </td>
                                <td data-column="published_date">
                                    <?php echo $article['published_date'] ? date('M d, Y', strtotime($article['published_date'])) : 'N/A'; ?>
                                </td>
                                <td data-column="views">
                                    <?php echo number_format($article['views']); ?>
                                </td>
                                <td>
                                    <button onclick="toggleFeatured('article', <?php echo $article['id']; ?>)" class="btn-icon" title="Toggle Featured">
                                        <i class="bi bi-star<?php echo $article['featured'] ? '-fill' : ''; ?>" style="color: <?php echo $article['featured'] ? '#ffd43b' : 'inherit'; ?>"></i>
                                    </button>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/articles/article.php?slug=<?php echo urlencode($article['slug']); ?>" target="_blank" class="btn-icon" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="/admin/articles/edit.php?id=<?php echo $article['id']; ?>" class="btn-icon" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button onclick="deleteItem('article', <?php echo $article['id']; ?>, '<?php echo htmlspecialchars(addslashes($article['title'])); ?>')" class="btn-icon" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&status=<?php echo urlencode($status); ?>&visibility=<?php echo urlencode($visibility); ?>" class="page-link">
                            <i class="bi bi-chevron-left"></i> Previous
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&status=<?php echo urlencode($status); ?>&visibility=<?php echo urlencode($visibility); ?>" class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&status=<?php echo urlencode($status); ?>&visibility=<?php echo urlencode($visibility); ?>" class="page-link">
                            Next <i class="bi bi-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<script>
    setupTableSorting('articlesTable');
</script>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
