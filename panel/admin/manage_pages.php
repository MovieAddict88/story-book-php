<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';
$conn = connect();

$story_id = isset($_GET['story_id']) ? (int)$_GET['story_id'] : 0;
if ($story_id === 0) {
    header("Location: stories.php");
    exit;
}

// Fetch story title for display
$stmt = $conn->prepare("SELECT title FROM stories WHERE id = ?");
$stmt->bind_param("i", $story_id);
$stmt->execute();
$stmt->bind_result($story_title);
$stmt->fetch();
$stmt->close();

// Fetch existing pages for this story
$pages_result = $conn->query("SELECT * FROM story_pages WHERE story_id = $story_id ORDER BY page_number ASC");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Pages for "<?php echo htmlspecialchars($story_title); ?>"</title>
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Manage Pages for: <em><?php echo htmlspecialchars($story_title); ?></em></h2>
        <a href="stories.php">Back to Stories List</a>
        <hr>

        <h3>Existing Pages</h3>
        <table>
            <thead>
                <tr>
                    <th>Page #</th>
                    <th>Text (Excerpt)</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($page = $pages_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($page['page_number']); ?></td>
                    <td><?php echo htmlspecialchars(substr($page['text'], 0, 50)) . '...'; ?></td>
                    <td><?php if($page['image']) { echo '<img src="../' . htmlspecialchars($page['image']) . '" width="100">'; } ?></td>
                    <td>
                        <a href="edit_page.php?id=<?php echo $page['id']; ?>">Edit</a> |
                        <a href="delete_page.php?id=<?php echo $page['id']; ?>&story_id=<?php echo $story_id; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <hr>

        <h3>Add New Page</h3>
        <form action="add_page.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">

            <label for="page_number">Page Number</label>
            <input type="number" name="page_number" id="page_number" required>

            <label for="text">Page Text</label>
            <textarea name="text" id="text" rows="6" required></textarea>

            <label for="image">Page Image (optional)</label>
            <input type="file" name="image" id="image">

            <h4>Audio Options</h4>
            <label><input type="checkbox" name="use_tts" value="1"> Use Text-to-Speech for this page</label>

            <label for="audio_url">Or, provide Audio URL</label>
            <input type="text" name="audio_url" id="audio_url" placeholder="http://example.com/audio.mp3">

            <label for="audio_file">Or, upload Audio File</label>
            <input type="file" name="audio_file" id="audio_file">

            <button type="submit">Add Page</button>
        </form>
    </div>
</body>
</html>