<?php
// SEO Meta Variables
$page_title = "Contact";
$page_description = "Get in touch with ZCOM Studios for software development projects, collaborations, or inquiries.";
$page_keywords = "contact, hire developer, software development, freelance, collaboration";
$page_type = "website";

include 'includes/header.php';
?>
</head>
<body class="cyber-theme">
<?php
include 'includes/navbar.php';
?>

<main class="container my-5" style="z-index: 3; pointer-events: auto;">
    
    <!-- Header Panel -->
    <div class="cyber-panel mb-4">
        <div class="cyber-panel-header">
            <span class="panel-title">CONTACT.FORM</span>
            <span class="panel-id">MSG.TX</span>
        </div>
        <div class="cyber-panel-body text-center">
            <h1 class="page-title mb-3">GET IN TOUCH</h1>
            <p class="lead" style="color: #7090a8;">We'd love to hear from you. Reach out with any questions or project inquiries.</p>
        </div>
    </div>

    <!-- Contact Form Panel -->
    <div class="cyber-panel">
        <div class="cyber-panel-header">
            <span class="panel-title">MESSAGE.INPUT</span>
            <span class="panel-status">READY</span>
        </div>
        <div class="cyber-panel-body">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <form>
                        <div class="mb-4">
                            <div class="cyber-input-group">
                                <span class="cyber-input-label">NAME</span>
                                <input type="text" class="cyber-input" id="name" placeholder="Enter your name...">
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="cyber-input-group">
                                <span class="cyber-input-label">EMAIL</span>
                                <input type="email" class="cyber-input" id="email" placeholder="name@example.com">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="cyber-label mb-2">MESSAGE</label>
                            <textarea class="cyber-input" id="message" rows="5" placeholder="Enter your message..." style="width: 100%;"></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn-scifi btn-scifi-primary">TRANSMIT MESSAGE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</main>

<?php
include 'includes/footer.php';
?>
