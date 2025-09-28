<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';

if (isset($_GET['id']) && isset($_GET['story_id'])) {
    $conn = connect();
    $page_id = (int)$_GET['id'];
    $story_id = (int)$_GET['story_id']; // For redirection

    // --- Get file paths before deleting records ---
    // Get page image path
    $stmt_page = $conn->prepare("SELECT image FROM story_pages WHERE id = ?");
    $stmt_page->bind_param("i", $page_id);
    $stmt_page->execute();
    $stmt_page->bind_result($image_path);
    $stmt_page->fetch();
    $stmt_page->close();

    // Get audio file path
    $stmt_audio = $conn->prepare("SELECT audio_file FROM audio WHERE story_page_id = ?");
    $stmt_audio->bind_param("i", $page_id);
    $stmt_audio->execute();
    $stmt_audio->bind_result($audio_file_path);
    $stmt_audio->fetch();
    $stmt_audio->close();

    // --- Delete database records ---
    // Must delete from 'audio' first due to foreign key constraint
    $delete_audio_stmt = $conn->prepare("DELETE FROM audio WHERE story_page_id = ?");
    $delete_audio_stmt->bind_param("i", $page_id);
    $delete_audio_stmt->execute();
    $delete_audio_stmt->close();

    // Now delete from 'story_pages'
    $delete_page_stmt = $conn->prepare("DELETE FROM story_pages WHERE id = ?");
    $delete_page_stmt->bind_param("i", $page_id);
    $delete_page_stmt->execute();
    $delete_page_stmt->close();

    // --- Delete associated files ---
    // Delete page image
    if (!empty($image_path) && file_exists('../' . $image_path)) {
        unlink('../' . $image_path);
    }

    // Delete audio file
    if (!empty($audio_file_path) && file_exists('../' . $audio_file_path)) {
        unlink('../' . $audio_file_path);
    }

    $conn->close();
    header("Location: manage_pages.php?story_id=" . $story_id);
    exit;
} else {
    // Redirect if IDs are not provided
    header("Location: stories.php");
    exit;
}
?>