<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = connect();

    // --- Form Data ---
    $page_id = (int)$_POST['page_id'];
    $story_id = (int)$_POST['story_id'];
    $page_number = (int)$_POST['page_number'];
    $text = $_POST['text'];
    $image_path = $_POST['current_image'];
    $audio_file_path = $_POST['current_audio_file'];
    $use_tts = isset($_POST['use_tts']) ? 1 : 0;
    $audio_url = $_POST['audio_url'];

    // --- Handle New Page Image Upload ---
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/pages/";
        $image_name = basename($_FILES["image"]["name"]);
        $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
        $unique_image_name = uniqid('page_', true) . '.' . $image_ext;
        $target_file = $target_dir . $unique_image_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Delete old image if it exists
            if (!empty($image_path) && file_exists('../' . $image_path)) {
                unlink('../' . $image_path);
            }
            $image_path = 'assets/images/pages/' . $unique_image_name;
        }
    }

    // --- Update story_pages table ---
    $stmt = $conn->prepare("UPDATE story_pages SET page_number = ?, text = ?, image = ? WHERE id = ?");
    $stmt->bind_param("issi", $page_number, $text, $image_path, $page_id);
    $stmt->execute();
    $stmt->close();

    // --- Handle New Audio File Upload ---
    if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] == 0) {
        $target_dir = "../assets/audio/";
        $audio_name = basename($_FILES["audio_file"]["name"]);
        $audio_ext = pathinfo($audio_name, PATHINFO_EXTENSION);
        $unique_audio_name = uniqid('audio_', true) . '.' . $audio_ext;
        $target_file = $target_dir . $unique_audio_name;

        if (move_uploaded_file($_FILES["audio_file"]["tmp_name"], $target_file)) {
            // Delete old audio file if it exists
            if (!empty($audio_file_path) && file_exists('../' . $audio_file_path)) {
                unlink('../' . $audio_file_path);
            }
            $audio_file_path = 'assets/audio/' . $unique_audio_name;
        }
    }

    // --- Update or Insert into audio table ---
    // Check if an audio record already exists for this page
    $check_stmt = $conn->prepare("SELECT id FROM audio WHERE story_page_id = ?");
    $check_stmt->bind_param("i", $page_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Update existing audio record
        $stmt_audio = $conn->prepare("UPDATE audio SET audio_url = ?, audio_file = ?, use_tts = ? WHERE story_page_id = ?");
        $stmt_audio->bind_param("ssii", $audio_url, $audio_file_path, $use_tts, $page_id);
    } else {
        // Insert new audio record
        $stmt_audio = $conn->prepare("INSERT INTO audio (story_page_id, audio_url, audio_file, use_tts) VALUES (?, ?, ?, ?)");
        $stmt_audio->bind_param("issi", $page_id, $audio_url, $audio_file_path, $use_tts);
    }
    $stmt_audio->execute();
    $stmt_audio->close();
    $check_stmt->close();

    $conn->close();
}

header("Location: manage_pages.php?story_id=" . $story_id);
exit;
?>