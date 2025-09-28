<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = connect();
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        $login_successful = false;
        // Check if the stored password is a valid hash
        if (isset($hashed_password) && password_get_info($hashed_password)['algo']) {
            if (password_verify($password, $hashed_password)) {
                $login_successful = true;
            }
        } else {
            // Otherwise, treat it as a plain-text password
            if ($password === $hashed_password) {
                $login_successful = true;
            }
        }

        if ($login_successful) {
            $_SESSION['admin_user'] = $username;
            $_SESSION['admin_id'] = $id;
            header("Location: dashboard.php");
            exit;
        }
    }

    header("Location: index.php?error=1");
    exit;
}
?>