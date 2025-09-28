<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../config/database.php';

$conn = connect();

$query = "SELECT id, name FROM categories ORDER BY name ASC";
$result = $conn->query($query);

$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

echo json_encode($categories);

$conn->close();
?>