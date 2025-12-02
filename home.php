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
$query = 'SELECT v.*, u.username, 
                 (SELECT COUNT(*) FROM likes l WHERE l.video_id = v.id) as like_count,
                 (SELECT COUNT(*) FROM comments c WHERE c.video_id = v.id) as comment_count
          FROM videos v JOIN users u ON v.user_id = u.id 
          ORDER BY v.upload_date DESC';
$result = $conn->query($query);
if (!$result) {
    die('Query error: ' . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - TikTok Clone</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/9e5ba2e3f5.js" crossorigin="anonymous" defer></script>
    <script src="main.js" defer></script>
</head>
<body>
    <div class="app-container">
        <?php include 'includes/navbar.php'; ?>
        <main class="content">
            <section class="box home active">
                <div class="video-feed">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="video-wrapper">
                                <div class="video-container">
                                    <video loop muted autoplay playsinline>
                                        <source src="<?php echo htmlspecialchars($row['video_path']); ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    <button class="play-btn">Play</button>
                                </div>
                                <div class="video-info">
                                    <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                                    <p>By: @<?php echo htmlspecialchars($row['username']); ?></p>
                                </div>
                                <aside class="video-sidebar">
                                    <ul>
                                        <li><button class="like-btn" data-video-id="<?php echo $row['id']; ?>"><i class="fa-solid fa-heart"></i><span><?php echo $row['like_count']; ?></span></button></li>
                                        <li><button class="comment-btn" data-video-id="<?php echo $row['id']; ?>"><i class="fa-solid fa-comment-dots"></i><span><?php echo $row['comment_count']; ?></span></button></li>
                                        <li><i class="fa-solid fa-share"></i><span>0</span></li>
                                    </ul>
                                </aside>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No videos uploaded yet.</p>
                    <?php endif; ?>
                </div>
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
<?php $conn->close(); ?>