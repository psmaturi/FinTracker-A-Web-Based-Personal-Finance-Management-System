<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $budgetAmount = floatval($_POST['budgetAmount']);

    if ($budgetAmount <= 0) {
        echo "<script>alert('Please enter a valid budget amount!'); window.history.back();</script>";
        exit();
    }

    // Check if the user already has a budget entry
    $checkQuery = "SELECT total_budget, budget_left FROM budget WHERE user_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch current budget values
        $row = $result->fetch_assoc();
        $newTotalBudget = $row['total_budget'] + $budgetAmount;
        $newBudgetLeft = $row['budget_left'] + $budgetAmount;

        // Update budget by adding to existing values
        $updateQuery = "UPDATE budget SET total_budget = ?, budget_left = ? WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ddi", $newTotalBudget, $newBudgetLeft, $user_id);
        $updateStmt->execute();
    } else {
        // Insert new budget if no entry exists
        $insertQuery = "INSERT INTO budget (user_id, total_budget, total_expense, budget_left) VALUES (?, ?, 0, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("idd", $user_id, $budgetAmount, $budgetAmount);
        $insertStmt->execute();
    }

    echo "<script>alert('Budget updated successfully!'); window.location.href='budget.php';</script>";
    exit();
}
?>
