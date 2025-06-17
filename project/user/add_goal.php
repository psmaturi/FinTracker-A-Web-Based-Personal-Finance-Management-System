<?php
header("Content-Type: application/json");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized access"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$goalName = trim($data['goalName'] ?? '');
$targetAmount = floatval($data['targetAmount'] ?? 0);
$deadline = $data['deadline'] ?? '';
$user_id = $_SESSION['user_id'];

if ($goalName && $targetAmount > 0 && $deadline) {
    $stmt = $conn->prepare("INSERT INTO goals (user_id, goal_name, target_amount, deadline) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["error" => "Prepare failed: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("isds", $user_id, $goalName, $targetAmount, $deadline);
    if ($stmt->execute()) {
        echo json_encode(["success" => "Goal added successfully"]);
    } else {
        echo json_encode(["error" => "Execute failed: " . $stmt->error]);
    }
} else {
    echo json_encode(["error" => "Missing or invalid goal data"]);
}
?>
