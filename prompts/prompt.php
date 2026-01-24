<?php
/* ========================================================
    //ANCHOR [PROMPT_DETAIL_PAGE]
    FUNCTION: Individual Prompt Detail Page
-----------------------------------------------------------
    Parameters: ?slug=prompt-slug (URL parameter)
    Returns: HTML
    Description: Displays full prompt details with copy functionality and related content
    UniqueID: 900040
=========================================================== */

require_once '../includes/db_config.php';

// Get prompt slug from URL
$prompt_slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($prompt_slug)) {
    header('Location: /prompts.php');
    exit;
}

// Query prompt from database
try {
    $stmt = $db->prepare("
        SELECT
            p.*,
            GROUP_CONCAT(DISTINCT CONCAT(tr.display_name, ':', tr.category) ORDER BY tr.display_name SEPARATOR '|||') as tags
        FROM prompts p
        LEFT JOIN prompt_tags pt ON p.id = pt.prompt_id
        LEFT JOIN tags_registry tr ON pt.tag_id = tr.id
        WHERE p.slug = :slug AND p.visibility = 'public'
        GROUP BY p.id
    ");

    $stmt->execute(['slug' => $prompt_slug]);
    $prompt = $stmt->fetch();

    if (!$prompt) {
        header('Location: /prompts.php');
        exit;
    }

    // Parse tags by category
    $tags_by_category = [];
    if ($prompt['tags']) {
        $tagPairs = explode('|||', $prompt['tags']);
        foreach ($tagPairs as $pair) {
            $parts = explode(':', $pair);
            if (count($parts) >= 2) {
                $tag = $parts[0];
                $cat = $parts[1];
                if (!isset($tags_by_category[$cat])) {
                    $tags_by_category[$cat] = [];
                }
                $tags_by_category[$cat][] = $tag;
            }
        }
    }

    // SEO Meta Variables
    $page_title = $prompt['title'];
    $page_description = $prompt['description'] ?? 'AI prompt template for developers and creators.';
    $page_keywords = implode(', ', array_merge(...array_values($tags_by_category ?: [[]]))) ?: 'AI prompt, ChatGPT, Claude';
    $page_type = 'website';
    $canonical_url = 'https://zcomstudios.com/prompts/prompt.php?slug=' . urlencode($prompt['slug']);

    // JSON-LD for HowTo (prompt as a template)
    $json_ld = [
        "@context" => "https://schema.org",
        "@type" => "HowTo",
        "name" => $prompt['title'],
        "description" => $page_description,
        "step" => [
            [
                "@type" => "HowToStep",
                "text" => "Copy the prompt below and paste it into your AI assistant"
            ]
        ]
    ];

} catch (PDOException $e) {
    error_log("Prompt Query Error: " . $e->getMessage());
    header('Location: /prompts.php');
    exit;
}

include '../includes/header.php';
?>
<link rel="stylesheet" href="../css/style-blog.css">
<style>
.prompt-box {
    background: rgba(15, 20, 40, 0.9);
    border: 1px solid #64b5f6;
    border-radius: 8px;
    padding: 1.5rem;
    margin: 2rem 0;
    position: relative;
}
.prompt-text {
    font-family: 'Courier New', monospace;
    white-space: pre-wrap;
    color: #e0e0e0;
    line-height: 1.6;
}
.copy-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
}
.rating-stars {
    color: #ffd700;
    font-size: 1.5rem;
}
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin: 2rem 0;
}
.info-item {
    background: rgba(100, 181, 246, 0.1);
    border: 1px solid #64b5f6;
    border-radius: 4px;
    padding: 1rem;
}
.info-label {
    font-size: 0.75rem;
    color: #64b5f6;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.5rem;
}
.info-value {
    font-size: 1rem;
    color: #e0e0e0;
}
</style>
</head>
<body>
<?php
include '../includes/navbar.php';
?>

<main class="container my-5" style="z-index: 3; pointer-events: auto;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="/prompts.php">Prompts</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($prompt['title']); ?></li>
        </ol>
    </nav>

    <!-- Prompt Header -->
    <article class="prompt-detail">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <span class="article-category font-nav">PROMPT</span>
                <?php if ($prompt['featured']): ?>
                <span class="article-featured-badge font-mono">FEATURED</span>
                <?php endif; ?>
            </div>
            <div class="text-muted font-mono">
                <?php echo $prompt['copies'] ?? 0; ?> copies
            </div>
        </div>

        <h1 class="page-title mb-3"><?php echo htmlspecialchars($prompt['title']); ?></h1>
        <p class="lead text-muted"><?php echo htmlspecialchars($prompt['description'] ?? ''); ?></p>

        <!-- The Prompt -->
        <section class="my-4">
            <h2 class="section-title mb-3">THE PROMPT</h2>
            <div class="prompt-box">
                <button class="btn-scifi-primary copy-btn" onclick="copyPrompt()">
                    COPY PROMPT
                </button>
                <pre class="prompt-text" id="promptText"><?php echo htmlspecialchars($prompt['prompt_text'] ?? ''); ?></pre>
            </div>
        </section>

        <!-- Info -->
        <section class="my-4">
            <h2 class="section-title mb-3">INFO</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Created</div>
                    <div class="info-value"><?php echo $prompt['created_at'] ? date('F j, Y', strtotime($prompt['created_at'])) : 'N/A'; ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Last Updated</div>
                    <div class="info-value"><?php echo $prompt['updated_at'] ? date('F j, Y', strtotime($prompt['updated_at'])) : 'N/A'; ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Times Copied</div>
                    <div class="info-value"><?php echo number_format($prompt['copies'] ?? 0); ?></div>
                </div>
            </div>
        </section>

        <!-- Tags -->
        <?php if (!empty($tags_by_category)): ?>
        <section class="my-4">
            <h2 class="section-title mb-3">TAGS</h2>
            <?php foreach ($tags_by_category as $category => $tags): ?>
                <div class="mb-2">
                    <strong class="text-metallic font-mono" style="font-size: 0.875rem;">
                        <?php echo strtoupper($category); ?>:
                    </strong>
                    <?php foreach ($tags as $tag): ?>
                        <a href="/tags/tag.php?tag=<?php echo urlencode($tag); ?>" class="article-tag font-mono">
                            <?php echo htmlspecialchars($tag); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </section>
        <?php endif; ?>

    </article>
</main>

<script>
function copyPrompt() {
    const promptText = document.getElementById('promptText').textContent;
    navigator.clipboard.writeText(promptText).then(() => {
        const btn = document.querySelector('.copy-btn');
        const originalText = btn.textContent;
        btn.textContent = 'COPIED!';
        btn.style.background = '#4caf50';

        // Update copy count
        fetch('/api/increment_prompt_copy.php?id=<?php echo $prompt['id']; ?>');

        setTimeout(() => {
            btn.textContent = originalText;
            btn.style.background = '';
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy:', err);
        alert('Failed to copy prompt. Please try selecting and copying manually.');
    });
}
</script>

<?php
include '../includes/footer.php';
?>


