<?php
/* ========================================================
    //ANCHOR [ADMIN_HEADER_BATCOMPUTER]
    FUNCTION: Admin Header - Batcomputer UI Style
-----------------------------------------------------------
    Parameters: None
    Returns: HTML header output
    Description: Renders admin header with Batcomputer dark tactical interface
    UniqueID: 793101
=========================================================== */
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: /admin/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Dashboard'; ?> // BATCOMPUTER ADMIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/style-batcomputer.css">
    <link rel="stylesheet" href="/admin/css/admin-style.css">
</head>
<body class="admin-body bat-theme">
    <div class="admin-wrapper">
        <!-- BATCOMPUTER Header Bar -->
        <header class="admin-header">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <span class="header-title">BATCOMPUTER // ADMIN CONSOLE</span>
            </div>
            <div class="header-right">
                <div class="header-stats">
                    <?php
                    // Quick stats
                    try {
                        $stmt = $db->query("SELECT COUNT(*) as count FROM articles WHERE status = 'draft'");
                        $drafts = $stmt->fetch()['count'];
                        echo "<span class='stat-badge' title='Draft Articles'><i class='bi bi-file-earmark-text'></i> DRAFTS: {$drafts}</span>";
                    } catch (PDOException $e) {
                        error_log("Stats error: " . $e->getMessage());
                    }
                    ?>
                </div>
                <div class="header-user">
                    <span class="user-name">
                        <i class="bi bi-person-circle"></i>
                        OPERATOR: <?php echo strtoupper(htmlspecialchars($_SESSION['admin_username'] ?? 'Admin')); ?>
                    </span>
                    <a href="/admin/logout.php" class="btn-logout" title="Logout">
                        <i class="bi bi-box-arrow-right"></i> EXIT
                    </a>
                </div>
            </div>
        </header>
