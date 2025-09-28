<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = connect();
    $name = $_POST['name'];
    $code = $_POST['code'];

    if (!empty($name) && !empty($code)) {
        $stmt = $conn->prepare("INSERT INTO languages (name, code) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $code);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: languages.php");
exit;
?>