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

// Fetch user info
$user_query = 'SELECT username, profile_picture FROM users WHERE id = ?';
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param('i', $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_stmt->close();

// Fetch following count
$following_query = 'SELECT COUNT(*) as following_count FROM friends WHERE follower_id = ?';
$following_stmt = $conn->prepare($following_query);
$following_stmt->bind_param('i', $user_id);
if (!$following_stmt->execute()) {
    error_log("Following query error: " . $following_stmt->error);
}
$following_result = $following_stmt->get_result();
$following_count = $following_result->fetch_assoc()['following_count'];
$following_stmt->close();

// Fetch followers count
$followers_query = 'SELECT COUNT(*) as followers_count FROM friends WHERE followed_id = ?';
$followers_stmt = $conn->prepare($followers_query);
$followers_stmt->bind_param('i', $user_id);
if (!$followers_stmt->execute()) {
    error_log("Followers query error: " . $followers_stmt->error);
}
$followers_result = $followers_stmt->get_result();
$followers_count = $followers_result->fetch_assoc()['followers_count'];
$followers_stmt->close();

// Fetch total likes count
$likes_query = 'SELECT COUNT(*) as likes_count 
                FROM likes l 
                JOIN videos v ON l.video_id = v.id 
                WHERE v.user_id = ?';
$likes_stmt = $conn->prepare($likes_query);
$likes_stmt->bind_param('i', $user_id);
if (!$likes_stmt->execute()) {
    error_log("Likes query error: " . $likes_stmt->error);
}
$likes_result = $likes_stmt->get_result();
$likes_count = $likes_result->fetch_assoc()['likes_count'];
$likes_stmt->close();

// Fetch videos
$video_query = 'SELECT v.*, 
                       (SELECT COUNT(*) FROM likes l WHERE l.video_id = v.id) as like_count,
                       (SELECT COUNT(*) FROM comments c WHERE c.video_id = v.id) as comment_count
                FROM videos v 
                WHERE v.user_id = ? 
                ORDER BY v.upload_date DESC';
$video_stmt = $conn->prepare($video_query);
$video_stmt->bind_param('i', $user_id);
$video_stmt->execute();
$video_result = $video_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - TikTok Clone</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/9e5ba2e3f5.js" crossorigin="anonymous" defer></script>
    <script src="main.js" defer></script>
</head>
<body>
    <div class="app-container">
        <?php include 'includes/navbar.php'; ?>
        <main class="content">
            <section class="box profile active">
                <?php if (isset($_SESSION['message'])): ?>
                    <p class="message"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
                <?php endif; ?>
                <div class="profile-info">
                    <div class="profile-img">
                        <img src="<?php echo htmlspecialchars($user['profile_picture'] . '?v=' . time()); ?>" alt="Profile">
                        <h3>@<?php echo htmlspecialchars($user['username']); ?></h3>
                    </div>
                    <ul>
                        <li><h5><?php echo htmlspecialchars($following_count); ?></h5><span>Following</span></li>
                        <li><h5><?php echo htmlspecialchars($followers_count); ?></h5><span>Followers</span></li>
                        <li><h5><?php echo htmlspecialchars($likes_count); ?></h5><span>Likes</span></li>
                    </ul>
                    <div class="buttons">
                        <button class="btn" id="editProfileBtn">Edit Profile</button>
                        <button class="btn" onclick="window.location.href='friends.php'">Add Friends</button>
                    </div>
                </div>
                <div class="videos-container">
                    <?php if ($video_result->num_rows > 0): ?>
                        <?php while ($row = $video_result->fetch_assoc()): ?>
                            <div class="video">
                                <video loop muted autoplay playsinline>
                                    <source src="<?php echo htmlspecialchars($row['video_path']); ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                                <button class="play-btn">Play</button>
                                <div class="video-info">
                                    <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                                    <p><?php echo htmlspecialchars($row['description']); ?></p>
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
                        <p>You haven't uploaded any videos yet.</p>
                    <?php endif; ?>
                </div>
                <div id="editProfileModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeEditProfile">×</span>
                        <h3>Edit Profile</h3>
                        <form id="editProfileForm" action="edit_profile.php" method="post" enctype="multipart/form-data">
                            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            <input type="email" name="email" placeholder="Email" required>
                            <input type="file" name="profile_picture" accept="image/*">
                            <button type="submit">Save</button>
                        </form>
                    </div>
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
<?php $video_stmt->close(); $conn->close(); ?>