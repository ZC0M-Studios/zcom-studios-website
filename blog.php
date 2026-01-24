<?php
// SEO Meta Variables
$page_title = "Blog";
$page_description = "Read articles on software development, AI collaboration, technology trends, and creative coding. Insights from a full-stack developer.";
$page_keywords = "blog, software development, AI, technology, coding, programming articles";
$page_type = "website";

include 'includes/header.php';
?>
</head>
<body class="cyber-theme">
<?php
include 'includes/navbar.php';
?>

<!-- ========================================================
    //ANCHOR [BLOG_PAGE_MAIN]
    FUNCTION: Blog Page Main Content
-----------------------------------------------------------
    Parameters: N/A
    Returns: HTML
    Description: Main blog page with featured article carousel and article list grid
    UniqueID: 890001
=========================================================== -->
<main class="container my-5" style="z-index: 3; pointer-events: auto;">
    
    <!-- Header Panel -->
    <div class="cyber-panel mb-4">
        <div class="cyber-panel-header">
            <span class="panel-title">BLOG.INDEX</span>
            <span class="panel-id">DEV.LOG</span>
        </div>
        <div class="cyber-panel-body text-center">
            <h1 class="page-title mb-3">DEV-BLOG</h1>
            <p class="lead" style="color: #7090a8;">Rantings of an average wannabe-developer</p>
        </div>
    </div>

    <!-- Featured Articles Panel -->
    <div class="cyber-panel-highlight mb-4" style="overflow: visible;">
        <div class="cyber-panel-header">
            <span class="panel-title">FEATURED.ARTICLES</span>
            <span class="panel-status">PRIORITY TX</span>
        </div>
        <div class="cyber-panel-body" style="padding: 0; overflow: visible;">
            <div class="carousel-container" style="border: none; box-shadow: none; overflow: visible;">
                <div class="carousel-wrapper" id="featuredCarousel">
                    <!-- Carousel items will be dynamically loaded here -->
                    <div class="carousel-loading">
                        <div class="loading-spinner"></div>
                        <p>Loading featured articles...</p>
                    </div>
                </div>
                <button class="carousel-btn carousel-btn-prev" id="carouselPrev" aria-label="Previous article">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <button class="carousel-btn carousel-btn-next" id="carouselNext" aria-label="Next article">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
                <div class="carousel-indicators" id="carouselIndicators">
                    <!-- Indicators will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- All Articles Panel -->
    <div class="cyber-panel">
        <div class="cyber-panel-header">
            <span class="panel-title">ARTICLE.LIST</span>
            <span class="panel-status">ALL ENTRIES</span>
        </div>
        <div class="cyber-panel-body">
            <div class="articles-grid" id="articlesGrid">
                <!-- Article cards will be dynamically loaded here -->
                <div class="articles-loading">
                    <div class="loading-spinner"></div>
                    <p>Loading articles...</p>
                </div>
            </div>
        </div>
    </div>
    
</main>

<script src="./js/articles.js"></script>

<?php
include 'includes/footer.php';
?>
