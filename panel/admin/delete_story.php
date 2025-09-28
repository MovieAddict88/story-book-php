<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';

if (isset($_GET['id'])) {
    $conn = connect();
    $story_id = (int)$_GET['id'];

    // --- Step 1: Get all page IDs for the story ---
    $pages_to_delete = [];
    $page_result = $conn->query("SELECT id FROM story_pages WHERE story_id = $story_id");
    while ($row = $page_result->fetch_assoc()) {
        $pages_to_delete[] = $row['id'];
    }

    // --- Step 2: For each page, delete associated files and records ---
    foreach ($pages_to_delete as $page_id) {
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

        // Delete records from 'audio' table first
        $delete_audio_stmt = $conn->prepare("DELETE FROM audio WHERE story_page_id = ?");
        $delete_audio_stmt->bind_param("i", $page_id);
        $delete_audio_stmt->execute();
        $delete_audio_stmt->close();

        // Delete records from 'story_pages' table
        $delete_page_stmt = $conn->prepare("DELETE FROM story_pages WHERE id = ?");
        $delete_page_stmt->bind_param("i", $page_id);
        $delete_page_stmt->execute();
        $delete_page_stmt->close();

        // Delete physical files
        if (!empty($image_path) && file_exists('../' . $image_path)) {
            unlink('../' . $image_path);
        }
        if (!empty($audio_file_path) && file_exists('../' . $audio_file_path)) {
            unlink('../' . $audio_file_path);
        }
    }

    // --- Step 3: Delete the main story record and its cover image ---
    // Get the cover image path
    $stmt_story = $conn->prepare("SELECT cover_image FROM stories WHERE id = ?");
    $stmt_story->bind_param("i", $story_id);
    $stmt_story->execute();
    $stmt_story->bind_result($cover_image_path);
    $stmt_story->fetch();
    $stmt_story->close();

    // Delete the story record
    $delete_story_stmt = $conn->prepare("DELETE FROM stories WHERE id = ?");
    $delete_story_stmt->bind_param("i", $story_id);
    if ($delete_story_stmt->execute()) {
        // If the record is deleted, delete the cover image file
        if (!empty($cover_image_path) && file_exists('../' . $cover_image_path)) {
            unlink('../' . $cover_image_path);
        }
    }
    $delete_story_stmt->close();

    $conn->close();
}

header("Location: stories.php");
exit;
?>