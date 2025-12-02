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
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['video'])) {
    $user_id = $_SESSION['user_id'];
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);

    $video_name = $_FILES['video']['name'];
    $video_tmp = $_FILES['video']['tmp_name'];
    $video_ext = strtolower(pathinfo($video_name, PATHINFO_EXTENSION));
    $allowed_exts = ['mp4', 'webm', 'ogg'];

    if (!in_array($video_ext, $allowed_exts)) {
        $message = 'Invalid video format.';
    } elseif ($_FILES['video']['size'] > 100000000) { // 100MB
        $message = 'Video file too large.';
    } else {
        $video_path = 'Uploads/videos/' . uniqid() . '.' . $video_ext;
        if (move_uploaded_file($video_tmp, $video_path)) {
            $stmt = $conn->prepare('INSERT INTO videos (user_id, title, description, video_path, upload_date, likes) VALUES (?, ?, ?, ?, NOW(), 0)');
            $stmt->bind_param('isss', $user_id, $title, $description, $video_path);
            if ($stmt->execute()) {
                $_SESSION['message'] = 'Video uploaded successfully.';
                header('Location: index.php');
            } else {
                $message = 'Database error: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = 'Video upload failed: ' . $_FILES['video']['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Video - TikTok Clone</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://kit.fontawesome.com/9e5ba2e3f5.js" crossorigin="anonymous" defer></script>
</head>
<body>
    <div class="upload-container">
        <h2>Upload New Video</h2>
        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form action="upload_video.php" method="post" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Video title" required>
            <textarea name="description" placeholder="Video description" required></textarea>
            <input type="file" name="video" accept="video/*" required>
            <button type="submit">Upload Video</button>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>