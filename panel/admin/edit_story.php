<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';
$conn = connect();

$story_id = $_GET['id'];

// Fetch story details
$stmt = $conn->prepare("SELECT * FROM stories WHERE id = ?");
$stmt->bind_param("i", $story_id);
$stmt->execute();
$story = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch categories and languages for dropdowns
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$languages = $conn->query("SELECT * FROM languages ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Story</title>
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Edit Story</h2>
        <form action="update_story.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="story_id" value="<?php echo $story['id']; ?>">

            <label for="title">Title</label>
            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($story['title']); ?>" required>

            <label for="description">Description</label>
            <textarea name="description" id="description" required><?php echo htmlspecialchars($story['description']); ?></textarea>

            <label for="cover_image">Cover Image (leave empty to keep current)</label>
            <p>Current Image: <img src="../<?php echo htmlspecialchars($story['cover_image']); ?>" alt="Cover" width="100"></p>
            <input type="file" name="cover_image" id="cover_image">
            <input type="hidden" name="current_cover_image" value="<?php echo htmlspecialchars($story['cover_image']); ?>">

            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" required>
                <?php while ($row = $categories->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>" <?php if($row['id'] == $story['category_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($row['name']); ?>
                </option>
                <?php endwhile; ?>
            </select>

            <label for="language_id">Language</label>
            <select name="language_id" id="language_id" required>
                <?php while ($row = $languages->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>" <?php if($row['id'] == $story['language_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($row['name']); ?>
                </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Update Story</button>
        </form>
        <a href="stories.php">Back to Stories</a>
    </div>
</body>
</html>