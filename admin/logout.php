<?php
/* ========================================================
    ADMIN LOGOUT
    Destroys session and redirects to login
=========================================================== */

session_start();

require_once __DIR__ . '/../includes/db_config.php';

// Delete session from database
if (isset($_SESSION['admin_session_token'])) {
    try {
        $stmt = $db->prepare("DELETE FROM admin_sessions WHERE session_token = ?");
        $stmt->execute([$_SESSION['admin_session_token']]);
    } catch (PDOException $e) {
        error_log("Logout error: " . $e->getMessage());
    }
}

// Clear remember me cookie
if (isset($_COOKIE['admin_remember'])) {
    setcookie('admin_remember', '', time() - 3600, '/', '', false, true);
}

// Destroy session
session_unset();
session_destroy();

// Redirect to login
header('Location: /admin/login.php?logout=1');
exit;
?>
