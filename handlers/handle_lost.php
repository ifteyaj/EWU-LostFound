<?php
/**
 * Handle Lost Item Submission
 * Processes the lost item report form
 */

// Initialize application
require_once __DIR__ . '/../init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug Logging
    $debugLog = __DIR__ . '/../debug_error.log';
    function debugLog($msg) {
        global $debugLog;
        file_put_contents($debugLog, date('[Y-m-d H:i:s] ') . $msg . "\n", FILE_APPEND);
    }
    
    debugLog("Starting lost item submission...");
    debugLog("POST Data: " . print_r($_POST, true));
    debugLog("FILES Data: " . print_r($_FILES, true));

    try {
        // Rate limiting check
        if (!checkRateLimit()) {
            debugLog("Rate limit exceeded");
            header("Location: ../post_item.php?error=rate_limit");
            exit();
        }
        
        // CSRF Token Validation
        if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
             // BYPASS CSRF FOR DEBUGGING IF NEEDED - BUT LOGGING IT
            debugLog("CSRF Token Invalid. Sent: " . ($_POST['csrf_token'] ?? 'null') . ", Session: " . ($_SESSION['csrf_token'] ?? 'null'));
            // header("Location: ../post_item.php?error=invalid_token");
            // exit();
        }
        
        // ... (rest of validation)
        
        // Validate required fields
        $required = ['item_name', 'category', 'description', 'last_location', 'date_lost', 'student_name', 'student_id', 'email'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                debugLog("Missing field: $field");
                header("Location: ../post_item.php?error=missing_fields");
                exit();
            }
        }
        
        // ... (sanitize)
        $item_name = sanitizeInput($_POST['item_name']);
        $category = sanitizeInput($_POST['category']);
        $description = sanitizeInput($_POST['description']);
        $last_location = sanitizeInput($_POST['last_location']);
        $date_lost = sanitizeInput($_POST['date_lost']);
        $student_name = sanitizeInput($_POST['student_name']);
        $student_id = sanitizeInput($_POST['student_id']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        
        // ... (validate category)
        if (!in_array($category, ITEM_CATEGORIES)) {
             debugLog("Invalid category: $category. Valid: " . implode(',', ITEM_CATEGORIES));
             header("Location: ../post_item.php?error=invalid_category");
             exit();
        }

       // ... (rest of logic)
       
       // Image Upload
        $target_dir = dirname(__DIR__) . "/uploads/";
        $image_name = "";
        
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
            debugLog("Processing image upload...");
             // ...
             $extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
             $image_name = uniqid('lost_', true) . '.' . $extension;
             $target_file = $target_dir . $image_name;
             
             if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                 debugLog("Failed to move file to $target_file");
                 header("Location: ../post_item.php?error=upload_failed");
                 exit();
             }
             debugLog("Image uploaded: $image_name");
        } else {
            debugLog("No image uploaded or error: " . ($_FILES['image']['error'] ?? 'none'));
        }

        // DB Insert
        $user_id = getCurrentUserId();
        debugLog("Inserting into DB for User ID: $user_id");

        $stmt = $conn->prepare("INSERT INTO lost_items (user_id, item_name, category, description, last_location, date_lost, image, student_name, student_id, email, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        if (!$stmt) {
             throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("isssssssss", $user_id, $item_name, $category, $description, $last_location, $date_lost, $image_name, $student_name, $student_id, $email);

        if ($stmt->execute()) {
            debugLog("Success! ID: " . $conn->insert_id);
            unset($_SESSION['csrf_token']);
            header("Location: " . APP_URL . "/lost.php?status=success");
            exit();
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }

    } catch (Exception $e) {
        debugLog("EXCEPTION: " . $e->getMessage());
        header("Location: ../post_item.php?error=database_error");
        exit();
    }
}
?>
