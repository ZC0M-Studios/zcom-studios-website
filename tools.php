<?php
// SEO Meta Variables
$page_title = "Developer Tools";
$page_description = "Free online developer tools including JSON formatter, word counter, and image compressor. Useful utilities for web developers.";
$page_keywords = "developer tools, JSON formatter, word counter, image compressor, online tools, web utilities";
$page_type = "website";

include 'includes/header.php';
?>
</head>
<body class="cyber-theme">
<?php
include 'includes/navbar.php';
?>

<!-- ========================================================
    //ANCHOR [TOOLS_PAGE_MAIN]
    FUNCTION: Tools Directory Page Main Content
-----------------------------------------------------------
    Parameters: N/A
    Returns: HTML
    Description: Tools listing page showcasing all available developer utilities
    UniqueID: 900050
=========================================================== -->
<main class="container my-5" style="z-index: 3; pointer-events: auto;">
    
    <!-- Header Panel -->
    <div class="cyber-panel mb-4">
        <div class="cyber-panel-header">
            <span class="panel-title">TOOLS.INDEX</span>
            <span class="panel-id">UTIL.DAT</span>
        </div>
        <div class="cyber-panel-body text-center">
            <h1 class="page-title mb-3">DEVELOPER TOOLS</h1>
            <p class="lead" style="color: #7090a8;">Free, client-side utilities for developers</p>
        </div>
    </div>

    <!-- Tools Grid Panel -->
    <div class="cyber-panel">
        <div class="cyber-panel-header">
            <span class="panel-title">TOOL.LIST</span>
            <span class="panel-status">AVAILABLE</span>
        </div>
        <div class="cyber-panel-body">
            <div class="articles-grid" id="toolsGrid">
                <!-- Tool cards will be dynamically loaded here -->
                <div class="articles-loading">
                    <div class="loading-spinner"></div>
                    <p>Loading tools...</p>
                </div>
            </div>
        </div>
    </div>
    
</main>

<script>
(function() {
    'use strict';

    // Hardcoded tools list (can be moved to API later)
    const tools = [
        {
            name: 'JSON Formatter',
            slug: 'json-formatter',
            description: 'Format, validate, and beautify JSON data with syntax highlighting',
            category: 'Data Processing',
            icon: '{ }',
            url: '/tools/json-formatter.php'
        },
        {
            name: 'Word Counter',
            slug: 'word-counter',
            description: 'Count words, characters, sentences, and estimate reading time',
            category: 'Text Analysis',
            icon: 'Aa',
            url: '/tools/word-counter.php'
        },
        {
            name: 'Image Compressor',
            slug: 'image-compressor',
            description: 'Compress images client-side to reduce file size without quality loss',
            category: 'Media',
            icon: '🖼️',
            url: '/tools/image-compressor.php'
        }
    ];

    function createToolCard(tool) {
        const card = document.createElement('div');
        card.className = 'article-card tool-card';

        card.innerHTML = `
            <div class="article-card-header">
                <span class="article-category font-nav">${tool.category}</span>
            </div>
            <div class="text-center my-3" style="font-size: 3rem;">${tool.icon}</div>
            <h3 class="article-card-title font-subheading">${tool.name}</h3>
            <p class="article-card-excerpt">${tool.description}</p>
            <div class="article-card-footer">
                <a href="${tool.url}" class="btn-scifi-primary w-100 text-center">
                    USE TOOL
                </a>
            </div>
        `;

        return card;
    }

    function renderTools() {
        const gridContainer = document.getElementById('toolsGrid');
        if (!gridContainer) return;

        gridContainer.innerHTML = '';

        tools.forEach(tool => {
            const card = createToolCard(tool);
            gridContainer.appendChild(card);
        });
    }

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderTools);
    } else {
        renderTools();
    }
})();
</script>

<?php
include 'includes/footer.php';
?>

