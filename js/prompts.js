/* ========================================================
    //ANCHOR [PROMPTS_MANAGER]
    FUNCTION: Prompts Library Management System
-----------------------------------------------------------
    Parameters: N/A
    Returns: N/A
    Description: Handles loading, displaying, searching, and filtering AI prompts
    UniqueID: 900031
=========================================================== */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        apiPath: '/api/get_prompts.php',
        promptsPath: '/prompts/prompt.php'
    };

    // State
    let allPrompts = [];
    let filteredPrompts = [];

    /* ========================================================
        //ANCHOR [LOAD_PROMPTS_DATA]
        FUNCTION: loadPromptsData
    -----------------------------------------------------------
        Parameters: N/A
        Returns: Promise<Array>
        Description: Loads prompt data from database API
        UniqueID: 900032
    =========================================================== */
    async function loadPromptsData() {
        try {
            const response = await fetch(CONFIG.apiPath);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (!data.success) {
                throw new Error('API returned error');
            }

            allPrompts = data.prompts;
            filteredPrompts = allPrompts;
            return allPrompts;
        } catch (error) {
            console.error('Error loading prompts:', error);
            throw error;
        }
    }

    /* ========================================================
        //ANCHOR [CREATE_PROMPT_CARD]
        FUNCTION: createPromptCard
    -----------------------------------------------------------
        Parameters: prompt (Object)
        Returns: HTMLElement
        Description: Creates a prompt card element for grid view
        UniqueID: 900033
    =========================================================== */
    function createPromptCard(prompt) {
        const card = document.createElement('div');
        card.className = 'article-card prompt-card';

        const difficultyColors = {
            'beginner': '#4caf50',
            'intermediate': '#ff9800',
            'advanced': '#f44336'
        };

        const tags = prompt.tags_by_category && prompt.tags_by_category.technology ? 
            prompt.tags_by_category.technology.slice(0, 3).map(tag =>
                `<span class="article-tag font-mono">${tag}</span>`
            ).join('') : '';

        const rating = '★'.repeat(prompt.success_rating) + '☆'.repeat(5 - prompt.success_rating);

        card.innerHTML = `
            <div class="article-card-header">
                <span class="article-category font-nav">${prompt.category || 'General'}</span>
                <span class="article-featured-badge font-mono" style="background: ${difficultyColors[prompt.difficulty]};">
                    ${prompt.difficulty.toUpperCase()}
                </span>
            </div>
            <h3 class="article-card-title font-subheading">${prompt.title}</h3>
            <p class="article-card-excerpt">${prompt.description}</p>
            <div class="article-tags">
                ${tags}
            </div>
            <div class="article-card-footer">
                <div class="article-meta">
                    <span class="article-date font-mono">${prompt.ai_model || 'AI'}</span>
                    <span class="article-reading-time font-mono" title="Success Rating">${rating}</span>
                </div>
                <a href="${prompt.prompt_url}" class="btn-scifi article-read-btn">
                    VIEW
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; margin-left: 4px;">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>
        `;

        return card;
    }

    /* ========================================================
        //ANCHOR [FILTER_PROMPTS]
        FUNCTION: filterPrompts
    -----------------------------------------------------------
        Parameters: N/A
        Returns: void
        Description: Filters prompts based on search and filter criteria
        UniqueID: 900034
    =========================================================== */
    function filterPrompts() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const category = document.getElementById('categoryFilter').value;
        const difficulty = document.getElementById('difficultyFilter').value;

        filteredPrompts = allPrompts.filter(prompt => {
            const matchesSearch = !searchTerm || 
                prompt.title.toLowerCase().includes(searchTerm) ||
                prompt.description.toLowerCase().includes(searchTerm);
            
            const matchesCategory = category === 'all' || prompt.category === category;
            const matchesDifficulty = difficulty === 'all' || prompt.difficulty === difficulty;

            return matchesSearch && matchesCategory && matchesDifficulty;
        });

        renderPromptsGrid();
    }

    /* ========================================================
        //ANCHOR [RENDER_PROMPTS_GRID]
        FUNCTION: renderPromptsGrid
    -----------------------------------------------------------
        Parameters: N/A
        Returns: void
        Description: Renders prompts in grid layout
        UniqueID: 900035
    =========================================================== */
    function renderPromptsGrid() {
        const gridContainer = document.getElementById('promptsGrid');

        if (!gridContainer) return;

        gridContainer.innerHTML = '';

        if (filteredPrompts.length === 0) {
            gridContainer.innerHTML = '<p class="text-center text-muted">No prompts found matching your criteria.</p>';
            return;
        }

        filteredPrompts.forEach(prompt => {
            const card = createPromptCard(prompt);
            gridContainer.appendChild(card);
        });
    }

    // Initialize
    async function init() {
        try {
            await loadPromptsData();
            renderPromptsGrid();

            // Setup event listeners
            document.getElementById('searchInput').addEventListener('input', filterPrompts);
            document.getElementById('categoryFilter').addEventListener('change', filterPrompts);
            document.getElementById('difficultyFilter').addEventListener('change', filterPrompts);
        } catch (error) {
            console.error('Failed to initialize prompts:', error);
            const gridContainer = document.getElementById('promptsGrid');
            if (gridContainer) {
                gridContainer.innerHTML = '<p class="text-center text-danger">Failed to load prompts. Please try again later.</p>';
            }
        }
    }

    // Start when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

