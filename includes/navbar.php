<div class="top-container">
    <div class="logout">
        <form method="POST" action="logout.php">
            <button type="submit">Logout</button>
        </form>
    </div>
    <nav class="menu-bar">
        <ul>
            <li><a href="home.php"><i class="fa-solid fa-house"></i><span>Home</span></a></li>
            <li><a href="friends.php"><i class="fa-solid fa-user-group"></i><span>Friends</span></a></li>
            <li id="uploadBtn"><i class="fa-solid fa-plus"></i><span>Upload</span></li>
            <li><a href="inbox.php"><i class="fa-regular fa-message"></i><span>Inbox</span></a></li>
            <li><a href="profile.php"><i class="fa-regular fa-user"></i><span>Profile</span></a></li>
        </ul>
    </nav>
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeUpload">&times;</span>
            <iframe src="upload_video.php" frameborder="0" style="width: 100%; height: 400px;"></iframe>
        </div>
    </div>
</div>