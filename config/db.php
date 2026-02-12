<?php
/**
 * Database Configuration File
 * AGRIBOOST SHOP - Database Connection
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'agriboost_shop');

// Create database connection using mysqli
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for proper character encoding
$conn->set_charset("utf8mb4");

// Function to sanitize user input
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
