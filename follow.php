<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'includes/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['followed_id'])) {
    $followed_id = (int)$_POST['followed_id'];

    // Prevent self-follow
    if ($followed_id === $user_id) {
        echo json_encode(['success' => false, 'message' => 'You cannot follow yourself.']);
        exit();
    }

    // Check if already following
    $check_query = 'SELECT COUNT(*) as count FROM friends WHERE follower_id = ? AND followed_id = ?';
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param('ii', $user_id, $followed_id);
    $check_stmt->execute();
    $count = $check_stmt->get_result()->fetch_assoc()['count'];
    $check_stmt->close();

    if ($count > 0) {
        echo json_encode(['success' => false, 'message' => 'Already following this user.']);
        exit();
    }

    // Insert follow
    $insert_query = 'INSERT INTO friends (follower_id, followed_id) VALUES (?, ?)';
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param('ii', $user_id, $followed_id);

    if ($insert_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Successfully followed user.']);
    } else {
        error_log("Follow error: " . $insert_stmt->error);
        echo json_encode(['success' => false, 'message' => 'Error following user: ' . $insert_stmt->error]);
    }
    $insert_stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$conn->close();
exit();
?>