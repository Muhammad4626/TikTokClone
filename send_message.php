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
$sender_id = $_SESSION['user_id'];
$receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
$message = isset($_POST['message']) ? $conn->real_escape_string($_POST['message']) : '';

if ($receiver_id && $message) {
    $sql = 'INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iis', $sender_id, $receiver_id, $message);
    if ($stmt->execute()) {
        $msg_query = 'SELECT m.message, u.username 
                      FROM messages m 
                      JOIN users u ON m.sender_id = u.id 
                      WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?) 
                      ORDER BY m.created_at';
        $msg_stmt = $conn->prepare($msg_query);
        $msg_stmt->bind_param('iiii', $sender_id, $receiver_id, $receiver_id, $sender_id);
        $msg_stmt->execute();
        $messages = $msg_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['success' => true, 'messages' => $messages]);
        $msg_stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error sending message']);
    }
    $stmt->close();
}
$conn->close();
?>