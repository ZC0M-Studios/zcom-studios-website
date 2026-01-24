<?php
/* ========================================================
    //ANCHOR [ADMIN_USERS_EDIT]
    FUNCTION: Edit Admin User Page
-----------------------------------------------------------
    Parameters: ?id=user_id (URL parameter)
    Returns: HTML
    Description: Form to edit an existing admin user
    UniqueID: 793403
=========================================================== */

$current_page = 'users';
require_once __DIR__ . '/../includes/auth_check.php';

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$user_id) {
    header('Location: /admin/users/list.php');
    exit;
}

// Fetch user data
try {
    $stmt = $db->prepare("SELECT id, username, email, created_at, last_login FROM admin_users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        header('Location: /admin/users/list.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("User fetch error: " . $e->getMessage());
    header('Location: /admin/users/list.php');
    exit;
}

$is_self = ($user_id == $_SESSION['admin_user_id']);

include __DIR__ . '/../includes/admin_header.php';
?>

<main class="admin-main">
    <div class="page-header">
        <h2>EDIT OPERATOR</h2>
        <p>MODIFY ADMIN ACCOUNT: <?php echo strtoupper(htmlspecialchars($user['username'])); ?></p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">OPERATOR DETAILS</h3>
                    <?php if ($is_self): ?>
                        <span class="badge bg-success">YOUR ACCOUNT</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <form id="userForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label required">USERNAME</label>
                            <input type="text" name="username" id="username" class="form-control" 
                                   required minlength="3" maxlength="50"
                                   pattern="[a-zA-Z0-9_]+"
                                   value="<?php echo htmlspecialchars($user['username']); ?>"
                                   placeholder="Enter username">
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">EMAIL</label>
                            <input type="email" name="email" id="email" class="form-control" 
                                   required maxlength="100"
                                   value="<?php echo htmlspecialchars($user['email']); ?>"
                                   placeholder="Enter email address">
                        </div>

                        <hr style="border-color: var(--bat-border);">
                        <p class="text-muted mb-3">Leave password fields empty to keep current password.</p>

                        <div class="mb-3">
                            <label class="form-label">NEW PASSWORD</label>
                            <input type="password" name="password" id="password" class="form-control" 
                                   minlength="8"
                                   placeholder="Enter new password (optional)">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">CONFIRM NEW PASSWORD</label>
                            <input type="password" name="password_confirm" id="password_confirm" class="form-control" 
                                   minlength="8"
                                   placeholder="Confirm new password">
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn-scifi btn-primary">
                                <i class="bi bi-check-lg"></i> UPDATE OPERATOR
                            </button>
                            <a href="/admin/users/list.php" class="btn-scifi btn-outline">
                                <i class="bi bi-x-lg"></i> CANCEL
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Info -->
            <div class="admin-card mt-4">
                <div class="card-header">
                    <h3 class="card-title">ACCOUNT INFO</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">CREATED</label>
                            <p class="font-mono" style="color: var(--bat-text-data);">
                                <?php echo date('Y-m-d H:i:s', strtotime($user['created_at'])); ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">LAST LOGIN</label>
                            <p class="font-mono" style="color: var(--bat-text-data);">
                                <?php echo $user['last_login'] ? date('Y-m-d H:i:s', strtotime($user['last_login'])) : 'NEVER'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('userForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirm').value;

        if (password && password !== passwordConfirm) {
            showToast('Passwords do not match', 'error');
            return;
        }

        if (password && password.length < 8) {
            showToast('Password must be at least 8 characters', 'error');
            return;
        }

        const formData = new FormData(this);

        fetch('/admin/api/update_user.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Operator updated successfully!', 'success');
                setTimeout(() => window.location.href = '/admin/users/list.php', 1000);
            } else {
                showToast(data.error || 'Failed to update operator', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    });
});
</script>

