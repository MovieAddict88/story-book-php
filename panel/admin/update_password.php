<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        header("Location: change_password.php?error=1"); // Passwords do not match
        exit;
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $admin_id = $_SESSION['admin_id'];

    $conn = connect();
    $stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $admin_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php?success=1"); // Password updated successfully
    } else {
        header("Location: change_password.php?error=2"); // Database error
    }
    exit;
}
?>