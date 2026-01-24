<?php
$page_title = "Tactical UI Demo";
include 'includes/header.php';
?>
<link rel="stylesheet" href="./css/style-blog.css">
</head>
<body class="tactical-theme tactical-grid">
<?php
include 'includes/navbar.php';
?>

<!-- ========================================================
    //ANCHOR [TACTICAL_DEMO_PAGE]
    FUNCTION: Tactical UI Component Showcase
-----------------------------------------------------------
    Parameters: N/A
    Returns: HTML
    Description: Comprehensive demo of all tactical stylesheet components
    UniqueID: 791100
=========================================================== -->
<main class="container my-5" style="position: relative; z-index: 3;">
    
    <!-- Page Header -->
    <section class="text-center mb-5">
        <h1 class="tactical-heading" style="font-size: 2.5rem; margin-bottom: 1rem;">
            TACTICAL UI SYSTEM
        </h1>
        <p class="tactical-label" style="font-size: 14px;">
            MILITARY-GRADE INTERFACE COMPONENTS / DESIGN SYSTEM v1.0
        </p>
    </section>

    <!-- Typography Section -->
    <section class="mb-5">
        <div class="tactical-panel">
            <div class="tactical-panel-header">
                <span class="panel-title">Typography System</span>
                <span class="panel-id">TYP-01</span>
            </div>
            <div class="tactical-panel-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h3 class="tactical-heading mb-3" style="font-size: 1.2rem;">Display Fonts</h3>
                        <div class="mb-3">
                            <p class="tactical-label mb-1">Tactical Heading</p>
                            <h2 class="tactical-heading">MISSION CRITICAL</h2>
                        </div>
                        <div class="mb-3">
                            <p class="tactical-label mb-1">Tactical Display</p>
                            <div class="tactical-display">00:28:17</div>
                        </div>
                        <div class="mb-3">
                            <p class="tactical-label mb-1">Glow Text Effect</p>
                            <div class="tactical-glow-text tactical-mono" style="font-size: 24px;">ENCRYPTED</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <h3 class="tactical-heading mb-3" style="font-size: 1.2rem;">Data Fonts</h3>
                        <div class="mb-3">
                            <p class="tactical-label mb-1">Tactical Label</p>
                            <p class="tactical-label">SYSTEM STATUS</p>
                        </div>
                        <div class="mb-3">
                            <p class="tactical-label mb-1">Tactical Mono</p>
                            <p class="tactical-mono" style="font-size: 14px;">192.168.1.100:8080</p>
                        </div>
                        <div class="mb-3">
                            <p class="tactical-label mb-1">Alert Text</p>
                            <p class="tactical-alert-text">CRITICAL WARNING</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Panels Section -->
    <section class="mb-5">
        <div class="tactical-grid cols-2">
            <div class="tactical-panel">
                <div class="tactical-panel-header">
                    <span class="panel-title">Standard Panel</span>
                    <span class="panel-id">PNL-01</span>
                </div>
                <div class="tactical-panel-body">
                    <p class="tactical-label mb-2">Panel Description</p>
                    <p style="color: #8fa4b0; font-size: 13px;">
                        Standard tactical panel with corner brackets, diagonal stripe pattern, 
                        and teal accent border. Used for general information display.
                    </p>
                </div>
            </div>

            <div class="tactical-panel-alert">
                <div class="tactical-panel-header">
                    <span class="panel-title">Alert Panel</span>
                    <span class="panel-id">ALT-01</span>
                </div>
                <div class="tactical-panel-body">
                    <p class="tactical-label mb-2">Alert Description</p>
                    <p style="color: #8fa4b0; font-size: 13px;">
                        Alert variant panel with red accent borders and background gradient. 
                        Used for warnings, errors, and critical notifications.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Status Indicators Section -->
    <section class="mb-5">
        <div class="tactical-panel">
            <div class="tactical-panel-header">
                <span class="panel-title">Status Indicators</span>
                <span class="panel-id">STS-01</span>
            </div>
            <div class="tactical-panel-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="tactical-status online">System Online</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="tactical-status alert">Critical Alert</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="tactical-status warning">Warning</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="tactical-status offline">Offline</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Data Display Section -->
    <section class="mb-5">
        <div class="tactical-panel">
            <div class="tactical-panel-header">
                <span class="panel-title">Data Display</span>
                <span class="panel-id">DAT-01</span>
            </div>
            <div class="tactical-panel-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h3 class="tactical-heading mb-3" style="font-size: 1rem;">System Metrics</h3>
                        <div class="tactical-data-row">
                            <span class="tactical-data-label">CPU Usage</span>
                            <span class="tactical-data-value">47.8%</span>
                        </div>
                        <div class="tactical-data-row">
                            <span class="tactical-data-label">Memory</span>
                            <span class="tactical-data-value">8.2 GB</span>
                        </div>
                        <div class="tactical-data-row">
                            <span class="tactical-data-label">Network</span>
                            <span class="tactical-data-value">125 Mbps</span>
                        </div>
                        <div class="tactical-data-row">
                            <span class="tactical-data-label">Uptime</span>
                            <span class="tactical-data-value">72:14:33</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <h3 class="tactical-heading mb-3" style="font-size: 1rem;">Mission Timer</h3>
                        <div class="text-center">
                            <div class="tactical-display mb-2">00:28:17</div>
                            <p class="tactical-label">Time Remaining</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Progress Bars Section -->
    <section class="mb-5">
        <div class="tactical-panel">
            <div class="tactical-panel-header">
                <span class="panel-title">Progress Indicators</span>
                <span class="panel-id">PRG-01</span>
            </div>
            <div class="tactical-panel-body">
                <div class="mb-4">
                    <p class="tactical-label mb-2">Standard Progress Bar</p>
                    <div class="tactical-progress">
                        <div class="tactical-progress-fill" style="width: 65%;"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="tactical-label mb-2">Segmented Progress (Normal)</p>
                    <div class="tactical-progress-segments">
                        <div class="tactical-segment filled"></div>
                        <div class="tactical-segment filled"></div>
                        <div class="tactical-segment filled"></div>
                        <div class="tactical-segment filled"></div>
                        <div class="tactical-segment filled"></div>
                        <div class="tactical-segment filled"></div>
                        <div class="tactical-segment"></div>
                        <div class="tactical-segment"></div>
                        <div class="tactical-segment"></div>
                        <div class="tactical-segment"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="tactical-label mb-2">Segmented Progress (Alert)</p>
                    <div class="tactical-progress-segments">
                        <div class="tactical-segment alert"></div>
                        <div class="tactical-segment alert"></div>
                        <div class="tactical-segment alert"></div>
                        <div class="tactical-segment alert"></div>
                        <div class="tactical-segment alert"></div>
                        <div class="tactical-segment alert"></div>
                        <div class="tactical-segment alert"></div>
                        <div class="tactical-segment alert"></div>
                        <div class="tactical-segment"></div>
                        <div class="tactical-segment"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Buttons Section -->
    <section class="mb-5">
        <div class="tactical-panel">
            <div class="tactical-panel-header">
                <span class="panel-title">Button Components</span>
                <span class="panel-id">BTN-01</span>
            </div>
            <div class="tactical-panel-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <p class="tactical-label mb-2">Standard Button</p>
                        <button class="tactical-btn">Execute Command</button>
                    </div>
                    <div class="col-md-4 mb-3">
                        <p class="tactical-label mb-2">Primary Button</p>
                        <button class="tactical-btn-primary">Confirm Action</button>
                    </div>
                    <div class="col-md-4 mb-3">
                        <p class="tactical-label mb-2">Alert Button</p>
                        <button class="tactical-btn-alert">Abort Mission</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Data Table Section -->
    <section class="mb-5">
        <div class="tactical-panel">
            <div class="tactical-panel-header">
                <span class="panel-title">Data Table</span>
                <span class="panel-id">TBL-01</span>
            </div>
            <div class="tactical-panel-body">
                <table class="tactical-table">
                    <thead>
                        <tr>
                            <th>Unit ID</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Signal</th>
                            <th>Last Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="tactical-mono">UNIT-01</td>
                            <td>Sector Alpha</td>
                            <td class="online">ONLINE</td>
                            <td class="tactical-mono">98%</td>
                            <td class="tactical-mono">00:00:12</td>
                        </tr>
                        <tr>
                            <td class="tactical-mono">UNIT-02</td>
                            <td>Sector Beta</td>
                            <td class="online">ONLINE</td>
                            <td class="tactical-mono">87%</td>
                            <td class="tactical-mono">00:00:08</td>
                        </tr>
                        <tr class="alert">
                            <td class="tactical-mono">UNIT-03</td>
                            <td>Sector Gamma</td>
                            <td class="alert">ALERT</td>
                            <td class="tactical-mono">23%</td>
                            <td class="tactical-mono">00:05:42</td>
                        </tr>
                        <tr>
                            <td class="tactical-mono">UNIT-04</td>
                            <td>Sector Delta</td>
                            <td class="online">ONLINE</td>
                            <td class="tactical-mono">95%</td>
                            <td class="tactical-mono">00:00:03</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Badges Section -->
    <section class="mb-5">
        <div class="tactical-panel">
            <div class="tactical-panel-header">
                <span class="panel-title">Badges & Tags</span>
                <span class="panel-id">BDG-01</span>
            </div>
            <div class="tactical-panel-body">
                <div class="mb-3">
                    <p class="tactical-label mb-2">Standard Badges</p>
                    <span class="tactical-badge mr-2">Active</span>
                    <span class="tactical-badge mr-2">Encrypted</span>
                    <span class="tactical-badge mr-2">Verified</span>
                    <span class="tactical-badge">Classified</span>
                </div>
                <div class="mb-3">
                    <p class="tactical-label mb-2">Alert Badges</p>
                    <span class="tactical-badge alert mr-2">Critical</span>
                    <span class="tactical-badge alert mr-2">Warning</span>
                    <span class="tactical-badge alert">Urgent</span>
                </div>
                <div class="mb-3">
                    <p class="tactical-label mb-2">Success Badges</p>
                    <span class="tactical-badge success mr-2">Operational</span>
                    <span class="tactical-badge success mr-2">Secure</span>
                    <span class="tactical-badge success">Confirmed</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Animations Section -->
    <section class="mb-5">
        <div class="tactical-panel">
            <div class="tactical-panel-header">
                <span class="panel-title">Animated Effects</span>
                <span class="panel-id">ANM-01</span>
            </div>
            <div class="tactical-panel-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <p class="tactical-label mb-2">Scanning Effect</p>
                        <div class="tactical-scanning" style="height: 100px; background: #0d171c; border: 1px solid #1a3d4a; display: flex; align-items: center; justify-content: center;">
                            <span class="tactical-mono">SCANNING...</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <p class="tactical-label mb-2">Pulsing Status</p>
                        <div style="display: flex; gap: 20px; align-items: center; height: 100px;">
                            <div class="tactical-status online">Online</div>
                            <div class="tactical-status alert">Alert</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Grid Layout Section -->
    <section class="mb-5">
        <div class="tactical-panel">
            <div class="tactical-panel-header">
                <span class="panel-title">Grid Layout System</span>
                <span class="panel-id">GRD-01</span>
            </div>
            <div class="tactical-panel-body">
                <p class="tactical-label mb-3">3-Column Grid</p>
                <div class="tactical-grid cols-3">
                    <div style="background: #0d171c; border: 1px solid #1a3d4a; padding: 20px; text-align: center;">
                        <div class="tactical-mono">Grid Item 1</div>
                    </div>
                    <div style="background: #0d171c; border: 1px solid #1a3d4a; padding: 20px; text-align: center;">
                        <div class="tactical-mono">Grid Item 2</div>
                    </div>
                    <div style="background: #0d171c; border: 1px solid #1a3d4a; padding: 20px; text-align: center;">
                        <div class="tactical-mono">Grid Item 3</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Color Palette Section -->
    <section class="mb-5">
        <div class="tactical-panel">
            <div class="tactical-panel-header">
                <span class="panel-title">Color Palette</span>
                <span class="panel-id">CLR-01</span>
            </div>
            <div class="tactical-panel-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <p class="tactical-label mb-2">Teal Accent</p>
                        <div style="background: #00cccc; height: 60px; border: 1px solid #00ffcc;"></div>
                        <p class="tactical-mono mt-1" style="font-size: 11px;">#00cccc</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="tactical-label mb-2">Teal Bright</p>
                        <div style="background: #00ffcc; height: 60px; border: 1px solid #00ffcc;"></div>
                        <p class="tactical-mono mt-1" style="font-size: 11px;">#00ffcc</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="tactical-label mb-2">Alert Red</p>
                        <div style="background: #ff1a3d; height: 60px; border: 1px solid #ff3355;"></div>
                        <p class="tactical-mono mt-1" style="font-size: 11px;">#ff1a3d</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="tactical-label mb-2">Online Green</p>
                        <div style="background: #00ff99; height: 60px; border: 1px solid #00ff99;"></div>
                        <p class="tactical-mono mt-1" style="font-size: 11px;">#00ff99</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php
include 'includes/footer.php';
?>
