<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../config/database.php';

$conn = connect();

$query = "SELECT id, name, code FROM languages ORDER BY name ASC";
$result = $conn->query($query);

$languages = [];
while ($row = $result->fetch_assoc()) {
    $languages[] = $row;
}

echo json_encode($languages);

$conn->close();
?>