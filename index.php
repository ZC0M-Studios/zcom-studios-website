<?php
// SEO Meta Variables
$page_title = "ZCOM Studios";
$page_description = "Software development, 3D design, and creative technology portfolio. Explore projects, articles, AI prompts, and developer tools.";
$page_keywords = "web development, PHP, JavaScript, 3D design, portfolio, software engineer, creative technology";
$page_type = "website";

include 'includes/header.php';
?>
</head>
<body class="cyber-theme">
<?php
include 'includes/navbar.php';
?>

<?php
/* ========================================================
    //ANCHOR [HOME_PAGE_CONTENT]
    FUNCTION: N/A
-----------------------------------------------------------
    Parameters: N/A
    Returns: N/A
    Description: This section represents the main content of the home page, including the hero section and a list of projects.
    UniqueID: 123456
=========================================================== */
?>
    <canvas id="headerCanvas"></canvas>

    <main class="container my-5">

        <section class="hero text-center mb-5">
            <img id="logo_header" src="./img/logo/logo_zcom-studios_1.png" width="200" alt="Zcom Studios Logo">
            <h1 id="title-1" class="text-glow">ZCOM</h1>
            <h1 id="title-2">STUDIOS</h1>
            <div style="margin-top: 16px;">
                <span id="tag-1" class="cyber-badge" style="margin: 4px;">Software/Web Developer</span>
                <span id="tag-2" class="cyber-badge" style="margin: 4px;">3D Model/Graphic Designer</span>
                <span id="tag-3" class="cyber-badge cyan" style="margin: 4px;">Video Game Enthusiast</span>
                <span id="tag-4" class="cyber-badge" style="margin: 4px;">Lifelong Nerd/ Learner</span>
            </div>
        </section>
<!--
        <section class="projects">
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">E-Commerce Platform</h5>
                            <p class="card-text">Full-stack PHP application with MySQL database and responsive design.</p>
                            <a href="#" class="btn btn-primary btn-sm">View Project</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Blog CMS</h5>
                            <p class="card-text">Content management system built with PHP and jQuery for dynamic content.</p>
                            <a href="#" class="btn btn-primary btn-sm">View Project</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Task Manager App</h5>
                            <p class="card-text">RESTful API with real-time task tracking and user authentication.</p>
                            <a href="#" class="btn btn-primary btn-sm">View Project</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
-->
    </main>

    <script src="js/canvas.js?v=1.5"></script>
<?php
include 'includes/footer.php';
?>
