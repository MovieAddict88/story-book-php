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
    $story_id = $_POST['story_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $language_id = $_POST['language_id'];
    $cover_image_path = $_POST['current_cover_image'];

    // --- Handle New File Upload ---
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        // A new file was uploaded, so process it
        $target_dir = "../assets/images/covers/";
        $image_name = basename($_FILES["cover_image"]["name"]);
        $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
        $unique_image_name = uniqid('cover_', true) . '.' . $image_ext;
        $target_file = $target_dir . $unique_image_name;

        $check = getimagesize($_FILES["cover_image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
                // Delete the old image file
                if (!empty($cover_image_path) && file_exists('../' . $cover_image_path)) {
                    unlink('../' . $cover_image_path);
                }
                // Set the new path
                $cover_image_path = 'assets/images/covers/' . $unique_image_name;
            } else {
                die("Error uploading new file.");
            }
        } else {
            die("New file is not an image.");
        }
    }

    // --- Update Database ---
    if (!empty($title) && !empty($story_id)) {
        $stmt = $conn->prepare("UPDATE stories SET title = ?, description = ?, cover_image = ?, category_id = ?, language_id = ? WHERE id = ?");
        $stmt->bind_param("sssiii", $title, $description, $cover_image_path, $category_id, $language_id, $story_id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: stories.php");
exit;
?>