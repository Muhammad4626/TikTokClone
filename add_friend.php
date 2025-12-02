<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login-signup.php');
    exit();
}

$conn = getDBConnection();
$follower_id = $_SESSION['user_id'];
$followed_id = isset($_POST['followed_id']) ? (int)$_POST['followed_id'] : 0;

if ($followed_id && $followed_id != $follower_id) {
    $sql = 'INSERT INTO friends (follower_id, followed_id) VALUES (?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $follower_id, $followed_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Followed successfully.';
    } else {
        $_SESSION['message'] = 'Error following user.';
    }
    $stmt->close();
}
$conn->close();
header('Location: friends.php');
?>