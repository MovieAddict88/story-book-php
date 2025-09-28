<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = connect();

    // --- Data from form ---
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $language_id = $_POST['language_id'];

    // --- Handle File Upload ---
    $cover_image_path = '';
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $target_dir = "../assets/images/covers/";
        // Ensure the directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $image_name = basename($_FILES["cover_image"]["name"]);
        $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
        // Create a unique name to prevent overwriting
        $unique_image_name = uniqid('cover_', true) . '.' . $image_ext;
        $target_file = $target_dir . $unique_image_name;

        // Basic validation (e.g., check if it's an image)
        $check = getimagesize($_FILES["cover_image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
                $cover_image_path = 'assets/images/covers/' . $unique_image_name;
            } else {
                // Handle upload error
                die("Error uploading file.");
            }
        } else {
            // Handle invalid file type
            die("File is not an image.");
        }
    }

    // --- Insert into Database ---
    if (!empty($title) && !empty($cover_image_path)) {
        $stmt = $conn->prepare("INSERT INTO stories (title, description, cover_image, category_id, language_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $title, $description, $cover_image_path, $category_id, $language_id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: stories.php");
exit;
?>