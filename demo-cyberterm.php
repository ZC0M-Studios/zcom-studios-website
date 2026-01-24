<?php
$page_title = "Cyberterm Interface Demo";
include 'includes/header.php';
?>
<link rel="stylesheet" href="/css/style-cyberterm.css">
</head>
<body class="cyber-theme cyber-scanlines">

<!-- ========================================================
    //ANCHOR [CYBERTERM_DEMO]
    FUNCTION: Cyberterm UI Component Showcase
-----------------------------------------------------------
    Parameters: N/A
    Returns: HTML
    Description: Comprehensive demo of Cyberterm stylesheet
    UniqueID: 794100
=========================================================== -->

<!-- Header Bar -->
<header class="cyber-header">
    <div class="cyber-breadcrumb">
        <span class="current">/DIAGNOSTICS</span>
        <span class="separator">:</span>
        <span>SYSTEM</span>
    </div>
    
    <nav class="cyber-nav-tabs">
        <span class="nav-tab">BIOS</span>
        <span class="nav-tab active">COMMUNICATIONS</span>
        <span class="nav-tab">NAVIGATION</span>
        <span class="nav-tab">ACCELERATOR</span>
        <span class="nav-tab">ORIENTATION</span>
        <span class="nav-tab">RH030</span>
    </nav>
    
    <div style="display: flex; align-items: center; gap: 16px;">
        <div style="display: flex; gap: 4px;">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #506070;"></span>
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #506070;"></span>
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #50c878;"></span>
        </div>
        <span class="cyber-label">DIAG.</span>
    </div>
</header>

<main style="display: grid; grid-template-columns: 40px 240px 1fr 280px 40px; gap: 8px; padding: 8px; min-height: calc(100vh - 50px);">
    
    <!-- Left Sidebar Label -->
    <aside style="display: flex; align-items: center; justify-content: center;">
        <span class="cyber-sidebar-label">SYS.</span>
    </aside>
    
    <!-- Left Panel Column -->
    <div style="display: flex; flex-direction: column; gap: 8px;">
        
        <!-- SRW Panel -->
        <div class="cyber-panel">
            <div class="cyber-panel-header">
                <span class="panel-title">VC.101</span>
                <span class="panel-id">SRW</span>
            </div>
            <div class="cyber-panel-body">
                <div class="cyber-terminal" style="font-size: 9px; padding: 8px;">
                    <div class="term-line"><span class="term-prefix">REF.LIN</span></div>
                    <div class="term-line"><span class="term-output">LOG_INIT</span></div>
                    <div class="term-line"><span class="term-output">ENTER DIAG Y/N?</span> <span class="term-command">DESC.093</span></div>
                    <div class="term-line"><span class="term-output">ENTER _/ACCEL_INIT</span></div>
                    <div class="term-line"><span class="term-output">ACCEL_INIT</span></div>
                </div>
            </div>
        </div>
        
        <!-- SIM A Panel -->
        <div class="cyber-panel">
            <div class="cyber-panel-header">
                <span class="panel-title">SIM.A</span>
                <span class="panel-status">TX.073</span>
            </div>
            <div class="cyber-panel-body">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span class="cyber-label">WIDE BAND</span>
                    <div class="cyber-btn-group">
                        <button class="cyber-btn" style="padding: 4px 8px; font-size: 9px;">LIM</button>
                        <button class="cyber-btn active" style="padding: 4px 8px; font-size: 9px;">×1E</button>
                        <button class="cyber-btn" style="padding: 4px 8px; font-size: 9px;">E2</button>
                    </div>
                </div>
                
                <div class="cyber-data-row">
                    <span class="data-key">SIM4.OUTPUT</span>
                    <span class="cyber-status error">&lt;ERROR&gt;</span>
                </div>
                
                <div class="cyber-waveform" style="margin-top: 8px;">
                    <div class="wave-bar" style="height: 40%;"></div>
                    <div class="wave-bar" style="height: 60%;"></div>
                    <div class="wave-bar" style="height: 80%;"></div>
                    <div class="wave-bar" style="height: 50%;"></div>
                    <div class="wave-bar" style="height: 70%;"></div>
                    <div class="wave-bar" style="height: 30%;"></div>
                    <div class="wave-bar" style="height: 90%;"></div>
                    <div class="wave-bar" style="height: 45%;"></div>
                </div>
            </div>
        </div>
        
        <!-- SIM B Panel -->
        <div class="cyber-panel">
            <div class="cyber-panel-header">
                <span class="panel-title">SIM.B</span>
            </div>
            <div class="cyber-panel-body">
                <div class="cyber-data-row">
                    <span class="data-key">SIM8.OUTPUT</span>
                    <span class="cyber-status online">ONLINE</span>
                </div>
            </div>
        </div>
        
        <!-- Log Panel -->
        <div class="cyber-panel" style="flex: 1;">
            <div class="cyber-panel-header">
                <span class="panel-title">REF.LIN</span>
            </div>
            <div class="cyber-panel-body">
                <div class="cyber-terminal" style="font-size: 9px;">
                    <div class="term-line"><span class="term-output">LOG_INIT</span></div>
                    <div class="term-line"><span class="term-output">ENTER DIAG Y/N?</span></div>
                    <div class="term-line"><span class="term-output">ENTER _/ACCEL_INIT</span></div>
                    <div class="term-line"><span class="term-output">ACCEL_INIT</span></div>
                </div>
            </div>
        </div>
        
        <!-- SYS Simulation -->
        <div class="cyber-panel">
            <div class="cyber-panel-header">
                <span class="panel-title">SYS SIMULATION</span>
                <span class="panel-status">PRI. E-6</span>
            </div>
            <div class="cyber-panel-body">
                <div style="display: flex; gap: 8px;">
                    <button class="cyber-btn" style="padding: 4px 12px; font-size: 9px;">BOOT</button>
                    <button class="cyber-btn" style="padding: 4px 12px; font-size: 9px;">NAV</button>
                </div>
            </div>
        </div>
        
    </div>
    
    <!-- Main Content Area -->
    <div style="display: flex; flex-direction: column; gap: 8px;">
        
        <!-- Trans.DAT Panel -->
        <div class="cyber-panel">
            <div class="cyber-panel-header">
                <span class="panel-title">TRANS.DAT</span>
                <span class="panel-status">TX.STAT</span>
            </div>
            <div class="cyber-panel-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <div class="cyber-label mb-2">DATA STREAM</div>
                        <div class="cyber-progress" style="margin-bottom: 8px;">
                            <div class="cyber-progress-fill" style="width: 65%;"></div>
                        </div>
                        <div class="cyber-terminal" style="height: 80px; font-size: 9px;">
                            <div class="term-line"><span class="term-output">0x5F 0E.05 0A.08 DA.E4</span></div>
                            <div class="term-line"><span class="term-output">0x50 08.88 B8.86 20.C0</span></div>
                            <div class="term-line"><span class="term-output">0x48 5E.0E C7.74 C3.44</span></div>
                        </div>
                    </div>
                    <div>
                        <div class="cyber-label mb-2">REC.STAT</div>
                        <div style="font-size: 10px; color: #7090a8;">
                            <div>DESC.093</div>
                            <div>DESC.093</div>
                            <div style="color: #c87090;">DESC.093</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stream.DAT Panel -->
        <div class="cyber-panel">
            <div class="cyber-panel-header">
                <span class="panel-title">STREAM.DAT</span>
                <span class="panel-status">STR.STAT / BUFFERING</span>
            </div>
            <div class="cyber-panel-body">
                <div class="cyber-terminal" style="font-size: 9px;">
                    <div class="term-line"><span class="term-prefix">0x5F</span> <span class="term-output">0E.05 0A.08 DA.E4 41.40</span></div>
                    <div class="term-line"><span class="term-prefix">0x50</span> <span class="term-output">08.88 B8.86 20.C0 00.00</span></div>
                </div>
                
                <div style="display: flex; gap: 8px; margin-top: 12px;">
                    <div class="cyber-jp-display">消</div>
                    <div class="cyber-jp-display">息</div>
                </div>
            </div>
        </div>
        
        <!-- Systems Panel -->
        <div class="cyber-panel-highlight">
            <div class="cyber-panel-header">
                <span class="panel-title">SYSTEMS</span>
            </div>
            <div class="cyber-panel-body" style="text-align: center; padding: 24px;">
                <div class="cyber-display cyber-display-lg" style="color: #c8d8e8;">&lt;SYSTEM OVERLOAD&gt;</div>
            </div>
        </div>
        
        <!-- Japanese Text Display -->
        <div style="text-align: center; padding: 16px;">
            <div class="cyber-jp-display" style="font-size: 24px; letter-spacing: 0.3em;">最近的消息</div>
        </div>
        
    </div>
    
    <!-- Right Panel Column -->
    <div style="display: flex; flex-direction: column; gap: 8px;">
        
        <!-- ORB Status Panel -->
        <div class="cyber-panel">
            <div class="cyber-panel-header">
                <span class="panel-title">ORB.STATUS</span>
            </div>
            <div class="cyber-panel-body">
                <div class="cyber-data-row">
                    <span class="data-key">ORB.5_IN</span>
                    <span class="cyber-status online">ONLINE</span>
                </div>
                <div class="cyber-data-row">
                    <span class="data-key">ORB.6_IN</span>
                    <span class="cyber-status online">ONLINE</span>
                </div>
            </div>
        </div>
        
        <!-- Terminal Panel -->
        <div class="cyber-panel">
            <div class="cyber-panel-header">
                <span class="panel-title">TERMINAL</span>
                <span class="panel-id">STREAM.DAT</span>
            </div>
            <div class="cyber-panel-body">
                <div class="cyber-label mb-2">N.38</div>
                <div class="cyber-terminal" style="font-size: 9px;">
                    <div class="term-line"><span class="term-output">TX.FREE</span></div>
                    <div class="term-line"><span class="term-output">NAV.7_OUT</span></div>
                </div>
            </div>
        </div>
        
        <!-- NAV Panel -->
        <div class="cyber-panel">
            <div class="cyber-panel-header">
                <span class="panel-title">NAV.STATUS</span>
            </div>
            <div class="cyber-panel-body">
                <div class="cyber-data-row">
                    <span class="data-key">NAV.0_OUT</span>
                    <span class="cyber-status error">&lt;ERROR&gt;</span>
                </div>
                <div class="cyber-data-row">
                    <span class="data-key">NAV.7_OUT</span>
                    <span class="cyber-status error">&lt;ERROR&gt;</span>
                </div>
            </div>
        </div>
        
        <!-- Idx Panel -->
        <div class="cyber-panel">
            <div class="cyber-panel-header">
                <span class="panel-title">REF.LIN</span>
                <span class="panel-status">Idx: 0x56</span>
            </div>
            <div class="cyber-panel-body">
                <div class="cyber-terminal" style="font-size: 8px; line-height: 1.6;">
                    <div class="term-line"><span class="term-output">0x54[4854,0252] 08.45:45:44:28 E5 00</span></div>
                    <div class="term-line"><span class="term-output">0x54[4854,0252] 08.45:45:44:28 E5 00</span></div>
                    <div class="term-line"><span class="term-output">0x54[4854,0252] 08.45:45:44:28 E5 00</span></div>
                </div>
            </div>
        </div>
        
        <!-- NAV Range Panel -->
        <div class="cyber-panel" style="flex: 1;">
            <div class="cyber-panel-header">
                <span class="panel-title">NAV.RANGE</span>
            </div>
            <div class="cyber-panel-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    <div>
                        <div class="cyber-label">NULL MODE</div>
                        <div style="font-size: 10px; color: #7090a8; margin-top: 4px;">
                            <div>CHNL 5</div>
                            <div>CHNL 8</div>
                            <div>LOCK 00</div>
                        </div>
                    </div>
                    <div>
                        <div class="cyber-label">NAV RANGE</div>
                        <div style="font-size: 10px; color: #7090a8; margin-top: 4px;">
                            <div>NAV.RANGE</div>
                            <div>NAV.RANGE</div>
                            <div>NAV.RANGE</div>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 16px;">
                    <div class="cyber-meter">
                        <div class="meter-segment active"></div>
                        <div class="meter-segment active"></div>
                        <div class="meter-segment active"></div>
                        <div class="meter-segment active"></div>
                        <div class="meter-segment active"></div>
                        <div class="meter-segment high"></div>
                        <div class="meter-segment high"></div>
                        <div class="meter-segment"></div>
                        <div class="meter-segment"></div>
                        <div class="meter-segment"></div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
    <!-- Right Sidebar Label -->
    <aside style="display: flex; align-items: center; justify-content: center;">
        <span class="cyber-sidebar-label" style="font-family: 'Noto Sans JP', sans-serif;">最近的消息</span>
    </aside>
    
</main>

<!-- Footer Status Bar -->
<footer style="background: #0a1018; border-top: 1px solid #2a4050; padding: 6px 16px; display: flex; justify-content: space-between; align-items: center;">
    <div style="display: flex; gap: 24px; align-items: center;">
        <div class="cyber-label">PRI8 CONTROL <span style="color: #4a9ead;">CMD</span></div>
        <div class="cyber-label">BOOT</div>
        <div class="cyber-label">NAV</div>
    </div>
    
    <div style="display: flex; gap: 24px; align-items: center;">
        <span class="cyber-badge">FILE</span>
        <span class="cyber-badge cyan">SHEPARD.DIAG</span>
        <span class="cyber-badge">HELIOS.COMM</span>
    </div>
    
    <div class="cyber-label">SYS/STARTUP_SFORCE_REBOOT <span style="color: #c87090;">/FILTER</span></div>
</footer>

</body>
</html>
