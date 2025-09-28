<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';
$conn = connect();

$page_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($page_id === 0) {
    header("Location: stories.php");
    exit;
}

// Fetch page data
$stmt = $conn->prepare("SELECT * FROM story_pages WHERE id = ?");
$stmt->bind_param("i", $page_id);
$stmt->execute();
$page = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch audio data
$stmt_audio = $conn->prepare("SELECT * FROM audio WHERE story_page_id = ?");
$stmt_audio->bind_param("i", $page_id);
$stmt_audio->execute();
$audio = $stmt_audio->get_result()->fetch_assoc();
$stmt_audio->close();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Page</title>
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Edit Page #<?php echo htmlspecialchars($page['page_number']); ?></h2>
        <form action="update_page.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="page_id" value="<?php echo $page['id']; ?>">
            <input type="hidden" name="story_id" value="<?php echo $page['story_id']; ?>">

            <label for="page_number">Page Number</label>
            <input type="number" name="page_number" id="page_number" value="<?php echo htmlspecialchars($page['page_number']); ?>" required>

            <label for="text">Page Text</label>
            <textarea name="text" id="text" rows="6" required><?php echo htmlspecialchars($page['text']); ?></textarea>

            <label for="image">Page Image (leave empty to keep current)</label>
            <?php if($page['image']): ?>
                <p>Current: <img src="../<?php echo htmlspecialchars($page['image']); ?>" width="100"></p>
            <?php endif; ?>
            <input type="file" name="image" id="image">
            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($page['image']); ?>">

            <h4>Audio Options</h4>
            <label><input type="checkbox" name="use_tts" value="1" <?php if($audio && $audio['use_tts']) echo 'checked'; ?>> Use Text-to-Speech</label>

            <label for="audio_url">Or, Audio URL</label>
            <input type="text" name="audio_url" id="audio_url" value="<?php echo htmlspecialchars($audio['audio_url'] ?? ''); ?>">

            <label for="audio_file">Or, upload new Audio File (replaces current)</label>
             <?php if($audio && $audio['audio_file']): ?>
                <p>Current: <?php echo htmlspecialchars(basename($audio['audio_file'])); ?></p>
            <?php endif; ?>
            <input type="file" name="audio_file" id="audio_file">
            <input type="hidden" name="current_audio_file" value="<?php echo htmlspecialchars($audio['audio_file'] ?? ''); ?>">

            <button type="submit">Update Page</button>
        </form>
        <a href="manage_pages.php?story_id=<?php echo $page['story_id']; ?>">Back to Page List</a>
    </div>
</body>
</html>