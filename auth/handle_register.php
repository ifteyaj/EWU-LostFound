<?php
/**
 * Handle Registration Form Submission
 */
require_once '../init.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: register.php");
    exit();
}

// Rate limiting
if (!checkRateLimit('register_' . $_SERVER['REMOTE_ADDR'])) {
    logActivity("Rate limit exceeded for registration", ['ip' => $_SERVER['REMOTE_ADDR']]);
    header("Location: register.php?error=rate_limit");
    exit();
}

// CSRF validation
if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    logActivity("Invalid CSRF token on registration");
    header("Location: register.php?error=invalid_token");
    exit();
}

// Validate required fields
$required = ['full_name', 'student_id', 'email', 'password', 'password_confirm'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        header("Location: register.php?error=missing_fields");
        exit();
    }
}

// Sanitize inputs
$fullName = sanitizeInput($_POST['full_name']);
$studentId = sanitizeInput($_POST['student_id']);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$phone = !empty($_POST['phone']) ? sanitizeInput($_POST['phone']) : null;
$password = $_POST['password'];
$passwordConfirm = $_POST['password_confirm'];

// Validate email format (Must be StudentID@std.ewubd.edu)
$expectedEmail = $studentId . '@std.ewubd.edu';
if ($email !== $expectedEmail) {
    header("Location: register.php?error=invalid_email_format");
    exit();
}

if (!isValidEmail($email)) {
    header("Location: register.php?error=invalid_email");
    exit();
}

// Validate password strength
if (strlen($password) < 8) {
    header("Location: register.php?error=password_weak");
    exit();
}

// Validate password confirmation
if ($password !== $passwordConfirm) {
    header("Location: register.php?error=password_mismatch");
    exit();
}

// Check if email already exists
if (findUserByEmail($email)) {
    header("Location: register.php?error=email_exists");
    exit();
}

// Check if student ID already exists
if (findUserByStudentId($studentId)) {
    header("Location: register.php?error=student_id_exists");
    exit();
}

// Create user
$result = createUser([
    'full_name' => $fullName,
    'student_id' => $studentId,
    'email' => $email,
    'phone' => $phone,
    'password' => $password
]);

if ($result['success']) {
    // Clear CSRF token
    unset($_SESSION['csrf_token']);
    
    // TODO: Send verification email (Phase 5)
    // For now, redirect to login
    header("Location: login.php?success=register_success");
    exit();
} else {
    logError("Registration failed", 'ERROR', ['error' => $result['error']]);
    header("Location: register.php?error=database_error");
    exit();
}
?>
