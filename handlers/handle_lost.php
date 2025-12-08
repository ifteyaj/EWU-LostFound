<?php
session_start();
include '../config/db.php';

// Allowed image types
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // CSRF Token Validation
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
        $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: post_item.php?error=invalid_token");
        exit();
    }
    
    // Validate required fields
    $required = ['item_name', 'category', 'description', 'last_location', 'date_lost', 'student_name', 'student_id', 'email'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            header("Location: post_item.php?error=missing_fields");
            exit();
        }
    }
    
    // Sanitize inputs
    $item_name = htmlspecialchars(trim($_POST['item_name']), ENT_QUOTES, 'UTF-8');
    $category = htmlspecialchars(trim($_POST['category']), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8');
    $last_location = htmlspecialchars(trim($_POST['last_location']), ENT_QUOTES, 'UTF-8');
    $date_lost = htmlspecialchars(trim($_POST['date_lost']), ENT_QUOTES, 'UTF-8');
    $student_name = htmlspecialchars(trim($_POST['student_name']), ENT_QUOTES, 'UTF-8');
    $student_id = htmlspecialchars(trim($_POST['student_id']), ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: post_item.php?error=invalid_email");
        exit();
    }
    
    // Validate date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_lost)) {
        header("Location: post_item.php?error=invalid_date");
        exit();
    }
    
    // Image Upload with validation
    $target_dir = "../uploads/";
    $image_name = "";
    
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $file_size = $_FILES["image"]["size"];
        $file_type = $_FILES["image"]["type"];
        
        // Check file size
        if ($file_size > MAX_FILE_SIZE) {
            header("Location: post_item.php?error=file_too_large");
            exit();
        }
        
        // Check file type
        if (!in_array($file_type, ALLOWED_TYPES)) {
            header("Location: post_item.php?error=invalid_file_type");
            exit();
        }
        
        // Generate secure filename
        $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $image_name = uniqid('lost_', true) . '.' . strtolower($extension);
        $target_file = $target_dir . $image_name;
        
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            header("Location: post_item.php?error=upload_failed");
            exit();
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO lost_items (item_name, category, description, last_location, date_lost, image, student_name, student_id, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $item_name, $category, $description, $last_location, $date_lost, $image_name, $student_name, $student_id, $email);

    if ($stmt->execute()) {
        // Clear CSRF token after successful submission
        unset($_SESSION['csrf_token']);
        header("Location: lost.php?status=success");
        exit();
    } else {
        header("Location: post_item.php?error=database_error");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
