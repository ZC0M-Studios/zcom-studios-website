<?php
$page_title = "Batcomputer Interface Demo";
include 'includes/header.php';
?>
<link rel="stylesheet" href="/css/style-batcomputer.css">
</head>
<body class="bat-theme bat-scanlines">

<!-- ========================================================
    //ANCHOR [BATCOMPUTER_DEMO]
    FUNCTION: Batcomputer UI Component Showcase
-----------------------------------------------------------
    Parameters: N/A
    Returns: HTML
    Description: Comprehensive demo of Batcomputer stylesheet
    UniqueID: 793100
=========================================================== -->

<!-- Header Bar -->
<header class="bat-header">
    <div class="bat-logo">
        <svg class="bat-icon" viewBox="0 0 100 60" xmlns="http://www.w3.org/2000/svg">
            <path d="M50 0 L30 20 L0 15 L15 30 L0 45 L30 40 L50 60 L70 40 L100 45 L85 30 L100 15 L70 20 Z" fill="currentColor"/>
        </svg>
        <div>
            <div class="bat-title">Wayne Enterprises</div>
            <div style="font-size: 9px; color: #505050;">APPLIED SCIENCES DIV.</div>
        </div>
    </div>
    
    <div class="bat-case-id">
        <span class="case-number">87A</span>
        <span class="case-title">CASE FILE 0714.2015_87A</span>
    </div>
    
    <div style="font-family: 'Share Tech Mono', monospace; font-size: 10px; color: #606060;">
        SMB //192.168.0.199
    </div>
</header>

<main style="padding: 8px; display: grid; grid-template-columns: 220px 1fr 280px; gap: 8px; min-height: calc(100vh - 60px);">
    
    <!-- Left Sidebar -->
    <aside style="display: flex; flex-direction: column; gap: 8px;">
        
        <!-- Sync Panel -->
        <div class="bat-panel">
            <div class="bat-panel-header">
                <span class="panel-title">SYNC*</span>
                <span class="panel-status">B7</span>
            </div>
            <div class="bat-panel-body">
                <div class="bat-label mb-2">System Status</div>
                <div style="font-size: 10px; color: #808080; margin-bottom: 12px;">
                    <div>PRIORITY: <span style="color: #b0b0b0;">HIGH</span></div>
                    <div>ENCRYPTION: <span style="color: #b0b0b0;">AES-256</span></div>
                </div>
                
                <div class="bat-label mb-2">Memory Addresses</div>
                <div class="bat-hex-display" style="font-size: 9px;">
                    <div><span class="hex-address">0x2580:</span> <span class="hex-data">SURV_10 03_14_SQ2_S206 DNG</span></div>
                    <div><span class="hex-address">0x2584:</span> <span class="hex-data">SURV_10 03_14_SQ2_S206 DNG</span></div>
                    <div><span class="hex-address">0x2588:</span> <span class="hex-data">SURV_10 03_14_SQ2_S205 DNG</span></div>
                    <div><span class="hex-address">0x258C:</span> <span class="hex-data">SURV_10 03_14_SQ2_S205 DNG</span></div>
                    <div><span class="hex-address">0x2590:</span> <span class="hex-data">SURV_10 03_14_SQ2_S311 DNG</span></div>
                </div>
            </div>
        </div>
        
        <!-- View Controls -->
        <div class="bat-panel">
            <div class="bat-panel-header">
                <span class="panel-title">View</span>
                <span class="panel-status">Zoom</span>
            </div>
            <div class="bat-panel-body">
                <div class="bat-mini-chart" style="margin-bottom: 8px;">
                    <div class="bat-chart-bar" style="height: 30%;"></div>
                    <div class="bat-chart-bar" style="height: 50%;"></div>
                    <div class="bat-chart-bar active" style="height: 70%;"></div>
                    <div class="bat-chart-bar highlight" style="height: 90%;"></div>
                    <div class="bat-chart-bar active" style="height: 60%;"></div>
                    <div class="bat-chart-bar" style="height: 40%;"></div>
                    <div class="bat-chart-bar" style="height: 20%;"></div>
                </div>
                <div class="bat-waveform"></div>
            </div>
        </div>
        
        <!-- Network Connections -->
        <div class="bat-panel" style="flex: 1;">
            <div class="bat-panel-header">
                <span class="panel-title">Network Log</span>
            </div>
            <div class="bat-panel-body">
                <div class="bat-network-list">
                    <div class="bat-network-item">
                        <span class="network-ip">72.229.28.185</span>
                        <span class="network-type">id:</span>
                        <span class="network-id">dkwer-xr-v</span>
                    </div>
                    <div class="bat-network-item">
                        <span class="network-ip">72.229.28.185</span>
                        <span class="network-type">id:</span>
                        <span class="network-id">dkwer-xr-v</span>
                    </div>
                    <div class="bat-network-item">
                        <span class="network-ip">72.229.28.185</span>
                        <span class="network-type">id:</span>
                        <span class="network-id" style="color: #00ff66;">2G09427.236L156</span>
                    </div>
                    <div class="bat-network-item">
                        <span class="network-ip">72.229.28.185</span>
                        <span class="network-type">id:</span>
                        <span class="network-id" style="color: #ffaa00;">dkwer-xr-k</span>
                    </div>
                </div>
            </div>
        </div>
        
    </aside>
    
    <!-- Main Content Area -->
    <div style="display: flex; flex-direction: column; gap: 8px;">
        
        <!-- Transfer Panel with Timer -->
        <div class="bat-panel">
            <div class="bat-panel-header">
                <span class="panel-title">TRANSFER</span>
                <span class="panel-status">SYNC*</span>
            </div>
            <div class="bat-panel-body">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div class="bat-label mb-2">Time Remaining</div>
                        <div class="bat-timer">07:01</div>
                    </div>
                    <div style="text-align: right;">
                        <div class="bat-label mb-2">Transfer Status</div>
                        <div class="bat-progress" style="width: 200px; margin-bottom: 8px;">
                            <div class="bat-progress-fill" style="width: 51%;"></div>
                        </div>
                        <div class="bat-percentage">51.4<span class="percent-symbol">%</span></div>
                    </div>
                </div>
                
                <div style="margin-top: 16px;">
                    <div class="bat-label mb-2">Data Fragment</div>
                    <table class="bat-data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fragment Data</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>0x3F</td>
                                <td class="highlight">4AED:30:40:7F:A0:7A:BC:00:4E:10</td>
                                <td class="green">90.0%</td>
                            </tr>
                            <tr>
                                <td>0x6F</td>
                                <td>3383:42:54:10:40:0F:C2:20:0F:F1</td>
                                <td>88.2%</td>
                            </tr>
                            <tr>
                                <td>0x72</td>
                                <td>2A8E:1F:0E:05:0A:08:DA:E4:41:40</td>
                                <td>96.4%</td>
                            </tr>
                            <tr>
                                <td>0x5F</td>
                                <td>0165:1C:00:77:53:05:20:08:F0:00</td>
                                <td>92.1%</td>
                            </tr>
                            <tr>
                                <td>0x50</td>
                                <td>5315:AF:08:88:B8:86:20:C0:00:00</td>
                                <td class="red">—</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Transfer Log -->
        <div class="bat-panel" style="flex: 1;">
            <div class="bat-panel-header">
                <span class="panel-title">Transfer Log</span>
            </div>
            <div class="bat-panel-body">
                <div class="bat-log">
                    <div class="bat-log-entry">
                        <span class="log-ip">192.168.0.199</span>
                        <span class="log-action">Transferring</span>
                        <span class="log-details">Fragment Idx: 0x28, SDCA:F7:E0:00:74:06:50:00:00</span>
                        <span class="log-status">Complete</span>
                    </div>
                    <div class="bat-log-entry">
                        <span class="log-ip">192.168.0.199</span>
                        <span class="log-action">Transfer</span>
                        <span class="log-details">Complete 6292:38:0F:24:3C:C1:34:85:00:72 (2400 msecs)</span>
                        <span class="log-status">Complete</span>
                    </div>
                    <div class="bat-log-entry">
                        <span class="log-ip">192.168.0.199</span>
                        <span class="log-action">Transferring</span>
                        <span class="log-details">Fragment Idx: 0x48, 8A9E:0F:00:00:00:00:00:00:00</span>
                        <span class="log-status">In Progress</span>
                    </div>
                    <div class="bat-log-entry">
                        <span class="log-ip">192.168.0.199</span>
                        <span class="log-action">Transfer</span>
                        <span class="log-details">Complete 537F:78:40:12:20:4E:FC:0E:1C (2032.7 msecs)</span>
                        <span class="log-status">Complete</span>
                    </div>
                    <div class="bat-log-entry">
                        <span class="log-ip">192.168.0.199</span>
                        <span class="log-action">Transferring</span>
                        <span class="log-details">Fragment Idx: 0x4B, TEEB:47:F3:34:00:58:5A:06:1F</span>
                        <span class="log-status error">Pending</span>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
    <!-- Right Sidebar -->
    <aside style="display: flex; flex-direction: column; gap: 8px;">
        
        <!-- Memory Display -->
        <div class="bat-panel">
            <div class="bat-panel-header">
                <span class="panel-title">MEM.D</span>
                <span class="panel-status">[ 0x000F 03 A8 ]</span>
            </div>
            <div class="bat-panel-body">
                <div class="bat-hex-display" style="font-size: 9px; line-height: 1.8;">
                    <div><span class="hex-address">CM 0</span> Elapsed 1 min | 00:14:30 v016.03</div>
                    <div><span class="hex-address">CM 0</span> Elapsed 12 min | 00:20:00.00</div>
                    <div style="margin-top: 8px; color: #606060;">
                        00:54-80:10:58:49 -73 48 Beacons #Data<br>
                        BSSID 18:A4:20:52:58:A8:47 0275 72650
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Packet Headers -->
        <div class="bat-panel">
            <div class="bat-panel-header">
                <span class="panel-title">PACKET.HDRS</span>
            </div>
            <div class="bat-panel-body">
                <div class="bat-packet-display">
                    <div class="packet-line">
                        <span class="packet-id">0x48[5052,0078]</span>
                        <span class="packet-data">42:AT:28:C2:48:00:1C:0E:72:50</span>
                        <span class="packet-status">→ Moving to 0xE5</span>
                    </div>
                    <div class="packet-line">
                        <span class="packet-id">0x48[0138,1780]</span>
                        <span class="packet-data">72:09:8F:00:42:40:CF:00:12</span>
                        <span class="packet-status">1024B[B1926] 1d</span>
                    </div>
                    <div class="packet-line">
                        <span class="packet-id">0x21[7230,0780]</span>
                        <span class="packet-data">14:A0:22:00:00:00:00:00:00</span>
                        <span class="packet-status">Prc 0x2037</span>
                    </div>
                    <div class="packet-line">
                        <span class="packet-id">0x48[4472,5498]</span>
                        <span class="packet-data">42:30:31:00:82</span>
                        <span class="packet-status">→ Moving to Idx: 0x03</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Report Panel (Red) -->
        <div class="bat-panel-red">
            <div class="bat-panel-header">
                <span class="panel-title">H4 REPORT</span>
                <span class="panel-status">04 / 21</span>
            </div>
            <div class="bat-panel-body">
                <div style="font-size: 10px; color: #808080; line-height: 1.6;">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...
                </div>
                <div style="margin-top: 12px;">
                    <div class="bat-label">Misc</div>
                    <div style="font-size: 24px; color: #fff; font-family: 'JetBrains Mono', monospace;">3/8</div>
                </div>
            </div>
        </div>
        
        <!-- Server Connections -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
            <div class="bat-server-box active">
                <div class="server-label">Primary</div>
                <div class="server-address">SKYSERVER[124.18.172.8</div>
            </div>
            <div class="bat-server-box">
                <div class="server-label">Secondary</div>
                <div class="server-address">SKYSERVER2[124.18.172.</div>
            </div>
        </div>
        
        <!-- Decryption Status -->
        <div class="bat-panel" style="flex: 1;">
            <div class="bat-panel-header">
                <span class="panel-title">OPT.HDRS</span>
            </div>
            <div class="bat-panel-body">
                <div style="margin-bottom: 12px;">
                    <div class="bat-label mb-1">Key Found:</div>
                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #00ff66; background: #111; padding: 4px 8px; border: 1px solid #333;">
                        [57:09:FA:90:A9:07:C8]
                    </div>
                </div>
                <div class="bat-status online" style="margin-bottom: 8px;">Decrypted</div>
                <div style="font-size: 10px; color: #606060;">
                    <div>DEPTH: 8</div>
                    <div>BYTES(valid): 0x1A(0x1E) 03,1943</div>
                    <div>Tested: 1,048,576</div>
                </div>
            </div>
        </div>
        
    </aside>
    
</main>

<!-- Footer Status Bar -->
<footer style="background: #0a0a0a; border-top: 1px solid #333; padding: 4px 16px; display: flex; justify-content: space-between; align-items: center;">
    <div style="display: flex; gap: 16px;">
        <div class="bat-status online">Oracle Network</div>
        <div style="font-size: 10px; color: #606060;">LINK ESTABLISHED</div>
    </div>
    <div style="font-family: 'Share Tech Mono', monospace; font-size: 10px; color: #606060;">
        02:15 PM 02/10/2025
    </div>
</footer>

</body>
</html>
