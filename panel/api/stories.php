<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../config/database.php';

$conn = connect();

$language_id = isset($_GET['language_id']) ? (int)$_GET['language_id'] : 0;
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

$query = "SELECT id, title, description, cover_image FROM stories";

$conditions = [];
$params = [];
$types = "";

if ($language_id > 0) {
    $conditions[] = "language_id = ?";
    $params[] = $language_id;
    $types .= "i";
}

if ($category_id > 0) {
    $conditions[] = "category_id = ?";
    $params[] = $category_id;
    $types .= "i";
}

if (count($conditions) > 0) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = $conn->prepare($query);

if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$stories = [];
while ($row = $result->fetch_assoc()) {
    // Prepend the base URL if the image path is relative
    $row['cover_image'] = 'http://' . $_SERVER['HTTP_HOST'] . '/backend/' . $row['cover_image'];
    $stories[] = $row;
}

echo json_encode($stories);

$stmt->close();
$conn->close();
?>