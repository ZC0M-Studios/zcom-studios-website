<?php
/* ========================================================
    ADMIN - Create Article
=========================================================== */

require_once __DIR__ . '/../includes/auth_check.php';

$page_title = 'Create Article';
$current_page = 'articles';

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
                <h2>Create New Article</h2>
                <p>Write and publish a new blog article</p>
            </div>
            <a href="/admin/articles/list.php" class="btn-outline">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
    
    <form id="articleForm" action="/admin/api/create_article.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
        
        <div class="row">
            <div class="col-md-8">
                <!-- Basic Info -->
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Article Content</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" id="title" name="title" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="slug" class="form-label">Slug *</label>
                            <input type="text" id="slug" name="slug" class="form-control" required>
                            <small class="text-muted">Auto-generated from title. Edit if needed.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="excerpt" class="form-label">Excerpt</label>
                            <textarea id="excerpt" name="excerpt" class="form-control" rows="3"></textarea>
                            <small class="text-muted">Brief summary of the article</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="content" class="form-label">Content *</label>
                            <textarea id="content" name="content" class="form-control" rows="15" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" id="category" name="category" class="form-control" list="categories">
                            <datalist id="categories">
                                <option value="Tutorial">
                                <option value="Guide">
                                <option value="News">
                                <option value="Opinion">
                                <option value="Case Study">
                            </datalist>
                        </div>
                    </div>
                </div>
                
                <!-- Author Info -->
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Author Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="author_name" class="form-label">Author Name</label>
                                    <input type="text" id="author_name" name="author_name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="author_role" class="form-label">Author Role</label>
                                    <input type="text" id="author_role" name="author_role" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="author_bio" class="form-label">Author Bio</label>
                            <textarea id="author_bio" name="author_bio" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="author_avatar_url" class="form-label">Author Avatar URL</label>
                            <input type="url" id="author_avatar_url" name="author_avatar_url" class="form-control">
                        </div>
                    </div>
                </div>
                
                <!-- SEO Metadata -->
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">SEO & Metadata</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" id="meta_title" name="meta_title" class="form-control" maxlength="60">
                            <small class="text-muted">Recommended: 50-60 characters</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea id="meta_description" name="meta_description" class="form-control" rows="2" maxlength="160"></textarea>
                            <small class="text-muted">Recommended: 150-160 characters</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="og_title" class="form-label">OG Title</label>
                            <input type="text" id="og_title" name="og_title" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="og_description" class="form-label">OG Description</label>
                            <textarea id="og_description" name="og_description" class="form-control" rows="2"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="og_image_url" class="form-label">OG Image URL</label>
                            <input type="url" id="og_image_url" name="og_image_url" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Publishing Options -->
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Publishing</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="status" class="form-label">Status *</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="visibility" class="form-label">Visibility *</label>
                            <select id="visibility" name="visibility" class="form-control" required>
                                <option value="public">Public</option>
                                <option value="private">Private</option>
                                <option value="unlisted">Unlisted</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="published_date" class="form-label">Published Date</label>
                            <input type="datetime-local" id="published_date" name="published_date" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" id="featured" name="featured" class="form-check-input" value="1">
                                <label for="featured" class="form-check-label">Featured Article</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" id="sticky" name="sticky" class="form-check-input" value="1">
                                <label for="sticky" class="form-check-label">Sticky (Pin to Top)</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" id="allow_comments" name="allow_comments" class="form-check-input" value="1" checked>
                                <label for="allow_comments" class="form-check-label">Allow Comments</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tags -->
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Tags</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Select Tags</label>
                            <div style="max-height: 300px; overflow-y: auto; border: 1px solid var(--admin-border); border-radius: 5px; padding: 10px;">
                                <?php foreach ($tags as $tag): ?>
                                    <div class="form-check mb-2">
                                        <input type="checkbox" id="tag_<?php echo $tag['id']; ?>" name="tags[]" value="<?php echo $tag['id']; ?>" class="form-check-input">
                                        <label for="tag_<?php echo $tag['id']; ?>" class="form-check-label">
                                            <?php echo htmlspecialchars($tag['display_name']); ?>
                                            <?php if ($tag['category']): ?>
                                                <small class="text-muted">(<?php echo htmlspecialchars($tag['category']); ?>)</small>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_tag" class="form-label">Add New Tag</label>
                            <input type="text" id="new_tag" name="new_tag" class="form-control" placeholder="Tag name">
                            <small class="text-muted">Create a new tag inline</small>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="admin-card">
                    <div class="card-body">
                        <button type="submit" name="action" value="publish" class="btn-scifi w-100 mb-2">
                            <i class="bi bi-check-circle"></i> Publish Article
                        </button>
                        <button type="submit" name="action" value="draft" class="btn-outline w-100 mb-2">
                            <i class="bi bi-save"></i> Save as Draft
                        </button>
                        <a href="/admin/articles/list.php" class="btn-outline w-100">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Setup slug generation
    setupSlugGeneration('title', 'slug');

    // Initialize rich text editor
    initRichTextEditor('#content');
    
    // Form submission
    document.getElementById('articleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm('articleForm')) {
            return;
        }
        
        const formData = new FormData(this);
        const action = e.submitter.value;
        
        // Override status based on action
        if (action === 'draft') {
            formData.set('status', 'draft');
        } else if (action === 'publish') {
            formData.set('status', 'published');
        }
        
        // Get TinyMCE content
        if (typeof tinymce !== 'undefined') {
            formData.set('content', tinymce.get('content').getContent());
        }
        
        fetch('/admin/api/create_article.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Article created successfully!', 'success');
                setTimeout(() => {
                    window.location.href = '/admin/articles/edit.php?id=' + data.article_id;
                }, 1000);
            } else {
                showToast(data.error || 'Failed to create article.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred. Please try again.', 'error');
        });
    });
    
    // Auto-save functionality
    enableAutoSave('articleForm', '/admin/api/create_article.php');
});
