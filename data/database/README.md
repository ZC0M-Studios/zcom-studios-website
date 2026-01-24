# Database Setup Instructions

## Prerequisites
- XAMPP with MySQL installed and running
- phpMyAdmin or MySQL command line access

## Setup Steps

### 1. Create the Database and Tables
Run the schema file to create the database structure:

```bash
# Option 1: Using MySQL command line
mysql -u root -p < schema.sql

# Option 2: Using phpMyAdmin
# - Open phpMyAdmin (http://localhost/phpmyadmin)
# - Click "Import" tab
# - Choose schema.sql file
# - Click "Go"
```

### 2. Insert Sample Articles
Run the insert file to add existing articles:

```bash
# Option 1: Using MySQL command line
mysql -u root -p < insert_articles.sql

# Option 2: Using phpMyAdmin
# - Open phpMyAdmin
# - Select "zcom_studios" database
# - Click "Import" tab
# - Choose insert_articles.sql file
# - Click "Go"
```

### 3. Configure Database Connection
Update the database credentials in `/includes/db_config.php` if needed:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'zcom_studios');
define('DB_USER', 'root');      // Change if needed
define('DB_PASS', '');          // Change if needed
```

### 4. Test the Connection
Visit your blog page to verify articles are loading from the database:
- http://localhost/blog.php
- http://localhost/articles/article.php?slug=developer-ai-handshake-2026

## Database Structure

### Tables Created
- **articles** - Main articles table with all content and metadata
- **article_tags** - Tags associated with articles
- **article_sections** - Section structure metadata
- **projects** - Portfolio projects showcase
- **project_tags** - Tags for projects
- **project_tech_stack** - Technologies used in projects
- **project_features** - Project features list
- **project_media** - Project images and videos

## API Endpoints

### Get Articles
```
GET /api/get_articles.php
GET /api/get_articles.php?featured=1
GET /api/get_articles.php?limit=10
```

Returns JSON array of published articles.

## Dynamic Article URLs

Articles are now accessed via:
```
/articles/article.php?slug=article-slug-here
```

The old static PHP files (article_0a01.php, etc.) can be removed once the database is populated.

## Troubleshooting

### Connection Errors
- Verify MySQL is running in XAMPP Control Panel
- Check database credentials in db_config.php
- Ensure zcom_studios database exists

### No Articles Showing
- Verify articles were inserted: `SELECT * FROM articles;`
- Check article status is 'published'
- Check visibility is 'public'
- View browser console for JavaScript errors

### API Errors
- Check PHP error logs in XAMPP
- Verify /api/get_articles.php is accessible
- Test API directly: http://localhost/api/get_articles.php
