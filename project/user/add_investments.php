<?php
session_start();
header("Content-Type: application/json");
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "Invalid JSON input."]);
    exit();
}

$type = trim($data['type'] ?? '');
$name = trim($data['name'] ?? '');
$amount = floatval($data['amount'] ?? 0);
$currentValue = floatval($data['currentValue'] ?? 0);
$user_id = $_SESSION['user_id'];

if ($type && $name && $amount > 0 && $currentValue >= 0) {
    $stmt = $conn->prepare("INSERT INTO investments (user_id, type, name, amount, current_value) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issdd", $user_id, $type, $name, $amount, $currentValue);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => $stmt->error]); // Show detailed SQL error
    }
} else {
    echo json_encode(["error" => "Invalid data."]);
}
?>
