<?php
/* ========================================================
    //ANCHOR [ADMIN_TAGS_LIST]
    FUNCTION: Admin - Tags List Page
-----------------------------------------------------------
    Parameters: GET (page, search, category)
    Returns: HTML table output
    Description: Compact table view of all tags with styling preview
    UniqueID: 793705
=========================================================== */

require_once __DIR__ . '/../includes/auth_check.php';

$page_title = 'TAGS';
$current_page = 'tags';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 30;
$offset = ($page - 1) * $per_page;

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$where = [];
$params = [];

if ($search) {
    $where[] = "(display_name LIKE ? OR slug LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category) {
    $where[] = "category = ?";
    $params[] = $category;
}

$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

try {
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM tags_registry $where_clause");
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];
    $total_pages = ceil($total / $per_page);
    
    $query = "SELECT id, display_name, slug, category, description, text_color, bg_color, border_color, border_type, shadow_color, created_at FROM tags_registry $where_clause ORDER BY category, display_name LIMIT ? OFFSET ?";
    $stmt = $db->prepare($query);
    $stmt->execute(array_merge($params, [$per_page, $offset]));
    $items = $stmt->fetchAll();
    
    $categories = $db->query("SELECT DISTINCT category FROM tags_registry ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    error_log("Tags list error: " . $e->getMessage());
    $items = [];
    $categories = [];
    $total = 0;
    $total_pages = 0;
}

include __DIR__ . '/../includes/admin_header.php';
include __DIR__ . '/../includes/admin_sidebar.php';
?>

<main class="admin-main">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>TAG REGISTRY // <?php echo $total; ?> RECORDS</h2>
                <p>CONTENT CLASSIFICATION SYSTEM</p>
            </div>
            <a href="/admin/tags/create.php" class="btn-scifi">
                <i class="bi bi-plus-circle"></i> NEW TAG
            </a>
        </div>
    </div>
    
    <div class="admin-card">
        <form method="GET" class="row g-3 mb-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="SEARCH TAGS..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-control">
                    <option value="">ALL CATEGORIES</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>><?php echo strtoupper($cat); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn-scifi btn-sm"><i class="bi bi-funnel"></i> FILTER</button>
                <a href="/admin/tags/list.php" class="btn-outline btn-sm"><i class="bi bi-x-circle"></i> CLEAR</a>
            </div>
        </form>
    </div>
    
    <div class="admin-card">
        <?php if (empty($items)): ?>
            <div class="empty-state">
                <i class="bi bi-tags"></i>
                <h3>NO TAGS FOUND</h3>
                <a href="/admin/tags/create.php" class="btn-scifi mt-3"><i class="bi bi-plus-circle"></i> CREATE TAG</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>PREVIEW</th>
                            <th>NAME</th>
                            <th>SLUG</th>
                            <th>CATEGORY</th>
                            <th>BORDER</th>
                            <th>CREATED</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): 
                            $style = sprintf(
                                'color:%s;background:%s;border:1px %s %s;%s padding:3px 8px;border-radius:3px;font-size:11px;text-transform:uppercase;font-family:monospace;',
                                $item['text_color'] ?? '#fff',
                                $item['bg_color'] ?? '#333',
                                $item['border_type'] ?? 'solid',
                                $item['border_color'] ?? '#666',
                                $item['shadow_color'] ? 'box-shadow:inset 0 0 6px '.$item['shadow_color'].';' : ''
                            );
                        ?>
                            <tr>
                                <td><span style="<?php echo $style; ?>"><?php echo htmlspecialchars($item['display_name']); ?></span></td>
                                <td><strong><?php echo htmlspecialchars($item['display_name']); ?></strong></td>
                                <td><code><?php echo htmlspecialchars($item['slug']); ?></code></td>
                                <td><span class="badge badge-info"><?php echo strtoupper($item['category']); ?></span></td>
                                <td><?php echo strtoupper($item['border_type'] ?? 'solid'); ?></td>
                                <td><?php echo date('M d, Y', strtotime($item['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/tags/tag.php?slug=<?php echo urlencode($item['slug']); ?>" target="_blank" class="btn-icon" title="View"><i class="bi bi-eye"></i></a>
                                        <a href="/admin/tags/edit.php?id=<?php echo $item['id']; ?>" class="btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <button onclick="deleteItem('tag', <?php echo $item['id']; ?>, '<?php echo htmlspecialchars(addslashes($item['display_name'])); ?>')" class="btn-icon" title="Delete"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?><a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>" class="page-link"><i class="bi bi-chevron-left"></i></a><?php endif; ?>
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>" class="page-link <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?><a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>" class="page-link"><i class="bi bi-chevron-right"></i></a><?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>

