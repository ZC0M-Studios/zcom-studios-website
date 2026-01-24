<?php
/* ========================================================
    //ANCHOR [API_CREATE_USER]
    FUNCTION: API endpoint to create admin user
-----------------------------------------------------------
    Parameters: POST (username, email, password)
    Returns: JSON
    Description: Creates a new admin user account
    UniqueID: 793404
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
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        throw new Exception('All fields are required');
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

    if (strlen($password) < 8) {
        throw new Exception('Password must be at least 8 characters');
    }

    // Check for existing username or email
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM admin_users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()['count'] > 0) {
        throw new Exception('Username or email already exists');
    }

    // Create user
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("INSERT INTO admin_users (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $password_hash]);

    echo json_encode([
        'success' => true,
        'message' => 'User created successfully',
        'id' => $db->lastInsertId()
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

