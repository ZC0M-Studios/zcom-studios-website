<?php
/* ========================================================
    //ANCHOR [SITEMAP_GENERATOR]
    FUNCTION: Dynamic XML sitemap generator
-----------------------------------------------------------
    Parameters: N/A
    Returns: XML sitemap
    Description: Generates XML sitemap from database content including articles, projects, prompts, tools, and static pages
    UniqueID: 900011
=========================================================== */

header('Content-Type: application/xml; charset=utf-8');

require_once 'includes/db_config.php';

$site_url = 'https://zcomstudios.com';

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Static Pages -->
    <url>
        <loc><?php echo $site_url; ?>/</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?php echo $site_url; ?>/blog.php</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?php echo $site_url; ?>/portfolio.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?php echo $site_url; ?>/prompts.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?php echo $site_url; ?>/tools.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?php echo $site_url; ?>/contact.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>

<?php
try {
    // Articles
    $stmt = $db->query("SELECT slug, updated_at FROM articles WHERE status = 'published' AND visibility = 'public' ORDER BY published_date DESC");
    while ($row = $stmt->fetch()) {
        $lastmod = date('Y-m-d', strtotime($row['updated_at']));
        echo "    <url>\n";
        echo "        <loc>{$site_url}/articles/article.php?slug=" . urlencode($row['slug']) . "</loc>\n";
        echo "        <lastmod>{$lastmod}</lastmod>\n";
        echo "        <changefreq>monthly</changefreq>\n";
        echo "        <priority>0.8</priority>\n";
        echo "    </url>\n";
    }

    // Projects
    $stmt = $db->query("SELECT slug, updated_at FROM projects ORDER BY created_at DESC");
    while ($row = $stmt->fetch()) {
        $lastmod = date('Y-m-d', strtotime($row['updated_at']));
        echo "    <url>\n";
        echo "        <loc>{$site_url}/portfolio/project.php?slug=" . urlencode($row['slug']) . "</loc>\n";
        echo "        <lastmod>{$lastmod}</lastmod>\n";
        echo "        <changefreq>monthly</changefreq>\n";
        echo "        <priority>0.7</priority>\n";
        echo "    </url>\n";
    }

    // Prompts
    $stmt = $db->query("SELECT slug, updated_at FROM prompts WHERE visibility = 'public' ORDER BY created_at DESC");
    while ($row = $stmt->fetch()) {
        $lastmod = date('Y-m-d', strtotime($row['updated_at']));
        echo "    <url>\n";
        echo "        <loc>{$site_url}/prompts/prompt.php?slug=" . urlencode($row['slug']) . "</loc>\n";
        echo "        <lastmod>{$lastmod}</lastmod>\n";
        echo "        <changefreq>monthly</changefreq>\n";
        echo "        <priority>0.6</priority>\n";
        echo "    </url>\n";
    }

    // Tags
    $stmt = $db->query("SELECT slug FROM tags_registry ORDER BY display_name");
    while ($row = $stmt->fetch()) {
        echo "    <url>\n";
        echo "        <loc>{$site_url}/tags/tag.php?slug=" . urlencode($row['slug']) . "</loc>\n";
        echo "        <changefreq>weekly</changefreq>\n";
        echo "        <priority>0.5</priority>\n";
        echo "    </url>\n";
    }

} catch (PDOException $e) {
    error_log("Sitemap Error: " . $e->getMessage());
}
?>

    <!-- Tools -->
    <url>
        <loc><?php echo $site_url; ?>/tools/json-formatter.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
</urlset>

