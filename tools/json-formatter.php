<?php
// SEO Meta Variables
$page_title = "JSON Formatter & Validator";
$page_description = "Free online JSON formatter and validator. Beautify, minify, and validate JSON data instantly in your browser. No data is sent to any server.";
$page_keywords = "JSON formatter, JSON validator, JSON beautifier, JSON minifier, online JSON tool, format JSON";
$page_type = "website";
$canonical_url = 'https://zcomstudios.com/tools/json-formatter.php';

// JSON-LD for SoftwareApplication
$json_ld = [
    "@context" => "https://schema.org",
    "@type" => "SoftwareApplication",
    "name" => "JSON Formatter & Validator",
    "description" => $page_description,
    "applicationCategory" => "DeveloperApplication",
    "operatingSystem" => "Any",
    "offers" => [
        "@type" => "Offer",
        "price" => "0",
        "priceCurrency" => "USD"
    ],
    "provider" => [
        "@type" => "Organization",
        "name" => "ZCOM Studios"
    ]
];

include '../includes/header.php';
?>
<link rel="stylesheet" href="../css/style-blog.css">
<style>
.tool-container {
    max-width: 1200px;
    margin: 0 auto;
}
.json-textarea {
    width: 100%;
    min-height: 300px;
    font-family: 'Courier New', monospace;
    background: rgba(15, 20, 40, 0.9);
    border: 1px solid #64b5f6;
    color: #e0e0e0;
    padding: 1rem;
    border-radius: 8px;
    resize: vertical;
}
.json-output {
    background: rgba(15, 20, 40, 0.9);
    border: 1px solid #64b5f6;
    padding: 1rem;
    border-radius: 8px;
    min-height: 300px;
    overflow-x: auto;
}
.json-output pre {
    margin: 0;
    color: #e0e0e0;
    font-family: 'Courier New', monospace;
}
.error-message {
    color: #f44336;
    padding: 1rem;
    background: rgba(244, 67, 54, 0.1);
    border: 1px solid #f44336;
    border-radius: 4px;
    margin-top: 1rem;
}
.success-message {
    color: #4caf50;
    padding: 1rem;
    background: rgba(76, 175, 80, 0.1);
    border: 1px solid #4caf50;
    border-radius: 4px;
    margin-top: 1rem;
}
.tool-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin: 1rem 0;
}
</style>
</head>
<body>
<?php
include '../includes/navbar.php';
?>

<!-- ========================================================
    //ANCHOR [JSON_FORMATTER_TOOL]
    FUNCTION: JSON Formatter & Validator Tool
-----------------------------------------------------------
    Parameters: N/A
    Returns: HTML
    Description: Client-side JSON formatter with validation and syntax highlighting
    UniqueID: 900060
=========================================================== -->
<main class="container my-5 tool-container" style="z-index: 3; pointer-events: auto;">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="/tools.php">Tools</a></li>
            <li class="breadcrumb-item active">JSON Formatter</li>
        </ol>
    </nav>

    <section class="text-center mb-5">
        <h1 class="page-title">JSON FORMATTER & VALIDATOR</h1>
        <p class="lead text-muted">Format, validate, and beautify JSON data instantly</p>
    </section>

    <section>
        <h2 class="section-title mb-3">INPUT JSON</h2>
        <textarea id="jsonInput" class="json-textarea" placeholder='Paste your JSON here... e.g., {"name":"John","age":30}'></textarea>
        
        <div class="tool-actions">
            <button onclick="formatJSON()" class="btn-scifi-primary">FORMAT JSON</button>
            <button onclick="minifyJSON()" class="btn-scifi">MINIFY</button>
            <button onclick="validateJSON()" class="btn-scifi">VALIDATE</button>
            <button onclick="copyOutput()" class="btn-scifi">COPY OUTPUT</button>
            <button onclick="clearAll()" class="btn-scifi-danger">CLEAR</button>
        </div>

        <div id="message"></div>

        <h2 class="section-title mb-3 mt-4">OUTPUT</h2>
        <div id="jsonOutput" class="json-output">
            <pre id="outputPre">Formatted JSON will appear here...</pre>
        </div>
    </section>

    <section class="my-5">
        <h2 class="section-title mb-3">HOW TO USE</h2>
        <ul class="feature-list">
            <li>Paste your JSON data into the input textarea</li>
            <li>Click "FORMAT JSON" to beautify and indent your JSON</li>
            <li>Click "MINIFY" to compress JSON into a single line</li>
            <li>Click "VALIDATE" to check if your JSON is valid</li>
            <li>Click "COPY OUTPUT" to copy the formatted result</li>
        </ul>
    </section>

    <section class="my-5">
        <h2 class="section-title mb-3">BENEFITS</h2>
        <p>This tool helps developers quickly format and validate JSON data without needing external services. All processing happens in your browser - your data never leaves your computer, ensuring privacy and security.</p>
    </section>
</main>

<script>
/* ========================================================
    //ANCHOR [JSON_FORMATTER_LOGIC]
    FUNCTION: JSON Formatter Tool Logic
-----------------------------------------------------------
    Parameters: N/A
    Returns: N/A
    Description: Client-side JSON formatting, validation, and manipulation
    UniqueID: 900061
=========================================================== */

function showMessage(message, type = 'success') {
    const messageDiv = document.getElementById('message');
    messageDiv.className = type === 'error' ? 'error-message' : 'success-message';
    messageDiv.textContent = message;
    messageDiv.style.display = 'block';
    
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 3000);
}

function formatJSON() {
    const input = document.getElementById('jsonInput').value.trim();
    const output = document.getElementById('outputPre');
    
    if (!input) {
        showMessage('Please enter JSON data', 'error');
        return;
    }
    
    try {
        const parsed = JSON.parse(input);
        const formatted = JSON.stringify(parsed, null, 2);
        output.textContent = formatted;
        showMessage('JSON formatted successfully!');
    } catch (error) {
        showMessage('Invalid JSON: ' + error.message, 'error');
        output.textContent = 'Error: ' + error.message;
    }
}

function minifyJSON() {
    const input = document.getElementById('jsonInput').value.trim();
    const output = document.getElementById('outputPre');
    
    if (!input) {
        showMessage('Please enter JSON data', 'error');
        return;
    }
    
    try {
        const parsed = JSON.parse(input);
        const minified = JSON.stringify(parsed);
        output.textContent = minified;
        showMessage('JSON minified successfully!');
    } catch (error) {
        showMessage('Invalid JSON: ' + error.message, 'error');
    }
}

function validateJSON() {
    const input = document.getElementById('jsonInput').value.trim();
    
    if (!input) {
        showMessage('Please enter JSON data', 'error');
        return;
    }
    
    try {
        JSON.parse(input);
        showMessage('✓ Valid JSON!');
    } catch (error) {
        showMessage('✗ Invalid JSON: ' + error.message, 'error');
    }
}

function copyOutput() {
    const output = document.getElementById('outputPre').textContent;
    
    if (output === 'Formatted JSON will appear here...') {
        showMessage('No output to copy', 'error');
        return;
    }
    
    navigator.clipboard.writeText(output).then(() => {
        showMessage('Copied to clipboard!');
    }).catch(() => {
        showMessage('Failed to copy', 'error');
    });
}

function clearAll() {
    document.getElementById('jsonInput').value = '';
    document.getElementById('outputPre').textContent = 'Formatted JSON will appear here...';
    showMessage('Cleared!');
}
</script>

<?php
include '../includes/footer.php';
?>

