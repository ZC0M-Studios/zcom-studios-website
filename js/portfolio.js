/* ========================================================
    //ANCHOR [PORTFOLIO_MANAGER]
    FUNCTION: Portfolio Management System
-----------------------------------------------------------
    Parameters: N/A
    Returns: N/A
    Description: Handles loading, displaying, and filtering portfolio projects
    UniqueID: 900011
=========================================================== */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        apiPath: '/api/get_projects.php',
        projectsPath: '/portfolio/project.php'
    };

    // State
    let allProjects = [];
    let currentFilter = 'all';

    /* ========================================================
        //ANCHOR [LOAD_PROJECTS_DATA]
        FUNCTION: loadProjectsData
    -----------------------------------------------------------
        Parameters: N/A
        Returns: Promise<Array>
        Description: Loads project data from database API
        UniqueID: 900012
    =========================================================== */
    async function loadProjectsData() {
        try {
            const response = await fetch(CONFIG.apiPath);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (!data.success) {
                throw new Error('API returned error');
            }

            allProjects = data.projects;
            return allProjects;
        } catch (error) {
            console.error('Error loading projects:', error);
            throw error;
        }
    }

    /* ========================================================
        //ANCHOR [CREATE_PROJECT_CARD]
        FUNCTION: createProjectCard
    -----------------------------------------------------------
        Parameters: project (Object)
        Returns: HTMLElement
        Description: Creates a project card element for grid view
        UniqueID: 900013
    =========================================================== */
    function createProjectCard(project) {
        const card = document.createElement('div');
        card.className = 'article-card project-card';
        card.dataset.type = project.type;

        const tags = project.tags_array ? project.tags_array.slice(0, 3).map(tag =>
            `<span class="article-tag font-mono">${tag}</span>`
        ).join('') : '';

        const thumbnail = project.thumbnail ? 
            `<div class="project-thumbnail" style="background-image: url('${project.thumbnail}');"></div>` : '';

        card.innerHTML = `
            ${thumbnail}
            <div class="article-card-header">
                <span class="article-category font-nav">${project.type.replace('_', ' ').toUpperCase()}</span>
                ${project.featured ? '<span class="article-featured-badge font-mono">FEATURED</span>' : ''}
                ${project.status === 'live' ? '<span class="article-featured-badge font-mono" style="background: #4caf50;">LIVE</span>' : ''}
            </div>
            <h3 class="article-card-title font-subheading">${project.name}</h3>
            <p class="article-card-excerpt">${project.tagline || project.description}</p>
            <div class="article-tags">
                ${tags}
            </div>
            <div class="article-card-footer">
                <div class="article-meta">
                    <span class="article-date font-mono">${project.date_display || project.date_completed_formatted || 'In Progress'}</span>
                    <span class="article-reading-time font-mono">${project.role || 'Developer'}</span>
                </div>
                <a href="${project.project_url}" class="btn-scifi article-read-btn">
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
        //ANCHOR [RENDER_PROJECTS_GRID]
        FUNCTION: renderProjectsGrid
    -----------------------------------------------------------
        Parameters: filter (String)
        Returns: void
        Description: Renders projects in grid layout with optional filtering
        UniqueID: 900014
    =========================================================== */
    function renderProjectsGrid(filter = 'all') {
        const gridContainer = document.getElementById('projectsGrid');

        if (!gridContainer) return;

        gridContainer.innerHTML = '';

        const filteredProjects = filter === 'all' ? 
            allProjects : 
            allProjects.filter(p => p.type === filter);

        if (filteredProjects.length === 0) {
            gridContainer.innerHTML = '<p class="text-center text-muted">No projects found for this filter.</p>';
            return;
        }

        filteredProjects.forEach(project => {
            const card = createProjectCard(project);
            gridContainer.appendChild(card);
        });
    }

    /* ========================================================
        //ANCHOR [INIT_FILTER_CONTROLS]
        FUNCTION: initFilterControls
    -----------------------------------------------------------
        Parameters: N/A
        Returns: void
        Description: Initializes filter button controls
        UniqueID: 900015
    =========================================================== */
    function initFilterControls() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        
        filterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                filterButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                currentFilter = this.dataset.filter;
                renderProjectsGrid(currentFilter);
            });
        });
    }

    // Initialize
    async function init() {
        try {
            await loadProjectsData();
            renderProjectsGrid();
            initFilterControls();
        } catch (error) {
            console.error('Failed to initialize portfolio:', error);
            const gridContainer = document.getElementById('projectsGrid');
            if (gridContainer) {
                gridContainer.innerHTML = '<p class="text-center text-danger">Failed to load projects. Please try again later.</p>';
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

