<?php
/* ========================================================
    //ANCHOR [ADMIN_USERS_LIST]
    FUNCTION: Admin Users List Page
-----------------------------------------------------------
    Parameters: N/A
    Returns: HTML
    Description: Lists all admin users with management options
    UniqueID: 793401
=========================================================== */

$current_page = 'users';
require_once __DIR__ . '/../includes/auth_check.php';

// Fetch all admin users
try {
    $stmt = $db->query("
        SELECT id, username, email, created_at, last_login 
        FROM admin_users 
        ORDER BY created_at DESC
    ");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Users list error: " . $e->getMessage());
    $users = [];
}

include __DIR__ . '/../includes/admin_header.php';
?>

<main class="admin-main">
    <div class="page-header">
        <h2>USER MANAGEMENT</h2>
        <p>ADMIN OPERATOR ACCOUNTS</p>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="data-count">
            <span class="count-value"><?php echo count($users); ?></span>
            <span class="count-label">OPERATORS REGISTERED</span>
        </div>
        <a href="/admin/users/create.php" class="btn-scifi btn-primary">
            <i class="bi bi-person-plus"></i> ADD OPERATOR
        </a>
    </div>

    <div class="admin-card">
        <div class="card-header">
            <h3 class="card-title">OPERATOR REGISTRY</h3>
        </div>
        <div class="card-body">
            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <i class="bi bi-people"></i>
                    <p>No operators registered</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>USERNAME</th>
                                <th>EMAIL</th>
                                <th>CREATED</th>
                                <th>LAST LOGIN</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="font-mono"><?php echo $user['id']; ?></td>
                                <td>
                                    <strong style="color: var(--bat-white);">
                                        <?php echo strtoupper(htmlspecialchars($user['username'])); ?>
                                    </strong>
                                    <?php if ($user['id'] == $_SESSION['admin_user_id']): ?>
                                        <span class="badge bg-success ms-2">YOU</span>
                                    <?php endif; ?>
                                </td>
                                <td class="font-mono" style="color: var(--bat-text-data);">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </td>
                                <td class="font-mono">
                                    <?php echo date('Y-m-d', strtotime($user['created_at'])); ?>
                                </td>
                                <td class="font-mono">
                                    <?php echo $user['last_login'] ? date('Y-m-d H:i', strtotime($user['last_login'])) : 'NEVER'; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/admin/users/edit.php?id=<?php echo $user['id']; ?>" 
                                           class="btn-action btn-edit" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($user['id'] != $_SESSION['admin_user_id']): ?>
                                        <button type="button" class="btn-action btn-delete" 
                                                onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')"
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
function deleteUser(id, username) {
    if (confirm(`Are you sure you want to delete operator "${username}"?\n\nThis action cannot be undone.`)) {
        fetch('/admin/api/delete_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                id: id,
                csrf_token: '<?php echo $_SESSION['csrf_token']; ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Operator deleted successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.error || 'Failed to delete operator', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }
}
</script>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>

