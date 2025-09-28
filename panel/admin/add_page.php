<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = connect();
    $story_id = (int)$_POST['story_id'];
    $page_number = (int)$_POST['page_number'];
    $text = $_POST['text'];

    // --- Handle Page Image Upload ---
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/pages/";
        $image_name = basename($_FILES["image"]["name"]);
        $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
        $unique_image_name = uniqid('page_', true) . '.' . $image_ext;
        $target_file = $target_dir . $unique_image_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = 'assets/images/pages/' . $unique_image_name;
        }
    }

    // --- Insert into story_pages table ---
    $stmt = $conn->prepare("INSERT INTO story_pages (story_id, page_number, text, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $story_id, $page_number, $text, $image_path);
    $stmt->execute();
    $page_id = $conn->insert_id; // Get the ID of the new page
    $stmt->close();

    // --- Handle Audio Options ---
    $use_tts = isset($_POST['use_tts']) ? 1 : 0;
    $audio_url = $_POST['audio_url'];
    $audio_file_path = null;

    if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] == 0) {
        $target_dir = "../assets/audio/";
        $audio_name = basename($_FILES["audio_file"]["name"]);
        $audio_ext = pathinfo($audio_name, PATHINFO_EXTENSION);
        $unique_audio_name = uniqid('audio_', true) . '.' . $audio_ext;
        $target_file = $target_dir . $unique_audio_name;

        if (move_uploaded_file($_FILES["audio_file"]["tmp_name"], $target_file)) {
            $audio_file_path = 'assets/audio/' . $unique_audio_name;
        }
    }

    // --- Insert into audio table ---
    if ($page_id > 0) {
        $stmt_audio = $conn->prepare("INSERT INTO audio (story_page_id, audio_url, audio_file, use_tts) VALUES (?, ?, ?, ?)");
        $stmt_audio->bind_param("issi", $page_id, $audio_url, $audio_file_path, $use_tts);
        $stmt_audio->execute();
        $stmt_audio->close();
    }

    $conn->close();
}

header("Location: manage_pages.php?story_id=" . $story_id);
exit;
?>