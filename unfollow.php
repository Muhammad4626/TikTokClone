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

    // Delete follow
    $delete_query = 'DELETE FROM friends WHERE follower_id = ? AND followed_id = ?';
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param('ii', $user_id, $followed_id);

    if ($delete_stmt->execute()) {
        if ($delete_stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Successfully unfollowed user.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Not following this user.']);
        }
    } else {
        error_log("Unfollow error: " . $delete_stmt->error);
        echo json_encode(['success' => false, 'message' => 'Error unfollowing user: ' . $delete_stmt->error]);
    }
    $delete_stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$conn->close();
exit();
?>