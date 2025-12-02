<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Signup - TikTok Clone</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/9e5ba2e3f5.js" crossorigin="anonymous" defer></script>
    <script src="main.js" defer></script>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <?php if ($message): ?>
                <p class="message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            
            <h2 id="loginTitle">Login</h2>
            <form id="loginForm" method="POST" action="login.php">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <p>Don't have an account? <a href="#" id="toggleSignup">Sign up</a></p>

            <h2 id="signupTitle" style="display: none;">Signup</h2>
            <form id="signupForm" method="POST" action="signup.php" style="display: none;">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Signup</button>
            </form>
            <p id="toggleLogin" style="display: none;">Already have an account? <a href="#" id="showLogin">Login</a></p>
        </div>
    </div>
</body>
</html>