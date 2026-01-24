<?php
/* ========================================================
    //ANCHOR [ADMIN_PROJECTS_LIST]
    FUNCTION: Admin - Projects List Page
-----------------------------------------------------------
    Parameters: GET (page, search, status)
    Returns: HTML table output
    Description: Compact table view of all projects with filtering
    UniqueID: 793702
=========================================================== */

require_once __DIR__ . '/../includes/auth_check.php';

$page_title = 'PROJECTS';
$current_page = 'projects';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';

$where = [];
$params = [];

if ($search) {
    $where[] = "(name LIKE ? OR tagline LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($status) {
    $where[] = "status = ?";
    $params[] = $status;
}

$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

try {
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM projects $where_clause");
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];
    $total_pages = ceil($total / $per_page);
    
    $query = "SELECT id, name, slug, tagline, status, featured, created_at FROM projects $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $db->prepare($query);
    $stmt->execute(array_merge($params, [$per_page, $offset]));
    $items = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Projects list error: " . $e->getMessage());
    $items = [];
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
                <h2>PROJECTS // <?php echo $total; ?> RECORDS</h2>
                <p>PORTFOLIO PROJECT MANAGEMENT</p>
            </div>
            <a href="/admin/projects/create.php" class="btn-scifi">
                <i class="bi bi-plus-circle"></i> NEW PROJECT
            </a>
        </div>
    </div>
    
    <div class="admin-card">
        <form method="GET" class="row g-3 mb-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="SEARCH PROJECTS..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-control">
                    <option value="">ALL STATUS</option>
                    <option value="concept" <?php echo $status === 'concept' ? 'selected' : ''; ?>>CONCEPT</option>
                    <option value="in_development" <?php echo $status === 'in_development' ? 'selected' : ''; ?>>IN DEVELOPMENT</option>
                    <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>COMPLETED</option>
                    <option value="live" <?php echo $status === 'live' ? 'selected' : ''; ?>>LIVE</option>
                    <option value="archived" <?php echo $status === 'archived' ? 'selected' : ''; ?>>ARCHIVED</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn-scifi btn-sm"><i class="bi bi-funnel"></i> FILTER</button>
                <a href="/admin/projects/list.php" class="btn-outline btn-sm"><i class="bi bi-x-circle"></i> CLEAR</a>
            </div>
        </form>
    </div>
    
    <div class="admin-card">
        <?php if (empty($items)): ?>
            <div class="empty-state">
                <i class="bi bi-folder"></i>
                <h3>NO PROJECTS FOUND</h3>
                <a href="/admin/projects/create.php" class="btn-scifi mt-3"><i class="bi bi-plus-circle"></i> CREATE PROJECT</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>NAME</th>
                            <th>TAGLINE</th>
                            <th>STATUS</th>
                            <th>FEATURED</th>
                            <th>CREATED</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($item['tagline'] ?? '-'); ?></td>
                                <td><span class="badge badge-<?php echo $item['status'] === 'live' ? 'success' : ($item['status'] === 'archived' ? 'danger' : 'info'); ?>"><?php echo strtoupper($item['status']); ?></span></td>
                                <td><i class="bi bi-star<?php echo $item['featured'] ? '-fill' : ''; ?>" style="color: <?php echo $item['featured'] ? '#ffd43b' : 'inherit'; ?>"></i></td>
                                <td><?php echo date('M d, Y', strtotime($item['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/portfolio/project.php?slug=<?php echo urlencode($item['slug']); ?>" target="_blank" class="btn-icon" title="View"><i class="bi bi-eye"></i></a>
                                        <a href="/admin/projects/edit.php?id=<?php echo $item['id']; ?>" class="btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <button onclick="deleteItem('project', <?php echo $item['id']; ?>, '<?php echo htmlspecialchars(addslashes($item['name'])); ?>')" class="btn-icon" title="Delete"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?><a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>" class="page-link"><i class="bi bi-chevron-left"></i></a><?php endif; ?>
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>" class="page-link <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?><a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>" class="page-link"><i class="bi bi-chevron-right"></i></a><?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>

