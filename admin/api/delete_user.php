<?php
/* ========================================================
    //ANCHOR [API_DELETE_USER]
    FUNCTION: API endpoint to delete admin user
-----------------------------------------------------------
    Parameters: POST JSON (id, csrf_token)
    Returns: JSON
    Description: Deletes an admin user account
    UniqueID: 793406
=========================================================== */

header('Content-Type: application/json');

session_start();
require_once __DIR__ . '/../../includes/db_config.php';

// Check authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Verify CSRF token
if (!isset($input['csrf_token']) || $input['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

try {
    $id = intval($input['id'] ?? 0);

    if (!$id) {
        throw new Exception('Invalid user ID');
    }

    // Prevent self-deletion
    if ($id == $_SESSION['admin_user_id']) {
        throw new Exception('You cannot delete your own account');
    }

    // Check if user exists
    $stmt = $db->prepare("SELECT id FROM admin_users WHERE id = ?");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        throw new Exception('User not found');
    }

    // Delete user sessions first (foreign key constraint)
    $stmt = $db->prepare("DELETE FROM admin_sessions WHERE user_id = ?");
    $stmt->execute([$id]);

    // Delete user
    $stmt = $db->prepare("DELETE FROM admin_users WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'User deleted successfully'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

