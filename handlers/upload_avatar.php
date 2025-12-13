<?php
/**
 * Avatar Upload Handler
 */
require_once '../init.php';

// Require login
requireLogin();

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: ' . APP_URL . '/profile.php?error=invalid_token');
    exit;
}

$user = getCurrentUser();
$userId = $user['id'];

// Check if file was uploaded
if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] === UPLOAD_ERR_NO_FILE) {
    header('Location: ' . APP_URL . '/profile.php?error=no_file');
    exit;
}

// Check for upload errors
if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    header('Location: ' . APP_URL . '/profile.php?error=upload_failed');
    exit;
}

// Validate file type
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$fileType = $_FILES['avatar']['type'];

if (!in_array($fileType, $allowedTypes)) {
    header('Location: ' . APP_URL . '/profile.php?error=invalid_type');
    exit;
}

// Validate file size (max 5MB)
$maxSize = 5 * 1024 * 1024; // 5MB in bytes
if ($_FILES['avatar']['size'] > $maxSize) {
    header('Location: ' . APP_URL . '/profile.php?error=file_too_large');
    exit;
}

// Create avatars directory if it doesn't exist
$uploadDir = dirname(__DIR__) . '/uploads/avatars/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Generate unique filename
$extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
$filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
$targetPath = $uploadDir . $filename;

// Move uploaded file
if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
    header('Location: ' . APP_URL . '/profile.php?error=upload_failed');
    exit;
}

// Delete old avatar if exists
if (!empty($user['avatar'])) {
    $oldAvatar = $uploadDir . $user['avatar'];
    if (file_exists($oldAvatar)) {
        unlink($oldAvatar);
    }
}

// Update database
$stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
$stmt->bind_param("si", $filename, $userId);

if ($stmt->execute()) {
    $stmt->close();
    header('Location: ' . APP_URL . '/profile.php?success=avatar_updated');
} else {
    $stmt->close();
    // Delete uploaded file if database update fails
    if (file_exists($targetPath)) {
        unlink($targetPath);
    }
    header('Location: ' . APP_URL . '/profile.php?error=database_error');
}
exit;
