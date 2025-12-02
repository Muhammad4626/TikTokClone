<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login-signup.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Video - TikTok Clone</title>
    <link rel="stylesheet" href="/tiktok_clone/html_structure/css/styles.css">
</head>
<body>
    <div class="upload-container">
        <h2>Upload New Video</h2>
        <form action="upload_video.php" method="post" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Video title" required>
            <textarea name="description" placeholder="Video description" required></textarea>
            <input type="file" name="video" accept="video/*" required>
            <button type="submit">Upload Video</button>
        </form>
    </div>
</body>
</html>