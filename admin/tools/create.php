<?php
/* ========================================================
    //ANCHOR [ADMIN_TOOL_CREATE]
    FUNCTION: Admin - Create Tool Page
-----------------------------------------------------------
    Parameters: None (form submission via POST)
    Returns: HTML form output
    Description: Form for creating new utilities/tools
    UniqueID: 793401
=========================================================== */

require_once __DIR__ . '/../includes/auth_check.php';

$page_title = 'CREATE TOOL';
$current_page = 'tools';

// Get all tags for multi-select
try {
    $tags = $db->query("SELECT id, display_name, category FROM tags_registry ORDER BY display_name")->fetchAll();
} catch (PDOException $e) {
    error_log("Tags fetch error: " . $e->getMessage());
    $tags = [];
}

include __DIR__ . '/../includes/admin_header.php';
include __DIR__ . '/../includes/admin_sidebar.php';
?>

<main class="admin-main">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>NEW TOOL</h2>
                <p>CREATE A NEW UTILITY/TOOL</p>
            </div>
            <a href="/admin/tools/list.php" class="btn-outline">
                <i class="bi bi-arrow-left"></i> BACK TO LIST
            </a>
        </div>
    </div>
    
    <form id="toolForm" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
        
        <div class="row">
            <div class="col-md-8">
                <!-- Basic Info -->
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">TOOL DETAILS</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name" class="form-label">TOOL NAME *</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="slug" class="form-label">SLUG *</label>
                            <input type="text" id="slug" name="slug" class="form-control" required>
                            <small class="text-muted">Auto-generated from name</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="description" class="form-label">DESCRIPTION *</label>
                            <textarea id="description" name="description" class="form-control" rows="6" required></textarea>
                            <small class="text-muted">Describe what the tool does</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="page_url" class="form-label">PAGE URL</label>
                            <input type="text" id="page_url" name="page_url" class="form-control" placeholder="/tools/my-tool">
                            <small class="text-muted">URL path to the tool page</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Options -->
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">OPTIONS</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" id="featured" name="featured" class="form-check-input" value="1">
                                <label for="featured" class="form-check-label">FEATURED TOOL</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tags -->
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">TAGS</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">SELECT TAGS</label>
                            <div style="max-height: 250px; overflow-y: auto; border: 1px solid var(--bat-border); border-radius: 4px; padding: 10px;">
                                <?php foreach ($tags as $tag): ?>
                                    <div class="form-check mb-2">
                                        <input type="checkbox" id="tag_<?php echo $tag['id']; ?>" name="tags[]" value="<?php echo $tag['id']; ?>" class="form-check-input">
                                        <label for="tag_<?php echo $tag['id']; ?>" class="form-check-label">
                                            <?php echo htmlspecialchars($tag['display_name']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="admin-card">
                    <div class="card-body">
                        <button type="submit" name="action" value="save" class="btn-scifi w-100 mb-2">
                            <i class="bi bi-check-circle"></i> CREATE TOOL
                        </button>
                        <a href="/admin/tools/list.php" class="btn-outline w-100">
                            <i class="bi bi-x-circle"></i> CANCEL
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setupSlugGeneration('name', 'slug');

    document.getElementById('toolForm').addEventListener('submit', function(e) {
        e.preventDefault();

        if (!validateForm('toolForm')) return;

        const formData = new FormData(this);

        fetch('/admin/api/create_tool.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Tool created successfully!', 'success');
                setTimeout(() => window.location.href = '/admin/tools/list.php', 1000);
            } else {
                showToast(data.error || 'Failed to create tool.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred.', 'error');
        });
    });
});
</script>

