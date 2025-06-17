<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$goalId = intval($data['goalId']);
$savedAmount = floatval($data['savedAmount']);
$user_id = $_SESSION['user_id'];

if ($savedAmount <= 0) {
    echo json_encode(["error" => "Enter a valid savings amount."]);
    exit();
}

// Update the saved amount
$update = "UPDATE goals SET saved_amount = saved_amount + ?, completed = (saved_amount + ? >= target_amount) WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("ddii", $savedAmount, $savedAmount, $goalId, $user_id);

if ($stmt->execute()) {
    echo json_encode(["success" => "Savings updated!"]);
} else {
    echo json_encode(["error" => "Failed to update savings."]);
}
?>
