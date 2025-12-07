<?php
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = htmlspecialchars($_POST['item_name']);
    $category = htmlspecialchars($_POST['category']);
    $description = htmlspecialchars($_POST['description']);
    $found_location = htmlspecialchars($_POST['found_location']);
    $date_found = htmlspecialchars($_POST['date_found']);
    $finder_name = htmlspecialchars($_POST['finder_name']);
    $finder_id = htmlspecialchars($_POST['finder_id']);
    $email = htmlspecialchars($_POST['email']);
    
    // Image Upload
    $target_dir = "../uploads/";
    $image_name = "";
    
    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }

    $stmt = $conn->prepare("INSERT INTO found_items (item_name, category, description, found_location, date_found, image, finder_name, finder_id, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $item_name, $category, $description, $found_location, $date_found, $image_name, $finder_name, $finder_id, $email);

    if ($stmt->execute()) {
        header("Location: ../found.php?status=success");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
