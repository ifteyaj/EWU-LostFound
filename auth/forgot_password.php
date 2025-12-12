<?php
/**
 * Forgot Password Page
 */
require_once '../init.php';

// Redirect if already logged in
requireGuest();

// Generate CSRF token
$csrf_token = generateCsrfToken();

// Messages
$error_messages = [
    'invalid_email' => 'Please enter a valid email address.',
    'user_not_found' => 'No account found with this email address.',
    'rate_limit' => 'Too many requests. Please try again later.',
    'invalid_token' => 'Security validation failed. Please try again.'
];

$success_messages = [
    'email_sent' => 'If an account exists with this email, you will receive password reset instructions.'
];

$error = isset($_GET['error']) && isset($error_messages[$_GET['error']]) 
    ? $error_messages[$_GET['error']] 
    : null;

$success = isset($_GET['success']) && isset($success_messages[$_GET['success']])
    ? $success_messages[$_GET['success']]
    : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Reset your EWU Lost & Found password">
    <title>Forgot Password - EWU Lost & Found</title>
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
            line-height: 1.5;
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
        .auth-footer a {
            color: var(--primary-navy);
            font-weight: 600;
            font-size: 0.9rem;
        }
        .auth-footer a:hover {
            text-decoration: underline;
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
                <h1>Forgot Password?</h1>
                <p>Enter your email address and we'll send you instructions to reset your password.</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form class="auth-form" action="handle_forgot_password.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="your.email@ewubd.edu" required autofocus>
                </div>
                
                <button type="submit" class="btn-pill">Send Reset Link</button>
            </form>
            
            <div class="auth-footer">
                <a href="login.php">← Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
