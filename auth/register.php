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
    'invalid_email_format' => 'Email must be in the format: (Student ID)@std.ewubd.edu',
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
            overflow-y: auto;
        }
        
        .form-wrapper {
            width: 100%;
            max-width: 480px;
            padding: 2rem 0;
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
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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
        
        .form-footer p {
            color: #9ca3af;
            font-size: 0.9rem;
        }
        
        .form-footer a {
            color: #ffffff;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        
        .form-footer a:hover {
            color: #869F86;
        }
        
        .password-hint {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.35rem;
        }
        
        .terms-text {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 1rem;
            line-height: 1.5;
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
            
            .form-row {
                grid-template-columns: 1fr;
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
                    <h2>Create your account</h2>
                    <p>Join a network of students helping each other recover lost items</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form action="handle_register.php" method="POST" class="auth-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="form-group">
                        <label for="full_name">Full name *</label>
                        <input type="text" id="full_name" name="full_name" required placeholder="Andrew Thomas">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email address *</label>
                        <input type="email" id="email" name="email" required placeholder="ID@std.ewubd.edu">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="student_id">Student ID *</label>
                            <input type="text" id="student_id" name="student_id" required placeholder="2020-3-60-001">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone (Optional)</label>
                            <input type="tel" id="phone" name="phone" placeholder="01XXXXXXXXX">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input type="password" id="password" name="password" required minlength="8" placeholder="••••••••••••">
                            <div class="password-hint">Password must be at least 8 characters, including a number and a special character</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirm">Confirm password *</label>
                            <input type="password" id="password_confirm" name="password_confirm" required placeholder="••••••••••••">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit">Create Account</button>
                    
                    <p class="terms-text">By creating an account, you agree to our Terms of Service and Privacy Policy</p>
                </form>
                
                <div class="form-footer">
                    <p>Already have an account? <a href="login.php">Sign In</a></p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Form validation
        document.querySelector('.auth-form').addEventListener('submit', function(e) {
            const studentId = document.getElementById('student_id').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirm').value;
            
            // Validate email format
            const expectedEmail = studentId + '@std.ewubd.edu';
            if (email !== expectedEmail) {
                e.preventDefault();
                alert('Email must be your Student ID followed by @std.ewubd.edu\nExample: ' + expectedEmail);
                return;
            }

            if (password !== confirm) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });
        
        // Auto-complete email based on Student ID
        document.getElementById('student_id').addEventListener('input', function(e) {
            const studentId = e.target.value.trim();
            if(studentId.length > 0) {
                document.getElementById('email').placeholder = studentId + '@std.ewubd.edu';
            } else {
                document.getElementById('email').placeholder = 'ID@std.ewubd.edu';
            }
        });
    </script>
</body>
</html>
