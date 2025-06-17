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

$saving_id = intval($_GET['id']);
$saving = $conn->query("SELECT goal_savings.*, goals.goal_name, users.full_name 
                        FROM goal_savings 
                        JOIN goals ON goal_savings.goal_id = goals.id 
                        JOIN users ON goals.user_id = users.user_id 
                        WHERE goal_savings.id = $saving_id")->fetch_assoc();

if (!$saving) {
    echo "Saving record not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    $date = $_POST['date'];

    $conn->query("UPDATE goal_savings SET amount = $amount, date = '$date' WHERE id = $saving_id");
    header("Location: admin_goals.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Goal Saving | Admin - FinTracker</title>
    <link rel="stylesheet" href="admin-dashboard.css">
    <style>
        .form-container {
            width: 50%;
            margin: 40px auto;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .form-container label {
            display: block;
            margin-top: 10px;
        }
        .form-container input[type="number"],
        .form-container input[type="date"] {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .form-container button {
            margin-top: 20px;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .form-container a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Saving for Goal: <?= htmlspecialchars($saving['goal_name']) ?> (<?= htmlspecialchars($saving['full_name']) ?>)</h2>
    <form method="post">
        <label for="amount">Amount (₹):</label>
        <input type="number" name="amount" id="amount" value="<?= $saving['amount'] ?>" step="0.01" required>

        <label for="date">Date:</label>
        <input type="date" name="date" id="date" value="<?= $saving['date'] ?>" required>

        <button type="submit">Update Saving</button>
    </form>
    <a href="admin_goals.php">← Back to Goals</a>
</div>

</body>
</html>
