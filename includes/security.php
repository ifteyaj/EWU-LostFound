<?php
/**
 * Security Module
 * Handles security headers, CSRF tokens, and rate limiting
 */

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/constants.php';

/**
 * Set security headers
 */
function setSecurityHeaders() {
    // Prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');
    
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');
    
    // Enable XSS filter in browser
    header('X-XSS-Protection: 1; mode=block');
    
    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Content Security Policy (adjust as needed)
    $csp = "default-src 'self'; ";
    $csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.googleapis.com; ";
    $csp .= "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.gstatic.com; ";
    $csp .= "font-src 'self' https://fonts.gstatic.com; ";
    $csp .= "img-src 'self' data: blob:; ";
    $csp .= "connect-src 'self'; ";
    $csp .= "frame-ancestors 'self';";
    header("Content-Security-Policy: {$csp}");
    
    // Only send cookies over HTTPS in production
    if (Env::isProduction()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

/**
 * Initialize secure session
 */
function initSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Configure session security
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_samesite', 'Strict');
        
        if (Env::isProduction()) {
            ini_set('session.cookie_secure', 1);
        }
        
        // Set session lifetime
        ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
        
        session_start();
        
        // Regenerate session ID periodically to prevent session fixation
        if (!isset($_SESSION['_created'])) {
            $_SESSION['_created'] = time();
        } elseif (time() - $_SESSION['_created'] > 1800) {
            // Regenerate session ID every 30 minutes
            session_regenerate_id(true);
            $_SESSION['_created'] = time();
        }
    }
}

/**
 * Generate CSRF token
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token input field
 */
function csrfField() {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Rate limiting check
 * Returns true if within limits, false if exceeded
 */
function checkRateLimit($identifier = null) {
    $identifier = $identifier ?? $_SERVER['REMOTE_ADDR'];
    $key = 'rate_limit_' . md5($identifier);
    
    initSecureSession();
    
    $now = time();
    $window = RATE_LIMIT_WINDOW;
    $maxRequests = RATE_LIMIT_REQUESTS;
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [
            'count' => 1,
            'start' => $now
        ];
        return true;
    }
    
    $data = $_SESSION[$key];
    
    // Reset if window has passed
    if ($now - $data['start'] >= $window) {
        $_SESSION[$key] = [
            'count' => 1,
            'start' => $now
        ];
        return true;
    }
    
    // Check if limit exceeded
    if ($data['count'] >= $maxRequests) {
        return false;
    }
    
    // Increment counter
    $_SESSION[$key]['count']++;
    return true;
}

/**
 * Sanitize input string
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate EWU email (optional - can be enabled for stricter validation)
 */
function isValidEwuEmail($email) {
    if (!isValidEmail($email)) {
        return false;
    }
    // Uncomment to restrict to EWU emails only
    // return preg_match('/@ewubd\.edu$/i', $email);
    return true;
}

/**
 * Clean filename for safe storage
 */
function sanitizeFilename($filename) {
    // Remove any path components
    $filename = basename($filename);
    
    // Remove dangerous characters
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    
    // Limit length
    if (strlen($filename) > 100) {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $filename = substr($name, 0, 90) . '.' . $ext;
    }
    
    return $filename;
}

/**
 * Validate uploaded file
 */
function validateUploadedFile($file, $maxSize = null, $allowedTypes = null) {
    $maxSize = $maxSize ?? MAX_FILE_SIZE;
    $allowedTypes = $allowedTypes ?? ALLOWED_IMAGE_TYPES;
    
    $errors = [];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds server size limit',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form size limit',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        $errors[] = $uploadErrors[$file['error']] ?? 'Unknown upload error';
        return $errors;
    }
    
    if ($file['size'] > $maxSize) {
        $errors[] = 'File size exceeds ' . ($maxSize / 1024 / 1024) . 'MB limit';
    }
    
    if (!in_array($file['type'], $allowedTypes)) {
        $errors[] = 'File type not allowed';
    }
    
    // Additional check using finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, $allowedTypes)) {
        $errors[] = 'File content type not allowed';
    }
    
    return $errors;
}

// Apply security headers on include
setSecurityHeaders();
?>
