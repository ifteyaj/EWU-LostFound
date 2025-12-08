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
        .auth-header p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        .auth-form .form-group {
            margin-bottom: 1.25rem;
        }
        .auth-form .form-group:last-of-type {
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
        .auth-form .btn-pill {
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
        }
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-light);
        }
        .auth-footer p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        .auth-footer a {
            color: var(--primary-navy);
            font-weight: 600;
        }
        .auth-footer a:hover {
            text-decoration: underline;
        }
        .forgot-link {
            display: block;
            text-align: right;
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-top: 0.5rem;
        }
        .forgot-link:hover {
            color: var(--primary-navy);
        }
        .alert {
            padding: 0.875rem 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #16a34a;
        }
        .back-home {
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .back-home:hover {
            color: var(--primary-navy);
        }
    </style>
</head>
<body>
    <a href="../index.php" class="back-home">← Back to Home</a>
    
    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-header">
                <a href="../index.php">
                    <img src="../assets/img/logo.png" alt="EWU Lost & Found" class="auth-logo">
                </a>
                <h1>Welcome Back</h1>
                <p>Sign in to your account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form class="auth-form" action="handle_login.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="your.email@ewubd.edu" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                    <a href="forgot_password.php" class="forgot-link">Forgot password?</a>
                </div>
                
                <button type="submit" class="btn-pill">Sign In</button>
            </form>
            
            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Create one</a></p>
            </div>
        </div>
    </div>
</body>
</html>
