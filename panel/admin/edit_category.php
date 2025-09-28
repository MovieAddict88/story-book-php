<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';
$conn = connect();

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Category</title>
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Edit Category</h2>
        <form action="update_category.php" method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <label for="name">Category Name</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required>
            <button type="submit">Update Category</button>
        </form>
        <a href="categories.php">Back to Categories</a>
    </div>
</body>
</html>