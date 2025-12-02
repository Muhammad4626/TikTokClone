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
$user_id = $_SESSION['user_id'];

// Fetch conversations
$query = 'SELECT DISTINCT u.id, u.username, u.profile_picture
          FROM users u
          JOIN messages m ON u.id = m.sender_id OR u.id = m.receiver_id
          WHERE m.sender_id = ? OR m.receiver_id = ?
          ORDER BY m.created_at DESC';
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $user_id, $user_id);
$stmt->execute();
$conversations = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox - TikTok Clone</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/9e5ba2e3f5.js" crossorigin="anonymous" defer></script>
    <script src="main.js" defer></script>
</head>
<body>
    <div class="app-container">
        <?php include 'includes/navbar.php'; ?>
        <main class="content">
            <section class="box inbox active">
                <h2>Inbox</h2>
                <div class="message-list">
                    <?php if ($conversations->num_rows > 0): ?>
                        <?php while ($user = $conversations->fetch_assoc()): ?>
                            <div class="conversation" data-user-id="<?php echo $user['id']; ?>">
                                <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile">
                                <span>@<?php echo htmlspecialchars($user['username']); ?></span>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No messages yet.</p>
                    <?php endif; ?>
                </div>
                <div id="messageModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeMessage">×</span>
                        <h3>Chat</h3>
                        <div id="messageList"></div>
                        <form id="messageForm">
                            <input type="hidden" id="receiverId" name="receiver_id">
                            <textarea name="message" placeholder="Type a message..." required></textarea>
                            <button type="submit">Send</button>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
<?php $stmt->close(); $conn->close(); ?>