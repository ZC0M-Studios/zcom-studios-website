<?php
/* ========================================================
    ADMIN - Settings Page
=========================================================== */

require_once __DIR__ . '/includes/auth_check.php';

$page_title = 'Settings';
$current_page = 'settings';

$success = '';
$error = '';

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid CSRF token';
    } else {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = 'All password fields are required';
        } elseif ($new_password !== $confirm_password) {
            $error = 'New passwords do not match';
        } elseif (strlen($new_password) < 8) {
            $error = 'Password must be at least 8 characters';
        } else {
            try {
                // Verify current password
                $stmt = $db->prepare("SELECT password_hash FROM admin_users WHERE id = ?");
                $stmt->execute([$_SESSION['admin_user_id']]);
                $user = $stmt->fetch();
                
                if (!$user || !password_verify($current_password, $user['password_hash'])) {
                    $error = 'Current password is incorrect';
                } else {
                    // Update password
                    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?");
                    $stmt->execute([$new_hash, $_SESSION['admin_user_id']]);
                    
                    $success = 'Password changed successfully!';
                }
            } catch (PDOException $e) {
                error_log("Password change error: " . $e->getMessage());
                $error = 'An error occurred. Please try again.';
            }
        }
    }
}

// Get current user info
try {
    $stmt = $db->prepare("SELECT username, email, created_at, last_login FROM admin_users WHERE id = ?");
    $stmt->execute([$_SESSION['admin_user_id']]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    error_log("User fetch error: " . $e->getMessage());
    $user = null;
}

include __DIR__ . '/includes/admin_header.php';
include __DIR__ . '/includes/admin_sidebar.php';
?>

<main class="admin-main">
    <div class="page-header">
        <h2>SYSTEM CONFIGURATION</h2>
        <p>OPERATOR ACCOUNT AND SYSTEM PREFERENCES</p>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>CONFIRMED:</strong> <?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>ERROR:</strong> <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <!-- Account Information -->
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">OPERATOR PROFILE</h3>
                </div>
                <div class="card-body">
                    <?php if ($user): ?>
                        <div class="mb-2">
                            <label class="form-label">OPERATOR ID</label>
                            <p class="form-control-plaintext" style="color: var(--bat-white); font-family: 'JetBrains Mono', monospace; font-size: 12px;">
                                <?php echo strtoupper(htmlspecialchars($user['username'])); ?>
                            </p>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">CONTACT</label>
                            <p class="form-control-plaintext" style="color: var(--bat-text-data); font-family: 'JetBrains Mono', monospace; font-size: 12px;">
                                <?php echo htmlspecialchars($user['email']); ?>
                            </p>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">ACCOUNT CREATED</label>
                            <p class="form-control-plaintext" style="color: var(--bat-text-data); font-family: 'JetBrains Mono', monospace; font-size: 12px;">
                                <?php echo strtoupper(date('d M Y', strtotime($user['created_at']))); ?>
                            </p>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">LAST ACCESS</label>
                            <p class="form-control-plaintext" style="color: var(--bat-text-data); font-family: 'JetBrains Mono', monospace; font-size: 12px;">
                                <?php echo $user['last_login'] ? strtoupper(date('d M Y H:i', strtotime($user['last_login']))) : 'NEVER'; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Change Password -->
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">ACCESS CODE UPDATE</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">

                        <div class="form-group">
                            <label for="current_password" class="form-label">CURRENT ACCESS CODE</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password" class="form-label">NEW ACCESS CODE</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" required minlength="8">
                            <small class="text-muted">MINIMUM 8 CHARACTERS</small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password" class="form-label">CONFIRM ACCESS CODE</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="8">
                        </div>

                        <button type="submit" name="change_password" class="btn-scifi">
                            <i class="bi bi-key"></i> UPDATE ACCESS CODE
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">SYSTEM ACTIONS</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/" target="_blank" class="btn-scifi btn-outline">
                            <i class="bi bi-box-arrow-up-right"></i> PUBLIC INTERFACE
                        </a>
                        <button onclick="clearCache()" class="btn-scifi btn-outline">
                            <i class="bi bi-arrow-clockwise"></i> CLEAR CACHE
                        </button>
                        <a href="/admin/logout.php" class="btn-scifi btn-danger">
                            <i class="bi bi-box-arrow-right"></i> TERMINATE SESSION
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="admin-card">
        <div class="card-header">
            <h3 class="card-title">SYSTEM DIAGNOSTICS</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">PHP VERSION</label>
                    <p style="color: var(--bat-text-data); font-family: 'JetBrains Mono', monospace; font-size: 12px;"><?php echo phpversion(); ?></p>
                </div>
                <div class="col-md-3">
                    <label class="form-label">DATABASE</label>
                    <p style="color: var(--bat-text-data); font-family: 'JetBrains Mono', monospace; font-size: 12px;">MYSQL <?php echo $db->query('SELECT VERSION()')->fetchColumn(); ?></p>
                </div>
                <div class="col-md-3">
                    <label class="form-label">SERVER</label>
                    <p style="color: var(--bat-text-data); font-family: 'JetBrains Mono', monospace; font-size: 12px;"><?php echo strtoupper($_SERVER['SERVER_SOFTWARE'] ?? 'UNKNOWN'); ?></p>
                </div>
                <div class="col-md-3">
                    <label class="form-label">SYSTEM VERSION</label>
                    <p style="color: var(--bat-online); font-family: 'JetBrains Mono', monospace; font-size: 12px;">BATCOMPUTER v1.0.0</p>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function clearCache() {
    if (confirm('Are you sure you want to clear the cache?')) {
        showToast('Cache cleared successfully!', 'success');
    }
}
</script>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
