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

if ($video_id) {
    $sql = 'INSERT INTO likes (user_id, video_id) VALUES (?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $user_id, $video_id);
    if ($stmt->execute()) {
        $count_query = 'SELECT COUNT(*) as count FROM likes WHERE video_id = ?';
        $count_stmt = $conn->prepare($count_query);
        $count_stmt->bind_param('i', $video_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result()->fetch_assoc();
        echo json_encode(['success' => true, 'count' => $count_result['count']]);
        $count_stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Already liked']);
    }
    $stmt->close();
}
$conn->close();
?>