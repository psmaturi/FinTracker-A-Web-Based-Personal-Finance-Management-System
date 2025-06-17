<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['expense_id'])) {
    $expenseId = intval($_POST['expense_id']);
    $userId = $_SESSION['user_id'];

    // Step 1: Get the expense amount BEFORE deleting
    $getAmountQuery = "SELECT amount FROM expenses WHERE id = ? AND user_id = ?";
    $getStmt = $conn->prepare($getAmountQuery);
    $getStmt->bind_param("ii", $expenseId, $userId);
    $getStmt->execute();
    $getResult = $getStmt->get_result();

    if ($row = $getResult->fetch_assoc()) {
        $amount = $row['amount'];

        // Step 2: Delete the expense
        $deleteQuery = "DELETE FROM expenses WHERE id = ? AND user_id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("ii", $expenseId, $userId);
        $deleteStmt->execute();

        // Step 3: Update the budget
        $updateBudgetQuery = "UPDATE budget SET total_expense = total_expense - ?, budget_left = budget_left + ? WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateBudgetQuery);
        $updateStmt->bind_param("ddi", $amount, $amount, $userId);
        $updateStmt->execute();

        // Step 4: Redirect back to budget page
        header("Location: budget.php");
        exit();
    } else {
        echo "Expense not found or unauthorized access.";
    }

    $getStmt->close();
    $deleteStmt->close();
    $updateStmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
    exit();
}
?>
