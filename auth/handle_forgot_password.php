<?php
/**
 * Handle Forgot Password Form
 * Note: Full email functionality will be added in Phase 5
 * For now, we generate a token and show it (in dev mode)
 */
require_once '../init.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: forgot_password.php");
    exit();
}

// Rate limiting
if (!checkRateLimit('forgot_' . $_SERVER['REMOTE_ADDR'])) {
    header("Location: forgot_password.php?error=rate_limit");
    exit();
}

// CSRF validation
if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    header("Location: forgot_password.php?error=invalid_token");
    exit();
}

if (empty($_POST['email'])) {
    header("Location: forgot_password.php?error=invalid_email");
    exit();
}

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

if (!isValidEmail($email)) {
    header("Location: forgot_password.php?error=invalid_email");
    exit();
}

// Find user
$user = findUserByEmail($email);

if ($user) {
    // Generate reset token
    $resetToken = generateToken();
    $resetExpires = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Save token to database
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
    $stmt->bind_param("ssi", $resetToken, $resetExpires, $user['id']);
    $stmt->execute();
    $stmt->close();
    
    logActivity("Password reset requested", ['user_id' => $user['id'], 'email' => $email]);
    
    // TODO: Send email in Phase 5
    // For now in development, we could log the reset link
    if (Env::isDevelopment()) {
        $resetLink = APP_URL . "/auth/reset_password.php?token=" . $resetToken;
        logActivity("Password reset link (DEV ONLY)", ['link' => $resetLink]);
    }
}

// Always show success to prevent email enumeration
unset($_SESSION['csrf_token']);
header("Location: forgot_password.php?success=email_sent");
exit();
?>
