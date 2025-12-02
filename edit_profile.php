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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $profile_picture = '';

    // Log form data
    error_log('POST data: ' . print_r($_POST, true));
    error_log('FILES data: ' . print_r($_FILES, true));

    // Define paths
    $image_dir = __DIR__ . '/images/';
    $relative_path = 'images/';

    // Ensure images directory exists
    if (!is_dir($image_dir)) {
        if (!mkdir($image_dir, 0777, true)) {
            $_SESSION['message'] = 'Failed to create images directory.';
            error_log('mkdir failed for ' . $image_dir);
            header('Location: profile.php');
            exit();
        }
    }

    // Check writability
    if (!is_writable($image_dir)) {
        $_SESSION['message'] = 'Images directory is not writable.';
        error_log('Directory not writable: ' . $image_dir);
        header('Location: profile.php');
        exit();
    }

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $img_name = $_FILES['profile_picture']['name'];
        $img_tmp = $_FILES['profile_picture']['tmp_name'];
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png'];

        error_log("Processing upload: name=$img_name, tmp=$img_tmp, ext=$img_ext");

        if (in_array($img_ext, $allowed_exts) && $_FILES['profile_picture']['size'] <= 5000000) {
            // Generate unique filename
            $filename = uniqid('', true) . '.' . $img_ext;
            $profile_picture = $relative_path . $filename;
            $absolute_path = $image_dir . $filename;

            error_log("Attempting to save: relative=$profile_picture, absolute=$absolute_path");

            if (move_uploaded_file($img_tmp, $absolute_path)) {
                error_log("File saved successfully: $absolute_path");
            } else {
                $_SESSION['message'] = 'Failed to save profile picture. Check server logs.';
                error_log("move_uploaded_file failed for $absolute_path");
                header('Location: profile.php');
                exit();
            }
        } else {
            $_SESSION['message'] = 'Invalid image format or size. Use JPG, JPEG, or PNG under 5MB.';
            error_log("Invalid file: ext=$img_ext, size=" . $_FILES['profile_picture']['size']);
            header('Location: profile.php');
            exit();
        }
    } elseif (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize.',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form size limit.',
            UPLOAD_ERR_PARTIAL => 'File only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the upload.'
        ];
        $error_code = $_FILES['profile_picture']['error'];
        $_SESSION['message'] = 'Upload error: ' . ($upload_errors[$error_code] ?? 'Unknown error code ' . $error_code);
        error_log("Upload error: code=$error_code");
        header('Location: profile.php');
        exit();
    } else {
        error_log('No file uploaded or UPLOAD_ERR_NO_FILE');
    }

    // Log profile picture value
    error_log("Profile picture path: '$profile_picture'");

    // Prepare SQL query
    $sql = $profile_picture ? 
           'UPDATE users SET username = ?, email = ?, profile_picture = ? WHERE id = ?' :
           'UPDATE users SET username = ?, email = ? WHERE id = ?';
    error_log("SQL query: $sql");

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $_SESSION['message'] = 'Database prepare error: ' . $conn->error;
        error_log("SQL prepare error: " . $conn->error);
        header('Location: profile.php');
        exit();
    }

    // Bind parameters
    if ($profile_picture) {
        $stmt->bind_param('sssi', $username, $email, $profile_picture, $user_id);
        error_log("Binding with profile_picture: username=$username, email=$email, profile_picture=$profile_picture, user_id=$user_id");
    } else {
        $stmt->bind_param('ssi', $username, $email, $user_id);
        error_log("Binding without profile_picture: username=$username, email=$email, user_id=$user_id");
    }

    // Execute and check result
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['username'] = $username;
            $_SESSION['message'] = 'Profile updated successfully.';
            error_log("Query executed, affected rows: " . $stmt->affected_rows);
        } else {
            $_SESSION['message'] = 'No changes made to profile.';
            error_log("Query executed, but no rows affected");
        }
    } else {
        $_SESSION['message'] = 'Error updating profile: ' . $stmt->error;
        error_log("SQL execute error: " . $stmt->error);
    }
    $stmt->close();
    header('Location: profile.php?v=' . time());
    exit();
}
$conn->close();
?>