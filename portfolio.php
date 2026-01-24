<?php
// SEO Meta Variables
$page_title = "Portfolio";
$page_description = "Explore software development projects, web applications, and creative technology work. Full-stack development portfolio showcasing PHP, JavaScript, and more.";
$page_keywords = "portfolio, projects, web development, software engineering, PHP, JavaScript, full-stack";
$page_type = "website";

include 'includes/header.php';
?>
</head>
<body class="cyber-theme">
<?php
include 'includes/navbar.php';
?>

<!-- ========================================================
    //ANCHOR [PORTFOLIO_PAGE_MAIN]
    FUNCTION: Portfolio Page Main Content
-----------------------------------------------------------
    Parameters: N/A
    Returns: HTML
    Description: Portfolio listing page with project grid and filtering
    UniqueID: 900010
=========================================================== -->
<main class="container my-5" style="z-index: 3; pointer-events: auto;">
    
    <!-- Header Panel -->
    <div class="cyber-panel mb-4">
        <div class="cyber-panel-header">
            <span class="panel-title">PORTFOLIO.INDEX</span>
            <span class="panel-id">PRJ.DAT</span>
        </div>
        <div class="cyber-panel-body text-center">
            <h1 class="page-title mb-3">PORTFOLIO</h1>
            <p class="lead" style="color: #7090a8;">Showcasing my development projects and technical work</p>
        </div>
    </div>

    <!-- Filter Controls Panel -->
    <div class="cyber-panel mb-4">
        <div class="cyber-panel-header">
            <span class="panel-title">FILTER.CTRL</span>
            <span class="panel-status">SELECT CATEGORY</span>
        </div>
        <div class="cyber-panel-body">
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <button class="btn-scifi filter-btn active" data-filter="all">ALL</button>
                <button class="btn-scifi filter-btn" data-filter="web_app">WEB APPS</button>
                <button class="btn-scifi filter-btn" data-filter="game">GAMES</button>
                <button class="btn-scifi filter-btn" data-filter="library">LIBRARIES</button>
                <button class="btn-scifi filter-btn" data-filter="design_system">DESIGN</button>
            </div>
        </div>
    </div>

    <!-- Projects Grid Panel -->
    <div class="cyber-panel">
        <div class="cyber-panel-header">
            <span class="panel-title">PROJECT.LIST</span>
            <span class="panel-status">LOADING...</span>
        </div>
        <div class="cyber-panel-body">
            <div class="articles-grid" id="projectsGrid">
                <!-- Project cards will be dynamically loaded here -->
                <div class="articles-loading">
                    <div class="loading-spinner"></div>
                    <p>Loading projects...</p>
                </div>
            </div>
        </div>
    </div>
    
</main>

<script src="./js/portfolio.js"></script>

<?php
include 'includes/footer.php';
?>

