<?php
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = htmlspecialchars($_POST['item_name']);
    $category = htmlspecialchars($_POST['category']);
    $description = htmlspecialchars($_POST['description']);
    $last_location = htmlspecialchars($_POST['last_location']);
    $date_lost = htmlspecialchars($_POST['date_lost']);
    $student_name = htmlspecialchars($_POST['student_name']);
    $student_id = htmlspecialchars($_POST['student_id']);
    $email = htmlspecialchars($_POST['email']);
    
    // Image Upload
    $target_dir = "../uploads/";
    $image_name = "";
    
    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }

    $stmt = $conn->prepare("INSERT INTO lost_items (item_name, category, description, last_location, date_lost, image, student_name, student_id, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $item_name, $category, $description, $last_location, $date_lost, $image_name, $student_name, $student_id, $email);

    if ($stmt->execute()) {
        header("Location: ../lost.php?status=success");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
