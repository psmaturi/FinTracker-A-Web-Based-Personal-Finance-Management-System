<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $expenseTitle = trim($_POST['expenseTitle']);
    $expenseAmount = floatval($_POST['expenseAmount']);

    if (empty($expenseTitle) || $expenseAmount <= 0) {
        echo "<script>alert('Invalid input! Please enter valid expense details.'); window.history.back();</script>";
        exit();
    }

    // Fetch the user's budget details before adding an expense
    $budgetQuery = "SELECT total_budget, total_expense, budget_left FROM budget WHERE user_id = ?";
    $stmt = $conn->prepare($budgetQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $budgetRow = $result->fetch_assoc();

    if (!$budgetRow) {
        echo "<script>alert('No budget found! Please add a budget first.'); window.history.back();</script>";
        exit();
    }

    $total_budget = $budgetRow['total_budget'];
    $total_expense = $budgetRow['total_expense'];
    $budget_left = $budgetRow['budget_left'];

    // Ensure expense does not exceed the remaining budget
    if ($expenseAmount > $budget_left) {
        echo "<script>alert('Expense exceeds your budget!'); window.history.back();</script>";
        exit();
    }

    // Insert new expense
    $query = "INSERT INTO expenses (user_id, title, amount) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isd", $user_id, $expenseTitle, $expenseAmount);

    if ($stmt->execute()) {
        // Update budget table
        $new_total_expense = $total_expense + $expenseAmount;
        $new_budget_left = $total_budget - $new_total_expense;

        $updateQuery = "UPDATE budget SET total_expense = ?, budget_left = ? WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ddi", $new_total_expense, $new_budget_left, $user_id);
        
        if ($updateStmt->execute()) {
            echo "<script>alert('Expense added successfully!'); window.location.href='budget.php';</script>";
        } else {
            echo "<script>alert('Error updating budget: " . $updateStmt->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error adding expense: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $updateStmt->close();
    $conn->close();
} else {
    header("Location: budget.php");
    exit();
}
?>
