<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

include 'db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: admin_investments.php");
    exit();
}

$id = intval($_GET['id']);
$investment = $conn->query("SELECT * FROM investments WHERE id = $id")->fetch_assoc();

if (!$investment) {
    echo "Investment not found.";
    exit();
}

// Update investment on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $conn->real_escape_string($_POST['type']);
    $name = $conn->real_escape_string($_POST['name']);
    $amount = floatval($_POST['amount']);
    $current_value = floatval($_POST['current_value']);

    $conn->query("UPDATE investments SET 
        type = '$type', 
        name = '$name', 
        amount = $amount, 
        current_value = $current_value 
        WHERE id = $id");

    header("Location: admin_investments.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Investment | Admin - FinTracker</title>
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
        .form-container input[type="text"],
        .form-container input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .form-container button {
            margin-top: 20px;
            padding: 10px 20px;
            background: #007bff;
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
    <h2>Edit Investment</h2>
    <form method="post">
        <label for="type">Investment Type:</label>
        <input type="text" id="type" name="type" value="<?= htmlspecialchars($investment['type']) ?>" required>

        <label for="name">Investment Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($investment['name']) ?>" required>

        <label for="amount">Amount:</label>
        <input type="number" step="0.01" id="amount" name="amount" value="<?= $investment['amount'] ?>" required>

        <label for="current_value">Current Value:</label>
        <input type="number" step="0.01" id="current_value" name="current_value" value="<?= $investment['current_value'] ?>" required>

        <button type="submit">Update Investment</button>
    </form>
    <a href="admin_investments.php">← Back to Investments</a>
</div>

</body>
</html>
