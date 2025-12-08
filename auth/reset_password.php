<?php
/**
 * Reset Password Page
 */
require_once '../init.php';

// Redirect if already logged in
requireGuest();

$token = $_GET['token'] ?? '';
$error = $_GET['error'] ?? '';

// Validate token format
if (empty($token) || strlen($token) !== 64) {
    if (empty($error)) {
        $error = 'invalid_token';
    }
}

// Generate CSRF token
$csrf_token = generateCsrfToken();

// Error messages
$error_messages = [
    'invalid_token' => 'This password reset link is invalid or has expired.',
    'password_mismatch' => 'Passwords do not match.',
    'password_weak' => 'Password must be at least 8 characters long.',
    'rate_limit' => 'Too many attempts. Please try again later.',
    'database_error' => 'An error occurred. Please try again.'
];

$display_error = isset($error_messages[$error]) ? $error_messages[$error] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - EWU Lost & Found</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: var(--bg-light);
        }
        .auth-card {
            width: 100%;
            max-width: 420px;
            background: var(--card-bg);
            border-radius: var(--radius-card);
            box-shadow: var(--card-shadow);
            padding: 2.5rem;
        }
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-logo {
            height: 50px;
            margin-bottom: 1.5rem;
        }
        .auth-header h1 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .auth-form .form-group {
            margin-bottom: 1.5rem;
        }
        .auth-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-secondary);
        }
        .auth-form .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 0.875rem 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-header">
                <a href="../index.php">
                    <img src="../assets/img/logo.png" alt="EWU Lost & Found" class="auth-logo">
                </a>
                <h1>Reset Password</h1>
                <p>Create a new strong password for your account</p>
            </div>
            
            <?php if ($display_error): ?>
                <div class="alert-error"><?php echo htmlspecialchars($display_error); ?></div>
            <?php endif; ?>
            
            <?php if ($error !== 'invalid_token'): ?>
            <form class="auth-form" action="handle_reset_password.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required minlength="8">
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirm New Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="btn-pill" style="width: 100%;">Set New Password</button>
            </form>
            <?php else: ?>
                <div style="text-align: center;">
                    <a href="forgot_password.php" class="btn-pill" style="display: block; text-align: center;">Request New Link</a>
                    <a href="login.php" style="display: block; margin-top: 1rem; color: var(--text-secondary); font-size: 0.9rem;">Back to Login</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
