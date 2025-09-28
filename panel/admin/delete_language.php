<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';

if (isset($_GET['id'])) {
    $conn = connect();
    $id = (int)$_GET['id'];

    // --- Validation: Check if the language is in use ---
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM stories WHERE language_id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_stmt->bind_result($story_count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($story_count > 0) {
        // Language is in use, redirect with an error message
        $_SESSION['error_message'] = "Cannot delete language because it is currently assigned to " . $story_count . " story/stories.";
    } else {
        // Language is not in use, proceed with deletion
        $stmt = $conn->prepare("DELETE FROM languages WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['success_message'] = "Language deleted successfully.";
    }
    $conn->close();
}

header("Location: languages.php");
exit;
?>