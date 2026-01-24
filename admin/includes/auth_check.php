<?php
/* ========================================================
    ADMIN AUTHENTICATION CHECK
    Validates admin session and redirects to login if invalid
=========================================================== */

session_start();

require_once __DIR__ . '/../../includes/db_config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: /admin/login.php');
    exit;
}

// Check session timeout (30 minutes)
$timeout_duration = 1800; // 30 minutes in seconds
if (isset($_SESSION['admin_last_activity']) && (time() - $_SESSION['admin_last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header('Location: /admin/login.php?timeout=1');
    exit;
}

// Update last activity time
$_SESSION['admin_last_activity'] = time();

// Validate session token in database
if (isset($_SESSION['admin_session_token'])) {
    try {
        $stmt = $db->prepare("
            SELECT s.*, u.username, u.email 
            FROM admin_sessions s
            JOIN admin_users u ON s.user_id = u.id
            WHERE s.session_token = ? 
            AND s.expires_at > NOW()
        ");
        $stmt->execute([$_SESSION['admin_session_token']]);
        $session = $stmt->fetch();
        
        if (!$session) {
            session_unset();
            session_destroy();
            header('Location: /admin/login.php?expired=1');
            exit;
        }
        
        // Update last activity in database
        $stmt = $db->prepare("UPDATE admin_sessions SET last_activity = NOW() WHERE session_token = ?");
        $stmt->execute([$_SESSION['admin_session_token']]);
        
        // Store user info in session
        $_SESSION['admin_user_id'] = $session['user_id'];
        $_SESSION['admin_username'] = $session['username'];
        $_SESSION['admin_email'] = $session['email'];
        
    } catch (PDOException $e) {
        error_log("Session validation error: " . $e->getMessage());
        session_unset();
        session_destroy();
        header('Location: /admin/login.php?error=1');
        exit;
    }
}

// CSRF token generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Helper function to verify CSRF token
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Helper function to get CSRF token
function getCsrfToken() {
    return $_SESSION['csrf_token'] ?? '';
}
?>
