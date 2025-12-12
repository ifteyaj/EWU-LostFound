<?php
/**
 * Handle Item Deletion
 * IMPORTANT: This should only be accessible by admins (authentication will be added in Phase 2)
 */

// Initialize application
require_once __DIR__ . '/../init.php';

// TODO: Add admin authentication check here in Phase 2
// if (!isAdmin()) {
//     showError(403);
// }

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id']) && isset($_GET['type'])) {
    
    // Rate limiting check
    if (!checkRateLimit()) {
        logActivity("Rate limit exceeded for delete operation", ['ip' => $_SERVER['REMOTE_ADDR']]);
        header("Location: ../index.php?error=rate_limit");
        exit();
    }
    
    // Validate CSRF token if using POST (will implement in Phase 2)
    // For now, we're using GET which is less secure but will be fixed with admin auth
    
    $id = intval($_GET['id']);
    $type = $_GET['type'];
    
    // Validate type
    if (!in_array($type, ['lost', 'found'])) {
        logActivity("Invalid delete type attempted", ['type' => $type, 'id' => $id]);
        header("Location: ../index.php?error=invalid_request");
        exit();
    }
    
    $table = ($type == 'found') ? 'found_items' : 'lost_items';
    $redirect = ($type == 'found') ? '../found.php' : '../lost.php';

    // First, get the image filename to delete the file
    $getStmt = $conn->prepare("SELECT image FROM $table WHERE id = ?");
    $getStmt->bind_param("i", $id);
    $getStmt->execute();
    $result = $getStmt->get_result();
    $item = $result->fetch_assoc();
    $getStmt->close();
    
    // Delete the record
    $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Delete the image file if it exists
        if (!empty($item['image'])) {
            $imagePath = dirname(__DIR__) . '/uploads/' . $item['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        logActivity("Item deleted", [
            'type' => $type,
            'id' => $id,
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
        
        $stmt->close();
        $conn->close();
        header("Location: $redirect?msg=deleted");
        exit();
    } else {
        logError("Error deleting item", 'ERROR', [
            'type' => $type,
            'id' => $id,
            'error' => $conn->error
        ]);
        $stmt->close();
        $conn->close();
        header("Location: $redirect?error=delete_failed");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
