<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];
$video_id = isset($_POST['video_id']) ? (int)$_POST['video_id'] : 0;
$comment = isset($_POST['comment']) ? $conn->real_escape_string($_POST['comment']) : '';

if ($video_id && $comment) {
    $sql = 'INSERT INTO comments (user_id, video_id, comment) VALUES (?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iis', $user_id, $video_id, $comment);
    if ($stmt->execute()) {
        $count_query = 'SELECT COUNT(*) as count FROM comments WHERE video_id = ?';
        $count_stmt = $conn->prepare($count_query);
        $count_stmt->bind_param('i', $video_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result()->fetch_assoc();
        $comment_query = 'SELECT c.comment, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.video_id = ? ORDER BY c.created_at DESC';
        $comment_stmt = $conn->prepare($comment_query);
        $comment_stmt->bind_param('i', $video_id);
        $comment_stmt->execute();
        $comments = $comment_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['success' => true, 'count' => $count_result['count'], 'comments' => $comments]);
        $count_stmt->close();
        $comment_stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error posting comment']);
    }
    $stmt->close();
}
$conn->close();
?>