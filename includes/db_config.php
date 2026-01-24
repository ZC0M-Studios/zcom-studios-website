<?php
/* ========================================================
    //ANCHOR [DATABASE_CONFIG]
    FUNCTION: Database configuration and connection
-----------------------------------------------------------
    Parameters: N/A
    Returns: PDO connection object
    Description: Establishes MySQL database connection using PDO with error handling
    UniqueID: 123470
=========================================================== */
/*
THIS CONFIG IS FOR THE REMOTE SERVER TEST
// Database configuration
define('DB_HOST', '50.6.108.182');
define('DB_NAME', 'jdwxjwte_zcom_db');
define('DB_USER', 'jdwxjwte_zcom_agent');  // Change this to your MySQL username
define('DB_PASS', '124ADR664leu_');      // Change this to your MySQL password
define('DB_CHARSET', 'utf8mb4');
*/

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'jdwxjwte_zcom_db');
define('DB_USER', 'jdwxjwte_zcom_agent');  // Change this to your MySQL username
define('DB_PASS', '124ADR664leu_');      // Change this to your MySQL password
define('DB_CHARSET', 'utf8mb4');

// Create PDO connection
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        // Check if this is an API request (expects JSON)
        if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Database connection failed']);
            exit;
        }
        die("Database connection failed. Please check your configuration.");
    }
}

// Global database connection
$db = getDBConnection();
// Connection successful - no output to avoid breaking JSON/HTML responses

?>
