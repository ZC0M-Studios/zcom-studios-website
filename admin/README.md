# ZCOM Studios Admin Dashboard

## Installation & Setup

### 1. Database Setup

Run the SQL migration script to create admin tables:

```sql
-- Execute this in phpMyAdmin or MySQL client
SOURCE admin/sql/migration_001_admin_tables.sql;
```

Or manually run the SQL file located at: `admin/sql/migration_001_admin_tables.sql`

**Default Admin Credentials:**
- Username: `admin`
- Password: `admin123`

**IMPORTANT:** Change this password immediately after first login!

### 2. File Structure

```
/admin/
├── index.php                 # Dashboard overview
├── login.php                 # Login page
├── logout.php                # Logout handler
├── settings.php              # Settings page (to be created)
├── /includes/
│   ├── auth_check.php        # Session validation
│   ├── admin_header.php      # Header component
│   ├── admin_sidebar.php     # Sidebar navigation
│   └── admin_footer.php      # Footer component
├── /css/
│   └── admin-style.css       # Admin dashboard styles
├── /js/
│   └── admin.js              # Admin JavaScript functions
├── /api/
│   ├── create_article.php    # Create article endpoint
│   ├── delete_article.php    # Delete article endpoint
│   └── toggle_featured.php   # Toggle featured status
├── /articles/
│   ├── list.php              # Articles list
│   ├── create.php            # Create article
│   └── edit.php              # Edit article (to be created)
├── /projects/
│   └── (to be created)
├── /prompts/
│   └── (to be created)
├── /tools/
│   └── (to be created)
└── /tags/
    └── (to be created)
```

### 3. Access the Dashboard

1. Navigate to: `http://localhost/admin/login.php`
2. Login with default credentials
3. Change your password in Settings

### 4. Security Checklist

- [ ] Change default admin password
- [ ] Update database credentials in `/includes/db_config.php`
- [ ] Add `.htaccess` protection to `/admin/` directory (optional)
- [ ] Enable HTTPS in production
- [ ] Review CSRF token implementation
- [ ] Set up regular database backups

### 5. Features Implemented

#### ✅ Completed
- Authentication system with session management
- Login/logout functionality
- Rate limiting (5 attempts per 15 minutes)
- Remember me functionality
- Dashboard overview with statistics
- Articles management (list, create, delete)
- Responsive admin layout
- Toast notification system
- CSRF protection
- Table sorting
- Pagination
- Search and filtering

#### 🚧 In Progress
- Articles edit page
- Projects management module
- Prompts management module
- Tools management module
- Tags management module

#### 📋 Planned
- Settings page
- User profile management
- Activity log
- Content versioning
- Bulk operations
- Image upload functionality
- Advanced analytics

### 6. API Endpoints

All API endpoints are located in `/admin/api/` and require authentication.

**Articles:**
- `POST /admin/api/create_article.php` - Create new article
- `POST /admin/api/update_article.php` - Update article (to be created)
- `POST /admin/api/delete_article.php` - Delete article

**General:**
- `POST /admin/api/toggle_featured.php` - Toggle featured status (works for all content types)

### 7. JavaScript Functions

Available global functions in `admin.js`:

```javascript
showToast(message, type)           // Show notification
deleteItem(type, id, name)         // Delete with confirmation
toggleFeatured(type, id)           // Toggle featured status
generateSlug(text)                 // Generate URL slug
setupSlugGeneration(titleId, slugId) // Auto-generate slug
setupTableSorting(tableId)         // Enable table sorting
copyToClipboard(text, message)     // Copy to clipboard
validateForm(formId)               // Validate form fields
enableAutoSave(formId, saveUrl)    // Enable auto-save
```

### 8. Keyboard Shortcuts

- `Ctrl+S` / `Cmd+S` - Save current form

### 9. Troubleshooting

**Login Issues:**
- Clear browser cookies
- Check database connection
- Verify admin_users table exists
- Check error logs

**Session Timeout:**
- Default timeout: 30 minutes
- Adjust in `auth_check.php` if needed

**Database Errors:**
- Verify all tables exist
- Check foreign key constraints
- Review error logs in PHP error log

### 10. Next Steps

To complete the implementation:

1. Create edit page for articles
2. Implement Projects management module
3. Implement Prompts management module
4. Implement Tools management module
5. Implement Tags management module
6. Add settings page
7. Implement frontend improvements (search, accessibility, mobile)

### 11. Development Notes

**Adding New Content Types:**

1. Create list/create/edit pages in `/admin/{type}/`
2. Create API endpoints in `/admin/api/`
3. Add navigation link in `admin_sidebar.php`
4. Follow existing patterns for consistency

**Styling:**

- Use CSS variables defined in `admin-style.css`
- Follow cyberpunk/sci-fi theme
- Maintain responsive design
- Use Bootstrap 5 classes where appropriate

### 12. Support

For issues or questions, refer to the original specification document:
`/docs/cozy-imagining-lighthouse.md`

## License

© 2026 ZCOM Studios. All rights reserved.
