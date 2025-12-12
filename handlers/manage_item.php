<?php
/**
 * Manage Item Handler
 * Handles actions like delete and resolve (mark as found/claimed)
 */
require_once '../init.php';

// Require login
requireLogin();

$action = $_GET['action'] ?? '';
$type = $_GET['type'] ?? '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$csrf_token = $_GET['csrf_token'] ?? '';

// Validate inputs
if (empty($action) || empty($type) || empty($id)) {
    header("Location: ../profile.php?error=invalid_request");
    exit();
}

if (!in_array($type, ['lost', 'found'])) {
    header("Location: ../profile.php?error=invalid_type");
    exit();
}

// CSRF check
if (!validateCsrfToken($csrf_token)) {
    header("Location: ../profile.php?error=invalid_token");
    exit();
}

$user = getCurrentUser();
$userId = $user['id'];
$table = ($type == 'found') ? 'found_items' : 'lost_items';

// Verify ownership
$stmt = $conn->prepare("SELECT id, image FROM $table WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $userId);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
    // Item not found or doesn't belong to user
    header("Location: ../profile.php?error=access_denied");
    exit();
}

if ($action === 'delete') {
    // Delete image file if exists
    if (!empty($item['image'])) {
        $imagePath = '../uploads/' . $item['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath); // Delete the file
        }
    }
    
    // Delete record
    $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        logActivity("Item deleted", ['user_id' => $userId, 'item_id' => $id, 'type' => $type]);
        header("Location: ../profile.php?success=item_deleted");
    } else {
        header("Location: ../profile.php?error=database_error");
    }
    $stmt->close();
    
} elseif ($action === 'resolve') {
    // Mark as resolved (found/claimed)
    $stmt = $conn->prepare("UPDATE $table SET status = 'resolved' WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        logActivity("Item marked resolved", ['user_id' => $userId, 'item_id' => $id, 'type' => $type]);
        header("Location: ../profile.php?success=item_resolved");
    } else {
        header("Location: ../profile.php?error=database_error");
    }
    $stmt->close();
    
} else {
    header("Location: ../profile.php?error=invalid_action");
}

$conn->close();
exit();
?>
