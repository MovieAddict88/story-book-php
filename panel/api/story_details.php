<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../config/database.php';

$conn = connect();
$story_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($story_id == 0) {
    http_response_code(400);
    echo json_encode(["message" => "Story ID is required."]);
    exit;
}

// --- Fetch Story Details ---
$stmt = $conn->prepare("SELECT id, title, description, cover_image FROM stories WHERE id = ?");
$stmt->bind_param("i", $story_id);
$stmt->execute();
$story = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$story) {
    http_response_code(404);
    echo json_encode(["message" => "Story not found."]);
    exit;
}

// Prepend base URL to cover image
$story['cover_image'] = 'http://' . $_SERVER['HTTP_HOST'] . '/backend/' . $story['cover_image'];

// --- Fetch Story Pages ---
$pages = [];
$stmt = $conn->prepare("SELECT id, page_number, text, image FROM story_pages WHERE story_id = ? ORDER BY page_number ASC");
$stmt->bind_param("i", $story_id);
$stmt->execute();
$result_pages = $stmt->get_result();

while ($page = $result_pages->fetch_assoc()) {
    // --- Fetch Audio for each page ---
    $audio_stmt = $conn->prepare("SELECT audio_url, audio_file, use_tts FROM audio WHERE story_page_id = ?");
    $audio_stmt->bind_param("i", $page['id']);
    $audio_stmt->execute();
    $audio_info = $audio_stmt->get_result()->fetch_assoc();
    $audio_stmt->close();

    // Prepend base URL to audio/image files if they exist
    if (!empty($page['image'])) {
        $page['image'] = 'http://' . $_SERVER['HTTP_HOST'] . '/backend/' . $page['image'];
    }
    if ($audio_info) {
        if (!empty($audio_info['audio_url'])) {
             // URL is assumed to be absolute already
        }
        if (!empty($audio_info['audio_file'])) {
            $audio_info['audio_file'] = 'http://' . $_SERVER['HTTP_HOST'] . '/backend/' . $audio_info['audio_file'];
        }
    }

    $page['audio'] = $audio_info ? $audio_info : null;
    $pages[] = $page;
}
$stmt->close();

$story['pages'] = $pages;

echo json_encode($story);

$conn->close();
?>