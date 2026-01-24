<?php
$page_title = "Sci-Fi UI Demo";
include 'includes/header.php';
?>
</head>
<body>
<?php
include 'includes/navbar.php';
?>

<?php
/* ========================================================
    //ANCHOR [SCIFI_UI_DEMO_PAGE]
    FUNCTION: N/A
-----------------------------------------------------------
    Parameters: N/A
    Returns: N/A
    Description: Demo page showcasing futuristic sci-fi UI components inspired by cyberpunk game menus
    UniqueID: 789100
=========================================================== */
?>
    <!-- <canvas id="headerCanvas"></canvas> -->

    <main class="container my-5" style="padding-top: 100px; padding-bottom: 200px;">
        
        <section class="text-center mb-5">
            <h2 class="text-metallic mb-4">FUTURISTIC UI COMPONENTS</h2>
            <p class="text-muted">Cyberpunk-inspired menu system with glowing borders and interactive states</p>
        </section>

        <div class="row g-5">
            <!-- Column 1: Basic Menu -->
            <div class="col-md-6 col-lg-4">
                <h3 class="text-cyan mb-4" style="color: #64b5f6; font-size: 1.2rem; letter-spacing: 2px;">EQUIPMENT MENU</h3>
                <div class="scifi-menu">
                    <div class="scifi-menu-item">
                        <span class="scifi-menu-glow-bar"></span>
                        <span class="scifi-menu-label">HELMETS</span>
                        <span class="scifi-menu-value">12%</span>
                    </div>
                    <div class="scifi-menu-item disabled">
                        <span class="scifi-menu-glow-bar"></span>
                        <span class="scifi-menu-label">BODY ARMOR</span>
                        <span class="scifi-menu-value">0%</span>
                    </div>
                    <div class="scifi-menu-item">
                        <span class="scifi-menu-glow-bar"></span>
                        <span class="scifi-menu-label">VISORS</span>
                        <span class="scifi-menu-value">25%</span>
                    </div>
                    <div class="scifi-menu-item active">
                        <span class="scifi-menu-glow-bar"></span>
                        <span class="scifi-menu-label">ARMOR FX</span>
                        <span class="scifi-menu-value">50%</span>
                    </div>
                    <div class="scifi-menu-item">
                        <span class="scifi-menu-glow-bar"></span>
                        <span class="scifi-menu-label">COLORS</span>
                        <span class="scifi-menu-value">12%</span>
                    </div>
                </div>
            </div>

            <!-- Column 2: Menu with Badges -->
            <div class="col-md-6 col-lg-4">
                <h3 class="text-cyan mb-4" style="color: #64b5f6; font-size: 1.2rem; letter-spacing: 2px;">INVENTORY</h3>
                <div class="scifi-menu">
                    <div class="scifi-menu-item">
                        <span class="scifi-menu-glow-bar"></span>
                        <span class="scifi-menu-label">
                            <span class="scifi-menu-badge">3</span>
                            HELMETS
                        </span>
                        <span class="scifi-menu-value">12%</span>
                    </div>
                    <div class="scifi-menu-item disabled">
                        <span class="scifi-menu-glow-bar"></span>
                        <span class="scifi-menu-label">
                            <span class="scifi-menu-badge" style="opacity: 0.4;">🔒</span>
                            BODY ARMOR
                        </span>
                        <span class="scifi-menu-value">0%</span>
                    </div>
                    <div class="scifi-menu-item">
                        <span class="scifi-menu-glow-bar"></span>
                        <span class="scifi-menu-label">
                            <span class="scifi-menu-badge">999</span>
                            VISORS
                        </span>
                        <span class="scifi-menu-value">25%</span>
                    </div>
                    <div class="scifi-menu-item active">
                        <span class="scifi-menu-glow-bar"></span>
                        <span class="scifi-menu-label">ARMOR FX</span>
                        <span class="scifi-menu-value">50%</span>
                    </div>
                    <div class="scifi-menu-item">
                        <span class="scifi-menu-glow-bar"></span>
                        <span class="scifi-menu-label">COLORS</span>
                        <span class="scifi-menu-value">12%</span>
                    </div>
                </div>
            </div>

            <!-- Column 3: Menu with Icons -->
            <div class="col-md-6 col-lg-4">
                <h3 class="text-cyan mb-4" style="color: #64b5f6; font-size: 1.2rem; letter-spacing: 2px;">LOADOUT</h3>
                <div class="scifi-menu">
                    <div class="scifi-menu-item">
                        <span class="scifi-menu-glow-bar"></span>
                        <span class="scifi-menu-label">
                            <span class="scifi-menu-badge">💎</span>
                            HELMETS
                        </span>
                        <span class="scifi-menu-value">12%</span>
                    </div>
                    <div class="scifi-menu-item disabled">
                        <span class="scifi-menu-glow-bar"></span>
                        <span class="scifi-menu-label">BODY ARMOR</span>
                        <span class="scifi-menu-value">0%</span>
                    </div>
                    <div class="scifi-menu-item">
                        <span class="scifi-menu-glow-bar"></span>
                        <span class="scifi-menu-label">
                            <span class="scifi-menu-badge">💠</span>
                            VISORS
                        </span>
                        <span class="scifi-menu-value">25%</span>
                    </div>
                    <div class="scifi-menu-item active">
                        <span class="scifi-menu-glow-bar"></span>
                        <span class="scifi-menu-label">ARMOR FX</span>
                        <span class="scifi-menu-value">50%</span>
                    </div>
                    <div class="scifi-menu-item">
                        <span class="scifi-menu-glow-bar"></span>
                        <span class="scifi-menu-label">COLORS</span>
                        <span class="scifi-menu-value">12%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sci-Fi Buttons Section -->
        <section class="mt-5 pt-5">
            <h2 class="text-metallic mb-4 text-center">SCI-FI BUTTONS</h2>
            <p class="text-muted text-center mb-5">Interactive buttons with angled corners and glowing effects</p>

            <div class="row g-4">
                <!-- Default Buttons -->
                <div class="col-md-6">
                    <h4 class="text-cyan mb-3" style="color: #64b5f6; font-size: 1rem; letter-spacing: 2px;">DEFAULT BUTTONS</h4>
                    <div class="d-flex flex-wrap gap-3">
                        <button class="btn-scifi">DEFAULT</button>
                        <button class="btn-scifi btn-scifi-sm">SMALL</button>
                        <button class="btn-scifi btn-scifi-lg">LARGE</button>
                        <button class="btn-scifi disabled">DISABLED</button>
                    </div>
                </div>

                <!-- Primary Buttons -->
                <div class="col-md-6">
                    <h4 class="text-cyan mb-3" style="color: #64b5f6; font-size: 1rem; letter-spacing: 2px;">PRIMARY BUTTONS</h4>
                    <div class="d-flex flex-wrap gap-3">
                        <button class="btn-scifi btn-scifi-primary">CONFIRM</button>
                        <button class="btn-scifi btn-scifi-primary btn-scifi-sm">ACCEPT</button>
                        <button class="btn-scifi btn-scifi-primary btn-scifi-lg">ACTIVATE</button>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="col-md-6">
                    <h4 class="text-cyan mb-3" style="color: #64b5f6; font-size: 1rem; letter-spacing: 2px;">ACTION BUTTONS</h4>
                    <div class="d-flex flex-wrap gap-3">
                        <button class="btn-scifi btn-scifi-success">SUCCESS</button>
                        <button class="btn-scifi btn-scifi-danger">DANGER</button>
                        <button class="btn-scifi btn-scifi-success btn-scifi-sm">SAVE</button>
                        <button class="btn-scifi btn-scifi-danger btn-scifi-sm">DELETE</button>
                    </div>
                </div>

                <!-- Link Buttons -->
                <div class="col-md-6">
                    <h4 class="text-cyan mb-3" style="color: #64b5f6; font-size: 1rem; letter-spacing: 2px;">LINK BUTTONS</h4>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#" class="btn-scifi">LINK BUTTON</a>
                        <a href="#" class="btn-scifi btn-scifi-primary">PRIMARY LINK</a>
                        <a href="#" class="btn-scifi btn-scifi-sm">SMALL LINK</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-5 pt-5">
            <h3 class="text-metallic mb-4">USAGE EXAMPLES</h3>

            <h5 class="text-cyan mb-3" style="color: #64b5f6;">Menu Items:</h5>
            <pre style="background: rgba(15, 20, 40, 0.7); padding: 20px; border: 1px solid #64b5f6; border-radius: 4px; color: #64b5f6; overflow-x: auto;"><code>&lt;div class="scifi-menu"&gt;
  &lt;div class="scifi-menu-item"&gt;
    &lt;span class="scifi-menu-glow-bar"&gt;&lt;/span&gt;
    &lt;span class="scifi-menu-label"&gt;HELMETS&lt;/span&gt;
    &lt;span class="scifi-menu-value"&gt;12%&lt;/span&gt;
  &lt;/div&gt;
  &lt;div class="scifi-menu-item active"&gt;
    &lt;span class="scifi-menu-glow-bar"&gt;&lt;/span&gt;
    &lt;span class="scifi-menu-label"&gt;ARMOR FX&lt;/span&gt;
    &lt;span class="scifi-menu-value"&gt;50%&lt;/span&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>

            <h5 class="text-cyan mb-3 mt-4" style="color: #64b5f6;">Buttons:</h5>
            <pre style="background: rgba(15, 20, 40, 0.7); padding: 20px; border: 1px solid #64b5f6; border-radius: 4px; color: #64b5f6; overflow-x: auto;"><code>&lt;button class="btn-scifi"&gt;DEFAULT&lt;/button&gt;
&lt;button class="btn-scifi btn-scifi-primary"&gt;PRIMARY&lt;/button&gt;
&lt;button class="btn-scifi btn-scifi-success"&gt;SUCCESS&lt;/button&gt;
&lt;button class="btn-scifi btn-scifi-danger"&gt;DANGER&lt;/button&gt;
&lt;button class="btn-scifi btn-scifi-sm"&gt;SMALL&lt;/button&gt;
&lt;button class="btn-scifi btn-scifi-lg"&gt;LARGE&lt;/button&gt;</code></pre>
        </section>

    </main>

    <!-- <script src="js/canvas.js?v=1.5"></script> -->

    <script>
    /* ========================================================
        //ANCHOR [SCIFI_MENU_INTERACTIONS]
        FUNCTION: Interactive click handlers for sci-fi menu items
    -----------------------------------------------------------
        Parameters: N/A
        Returns: N/A
        Description: Adds click-to-select functionality with visual feedback
        UniqueID: 789004
    =========================================================== */
    document.addEventListener('DOMContentLoaded', function() {
        // Get all menu containers
        const menus = document.querySelectorAll('.scifi-menu');

        menus.forEach(menu => {
            const items = menu.querySelectorAll('.scifi-menu-item:not(.disabled)');

            items.forEach(item => {
                item.addEventListener('click', function() {
                    // Remove active class from all items in this menu
                    items.forEach(i => i.classList.remove('active'));

                    // Add active class to clicked item
                    this.classList.add('active');

                    // Optional: Play a sound effect here
                    // new Audio('sounds/click.mp3').play();

                    // Optional: Trigger haptic feedback on mobile
                    if (navigator.vibrate) {
                        navigator.vibrate(10);
                    }

                    console.log('Selected:', this.querySelector('.scifi-menu-label').textContent.trim());
                });

                // Add hover sound effect (optional)
                item.addEventListener('mouseenter', function() {
                    // new Audio('sounds/hover.mp3').play();
                });
            });
        });

        // Add keyboard navigation
        document.addEventListener('keydown', function(e) {
            const activeItem = document.querySelector('.scifi-menu-item.active');
            if (!activeItem) return;

            const menu = activeItem.closest('.scifi-menu');
            const items = Array.from(menu.querySelectorAll('.scifi-menu-item:not(.disabled)'));
            const currentIndex = items.indexOf(activeItem);

            let newIndex = currentIndex;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                newIndex = (currentIndex + 1) % items.length;
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                newIndex = (currentIndex - 1 + items.length) % items.length;
            } else if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                activeItem.click();
                return;
            }

            if (newIndex !== currentIndex) {
                items.forEach(i => i.classList.remove('active'));
                items[newIndex].classList.add('active');
                items[newIndex].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });

        // ========================================================
        // Sci-Fi Button Click Handlers
        // ========================================================

        /* ========================================================
            //ANCHOR [SCIFI_BUTTON_INTERACTIONS]
            FUNCTION: Button click feedback and logging
        -----------------------------------------------------------
            Parameters: N/A
            Returns: N/A
            Description: Adds click feedback, haptic response, and console logging for sci-fi buttons
            UniqueID: 789011
        =========================================================== */
        const scifiButtons = document.querySelectorAll('.btn-scifi:not(.disabled):not([disabled])');

        scifiButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Prevent default for demo links
                if (this.tagName === 'A' && this.getAttribute('href') === '#') {
                    e.preventDefault();
                }

                // Haptic feedback on mobile
                if (navigator.vibrate) {
                    navigator.vibrate(15);
                }

                // Visual feedback - add a temporary class
                this.style.transform = 'translateY(0) scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);

                // Log button action
                const buttonText = this.textContent.trim();
                const buttonType = this.classList.contains('btn-scifi-primary') ? 'PRIMARY' :
                                 this.classList.contains('btn-scifi-danger') ? 'DANGER' :
                                 this.classList.contains('btn-scifi-success') ? 'SUCCESS' : 'DEFAULT';

                console.log(`[SCI-FI BUTTON] ${buttonType}: "${buttonText}" clicked`);

                // Optional: Show a temporary notification
                showNotification(`${buttonText} activated`);
            });
        });

        // Simple notification system
        function showNotification(message) {
            const notification = document.createElement('div');
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: rgba(100, 181, 246, 0.9);
                color: #000;
                padding: 12px 24px;
                border: 2px solid #64b5f6;
                box-shadow: 0 0 20px rgba(100, 181, 246, 0.6);
                font-family: 'Electrolize', sans-serif;
                font-size: 0.9rem;
                text-transform: uppercase;
                letter-spacing: 1px;
                z-index: 10000;
                animation: slideInRight 0.3s ease;
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 2000);
        }

        // Add notification animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(400px);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    });
    </script>

<?php
include 'includes/footer.php';
?>

