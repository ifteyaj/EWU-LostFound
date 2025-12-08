<?php
/**
 * Authentication Middleware & Helpers
 * Handles user authentication state and access control
 */

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current logged in user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current logged in user data
 */
function getCurrentUser() {
    global $conn;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    $userId = getCurrentUserId();
    $stmt = $conn->prepare("SELECT id, student_id, email, full_name, phone, avatar, is_admin, is_verified, last_login, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    return $user;
}

/**
 * Check if current user is admin
 */
function isAdmin() {
    if (!isLoggedIn()) {
        return false;
    }
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

/**
 * Require user to be logged in
 * Redirects to login page if not authenticated
 */
function requireLogin($redirectUrl = null) {
    if (!isLoggedIn()) {
        $redirect = $redirectUrl ?? $_SERVER['REQUEST_URI'];
        $_SESSION['redirect_after_login'] = $redirect;
        header("Location: " . APP_URL . "/auth/login.php?error=login_required");
        exit();
    }
}

/**
 * Require user to be admin
 * Shows 403 error if not admin
 */
function requireAdmin() {
    requireLogin();
    
    if (!isAdmin()) {
        showError(403, "Admin access required.");
    }
}

/**
 * Require user to be a guest (not logged in)
 * Redirects to home if already logged in
 */
function requireGuest() {
    if (isLoggedIn()) {
        header("Location: " . APP_URL . "/index.php");
        exit();
    }
}

/**
 * Log in a user
 */
function loginUser($user) {
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['is_admin'] = (bool) $user['is_admin'];
    $_SESSION['is_verified'] = (bool) $user['is_verified'];
    $_SESSION['login_time'] = time();
    
    // Update last login time
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $stmt->close();
    
    logActivity("User logged in", ['user_id' => $user['id'], 'email' => $user['email']]);
}

/**
 * Log out the current user
 */
function logoutUser() {
    $userId = getCurrentUserId();
    
    logActivity("User logged out", ['user_id' => $userId]);
    
    // Clear all session data
    $_SESSION = [];
    
    // Destroy the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
}

/**
 * Hash a password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify a password against a hash
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate a secure random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Find user by email
 */
function findUserByEmail($email) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    return $user;
}

/**
 * Find user by student ID
 */
function findUserByStudentId($studentId) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE student_id = ?");
    $stmt->bind_param("s", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    return $user;
}

/**
 * Create a new user
 */
function createUser($data) {
    global $conn;
    
    $studentId = $data['student_id'];
    $email = $data['email'];
    $passwordHash = hashPassword($data['password']);
    $fullName = $data['full_name'];
    $phone = $data['phone'] ?? null;
    $verificationToken = generateToken();
    
    $stmt = $conn->prepare("INSERT INTO users (student_id, email, password_hash, full_name, phone, verification_token) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $studentId, $email, $passwordHash, $fullName, $phone, $verificationToken);
    
    if ($stmt->execute()) {
        $userId = $conn->insert_id;
        $stmt->close();
        
        logActivity("New user registered", ['user_id' => $userId, 'email' => $email]);
        
        return [
            'success' => true,
            'user_id' => $userId,
            'verification_token' => $verificationToken
        ];
    } else {
        $error = $stmt->error;
        $stmt->close();
        
        logError("User registration failed", 'ERROR', ['error' => $error, 'email' => $email]);
        
        return [
            'success' => false,
            'error' => $error
        ];
    }
}

/**
 * Get user's display name for navbar
 */
function getUserDisplayName() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $name = $_SESSION['user_name'] ?? 'User';
    // Return first name only
    $parts = explode(' ', $name);
    return $parts[0];
}
?>
