<?php
/*
╔══════════════════════════════════════════════════════════════╗
║                    INCLUDED: HEADER FILE                     ║
╚══════════════════════════════════════════════════════════════╝
*/

// include the utilities file
include_once __DIR__ . '/util.php';

/* ========================================================
    //ANCHOR [PAGE_HEADER]
    FUNCTION: N/A
-----------------------------------------------------------
    Parameters: N/A
    Returns: N/A
    Description: This section represents the header of the page, including SEO meta tags, Open Graph, Twitter Cards, canonical URLs, and JSON-LD structured data.
    UniqueID: 123458
=========================================================== */
$include_file = (object) [
    'uniqueID' => 123458,
    'name' => 'header',
    'path' => 'includes/header.php'
];

// SEO defaults - pages can override these before including header.php
$site_name = 'ZCOM Studios';
$site_url = 'https://zcomstudios.com';
$default_image = $site_url . '/img/logo/logo_zcom-studios_1.png';

// Use defaults if not set by page
$page_title = isset($page_title) ? $page_title : 'ZCOM Studios';
$page_description = isset($page_description) ? $page_description : 'Software development, 3D design, and creative technology portfolio by ZCOM Studios.';
$page_keywords = isset($page_keywords) ? $page_keywords : 'web development, PHP, JavaScript, 3D design, portfolio';
$page_image = isset($page_image) ? $page_image : $default_image;
$page_type = isset($page_type) ? $page_type : 'website'; // website, article, product
$canonical_url = isset($canonical_url) ? $canonical_url : $site_url . $_SERVER['REQUEST_URI'];
$page_author = isset($page_author) ? $page_author : 'ZCOM Studios';
$publish_date = isset($publish_date) ? $publish_date : null;
$modified_date = isset($modified_date) ? $modified_date : null;

// Build full page title with site name
$full_title = ($page_title !== $site_name) ? $page_title . ' | ' . $site_name : $site_name;

// JSON-LD structured data
$json_ld = isset($json_ld) ? $json_ld : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Primary Meta Tags -->
    <title><?php echo htmlspecialchars($full_title); ?></title>
    <meta name="title" content="<?php echo htmlspecialchars($full_title); ?>">
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($page_keywords); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($page_author); ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?php echo htmlspecialchars($page_type); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($full_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($page_image); ?>">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($site_name); ?>">
    <?php if ($publish_date): ?>
    <meta property="article:published_time" content="<?php echo htmlspecialchars($publish_date); ?>">
    <?php endif; ?>
    <?php if ($modified_date): ?>
    <meta property="article:modified_time" content="<?php echo htmlspecialchars($modified_date); ?>">
    <?php endif; ?>

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($full_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($page_image); ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- JSON-LD Structured Data -->
    <?php if ($json_ld): ?>
    <script type="application/ld+json">
    <?php echo json_encode($json_ld, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
    </script>
    <?php else: ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "<?php echo htmlspecialchars($site_name); ?>",
        "url": "<?php echo htmlspecialchars($site_url); ?>",
        "description": "<?php echo htmlspecialchars($page_description); ?>",
        "publisher": {
            "@type": "Organization",
            "name": "<?php echo htmlspecialchars($site_name); ?>",
            "logo": {
                "@type": "ImageObject",
                "url": "<?php echo htmlspecialchars($default_image); ?>"
            }
        }
    }
    </script>
    <?php endif; ?>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/style-cyberterm.css">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

