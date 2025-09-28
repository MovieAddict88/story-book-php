<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = connect();
    $id = $_POST['id'];
    $name = $_POST['name'];
    $code = $_POST['code'];

    if (!empty($name) && !empty($code) && !empty($id)) {
        $stmt = $conn->prepare("UPDATE languages SET name = ?, code = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $code, $id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: languages.php");
exit;
?>