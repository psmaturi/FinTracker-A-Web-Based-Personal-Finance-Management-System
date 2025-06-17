<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}
include 'db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: admin_goals.php");
    exit();
}

$id = intval($_GET['id']);
$goal = $conn->query("SELECT * FROM goals WHERE user_id = $id")->fetch_assoc();

if (!$goal) {
    echo "Goal not found.";
    exit();
}

// Update goal on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $goal_name = trim($_POST['goal_name']);
    $target_amount = floatval($_POST['target_amount']);
    $saved_amount = floatval($_POST['saved_amount']);
    $deadline = $_POST['deadline'];

    $stmt = $conn->prepare("UPDATE goals SET goal_name = ?, target_amount = ?, saved_amount = ?, deadline = ? WHERE user_id = ?");
    $stmt->bind_param("sddsi", $goal_name, $target_amount, $saved_amount, $deadline, $id);
    $stmt->execute();

    header("Location: admin_goals.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Goal | Admin - FinTracker</title>
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
            <li><a href="admin_budgets.php">Budgets</a></li>
            <li class="active"><a href="admin_goals.php">Goals</a></li>
            <li><a href="admin_investments.php">Investments</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-nav">
            <div class="dashboard-title">Edit Goal</div>
        </div>

        <div class="content-form" style="padding: 20px;">
            <form method="POST">
                <label>Goal Name:</label><br>
                <input type="text" name="goal_name" value="<?= htmlspecialchars($goal['goal_name']) ?>" required><br><br>

                <label>Target Amount:</label><br>
                <input type="number" name="target_amount" step="0.01" value="<?= $goal['target_amount'] ?>" required><br><br>

                <label>Saved Amount:</label><br>
                <input type="number" name="saved_amount" step="0.01" value="<?= $goal['saved_amount'] ?>" required><br><br>

                <label>Deadline:</label><br>
                <input type="date" name="deadline" value="<?= $goal['deadline'] ?>" required><br><br>

                <button type="submit">Update Goal</button>
                <a href="admin_goals.php" style="margin-left: 10px;">Cancel</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
