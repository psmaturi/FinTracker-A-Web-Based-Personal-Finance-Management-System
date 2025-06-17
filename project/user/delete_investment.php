<?php
session_start();
header("Content-Type: application/json");
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$name = trim($data['name'] ?? '');
$user_id = $_SESSION['user_id'];

if ($name) {
    $stmt = $conn->prepare("DELETE FROM investments WHERE user_id = ? AND name = ?");
    $stmt->bind_param("is", $user_id, $name);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Deletion failed."]);
    }
} else {
    echo json_encode(["error" => "Invalid request."]);
}
?>
