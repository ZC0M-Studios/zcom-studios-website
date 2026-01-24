<?php
/* ========================================================
    //ANCHOR [ADMIN_USERS_CREATE]
    FUNCTION: Create New Admin User Page
-----------------------------------------------------------
    Parameters: N/A
    Returns: HTML
    Description: Form to create a new admin user
    UniqueID: 793402
=========================================================== */

$current_page = 'users';
require_once __DIR__ . '/../includes/auth_check.php';

include __DIR__ . '/../includes/admin_header.php';
?>

<main class="admin-main">
    <div class="page-header">
        <h2>ADD NEW OPERATOR</h2>
        <p>CREATE ADMIN ACCOUNT</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">OPERATOR DETAILS</h3>
                </div>
                <div class="card-body">
                    <form id="userForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label required">USERNAME</label>
                            <input type="text" name="username" id="username" class="form-control" 
                                   required minlength="3" maxlength="50"
                                   pattern="[a-zA-Z0-9_]+"
                                   placeholder="Enter username (letters, numbers, underscore)">
                            <small class="form-text">3-50 characters. Letters, numbers, and underscores only.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">EMAIL</label>
                            <input type="email" name="email" id="email" class="form-control" 
                                   required maxlength="100"
                                   placeholder="Enter email address">
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">PASSWORD</label>
                            <input type="password" name="password" id="password" class="form-control" 
                                   required minlength="8"
                                   placeholder="Enter password (min 8 characters)">
                            <small class="form-text">Minimum 8 characters.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">CONFIRM PASSWORD</label>
                            <input type="password" name="password_confirm" id="password_confirm" class="form-control" 
                                   required minlength="8"
                                   placeholder="Confirm password">
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn-scifi btn-primary">
                                <i class="bi bi-person-plus"></i> CREATE OPERATOR
                            </button>
                            <a href="/admin/users/list.php" class="btn-scifi btn-outline">
                                <i class="bi bi-x-lg"></i> CANCEL
                            </a>
                        </div>
                    </form>
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

        if (password !== passwordConfirm) {
            showToast('Passwords do not match', 'error');
            return;
        }

        if (password.length < 8) {
            showToast('Password must be at least 8 characters', 'error');
            return;
        }

        const formData = new FormData(this);

        fetch('/admin/api/create_user.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Operator created successfully!', 'success');
                setTimeout(() => window.location.href = '/admin/users/list.php', 1000);
            } else {
                showToast(data.error || 'Failed to create operator', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    });
});
</script>

