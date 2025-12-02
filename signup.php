<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'includes/db_connect.php';

$conn = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $profile_picture = 'images/default.jpeg';

    $sql = "INSERT INTO users (username, email, password, profile_picture) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $_SESSION['message'] = 'Database error: ' . $conn->error;
        header('Location: login-signup.php');
        exit();
    }
    $stmt->bind_param('ssss', $username, $email, $password, $profile_picture);

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Signup successful! Please login.';
        header('Location: login-signup.php');
    } else {
        $_SESSION['message'] = 'Error: Username or email already exists.';
        header('Location: login-signup.php');
    }
    $stmt->close();
}
$conn->close();
?>