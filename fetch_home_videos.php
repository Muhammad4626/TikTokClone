<?php
session_start();
include('db_connect.php');

$stmt = $conn->prepare("SELECT * FROM videos ORDER BY id DESC");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($video = $result->fetch_assoc()) {
        echo '<section class="box home">';
        echo '<video controls loop autoplay muted>';
        echo '<source src="' . htmlspecialchars($video['video_path']) . '" type="video/mp4">';
        echo '</video>';
        echo '<div class="top-bar">
                <div class="live"><div class="inner-live">live</div></div>
                <div class="following-group">
                    <span class="following">following</span>
                    <span class="for-you active">for you</span>
                </div>
                <div class="search"><i class="fa-solid fa-magnifying-glass"></i></div>
              </div>';
        echo '<div class="right-bar">
                <ul>
                    <li><img src="img/profile.jpeg" alt="User profile"></li>
                    <li><i class="fa-solid fa-heart"></i><span>0</span></li>
                    <li><i class="fa-solid fa-comment-dots"></i><span>0</span></li>
                    <li><i class="fa-solid fa-bookmark"></i><span>0</span></li>
                    <li><i class="fa-solid fa-share"></i><span>0</span></li>
                </ul>
              </div>';
        echo '</section>';
    }
} else {
    echo "<p>No videos uploaded yet.</p>";
}
?>
