<?php
/**
 * Registration Page
 */
require_once '../init.php';

// Redirect if already logged in
requireGuest();

// Generate CSRF token
$csrf_token = generateCsrfToken();

// Error messages
$error_messages = [
    'email_exists' => 'An account with this email already exists.',
    'student_id_exists' => 'An account with this Student ID already exists.',
    'password_mismatch' => 'Passwords do not match.',
    'password_weak' => 'Password must be at least 8 characters long.',
    'invalid_email' => 'Please enter a valid email address.',
    'missing_fields' => 'Please fill in all required fields.',
    'invalid_token' => 'Security validation failed. Please try again.',
    'database_error' => 'Registration failed. Please try again.',
    'rate_limit' => 'Too many attempts. Please try again later.'
];

$error = isset($_GET['error']) && isset($error_messages[$_GET['error']]) 
    ? $error_messages[$_GET['error']] 
    : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Create an account on EWU Lost & Found">
    <title>Register - EWU Lost & Found</title>
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
            max-width: 480px;
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
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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
        .password-hint {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }
        @media (max-width: 500px) {
            .form-row {
                grid-template-columns: 1fr;
            }
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
                <h1>Create Account</h1>
                <p>Join the EWU Lost & Found community</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form class="auth-form" action="handle_register.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" placeholder="John Doe" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="student_id">Student ID</label>
                        <input type="text" id="student_id" name="student_id" class="form-control" placeholder="2020-3-60-001" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone (Optional)</label>
                        <input type="tel" id="phone" name="phone" class="form-control" placeholder="01XXXXXXXXX">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="your.email@ewubd.edu" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required minlength="8">
                        <span class="password-hint">At least 8 characters</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm">Confirm Password</label>
                        <input type="password" id="password_confirm" name="password_confirm" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>
                
                <button type="submit" class="btn-pill">Create Account</button>
            </form>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Sign in</a></p>
            </div>
        </div>
    </div>
    
    <script>
        // Simple password match validation
        document.querySelector('.auth-form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirm').value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });
    </script>
</body>
</html>
