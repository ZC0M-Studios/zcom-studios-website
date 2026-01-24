<?php
/* ========================================================
    //ANCHOR [ADMIN_PROJECT_CREATE]
    FUNCTION: Admin - Create Project Page
-----------------------------------------------------------
    Parameters: None (form submission via POST)
    Returns: HTML form output
    Description: Form for creating new portfolio projects
    UniqueID: 793201
=========================================================== */

require_once __DIR__ . '/../includes/auth_check.php';

$page_title = 'CREATE PROJECT';
$current_page = 'projects';

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
                <h2>NEW PROJECT</h2>
                <p>CREATE A NEW PORTFOLIO PROJECT</p>
            </div>
            <a href="/admin/projects/list.php" class="btn-outline">
                <i class="bi bi-arrow-left"></i> BACK TO LIST
            </a>
        </div>
    </div>
    
    <form id="projectForm" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
        
        <div class="row">
            <div class="col-md-8">
                <!-- Basic Info -->
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">PROJECT DETAILS</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name" class="form-label">PROJECT NAME *</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="slug" class="form-label">SLUG *</label>
                            <input type="text" id="slug" name="slug" class="form-control" required>
                            <small class="text-muted">Auto-generated from name</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="tagline" class="form-label">TAGLINE</label>
                            <input type="text" id="tagline" name="tagline" class="form-control" maxlength="255">
                            <small class="text-muted">Brief one-liner for the project</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="description" class="form-label">DESCRIPTION *</label>
                            <textarea id="description" name="description" class="form-control" rows="8" required></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Status & Options -->
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">STATUS</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="status" class="form-label">PROJECT STATUS *</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="concept">CONCEPT</option>
                                <option value="in_development">IN DEVELOPMENT</option>
                                <option value="completed">COMPLETED</option>
                                <option value="live">LIVE</option>
                                <option value="archived">ARCHIVED</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" id="featured" name="featured" class="form-check-input" value="1">
                                <label for="featured" class="form-check-label">FEATURED PROJECT</label>
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
                            <i class="bi bi-check-circle"></i> CREATE PROJECT
                        </button>
                        <a href="/admin/projects/list.php" class="btn-outline w-100">
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

    document.getElementById('projectForm').addEventListener('submit', function(e) {
        e.preventDefault();

        if (!validateForm('projectForm')) return;

        const formData = new FormData(this);

        fetch('/admin/api/create_project.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Project created successfully!', 'success');
                setTimeout(() => window.location.href = '/admin/projects/list.php', 1000);
            } else {
                showToast(data.error || 'Failed to create project.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred.', 'error');
        });
    });
});
</script>

