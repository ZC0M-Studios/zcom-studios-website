<?php
/* ========================================================
    //ANCHOR [ADMIN_TAG_EDIT]
    FUNCTION: Admin - Edit Tag Page
-----------------------------------------------------------
    Parameters: GET id (tag ID to edit)
    Returns: HTML form output
    Description: Form for editing existing tags in the registry
    UniqueID: 793503
=========================================================== */

require_once __DIR__ . '/../includes/auth_check.php';

$page_title = 'EDIT TAG';
$current_page = 'tags';

// Get tag ID from URL
$tag_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$tag_id) {
    header('Location: /admin/tags/list.php');
    exit;
}

// Fetch tag data
try {
    $stmt = $db->prepare("SELECT * FROM tags_registry WHERE id = ?");
    $stmt->execute([$tag_id]);
    $tag = $stmt->fetch();
    
    if (!$tag) {
        header('Location: /admin/tags/list.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("Tag edit fetch error: " . $e->getMessage());
    header('Location: /admin/tags/list.php');
    exit;
}

// Define tag categories
$tag_categories = [
    'custom' => 'Custom',
    'technology' => 'Technology',
    'language' => 'Programming Language',
    'framework' => 'Framework',
    'tool' => 'Tool',
    'topic' => 'Topic',
    'skill' => 'Skill',
    'industry' => 'Industry'
];

include __DIR__ . '/../includes/admin_header.php';
include __DIR__ . '/../includes/admin_sidebar.php';
?>

<main class="admin-main">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>EDIT TAG</h2>
                <p>MODIFY TAG: <?php echo htmlspecialchars($tag['display_name']); ?></p>
            </div>
            <a href="/admin/tags/list.php" class="btn-outline">
                <i class="bi bi-arrow-left"></i> BACK TO LIST
            </a>
        </div>
    </div>
    
    <form id="tagForm" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
        <input type="hidden" name="tag_id" value="<?php echo $tag_id; ?>">
        
        <div class="row">
            <div class="col-md-6">
                <!-- Basic Info -->
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">TAG DETAILS</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="display_name" class="form-label">DISPLAY NAME *</label>
                            <input type="text" id="display_name" name="display_name" class="form-control" value="<?php echo htmlspecialchars($tag['display_name']); ?>" required>
                            <small class="text-muted">How the tag appears to users</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="slug" class="form-label">SLUG *</label>
                            <input type="text" id="slug" name="slug" class="form-control" value="<?php echo htmlspecialchars($tag['slug']); ?>" required>
                            <small class="text-muted">URL-friendly identifier</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="category" class="form-label">CATEGORY *</label>
                            <select id="category" name="category" class="form-control" required>
                                <?php foreach ($tag_categories as $value => $label): ?>
                                    <option value="<?php echo $value; ?>" <?php echo $tag['category'] === $value ? 'selected' : ''; ?>><?php echo strtoupper($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="description" class="form-label">DESCRIPTION</label>
                            <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($tag['description'] ?? ''); ?></textarea>
                            <small class="text-muted">Optional: explain what this tag represents</small>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="admin-card">
                    <div class="card-body">
                        <button type="submit" name="action" value="save" class="btn-scifi w-100 mb-2">
                            <i class="bi bi-check-circle"></i> UPDATE TAG
                        </button>
                        <a href="/admin/tags/list.php" class="btn-outline w-100">
                            <i class="bi bi-x-circle"></i> CANCEL
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <!-- Display Styling -->
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">DISPLAY STYLING</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="text_color" class="form-label">TEXT COLOR</label>
                                    <input type="color" id="text_color" name="text_color" class="form-control form-control-color" value="<?php echo htmlspecialchars($tag['text_color'] ?? '#ffffff'); ?>">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="bg_color" class="form-label">BACKGROUND</label>
                                    <input type="color" id="bg_color" name="bg_color" class="form-control form-control-color" value="<?php echo htmlspecialchars($tag['bg_color'] ?? '#333333'); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="border_color" class="form-label">BORDER COLOR</label>
                                    <input type="color" id="border_color" name="border_color" class="form-control form-control-color" value="<?php echo htmlspecialchars($tag['border_color'] ?? '#666666'); ?>">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="border_type" class="form-label">BORDER TYPE</label>
                                    <select id="border_type" name="border_type" class="form-control">
                                        <?php
                                        $border_types = ['solid', 'dashed', 'dotted', 'double', 'none'];
                                        foreach ($border_types as $bt): ?>
                                            <option value="<?php echo $bt; ?>" <?php echo ($tag['border_type'] ?? 'solid') === $bt ? 'selected' : ''; ?>><?php echo strtoupper($bt); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="shadow_color" class="form-label">INSET SHADOW COLOR</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" id="shadow_color_picker" class="form-control form-control-color" value="#666666" style="width: 50px;">
                                <input type="text" id="shadow_color" name="shadow_color" class="form-control" value="<?php echo htmlspecialchars($tag['shadow_color'] ?? ''); ?>" placeholder="rgba(102,102,102,0.3) or leave empty">
                            </div>
                            <small class="text-muted">Use RGBA for transparency. Leave empty for no shadow.</small>
                        </div>

                        <!-- Preview -->
                        <div class="form-group">
                            <label class="form-label">PREVIEW</label>
                            <div id="tagPreview" class="p-3 text-center" style="background: var(--bat-panel-bg); border-radius: 4px;">
                                <span id="previewTag" class="tag-preview"><?php echo htmlspecialchars($tag['display_name']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<script>
    // Tag preview functionality
    function updateTagPreview() {
        const tag = document.getElementById('previewTag');
        const name = document.getElementById('display_name').value || 'Sample Tag';
        const textColor = document.getElementById('text_color').value;
        const bgColor = document.getElementById('bg_color').value;
        const borderColor = document.getElementById('border_color').value;
        const borderType = document.getElementById('border_type').value;
        const shadowColor = document.getElementById('shadow_color').value;

        tag.textContent = name;
        tag.style.color = textColor;
        tag.style.backgroundColor = bgColor;
        tag.style.border = borderType !== 'none' ? `1px ${borderType} ${borderColor}` : 'none';
        tag.style.boxShadow = shadowColor ? `inset 0 0 8px ${shadowColor}` : 'none';
        tag.style.padding = '4px 12px';
        tag.style.borderRadius = '4px';
        tag.style.display = 'inline-block';
        tag.style.fontFamily = 'var(--bat-font-mono)';
        tag.style.fontSize = '12px';
        tag.style.textTransform = 'uppercase';
    }

    // Sync color picker with rgba input
    document.getElementById('shadow_color_picker').addEventListener('input', function() {
        const hex = this.value;
        const r = parseInt(hex.slice(1,3), 16);
        const g = parseInt(hex.slice(3,5), 16);
        const b = parseInt(hex.slice(5,7), 16);
        document.getElementById('shadow_color').value = `rgba(${r},${g},${b},0.3)`;
        updateTagPreview();
    });

    // Bind preview updates
    ['display_name', 'text_color', 'bg_color', 'border_color', 'border_type', 'shadow_color'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', updateTagPreview);
            el.addEventListener('change', updateTagPreview);
        }
    });
    updateTagPreview();

    document.getElementById('tagForm').addEventListener('submit', function(e) {
        e.preventDefault();

        if (!validateForm('tagForm')) return;

        const formData = new FormData(this);

        fetch('/admin/api/update_tag.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Tag updated successfully!', 'success');
                setTimeout(() => window.location.href = '/admin/tags/list.php', 1000);
            } else {
                showToast(data.error || 'Failed to update tag.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred.', 'error');
        });
    });
</script>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>

