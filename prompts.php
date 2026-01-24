<?php
// SEO Meta Variables
$page_title = "AI Prompt Library";
$page_description = "Browse curated AI prompts for development, writing, and creative tasks. Copy-ready prompts for ChatGPT, Claude, and other AI assistants.";
$page_keywords = "AI prompts, ChatGPT prompts, Claude prompts, AI templates, prompt engineering";
$page_type = "website";

include 'includes/header.php';
?>
</head>
<body class="cyber-theme">
<?php
include 'includes/navbar.php';
?>

<!-- ========================================================
    //ANCHOR [PROMPTS_PAGE_MAIN]
    FUNCTION: Prompts Library Page Main Content
-----------------------------------------------------------
    Parameters: N/A
    Returns: HTML
    Description: Prompts listing page with search, filtering, and prompt cards
    UniqueID: 900030
=========================================================== -->
<main class="container my-5" style="z-index: 3; pointer-events: auto;">
    
    <!-- Header Panel -->
    <div class="cyber-panel mb-4">
        <div class="cyber-panel-header">
            <span class="panel-title">PROMPT.LIBRARY</span>
            <span class="panel-id">AI.DAT</span>
        </div>
        <div class="cyber-panel-body text-center">
            <h1 class="page-title mb-3">AI PROMPT LIBRARY</h1>
            <p class="lead" style="color: #7090a8;">Production-tested prompts for coding, debugging, and development</p>
        </div>
    </div>

    <!-- Search and Filter Controls Panel -->
    <div class="cyber-panel mb-4">
        <div class="cyber-panel-header">
            <span class="panel-title">SEARCH.FILTER</span>
            <span class="panel-status">QUERY ACTIVE</span>
        </div>
        <div class="cyber-panel-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="cyber-input-group">
                        <span class="cyber-input-label">SEARCH</span>
                        <input type="text" id="searchInput" class="cyber-input" placeholder="Enter search query...">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="cyber-input-group">
                        <span class="cyber-input-label">CAT</span>
                        <select id="categoryFilter" class="cyber-input">
                            <option value="all">All Categories</option>
                            <option value="code-generation">Code Generation</option>
                            <option value="debugging">Debugging</option>
                            <option value="architecture">Architecture</option>
                            <option value="documentation">Documentation</option>
                            <option value="optimization">Optimization</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="cyber-input-group">
                        <span class="cyber-input-label">LVL</span>
                        <select id="difficultyFilter" class="cyber-input">
                            <option value="all">All Levels</option>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Prompts Grid Panel -->
    <div class="cyber-panel">
        <div class="cyber-panel-header">
            <span class="panel-title">PROMPT.LIST</span>
            <span class="panel-status">LOADING...</span>
        </div>
        <div class="cyber-panel-body">
            <div class="articles-grid" id="promptsGrid">
                <!-- Prompt cards will be dynamically loaded here -->
                <div class="articles-loading">
                    <div class="loading-spinner"></div>
                    <p>Loading prompts...</p>
                </div>
            </div>
        </div>
    </div>
    
</main>

<script src="./js/prompts.js"></script>

<?php
include 'includes/footer.php';
?>

