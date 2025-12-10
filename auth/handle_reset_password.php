<?php
/**
 * Handle Password Reset
 */
require_once '../init.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

// Rate limiting
if (!checkRateLimit('reset_' . $_SERVER['REMOTE_ADDR'])) {
    header("Location: login.php?error=rate_limit");
    exit();
}

$token = $_POST['token'] ?? '';

// Basic token validation
if (empty($token) || strlen($token) !== 64) {
    header("Location: reset_password.php?error=invalid_token");
    exit();
}

// CSRF validation
if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    header("Location: reset_password.php?token=$token&error=invalid_token");
    exit();
}

$password = $_POST['password'];
$passwordConfirm = $_POST['password_confirm'];

// Validate password
if (strlen($password) < 8) {
    header("Location: reset_password.php?token=$token&error=password_weak");
    exit();
}

if ($password !== $passwordConfirm) {
    header("Location: reset_password.php?token=$token&error=password_mismatch");
    exit();
}

// Check database for valid token
// Token must exist and expiration must be in the future
$stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    logActivity("Invalid password reset attempt", ['token_fragment' => substr($token, 0, 8)]);
    header("Location: reset_password.php?error=invalid_token");
    exit();
}

// Update password and clear token
$passwordHash = hashPassword($password);

$stmt = $conn->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
$stmt->bind_param("si", $passwordHash, $user['id']);

if ($stmt->execute()) {
    logActivity("Password reset successful", ['user_id' => $user['id']]);
    header("Location: login.php?success=password_reset");
} else {
    logError("Password reset database error", 'ERROR', ['error' => $stmt->error]);
    header("Location: reset_password.php?token=$token&error=database_error");
}

$stmt->close();
$conn->close();
?>
