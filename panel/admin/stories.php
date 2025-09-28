<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';
$conn = connect();

// Fetch all stories with their category and language
$result = $conn->query("
    SELECT s.id, s.title, c.name AS category_name, l.name AS language_name
    FROM stories s
    JOIN categories c ON s.category_id = c.id
    JOIN languages l ON s.language_id = l.id
    ORDER BY s.title ASC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Stories</title>
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Manage Stories</h2>
        <a href="dashboard.php">Back to Dashboard</a>
        <hr>

        <a href="add_story.php">Add New Story</a>

        <hr>

        <h3>Existing Stories</h3>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Language</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['language_name']); ?></td>
                    <td>
                        <a href="edit_story.php?id=<?php echo $row['id']; ?>">Edit Story</a> |
                        <a href="manage_pages.php?story_id=<?php echo $row['id']; ?>">Manage Pages</a> |
                        <a href="delete_story.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete Story</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>