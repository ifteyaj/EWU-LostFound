<?php
/**
 * Login Page
 */
require_once '../init.php';

// Redirect if already logged in
requireGuest();

// Generate CSRF token
$csrf_token = generateCsrfToken();

// Error messages
$error_messages = [
    'invalid_credentials' => 'Invalid email or password.',
    'login_required' => 'Please log in to continue.',
    'account_disabled' => 'Your account has been disabled.',
    'logout_success' => 'You have been logged out successfully.',
    'register_success' => 'Registration successful! Please log in.',
    'password_reset' => 'Password reset successful. Please log in with your new password.',
    'rate_limit' => 'Too many login attempts. Please try again later.'
];

$error = isset($_GET['error']) && isset($error_messages[$_GET['error']]) 
    ? $error_messages[$_GET['error']] 
    : null;

$success = isset($_GET['success']) && isset($error_messages[$_GET['success']])
    ? $error_messages[$_GET['success']]
    : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to EWU Lost & Found - Access your account">
    <title>Login - EWU Lost & Found</title>
    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>
    <div class="container" style="padding-top: 8rem; padding-bottom: 5rem;">
        <div class="form-card" style="max-width: 420px;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <a href="../index.php" style="display: inline-block; margin-bottom: 1.5rem;">
                    <img src="../assets/img/logo.png" alt="EWU Lost & Found" style="height: 50px;">
                </a>
                <h1 style="font-size: 1.75rem; margin-bottom: 0.5rem;">Welcome Back</h1>
                <p style="color: var(--text-body);">Sign in to your account</p>
            </div>
            
            <?php if ($error): ?>
                <div style="background: var(--status-lost-bg); color: var(--status-lost-text); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid rgba(220, 38, 38, 0.2);"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div style="background: var(--status-found-bg); color: var(--status-found-text); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid rgba(22, 163, 74, 0.2);"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form action="handle_login.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required autofocus placeholder="ID@std.ewubd.edu">
                </div>
                
                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <label for="password" style="margin-bottom: 0;">Password</label>
                        <a href="forgot_password.php" style="font-size: 0.85rem; color: var(--primary-brand);">Forgot password?</a>
                    </div>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn-pill btn-primary" style="width: 100%; justify-content: center; font-size: 1rem;">Sign In</button>
            </form>
            
            <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--glass-border);">
                <p style="color: var(--text-body); font-size: 0.95rem;">Don't have an account? <a href="register.php" style="color: var(--primary-brand); font-weight: 600;">Create one</a></p>
            </div>
        </div>
    </div>
</body>
</html>
