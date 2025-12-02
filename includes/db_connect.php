<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = 'localhost';
$db = 'tiktok_clone';
$user = 'root'; // Default XAMPP MySQL user
$pass = '';     // Default XAMPP MySQL password (empty)

// Initialize connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    $_SESSION['message'] = 'Database connection failed: ' . $conn->connect_error;
    header('Location: /tiktok_clone/html_structure/login-signup.php');
    exit();
}

// Set charset
$conn->set_charset('utf8mb4');

// Function to get connection
function getDBConnection() {
    global $conn;
    if ($conn->ping()) {
        return $conn;
    }
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        $_SESSION['message'] = 'Database reconnection failed: ' . $conn->connect_error;
        header('Location: /tiktok_clone/html_structure/login-signup.php');
        exit();
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}
?>