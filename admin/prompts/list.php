<?php
/* ========================================================
    //ANCHOR [ADMIN_PROMPTS_LIST]
    FUNCTION: Admin - Prompts List Page
-----------------------------------------------------------
    Parameters: GET (page, search, visibility)
    Returns: HTML table output
    Description: Compact table view of all prompts with filtering
    UniqueID: 793703
=========================================================== */

require_once __DIR__ . '/../includes/auth_check.php';

$page_title = 'PROMPTS';
$current_page = 'prompts';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

$search = $_GET['search'] ?? '';
$visibility = $_GET['visibility'] ?? '';

$where = [];
$params = [];

if ($search) {
    $where[] = "(title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($visibility) {
    $where[] = "visibility = ?";
    $params[] = $visibility;
}

$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

try {
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM prompts $where_clause");
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];
    $total_pages = ceil($total / $per_page);
    
    $query = "SELECT id, title, slug, description, visibility, featured, copies, created_at FROM prompts $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $db->prepare($query);
    $stmt->execute(array_merge($params, [$per_page, $offset]));
    $items = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Prompts list error: " . $e->getMessage());
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
                <h2>PROMPTS // <?php echo $total; ?> RECORDS</h2>
                <p>AI PROMPT LIBRARY MANAGEMENT</p>
            </div>
            <a href="/admin/prompts/create.php" class="btn-scifi">
                <i class="bi bi-plus-circle"></i> NEW PROMPT
            </a>
        </div>
    </div>
    
    <div class="admin-card">
        <form method="GET" class="row g-3 mb-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="SEARCH PROMPTS..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <select name="visibility" class="form-control">
                    <option value="">ALL VISIBILITY</option>
                    <option value="public" <?php echo $visibility === 'public' ? 'selected' : ''; ?>>PUBLIC</option>
                    <option value="private" <?php echo $visibility === 'private' ? 'selected' : ''; ?>>PRIVATE</option>
                    <option value="unlisted" <?php echo $visibility === 'unlisted' ? 'selected' : ''; ?>>UNLISTED</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn-scifi btn-sm"><i class="bi bi-funnel"></i> FILTER</button>
                <a href="/admin/prompts/list.php" class="btn-outline btn-sm"><i class="bi bi-x-circle"></i> CLEAR</a>
            </div>
        </form>
    </div>
    
    <div class="admin-card">
        <?php if (empty($items)): ?>
            <div class="empty-state">
                <i class="bi bi-chat-square-quote"></i>
                <h3>NO PROMPTS FOUND</h3>
                <a href="/admin/prompts/create.php" class="btn-scifi mt-3"><i class="bi bi-plus-circle"></i> CREATE PROMPT</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>TITLE</th>
                            <th>DESCRIPTION</th>
                            <th>VISIBILITY</th>
                            <th>FEATURED</th>
                            <th>COPIES</th>
                            <th>CREATED</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($item['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars(substr($item['description'] ?? '-', 0, 50)) . (strlen($item['description'] ?? '') > 50 ? '...' : ''); ?></td>
                                <td><span class="badge badge-<?php echo $item['visibility'] === 'public' ? 'success' : ($item['visibility'] === 'private' ? 'danger' : 'info'); ?>"><?php echo strtoupper($item['visibility']); ?></span></td>
                                <td><i class="bi bi-star<?php echo $item['featured'] ? '-fill' : ''; ?>" style="color: <?php echo $item['featured'] ? '#ffd43b' : 'inherit'; ?>"></i></td>
                                <td><?php echo number_format($item['copies']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($item['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/prompts/prompt.php?slug=<?php echo urlencode($item['slug']); ?>" target="_blank" class="btn-icon" title="View"><i class="bi bi-eye"></i></a>
                                        <a href="/admin/prompts/edit.php?id=<?php echo $item['id']; ?>" class="btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <button onclick="deleteItem('prompt', <?php echo $item['id']; ?>, '<?php echo htmlspecialchars(addslashes($item['title'])); ?>')" class="btn-icon" title="Delete"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?><a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&visibility=<?php echo urlencode($visibility); ?>" class="page-link"><i class="bi bi-chevron-left"></i></a><?php endif; ?>
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&visibility=<?php echo urlencode($visibility); ?>" class="page-link <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?><a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&visibility=<?php echo urlencode($visibility); ?>" class="page-link"><i class="bi bi-chevron-right"></i></a><?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>

