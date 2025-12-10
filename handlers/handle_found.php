<?php
/**
 * Handle Found Item Submission
 * Processes the found item report form
 */

// Initialize application
require_once __DIR__ . '/../init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Rate limiting check
    if (!checkRateLimit()) {
        logActivity("Rate limit exceeded for found item submission", ['ip' => $_SERVER['REMOTE_ADDR']]);
        header("Location: " . APP_URL . "/post_item.php?error=rate_limit");
        exit();
    }
    
    // CSRF Token Validation
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        logActivity("Invalid CSRF token on found item submission");
        header("Location: " . APP_URL . "/post_item.php?error=invalid_token");
        exit();
    }
    
    // Validate required fields
    $required = ['item_name', 'category', 'description', 'found_location', 'date_found'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            header("Location: " . APP_URL . "/post_item.php?error=missing_fields");
            exit();
        }
    }
    
    // Get current user details securely
    $currentUser = getCurrentUser();
    if (!$currentUser) {
        header("Location: " . APP_URL . "/login.php");
        exit();
    }

    $finder_name = $currentUser['full_name'];
    $finder_id = $currentUser['student_id'];
    $email = $currentUser['email'];

    // Sanitize inputs
    $item_name = sanitizeInput($_POST['item_name']);
    $category = sanitizeInput($_POST['category']);
    $description = sanitizeInput($_POST['description']);
    $found_location = sanitizeInput($_POST['found_location']);
    $date_found = sanitizeInput($_POST['date_found']);
    
    // Validate date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_found)) {
        header("Location: " . APP_URL . "/post_item.php?error=invalid_date");
        exit();
    }
    
    // Validate category
    if (!in_array($category, ITEM_CATEGORIES)) {
        header("Location: " . APP_URL . "/post_item.php?error=invalid_category");
        exit();
    }
    
    // Image Upload with validation
    $target_dir = dirname(__DIR__) . "/uploads/";
    $image_name = "";
    
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
        $errors = validateUploadedFile($_FILES["image"]);
        
        if (!empty($errors)) {
            logError("File upload validation failed", 'WARNING', ['errors' => $errors]);
            header("Location: " . APP_URL . "/post_item.php?error=invalid_file");
            exit();
        }
        
        // Generate secure filename
        $extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $image_name = uniqid('found_', true) . '.' . $extension;
        $target_file = $target_dir . $image_name;
        
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            logError("Failed to move uploaded file", 'ERROR', ['target' => $target_file]);
            header("Location: " . APP_URL . "/post_item.php?error=upload_failed");
            exit();
        }
    }

    // Get current user ID
    $user_id = getCurrentUserId();

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO found_items (user_id, item_name, category, description, found_location, date_found, image, finder_name, finder_id, email, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("isssssssss", $user_id, $item_name, $category, $description, $found_location, $date_found, $image_name, $finder_name, $finder_id, $email);

    if ($stmt->execute()) {
        // Log successful submission
        logActivity("Found item reported", [
            'item_name' => $item_name,
            'finder_id' => $finder_id,
            'id' => $conn->insert_id
        ]);
        
        // Clear CSRF token after successful submission
        unset($_SESSION['csrf_token']);
        header("Location: " . APP_URL . "/found.php?status=success");
        exit();
    } else {
        logError("Database error on found item insert", 'ERROR', ['error' => $stmt->error]);
        $stmt->close();
        $conn->close();
        header("Location: " . APP_URL . "/post_item.php?error=database_error");
        exit();
    }
}
?>
