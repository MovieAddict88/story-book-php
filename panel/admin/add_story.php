<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';
$conn = connect();

// Fetch categories and languages for dropdowns
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$languages = $conn->query("SELECT * FROM languages ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add New Story</title>
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Add New Story</h2>
        <form action="create_story.php" method="post" enctype="multipart/form-data">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" required>

            <label for="description">Description</label>
            <textarea name="description" id="description" required></textarea>

            <label for="cover_image">Cover Image</label>
            <input type="file" name="cover_image" id="cover_image" required>

            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" required>
                <?php while ($row = $categories->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                <?php endwhile; ?>
            </select>

            <label for="language_id">Language</label>
            <select name="language_id" id="language_id" required>
                <?php while ($row = $languages->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Add Story</button>
        </form>
        <a href="stories.php">Back to Stories</a>
    </div>
</body>
</html>