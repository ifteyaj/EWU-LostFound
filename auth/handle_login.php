<?php
/**
 * Handle Login Form Submission
 */
require_once '../init.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

// Rate limiting
if (!checkRateLimit('login_' . $_SERVER['REMOTE_ADDR'])) {
    logActivity("Rate limit exceeded for login", ['ip' => $_SERVER['REMOTE_ADDR']]);
    header("Location: login.php?error=rate_limit");
    exit();
}

// CSRF validation
if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    logActivity("Invalid CSRF token on login");
    header("Location: login.php?error=invalid_token");
    exit();
}

// Validate required fields
if (empty($_POST['email']) || empty($_POST['password'])) {
    header("Location: login.php?error=invalid_credentials");
    exit();
}

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];

// Validate email format
if (!isValidEmail($email)) {
    header("Location: login.php?error=invalid_credentials");
    exit();
}

// Find user by email
$user = findUserByEmail($email);

if (!$user) {
    logActivity("Failed login attempt - user not found", ['email' => $email]);
    header("Location: login.php?error=invalid_credentials");
    exit();
}

// Verify password
if (!verifyPassword($password, $user['password_hash'])) {
    logActivity("Failed login attempt - wrong password", ['email' => $email, 'user_id' => $user['id']]);
    header("Location: login.php?error=invalid_credentials");
    exit();
}

// Login successful
loginUser($user);

// Clear CSRF token
unset($_SESSION['csrf_token']);

// Redirect to intended page or home
$redirect = $_SESSION['redirect_after_login'] ?? '../index.php';
unset($_SESSION['redirect_after_login']);

header("Location: $redirect");
exit();
?>
