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
    <link rel="stylesheet" href="../assets/css/style.css">

</head>
    <div class="container" style="padding-top: 6rem; padding-bottom: 5rem;">
        <div class="form-card" style="max-width: 500px;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <a href="../index.php" style="display: inline-block; margin-bottom: 1.5rem;">
                    <img src="../assets/img/logo.png" alt="EWU Lost & Found" style="height: 50px;">
                </a>
                <h1 style="font-size: 1.75rem; margin-bottom: 0.5rem;">Create Account</h1>
                <p style="color: var(--text-body);">Join the EWU Lost & Found community</p>
            </div>
            
            <?php if ($error): ?>
                <div style="background: var(--status-lost-bg); color: var(--status-lost-text); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid rgba(220, 38, 38, 0.2);"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form action="handle_register.php" method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" required placeholder="John Doe">
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="student_id">Student ID</label>
                        <input type="text" id="student_id" name="student_id" required placeholder="2020-3-60-001">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone (Optional)</label>
                        <input type="tel" id="phone" name="phone" placeholder="01XXXXXXXXX">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="ID@std.ewubd.edu">
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required minlength="8" placeholder="••••••••">
                        <span style="font-size: 0.75rem; color: var(--text-muted);">Min 8 chars</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm">Confirm</label>
                        <input type="password" id="password_confirm" name="password_confirm" required placeholder="••••••••">
                    </div>
                </div>
                
                <button type="submit" class="btn-pill btn-primary" style="width: 100%; justify-content: center; font-size: 1rem; margin-top: 1rem;">Create Account</button>
            </form>
            
            <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--glass-border);">
                <p style="color: var(--text-body); font-size: 0.95rem;">Already have an account? <a href="login.php" style="color: var(--primary-brand); font-weight: 600;">Sign in</a></p>
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
