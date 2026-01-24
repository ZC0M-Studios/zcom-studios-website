<?php
/* ========================================================
    //ANCHOR [API_UPDATE_USER]
    FUNCTION: API endpoint to update admin user
-----------------------------------------------------------
    Parameters: POST (id, username, email, password optional)
    Returns: JSON
    Description: Updates an existing admin user account
    UniqueID: 793405
=========================================================== */

header('Content-Type: application/json');

session_start();
require_once __DIR__ . '/../../includes/db_config.php';

// Check authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

try {
    $id = intval($_POST['id'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$id) {
        throw new Exception('Invalid user ID');
    }

    // Validation
    if (empty($username) || empty($email)) {
        throw new Exception('Username and email are required');
    }

    if (strlen($username) < 3 || strlen($username) > 50) {
        throw new Exception('Username must be 3-50 characters');
    }

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        throw new Exception('Username can only contain letters, numbers, and underscores');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }

    if ($password && strlen($password) < 8) {
        throw new Exception('Password must be at least 8 characters');
    }

    // Check for existing username or email (excluding current user)
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM admin_users WHERE (username = ? OR email = ?) AND id != ?");
    $stmt->execute([$username, $email, $id]);
    if ($stmt->fetch()['count'] > 0) {
        throw new Exception('Username or email already exists');
    }

    // Update user
    if ($password) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE admin_users SET username = ?, email = ?, password_hash = ? WHERE id = ?");
        $stmt->execute([$username, $email, $password_hash, $id]);
    } else {
        $stmt = $db->prepare("UPDATE admin_users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $id]);
    }

    // If user updated their own username, update session
    if ($id == $_SESSION['admin_user_id']) {
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_email'] = $email;
    }

    echo json_encode([
        'success' => true,
        'message' => 'User updated successfully'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

