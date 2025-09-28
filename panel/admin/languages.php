<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';
$conn = connect();

// Fetch all languages
$result = $conn->query("SELECT * FROM languages ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Languages</title>
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Manage Languages</h2>
        <a href="dashboard.php">Back to Dashboard</a>

        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<p style="color: red;">' . $_SESSION['error_message'] . '</p>';
            unset($_SESSION['error_message']);
        }
        if (isset($_SESSION['success_message'])) {
            echo '<p style="color: green;">' . $_SESSION['success_message'] . '</p>';
            unset($_SESSION['success_message']);
        }
        ?>

        <hr>

        <h3>Add New Language</h3>
        <form action="add_language.php" method="post">
            <label for="name">Language Name</label>
            <input type="text" name="name" id="name" required>
            <label for="code">Language Code (e.g., 'en', 'es')</label>
            <input type="text" name="code" id="code" required>
            <button type="submit">Add Language</button>
        </form>

        <hr>

        <h3>Existing Languages</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['code']); ?></td>
                    <td>
                        <a href="edit_language.php?id=<?php echo $row['id']; ?>">Edit</a> |
                        <a href="delete_language.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>