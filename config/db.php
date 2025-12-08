<?php
/**
 * Database Configuration
 * Uses environment variables for credentials
 */

require_once __DIR__ . '/env.php';

// Get database credentials from environment
$host = Env::get('DB_HOST', 'localhost');
$user = Env::get('DB_USER', 'root');
$pass = Env::get('DB_PASS', '');
$dbname = Env::get('DB_NAME', 'ewu_lost_found');

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    // Log the error
    if (function_exists('logError')) {
        logError("Database Connection Failed: " . $conn->connect_error, 'CRITICAL');
    }
    
    // Show appropriate error based on environment
    if (Env::isDebug()) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        die("A database error occurred. Please try again later.");
    }
}

// Set charset to prevent encoding issues
$conn->set_charset("utf8mb4");
?>
