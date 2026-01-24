<?php
/* ========================================================
    //ANCHOR [TAG_AGGREGATION_PAGE]
    FUNCTION: Tag Aggregation Page
-----------------------------------------------------------
    Parameters: ?tag=tag-name (URL parameter)
    Returns: HTML
    Description: Displays all content (blog posts, projects, prompts) with a specific tag
    UniqueID: 900070
=========================================================== */

require_once '../includes/db_config.php';

// Get tag from URL (supports both ?tag= and ?slug=)
$tag = isset($_GET['tag']) ? $_GET['tag'] : (isset($_GET['slug']) ? $_GET['slug'] : '');

if (empty($tag)) {
    header('Location: /index.php');
    exit;
}

// SEO Meta Variables
$page_title = "Tag: " . htmlspecialchars($tag);
$page_description = "Browse all articles, projects, and prompts tagged with '{$tag}'. Discover related content on ZCOM Studios.";
$page_keywords = $tag . ", ZCOM Studios, tagged content";
$page_type = "website";
$canonical_url = 'https://zcomstudios.com/tags/tag.php?tag=' . urlencode($tag);

// JSON-LD for CollectionPage
$json_ld = [
    "@context" => "https://schema.org",
    "@type" => "CollectionPage",
    "name" => "Content tagged: " . $tag,
    "description" => $page_description,
    "url" => $canonical_url
];

include '../includes/header.php';
?>
<link rel="stylesheet" href="../css/style-blog.css">
</head>
<body>
<?php
include '../includes/navbar.php';
?>

<main class="container my-5" style="z-index: 3; pointer-events: auto;">
    <section class="text-center mb-5">
        <h1 class="page-title">TAG: <?php echo strtoupper(htmlspecialchars($tag)); ?></h1>
        <p class="lead text-muted">All content tagged with "<?php echo htmlspecialchars($tag); ?>"</p>
    </section>

    <!-- Content Grid -->
    <section class="tag-content-section">
        <div class="articles-grid" id="tagContentGrid">
            <div class="articles-loading">
                <div class="loading-spinner"></div>
                <p>Loading content...</p>
            </div>
        </div>
    </section>
</main>

<script>
/* ========================================================
    //ANCHOR [TAG_AGGREGATION_LOGIC]
    FUNCTION: Tag Aggregation Logic
-----------------------------------------------------------
    Parameters: N/A
    Returns: N/A
    Description: Loads and displays all content with the specified tag
    UniqueID: 900071
=========================================================== */

(function() {
    'use strict';

    const tag = '<?php echo addslashes($tag); ?>';

    async function loadTagContent() {
        try {
            const response = await fetch(`/api/get_tag_content.php?tag=${encodeURIComponent(tag)}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (!data.success) {
                throw new Error('API returned error');
            }

            renderContent(data.content);
        } catch (error) {
            console.error('Error loading tag content:', error);
            const gridContainer = document.getElementById('tagContentGrid');
            if (gridContainer) {
                gridContainer.innerHTML = '<p class="text-center text-danger">Failed to load content. Please try again later.</p>';
            }
        }
    }

    function createContentCard(item) {
        const card = document.createElement('div');
        card.className = 'article-card';

        let categoryLabel = '';
        let url = '';
        
        if (item.type === 'blog') {
            categoryLabel = item.category || 'Blog';
            url = `/blog/post.php?slug=${item.slug}`;
        } else if (item.type === 'project') {
            categoryLabel = item.project_type ? item.project_type.replace('_', ' ').toUpperCase() : 'Project';
            url = `/portfolio/project.php?slug=${item.slug}`;
        } else if (item.type === 'prompt') {
            categoryLabel = item.category || 'Prompt';
            url = `/prompts/prompt.php?slug=${item.slug}`;
        }

        const typeLabel = item.type.toUpperCase();
        const thumbnail = item.thumbnail ? 
            `<div class="project-thumbnail" style="background-image: url('${item.thumbnail}');"></div>` : '';

        card.innerHTML = `
            ${thumbnail}
            <div class="article-card-header">
                <span class="article-category font-nav">${categoryLabel}</span>
                <span class="article-featured-badge font-mono">${typeLabel}</span>
            </div>
            <h3 class="article-card-title font-subheading">${item.title}</h3>
            <p class="article-card-excerpt">${item.description || item.excerpt || ''}</p>
            <div class="article-card-footer">
                <div class="article-meta">
                    <span class="article-date font-mono">${item.date || 'N/A'}</span>
                </div>
                <a href="${url}" class="btn-scifi article-read-btn">
                    VIEW
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; margin-left: 4px;">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>
        `;

        return card;
    }

    function renderContent(content) {
        const gridContainer = document.getElementById('tagContentGrid');
        if (!gridContainer) return;

        gridContainer.innerHTML = '';

        if (!content || content.length === 0) {
            gridContainer.innerHTML = '<p class="text-center text-muted">No content found with this tag.</p>';
            return;
        }

        content.forEach(item => {
            const card = createContentCard(item);
            gridContainer.appendChild(card);
        });
    }

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadTagContent);
    } else {
        loadTagContent();
    }
})();
</script>

<?php
include '../includes/footer.php';
?>

