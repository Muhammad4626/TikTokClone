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

// Fetch videos from followed users
$query = 'SELECT v.*, u.username,
                 (SELECT COUNT(*) FROM likes l WHERE l.video_id = v.id) as like_count,
                 (SELECT COUNT(*) FROM comments c WHERE c.video_id = v.id) as comment_count
          FROM videos v 
          JOIN users u ON v.user_id = u.id
          JOIN friends f ON v.user_id = f.followed_id
          WHERE f.follower_id = ?
          ORDER BY v.upload_date DESC';
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch suggested users
$suggested_query = 'SELECT u.id, u.username, COALESCE(u.profile_picture, "images/default.png") as profile_picture 
                   FROM users u 
                   WHERE u.id != ? 
                   AND u.id NOT IN (SELECT followed_id FROM friends WHERE follower_id = ?)
                   LIMIT 5';
$suggested_stmt = $conn->prepare($suggested_query);
$suggested_stmt->bind_param('ii', $user_id, $user_id);
$suggested_stmt->execute();
$suggested_result = $suggested_stmt->get_result();

// Fetch followed users
$followed_query = 'SELECT u.id, u.username, COALESCE(u.profile_picture, "images/default.png") as profile_picture
                  FROM users u
                  JOIN friends f ON u.id = f.followed_id
                  WHERE f.follower_id = ?';
$followed_stmt = $conn->prepare($followed_query);
$followed_stmt->bind_param('i', $user_id);
$followed_stmt->execute();
$followed_result = $followed_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Following - TikTok Clone</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/9e5ba2e3f5.js" crossorigin="anonymous" defer></script>
    <script src="main.js" defer></script>
</head>
<body>
    <div class="app-container">
        <?php include 'includes/navbar.php'; ?>
        <main class="content">
            <section class="box friends active">
                <?php if (isset($_SESSION['message'])): ?>
                    <p class="message"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
                <?php endif; ?>
                <div class="top-bar">
                    <h3>Following</h3>
                    <div class="search">
                        <input type="text" id="friendSearch" placeholder="Search users">
                    </div>
                </div>
                <div class="followed-users">
                    <h4>Following</h4>
                    <?php if ($followed_result->num_rows > 0): ?>
                        <ul>
                            <?php while ($user = $followed_result->fetch_assoc()): ?>
                                <li>
                                    <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile">
                                    <span>@<?php echo htmlspecialchars($user['username']); ?></span>
                                    <button class="unfollow-btn" data-user-id="<?php echo $user['id']; ?>">Unfollow</button>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p>You are not following anyone yet.</p>
                    <?php endif; ?>
                </div>
                <div class="suggested-users">
                    <h4>Suggested Users</h4>
                    <ul>
                        <?php while ($user = $suggested_result->fetch_assoc()): ?>
                            <li>
                                <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile">
                                <span>@<?php echo htmlspecialchars($user['username']); ?></span>
                                <button class="follow-btn" data-user-id="<?php echo $user['id']; ?>">Follow</button>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <!-- Video feed commented out -->
                <div id="commentModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeComment">×</span>
                        <h3>Comments</h3>
                        <div id="commentList"></div>
                        <form id="commentForm">
                            <input type="hidden" id="commentVideoId" name="video_id">
                            <textarea name="comment" placeholder="Add a comment..." required></textarea>
                            <button type="submit">Post</button>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
<?php $stmt->close(); $suggested_stmt->close(); $followed_stmt->close(); $conn->close(); ?>