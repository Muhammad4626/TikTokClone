<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit();
} else {
    header('Location: login-signup.php');
    exit();
}
?>