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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            overflow: hidden;
            height: 100vh;
        }
        
        .auth-container {
            display: flex;
            height: 100vh;
            position: relative;
        }
        
        /* Left Hero Section */
        .hero-section {
            flex: 1;
            position: relative;
            overflow: hidden;
            clip-path: polygon(0 0, 100% 0, 85% 100%, 0% 100%);
        }
        
        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.3) 0%, rgba(118, 75, 162, 0.3) 100%), 
                        url('../assets/img/campus-hero.jpg') center/cover;
            background-size: cover;
            background-position: center;
        }
        
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.3) 100%);
        }
        
        .hero-content {
            position: relative;
            z-index: 10;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 4rem;
            color: white;
        }
        
        .hero-logo {
            margin-bottom: auto;
            padding-top: 2rem;
        }
        
        .hero-logo img {
            height: 60px;
            filter: brightness(0) invert(1);
        }
        
        .hero-content h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        
        .hero-content p {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            opacity: 0.95;
            max-width: 500px;
        }
        
        .hero-content .tagline {
            font-size: 0.95rem;
            opacity: 0.75;
            font-weight: 500;
        }
        
        /* Right Form Section */
        .form-section {
            flex: 1;
            background: #0a0a0a;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            position: relative;
        }
        
        .form-wrapper {
            width: 100%;
            max-width: 420px;
        }
        
        .form-header h2 {
            color: #ffffff;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }
        
        .form-header p {
            color: #9ca3af;
            font-size: 0.95rem;
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
        
        .alert-success {
            background: rgba(134, 159, 134, 0.1);
            border: 1px solid rgba(134, 159, 134, 0.3);
            color: #a8c5a8;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            color: #d1d5db;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.875rem 1rem;
            background: transparent;
            border: 1px solid #2d2d2d;
            border-radius: 8px;
            color: #ffffff;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            font-family: inherit;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #4a5568;
            background: rgba(255, 255, 255, 0.02);
        }
        
        .form-group input::placeholder {
            color: #6b7280;
        }
        
        .btn-submit {
            width: 100%;
            padding: 0.95rem;
            background: #869F86;
            color: #0a0a0a;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
            font-family: inherit;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-submit:hover {
            background: #9bb29b;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(134, 159, 134, 0.3);
        }
        
        .form-footer {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #1f1f1f;
            text-align: center;
        }
        
        .form-footer a {
            color: #9ca3af;
            font-size: 0.9rem;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        
        .form-footer a:hover {
            color: #ffffff;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .hero-section {
                display: none;
            }
            .form-section {
                flex: 1;
            }
        }
        
        @media (max-width: 640px) {
            .form-section {
                padding: 2rem 1.5rem;
            }
            
            .form-wrapper {
                max-width: 100%;
            }
            
            .form-header h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Left Hero Section -->
        <div class="hero-section">
            <div class="hero-bg"></div>
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <div class="hero-logo">
                    <a href="../index.php">
                        <img src="../assets/img/logo.png" alt="EWU Lost & Found">
                    </a>
                </div>
                <div>
                    <h1>Reunite lost items with their owners.</h1>
                    <p>Join a community of students helping each other find what matters most.</p>
                    <p class="tagline">EWU Lost & Found — Your gateway to campus item recovery</p>
                </div>
            </div>
        </div>
        
        <!-- Right Form Section -->
        <div class="form-section">
            <div class="form-wrapper">
                <div class="form-header">
                    <h2>Reset your password</h2>
                    <p>Enter your email address and we'll send you instructions to reset your password</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <form action="handle_forgot_password.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" id="email" name="email" required autofocus placeholder="ID@std.ewubd.edu">
                    </div>
                    
                    <button type="submit" class="btn-submit">Send Reset Link</button>
                </form>
                
                <div class="form-footer">
                    <a href="login.php">← Back to Sign In</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
