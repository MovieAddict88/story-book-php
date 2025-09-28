<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_user']); ?>!</h2>
        <p>This is the admin dashboard. From here, you can manage stories, categories, languages, and more.</p>
        <ul>
            <li><a href="categories.php">Manage Categories</a></li>
            <li><a href="languages.php">Manage Languages</a></li>
            <li><a href="stories.php">Manage Stories</a></li>
            <li><a href="change_password.php">Change Password</a></li>
            <!-- More links will be added here -->
        </ul>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>