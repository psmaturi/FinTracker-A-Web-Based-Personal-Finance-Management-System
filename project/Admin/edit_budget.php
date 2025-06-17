<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}
include 'db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: admin_budgets.php");
    exit();
}

$id = intval($_GET['id']);
$budget = $conn->query("SELECT * FROM budget WHERE user_id = $id")->fetch_assoc();

if (!$budget) {
    echo "Budget not found.";
    exit();
}

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    $month = trim($_POST['month']);

    $stmt = $conn->prepare("UPDATE budget SET total_budget = ?, created_at = ? WHERE id = ?");
    $stmt->bind_param("dsi", $amount, $month, $id);
    $stmt->execute();

    header("Location: admin_budgets.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Budget | Admin - FinTracker</title>
    <link rel="stylesheet" href="admin-dashboard.css">
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>FinTracker</h2>
        </div>
        <ul class="nav-links">
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="admin_users.php">Users</a></li>
            <li class="active"><a href="admin_budgets.php">Budgets</a></li>
            <li><a href="admin_goals.php">Goals</a></li>
            <li><a href="admin_investments.php">Investments</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-nav">
            <div class="dashboard-title">Edit Budget</div>
        </div>

        <div class="content-form" style="padding: 20px;">
            <form method="POST">
                <label>Amount:</label><br>
                <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($budget['total_budget']) ?>" required><br><br>

                <label>Month:</label><br>
                <input type="month" name="month" value="<?= date('Y-m', strtotime($budget['created_at'])) ?>" required><br><br>

                <button type="submit">Update Budget</button>
                <a href="admin_budgets.php" style="margin-left: 10px;">Cancel</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
