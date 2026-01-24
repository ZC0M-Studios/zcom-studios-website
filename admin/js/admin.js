/* ========================================================
   ZCOM Studios Admin Dashboard JavaScript
=========================================================== */

// Sidebar Toggle
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');
    const mainContent = document.querySelector('.admin-main');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            if (mainContent) {
                mainContent.classList.toggle('expanded');
            }
        });
    }
    
    // Mobile: Close sidebar when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.add('collapsed');
            }
        }
    });
});

// Toast Notification System
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) return;
    
    const toastId = 'toast-' + Date.now();
    const iconMap = {
        success: 'bi-check-circle-fill',
        error: 'bi-exclamation-triangle-fill',
        warning: 'bi-exclamation-circle-fill',
        info: 'bi-info-circle-fill'
    };
    
    const colorMap = {
        success: '#51cf66',
        error: '#ff6b6b',
        warning: '#ffd43b',
        info: '#00ffff'
    };
    
    const toastHTML = `
        <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi ${iconMap[type]} me-2" style="color: ${colorMap[type]}"></i>
                <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

// Confirmation Dialog
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Delete Item with Confirmation
function deleteItem(type, id, name) {
    const message = `Are you sure you want to delete "${name}"? This action cannot be undone.`;
    
    if (confirm(message)) {
        fetch(`/admin/api/delete_${type}.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(`${type.charAt(0).toUpperCase() + type.slice(1)} deleted successfully!`, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.error || 'Failed to delete item.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred. Please try again.', 'error');
        });
    }
}

// Toggle Featured Status
function toggleFeatured(type, id) {
    fetch(`/admin/api/toggle_featured.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ type: type, id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Featured status updated!', 'success');
            setTimeout(() => location.reload(), 500);
        } else {
            showToast(data.error || 'Failed to update status.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
    });
}

// Auto-save Draft (for create/edit forms)
let autoSaveTimer;
function enableAutoSave(formId, saveUrl) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                saveDraft(form, saveUrl);
            }, 60000); // Auto-save after 60 seconds of inactivity
        });
    });
}

function saveDraft(form, saveUrl) {
    const formData = new FormData(form);
    formData.append('auto_save', '1');
    
    fetch(saveUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Draft auto-saved');
            // Optionally show a subtle indicator
        }
    })
    .catch(error => {
        console.error('Auto-save error:', error);
    });
}

// Slug Generator
function generateSlug(text) {
    return text
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

// Auto-generate slug from title
function setupSlugGeneration(titleInputId, slugInputId) {
    const titleInput = document.getElementById(titleInputId);
    const slugInput = document.getElementById(slugInputId);
    
    if (titleInput && slugInput) {
        titleInput.addEventListener('input', function() {
            if (!slugInput.dataset.manuallyEdited) {
                slugInput.value = generateSlug(this.value);
            }
        });
        
        slugInput.addEventListener('input', function() {
            this.dataset.manuallyEdited = 'true';
        });
    }
}

// Table Sorting
function setupTableSorting(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const headers = table.querySelectorAll('th[data-sortable]');
    headers.forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            const column = this.dataset.column;
            const currentOrder = this.dataset.order || 'asc';
            const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
            
            sortTable(table, column, newOrder);
            
            // Update header indicators
            headers.forEach(h => h.classList.remove('sorted-asc', 'sorted-desc'));
            this.classList.add(`sorted-${newOrder}`);
            this.dataset.order = newOrder;
        });
    });
}

function sortTable(table, column, order) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        const aValue = a.querySelector(`[data-column="${column}"]`)?.textContent || '';
        const bValue = b.querySelector(`[data-column="${column}"]`)?.textContent || '';
        
        if (order === 'asc') {
            return aValue.localeCompare(bValue, undefined, { numeric: true });
        } else {
            return bValue.localeCompare(aValue, undefined, { numeric: true });
        }
    });
    
    rows.forEach(row => tbody.appendChild(row));
}

// Keyboard Shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+S or Cmd+S to save
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        const saveButton = document.querySelector('button[type="submit"]');
        if (saveButton) {
            saveButton.click();
            showToast('Saving...', 'info');
        }
    }
});

// Copy to Clipboard
function copyToClipboard(text, successMessage = 'Copied to clipboard!') {
    navigator.clipboard.writeText(text).then(() => {
        showToast(successMessage, 'success');
    }).catch(err => {
        console.error('Failed to copy:', err);
        showToast('Failed to copy to clipboard.', 'error');
    });
}

// Initialize Rich Text Editor (TinyMCE)
function initRichTextEditor(selector) {
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: selector,
            height: 500,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | code | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
            skin: 'oxide-dark',
            content_css: 'dark'
        });
    }
}

// Form Validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        showToast('Please fill in all required fields.', 'error');
    }
    
    return isValid;
}

// Export functions for global use
window.showToast = showToast;
window.confirmAction = confirmAction;
window.deleteItem = deleteItem;
window.toggleFeatured = toggleFeatured;
window.generateSlug = generateSlug;
window.setupSlugGeneration = setupSlugGeneration;
window.setupTableSorting = setupTableSorting;
window.copyToClipboard = copyToClipboard;
window.initRichTextEditor = initRichTextEditor;
window.validateForm = validateForm;
window.enableAutoSave = enableAutoSave;
