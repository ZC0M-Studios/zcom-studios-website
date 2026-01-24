<?php
$page_title = "FUI Design System Demo";
include 'includes/header.php';
?>
<link rel="stylesheet" href="/css/style-fui.css">
<link rel="stylesheet" href="./css/style-blog.css">
</head>
<body class="fui-theme fui-scanlines">
<?php
include 'includes/navbar.php';
?>

<!-- ========================================================
    //ANCHOR [FUI_DEMO_PAGE]
    FUNCTION: FUI Component Showcase
-----------------------------------------------------------
    Parameters: N/A
    Returns: HTML
    Description: Comprehensive demo of all FUI stylesheet components
    UniqueID: 792100
=========================================================== -->
<main class="container my-5" style="position: relative; z-index: 3;">
    
    <!-- Page Header -->
    <section class="text-center mb-5">
        <h1 class="fui-header" style="font-size: 2.5rem; margin-bottom: 1rem;">
            FUI DESIGN SYSTEM
        </h1>
        <p class="fui-label" style="font-size: 14px;">
            FICTIONAL USER INTERFACE / COMPONENT LIBRARY v2.0
        </p>
    </section>

    <!-- Typography Section -->
    <section class="mb-5">
        <div class="fui-panel">
            <div class="fui-panel-header">
                <span class="panel-title">Typography System</span>
                <span class="panel-id">TYP-FUI-01</span>
            </div>
            <div class="fui-panel-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h3 class="fui-header mb-3">Display Fonts</h3>
                        <div class="mb-3">
                            <p class="fui-label mb-1">FUI Header</p>
                            <h2 class="fui-header">SYSTEM INTERFACE</h2>
                        </div>
                        <div class="mb-3">
                            <p class="fui-label mb-1">FUI Display</p>
                            <div class="fui-display">47.8%</div>
                        </div>
                        <div class="mb-3">
                            <p class="fui-label mb-1">Text Glow Effect</p>
                            <div class="fui-text-glow fui-mono" style="font-size: 24px; color: #00d4ff;">ENCRYPTED</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <h3 class="fui-header mb-3">Data Fonts</h3>
                        <div class="mb-3">
                            <p class="fui-label mb-1">FUI Label</p>
                            <p class="fui-label">SYSTEM STATUS</p>
                        </div>
                        <div class="mb-3">
                            <p class="fui-label mb-1">FUI Mono</p>
                            <p class="fui-mono" style="font-size: 14px;">192.168.1.100:8080</p>
                        </div>
                        <div class="mb-3">
                            <p class="fui-label mb-1">FUI Data Value</p>
                            <p class="fui-data">8,192 MB</p>
                        </div>
                        <div class="mb-3">
                            <p class="fui-label mb-1">FUI Micro</p>
                            <p class="fui-micro">MICRO TEXT LABEL</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Status Colors Section -->
    <section class="mb-5">
        <div class="fui-panel">
            <div class="fui-panel-header">
                <span class="panel-title">Status Colors</span>
                <span class="panel-id">CLR-FUI-01</span>
            </div>
            <div class="fui-panel-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <p class="fui-label mb-2">Success</p>
                        <div class="fui-success" style="font-size: 24px; font-weight: bold;">SUCCESS</div>
                        <div class="fui-success-dim" style="font-size: 14px;">Success Dim</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="fui-label mb-2">Warning</p>
                        <div class="fui-warning" style="font-size: 24px; font-weight: bold;">WARNING</div>
                        <div class="fui-warning-dim" style="font-size: 14px;">Warning Dim</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="fui-label mb-2">Error</p>
                        <div class="fui-error" style="font-size: 24px; font-weight: bold;">ERROR</div>
                        <div class="fui-error-dim" style="font-size: 14px;">Error Dim</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="fui-label mb-2">Info</p>
                        <div class="fui-info" style="font-size: 24px; font-weight: bold;">INFO</div>
                        <div class="fui-scanning" style="font-size: 14px;">Scanning</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Panels & Frames Section -->
    <section class="mb-5">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="fui-panel">
                    <div class="fui-panel-header">
                        <span class="panel-title">FUI Panel</span>
                        <span class="panel-id">PNL-01</span>
                    </div>
                    <div class="fui-panel-body">
                        <p class="fui-label mb-2">Panel Description</p>
                        <p style="color: #8fa4b0; font-size: 13px;">
                            Standard FUI panel with corner brackets and cyan accent borders. 
                            Features gradient header and auto-scrolling body.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="fui-frame" style="background: #0d1117; padding: 20px;">
                    <p class="fui-label mb-2">FUI Frame</p>
                    <p style="color: #8fa4b0; font-size: 13px;">
                        Lightweight frame component with corner brackets only. 
                        No background or header - just decorative corners.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Status Indicators Section -->
    <section class="mb-5">
        <div class="fui-panel">
            <div class="fui-panel-header">
                <span class="panel-title">Status Indicators</span>
                <span class="panel-id">STS-FUI-01</span>
            </div>
            <div class="fui-panel-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="fui-status online">System Online</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="fui-status offline">System Offline</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="fui-status warning">Warning State</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="fui-status standby">Standby Mode</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="fui-status scanning">Scanning</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Data Display Section -->
    <section class="mb-5">
        <div class="fui-panel">
            <div class="fui-panel-header">
                <span class="panel-title">Data Display Components</span>
                <span class="panel-id">DAT-FUI-01</span>
            </div>
            <div class="fui-panel-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h3 class="fui-header mb-3" style="font-size: 1rem;">Key-Value Pairs</h3>
                        <div class="fui-data-pair">
                            <span class="fui-data-key">CPU Usage</span>
                            <span class="fui-data-val">47.8%</span>
                        </div>
                        <div class="fui-data-pair">
                            <span class="fui-data-key">Memory</span>
                            <span class="fui-data-val">8.2 GB</span>
                        </div>
                        <div class="fui-data-pair">
                            <span class="fui-data-key">Network</span>
                            <span class="fui-data-val">125 Mbps</span>
                        </div>
                        <div class="fui-data-pair">
                            <span class="fui-data-key">Uptime</span>
                            <span class="fui-data-val">72:14:33</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <h3 class="fui-header mb-3" style="font-size: 1rem;">Large Display</h3>
                        <div class="text-center">
                            <div class="fui-display mb-2">47.8%</div>
                            <p class="fui-label">System Load</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Progress Bars Section -->
    <section class="mb-5">
        <div class="fui-panel">
            <div class="fui-panel-header">
                <span class="panel-title">Progress Indicators</span>
                <span class="panel-id">PRG-FUI-01</span>
            </div>
            <div class="fui-panel-body">
                <div class="mb-4">
                    <p class="fui-label mb-2">Standard Progress Bar</p>
                    <div class="fui-progress-bar">
                        <div class="fui-progress-fill" style="width: 65%;"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="fui-label mb-2">Segmented Progress</p>
                    <div class="fui-progress">
                        <div class="fui-progress-segment filled"></div>
                        <div class="fui-progress-segment filled"></div>
                        <div class="fui-progress-segment filled"></div>
                        <div class="fui-progress-segment filled"></div>
                        <div class="fui-progress-segment filled"></div>
                        <div class="fui-progress-segment filled"></div>
                        <div class="fui-progress-segment filled"></div>
                        <div class="fui-progress-segment"></div>
                        <div class="fui-progress-segment"></div>
                        <div class="fui-progress-segment"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Buttons Section -->
    <section class="mb-5">
        <div class="fui-panel">
            <div class="fui-panel-header">
                <span class="panel-title">Button Components</span>
                <span class="panel-id">BTN-FUI-01</span>
            </div>
            <div class="fui-panel-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <p class="fui-label mb-2">Standard Button</p>
                        <button class="fui-btn">Execute Command</button>
                    </div>
                    <div class="col-md-4 mb-3">
                        <p class="fui-label mb-2">Primary Button</p>
                        <button class="fui-btn-primary">Confirm Action</button>
                    </div>
                    <div class="col-md-4 mb-3">
                        <p class="fui-label mb-2">Danger Button</p>
                        <button class="fui-btn-danger">Abort Mission</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <p class="fui-label mb-2">Icon Buttons</p>
                        <button class="fui-btn fui-btn-icon mr-2">+</button>
                        <button class="fui-btn fui-btn-icon mr-2">-</button>
                        <button class="fui-btn fui-btn-icon mr-2">×</button>
                        <button class="fui-btn fui-btn-icon">✓</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Input Components Section -->
    <section class="mb-5">
        <div class="fui-panel">
            <div class="fui-panel-header">
                <span class="panel-title">Input Components</span>
                <span class="panel-id">INP-FUI-01</span>
            </div>
            <div class="fui-panel-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <p class="fui-label mb-2">Standard Input</p>
                        <input type="text" class="fui-input" placeholder="Enter command...">
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="fui-label mb-2">Input with Label</p>
                        <div class="fui-input-group">
                            <span class="fui-input-label">IP:</span>
                            <input type="text" class="fui-input" placeholder="192.168.1.1">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Navigation Components Section -->
    <section class="mb-5">
        <div class="fui-panel">
            <div class="fui-panel-header">
                <span class="panel-title">Navigation Components</span>
                <span class="panel-id">NAV-FUI-01</span>
            </div>
            <div class="fui-panel-body">
                <div class="mb-4">
                    <p class="fui-label mb-2">Tabs</p>
                    <div class="fui-tabs">
                        <button class="fui-tab active">Overview</button>
                        <button class="fui-tab">Metrics</button>
                        <button class="fui-tab">Settings</button>
                        <button class="fui-tab">Logs</button>
                    </div>
                </div>
                <div class="mb-4">
                    <p class="fui-label mb-2">Navigation List</p>
                    <ul class="fui-nav-list">
                        <li class="fui-nav-item active">Dashboard</li>
                        <li class="fui-nav-item">Systems</li>
                        <li class="fui-nav-item">Analytics</li>
                        <li class="fui-nav-item">Configuration</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Glow Effects Section -->
    <section class="mb-5">
        <div class="fui-panel">
            <div class="fui-panel-header">
                <span class="panel-title">Glow Effects</span>
                <span class="panel-id">GLW-FUI-01</span>
            </div>
            <div class="fui-panel-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <p class="fui-label mb-2">Standard Glow</p>
                        <div class="fui-glow" style="background: #0d1117; padding: 20px; text-align: center;">
                            <span class="fui-mono">GLOW BOX</span>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <p class="fui-label mb-2">Strong Glow</p>
                        <div class="fui-glow-strong" style="background: #0d1117; padding: 20px; text-align: center;">
                            <span class="fui-mono">STRONG GLOW</span>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <p class="fui-label mb-2">Pulsing Glow</p>
                        <div class="fui-glow-pulse" style="background: #0d1117; padding: 20px; text-align: center;">
                            <span class="fui-mono">PULSE GLOW</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Animations Section -->
    <section class="mb-5">
        <div class="fui-panel">
            <div class="fui-panel-header">
                <span class="panel-title">Animated Effects</span>
                <span class="panel-id">ANM-FUI-01</span>
            </div>
            <div class="fui-panel-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <p class="fui-label mb-2">Scanning Effect</p>
                        <div class="fui-animate-scan" style="height: 100px; background: #0d1117; border: 1px solid #007a94; display: flex; align-items: center; justify-content: center;">
                            <span class="fui-mono">SCANNING...</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <p class="fui-label mb-2">Blinking Cursor</p>
                        <div style="height: 100px; background: #0d1117; border: 1px solid #007a94; display: flex; align-items: center; justify-content: center;">
                            <span class="fui-mono">READY<span class="fui-animate-blink"></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Hexagonal Clip Section -->
    <section class="mb-5">
        <div class="fui-panel">
            <div class="fui-panel-header">
                <span class="panel-title">Hexagonal Clip Paths</span>
                <span class="panel-id">HEX-FUI-01</span>
            </div>
            <div class="fui-panel-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="fui-hex" style="background: linear-gradient(135deg, #007a94 0%, #00d4ff 100%); padding: 40px; text-align: center;">
                            <span class="fui-mono" style="color: #000;">HEX CLIP</span>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="fui-hex" style="background: linear-gradient(135deg, #cc2944 0%, #ff3355 100%); padding: 40px; text-align: center;">
                            <span class="fui-mono" style="color: #fff;">HEX CLIP</span>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="fui-hex" style="background: linear-gradient(135deg, #00cc6a 0%, #00ff88 100%); padding: 40px; text-align: center;">
                            <span class="fui-mono" style="color: #000;">HEX CLIP</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Color Palette Section -->
    <section class="mb-5">
        <div class="fui-panel">
            <div class="fui-panel-header">
                <span class="panel-title">Color Palette</span>
                <span class="panel-id">PAL-FUI-01</span>
            </div>
            <div class="fui-panel-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <p class="fui-label mb-2">Primary 500</p>
                        <div style="background: #00d4ff; height: 60px; border: 1px solid #00d4ff;"></div>
                        <p class="fui-mono mt-1" style="font-size: 11px;">#00d4ff</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="fui-label mb-2">Primary 700</p>
                        <div style="background: #0096b4; height: 60px; border: 1px solid #0096b4;"></div>
                        <p class="fui-mono mt-1" style="font-size: 11px;">#0096b4</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="fui-label mb-2">Success</p>
                        <div style="background: #00ff88; height: 60px; border: 1px solid #00ff88;"></div>
                        <p class="fui-mono mt-1" style="font-size: 11px;">#00ff88</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="fui-label mb-2">Error</p>
                        <div style="background: #ff3355; height: 60px; border: 1px solid #ff3355;"></div>
                        <p class="fui-mono mt-1" style="font-size: 11px;">#ff3355</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php
include 'includes/footer.php';
?>
