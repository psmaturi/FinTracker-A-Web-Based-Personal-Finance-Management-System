<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT id, goal_name, target_amount, saved_amount, deadline, (saved_amount >= target_amount) AS completed FROM goals WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$goals = [];
while ($row = $result->fetch_assoc()) {
    $row['completed'] = (bool)$row['completed'];
    $goals[] = $row;
}

echo json_encode($goals);
?>
