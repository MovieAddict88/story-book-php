<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';
$conn = connect();

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT name, code FROM languages WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name, $code);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Language</title>
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Edit Language</h2>
        <form action="update_language.php" method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <label for="name">Language Name</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required>
            <label for="code">Language Code</label>
            <input type="text" name="code" id="code" value="<?php echo htmlspecialchars($code); ?>" required>
            <button type="submit">Update Language</button>
        </form>
        <a href="languages.php">Back to Languages</a>
    </div>
</body>
</html>