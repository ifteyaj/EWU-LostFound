<?php
include '../config/db.php';

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = intval($_GET['id']);
    $type = $_GET['type'];
    
    $table = ($type == 'found') ? 'found_items' : 'lost_items';
    $redirect = ($type == 'found') ? '../found.php' : '../lost.php';

    // In a real app, you would check admin session here
    $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: $redirect?msg=deleted");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    
    $stmt->close();
    $conn->close();
}
?>
