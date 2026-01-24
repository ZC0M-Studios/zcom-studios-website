        <!-- BATCOMPUTER Sidebar Navigation -->
        <?php
        /* ========================================================
            //ANCHOR [ADMIN_SIDEBAR_BATCOMPUTER]
            FUNCTION: Admin Sidebar - Batcomputer UI Style
        -----------------------------------------------------------
            UniqueID: 793102
        =========================================================== */
        ?>
        <aside class="admin-sidebar" id="adminSidebar">
            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="/admin/index.php" class="nav-link <?php echo ($current_page ?? '') === 'dashboard' ? 'active' : ''; ?>">
                            <i class="bi bi-speedometer2"></i>
                            <span>OVERVIEW</span>
                        </a>
                    </li>

                    <li class="nav-section">DATA MANAGEMENT</li>

                    <li class="nav-item">
                        <a href="/admin/articles/list.php" class="nav-link <?php echo ($current_page ?? '') === 'articles' ? 'active' : ''; ?>">
                            <i class="bi bi-file-earmark-text"></i>
                            <span>ARTICLES</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/projects/list.php" class="nav-link <?php echo ($current_page ?? '') === 'projects' ? 'active' : ''; ?>">
                            <i class="bi bi-folder"></i>
                            <span>PROJECTS</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/prompts/list.php" class="nav-link <?php echo ($current_page ?? '') === 'prompts' ? 'active' : ''; ?>">
                            <i class="bi bi-chat-square-quote"></i>
                            <span>PROMPTS</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/tools/list.php" class="nav-link <?php echo ($current_page ?? '') === 'tools' ? 'active' : ''; ?>">
                            <i class="bi bi-tools"></i>
                            <span>TOOLS</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/tags/list.php" class="nav-link <?php echo ($current_page ?? '') === 'tags' ? 'active' : ''; ?>">
                            <i class="bi bi-tags"></i>
                            <span>TAG REGISTRY</span>
                        </a>
                    </li>

                    <li class="nav-section">SYSTEM CONFIG</li>

                    <li class="nav-item">
                        <a href="/admin/users/list.php" class="nav-link <?php echo ($current_page ?? '') === 'users' ? 'active' : ''; ?>">
                            <i class="bi bi-people"></i>
                            <span>OPERATORS</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/settings.php" class="nav-link <?php echo ($current_page ?? '') === 'settings' ? 'active' : ''; ?>">
                            <i class="bi bi-gear"></i>
                            <span>SETTINGS</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/" class="nav-link" target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i>
                            <span>PUBLIC INTERFACE</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>
