/* ========================================================
    //ANCHOR [ARTICLES_MANAGER]
    FUNCTION: Articles Management System
-----------------------------------------------------------
    Parameters: N/A
    Returns: N/A
    Description: Handles loading, displaying, and managing blog articles including carousel and grid view
    UniqueID: 890002
=========================================================== */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        apiPath: '/api/get_articles.php',
        articlesPath: '/articles/article.php',
        carouselAutoplayDelay: 5000,
        carouselTransitionDuration: 500
    };

    // State
    let articlesData = {};
    let featuredArticles = [];
    let allArticles = [];
    let currentCarouselIndex = 0;
    let carouselAutoplayInterval = null;

    /* ========================================================
        //ANCHOR [LOAD_ARTICLES_DATA]
        FUNCTION: loadArticlesData
    -----------------------------------------------------------
        Parameters: N/A
        Returns: Promise<Object>
        Description: Loads article data from database API
        UniqueID: 890003
    =========================================================== */
    async function loadArticlesData() {
        try {
            const response = await fetch(CONFIG.apiPath);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (!data.success) {
                throw new Error('API returned error');
            }

            // Process articles from API
            allArticles = data.articles;

            // Separate featured articles
            featuredArticles = allArticles.filter(article => article.featured === true);

            // If no featured articles, use the 3 most recent
            if (featuredArticles.length === 0) {
                featuredArticles = allArticles.slice(0, 3);
            }

            return data.articles;
        } catch (error) {
            console.error('Error loading articles:', error);
            throw error;
        }
    }

    /* ========================================================
        //ANCHOR [FORMAT_DATE]
        FUNCTION: formatDate
    -----------------------------------------------------------
        Parameters: dateString (String)
        Returns: String
        Description: Formats ISO date string to readable format
        UniqueID: 890004
    =========================================================== */
    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }

    /* ========================================================
        //ANCHOR [CREATE_CAROUSEL_ITEM]
        FUNCTION: createCarouselItem
    -----------------------------------------------------------
        Parameters: article (Object), index (Number)
        Returns: HTMLElement
        Description: Creates a carousel item element for featured article
        UniqueID: 890005
    =========================================================== */
    function createCarouselItem(article, index) {
        const item = document.createElement('div');
        item.className = `carousel-item ${index === 0 ? 'active' : ''}`;
        item.innerHTML = `
            <div class="carousel-content">
                <div class="carousel-badge">
                    <span class="font-mono">FEATURED</span>
                </div>
                <div class="carousel-category">
                    <span class="font-nav">${article.category || 'Technology'}</span>
                </div>
                <h2 class="carousel-title font-subheading">${article.title}</h2>
                <p class="carousel-excerpt">${article.excerpt}</p>
                <div class="carousel-meta">
                    <span class="carousel-date font-mono">${article.published_date_formatted}</span>
                    <span class="carousel-reading-time font-mono">${article.reading_time} min read</span>
                </div>
                <a href="${article.article_url}" class="btn-scifi-primary carousel-cta">
                    READ ARTICLE
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; margin-left: 8px;">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        `;
        return item;
    }

    /* ========================================================
        //ANCHOR [CREATE_ARTICLE_CARD]
        FUNCTION: createArticleCard
    -----------------------------------------------------------
        Parameters: article (Object)
        Returns: HTMLElement
        Description: Creates an article card element for grid view
        UniqueID: 890006
    =========================================================== */
    function createArticleCard(article) {
        const card = document.createElement('div');
        card.className = 'article-card';

        const tags = article.tags_array ? article.tags_array.slice(0, 3).map(tag =>
            `<span class="article-tag font-mono">${tag}</span>`
        ).join('') : '';

        card.innerHTML = `
            <div class="article-card-header">
                <span class="article-category font-nav">${article.category || 'Technology'}</span>
                ${article.featured ? '<span class="article-featured-badge font-mono">FEATURED</span>' : ''}
            </div>
            <h3 class="article-card-title font-subheading">${article.title}</h3>
            <p class="article-card-excerpt">${article.excerpt}</p>
            <div class="article-tags">
                ${tags}
            </div>
            <div class="article-card-footer">
                <div class="article-meta">
                    <span class="article-date font-mono">${article.published_date_formatted}</span>
                    <span class="article-reading-time font-mono">${article.reading_time} min</span>
                </div>
                <a href="${article.article_url}" class="btn-scifi article-read-btn">
                    READ
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; margin-left: 4px;">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>
        `;

        return card;
    }

    /* ========================================================
        //ANCHOR [RENDER_CAROUSEL]
        FUNCTION: renderCarousel
    -----------------------------------------------------------
        Parameters: N/A
        Returns: void
        Description: Renders the featured articles carousel
        UniqueID: 890007
    =========================================================== */
    function renderCarousel() {
        const carouselWrapper = document.getElementById('featuredCarousel');
        const indicatorsContainer = document.getElementById('carouselIndicators');

        if (!carouselWrapper || !indicatorsContainer) return;

        carouselWrapper.innerHTML = '';
        indicatorsContainer.innerHTML = '';

        featuredArticles.forEach((article, index) => {
            const item = createCarouselItem(article, index);
            carouselWrapper.appendChild(item);

            // Create indicator
            const indicator = document.createElement('button');
            indicator.className = `carousel-indicator ${index === 0 ? 'active' : ''}`;
            indicator.setAttribute('aria-label', `Go to slide ${index + 1}`);
            indicator.addEventListener('click', () => goToSlide(index));
            indicatorsContainer.appendChild(indicator);
        });

        startCarouselAutoplay();
    }

    /* ========================================================
        //ANCHOR [GO_TO_SLIDE]
        FUNCTION: goToSlide
    -----------------------------------------------------------
        Parameters: index (Number)
        Returns: void
        Description: Navigates to specific carousel slide
        UniqueID: 890008
    =========================================================== */
    function goToSlide(index) {
        const items = document.querySelectorAll('.carousel-item');
        const indicators = document.querySelectorAll('.carousel-indicator');

        if (items.length === 0) return;

        // Remove active class from all
        items.forEach(item => item.classList.remove('active'));
        indicators.forEach(ind => ind.classList.remove('active'));

        // Add active class to target
        currentCarouselIndex = (index + items.length) % items.length;
        items[currentCarouselIndex].classList.add('active');
        indicators[currentCarouselIndex].classList.add('active');

        // Reset autoplay
        stopCarouselAutoplay();
        startCarouselAutoplay();
    }

    /* ========================================================
        //ANCHOR [NEXT_SLIDE]
        FUNCTION: nextSlide
    -----------------------------------------------------------
        Parameters: N/A
        Returns: void
        Description: Advances carousel to next slide
        UniqueID: 890009
    =========================================================== */
    function nextSlide() {
        goToSlide(currentCarouselIndex + 1);
    }

    /* ========================================================
        //ANCHOR [PREV_SLIDE]
        FUNCTION: prevSlide
    -----------------------------------------------------------
        Parameters: N/A
        Returns: void
        Description: Moves carousel to previous slide
        UniqueID: 890010
    =========================================================== */
    function prevSlide() {
        goToSlide(currentCarouselIndex - 1);
    }

    /* ========================================================
        //ANCHOR [START_CAROUSEL_AUTOPLAY]
        FUNCTION: startCarouselAutoplay
    -----------------------------------------------------------
        Parameters: N/A
        Returns: void
        Description: Starts automatic carousel rotation
        UniqueID: 890011
    =========================================================== */
    function startCarouselAutoplay() {
        if (carouselAutoplayInterval) {
            clearInterval(carouselAutoplayInterval);
        }
        carouselAutoplayInterval = setInterval(nextSlide, CONFIG.carouselAutoplayDelay);
    }

    /* ========================================================
        //ANCHOR [STOP_CAROUSEL_AUTOPLAY]
        FUNCTION: stopCarouselAutoplay
    -----------------------------------------------------------
        Parameters: N/A
        Returns: void
        Description: Stops automatic carousel rotation
        UniqueID: 890012
    =========================================================== */
    function stopCarouselAutoplay() {
        if (carouselAutoplayInterval) {
            clearInterval(carouselAutoplayInterval);
            carouselAutoplayInterval = null;
        }
    }

    /* ========================================================
        //ANCHOR [RENDER_ARTICLES_GRID]
        FUNCTION: renderArticlesGrid
    -----------------------------------------------------------
        Parameters: N/A
        Returns: void
        Description: Renders all articles in grid layout
        UniqueID: 890013
    =========================================================== */
    function renderArticlesGrid() {
        const gridContainer = document.getElementById('articlesGrid');

        if (!gridContainer) return;

        gridContainer.innerHTML = '';

        allArticles.forEach(article => {
            const card = createArticleCard(article);
            gridContainer.appendChild(card);
        });
    }

    /* ========================================================
        //ANCHOR [INIT_CAROUSEL_CONTROLS]
        FUNCTION: initCarouselControls
    -----------------------------------------------------------
        Parameters: N/A
        Returns: void
        Description: Initializes carousel navigation controls
        UniqueID: 890014
    =========================================================== */
    function initCarouselControls() {
        const prevBtn = document.getElementById('carouselPrev');
        const nextBtn = document.getElementById('carouselNext');

        if (prevBtn) {
            prevBtn.addEventListener('click', prevSlide);
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', nextSlide);
        }

        // Pause autoplay on hover
        const carouselWrapper = document.getElementById('featuredCarousel');
        if (carouselWrapper) {
            carouselWrapper.addEventListener('mouseenter', stopCarouselAutoplay);
            carouselWrapper.addEventListener('mouseleave', startCarouselAutoplay);
        }

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                prevSlide();
            } else if (e.key === 'ArrowRight') {
                nextSlide();
            }
        });
    }

    /* ========================================================
        //ANCHOR [INIT_ARTICLES]
        FUNCTION: init
    -----------------------------------------------------------
        Parameters: N/A
        Returns: Promise<void>
        Description: Initializes the articles system
        UniqueID: 890015
    =========================================================== */
    async function init() {
        try {
            await loadArticlesData();
            renderCarousel();
            renderArticlesGrid();
            initCarouselControls();
        } catch (error) {
            console.error('Failed to initialize articles:', error);

            // Show error message
            const carouselWrapper = document.getElementById('featuredCarousel');
            const gridContainer = document.getElementById('articlesGrid');

            const errorMessage = '<div class="error-message">Failed to load articles. Please try again later.</div>';

            if (carouselWrapper) {
                carouselWrapper.innerHTML = errorMessage;
            }

            if (gridContainer) {
                gridContainer.innerHTML = errorMessage;
            }
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
