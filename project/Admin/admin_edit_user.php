<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}
include 'db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: admin_users.php");
    exit();
}

$id = intval($_GET['id']);
$user = $conn->query("SELECT * FROM users WHERE user_id = $id")->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    $stmt = $conn->prepare("UPDATE users SET username = ?, full_name = ?, email = ?, phone = ? WHERE user_id = ?");
    $stmt->bind_param("ssssi", $username, $name, $email, $phone, $id);
    $stmt->execute();

    header("Location: admin_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User | Admin - FinTracker</title>
    <link rel="stylesheet" href="admin-dashboard.css">
    <style>
        body {
            background-color: #F3F4F6;
            font-family: 'Segoe UI', sans-serif;
            color: #111827;
        }

        .main-content {
            padding: 30px;
        }

        .dashboard-title {
            font-size: 24px;
            font-weight: 600;
            color:rgb(234, 236, 243);
            margin-bottom: 20px;
            border-bottom: 2px solid #10B981;
            display: inline-block;
            padding-bottom: 5px;
        }

        .content-form {
            background-color: #fff;
            border-radius: 16px;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
        }

        .content-form form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 600;
            margin-bottom: 6px;
            color: #1E3A8A;
        }

        input[type="text"], input[type="email"] {
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            transition: all 0.2s ease-in-out;
            font-size: 15px;
        }

        input[type="text"]:focus, input[type="email"]:focus {
            border-color: #10B981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        button[type="submit"] {
            margin-top: 20px;
            padding: 12px 20px;
            background-color: #10B981;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }

        button[type="submit"]:hover {
            background-color: #0f9f78;
        }

        .cancel-btn {
            margin-top: 10px;
            text-align: center;
            display: inline-block;
            color: #1E3A8A;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease-in-out;
        }

        .cancel-btn:hover {
            color: #0e2566;
        }

        @media (max-width: 600px) {
            .content-form {
                padding: 20px;
                margin: 0 10px;
            }

            .dashboard-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>FinTracker</h2>
        </div>
        <ul class="nav-links">
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li class="active"><a href="admin_users.php">Users</a></li>
            <li><a href="admin_budgets.php">Budgets</a></li>
            <li><a href="admin_goals.php">Goals</a></li>
            <li><a href="admin_investments.php">Investments</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-nav">
            <div class="dashboard-title">Edit User</div>
        </div>

        <div class="content-form">
            <form method="POST">
                <label>Username:</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

                <label>Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['full_name']) ?>" required>

                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

                <label>Phone:</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">

                <button type="submit">Update User</button>
                <a href="admin_users.php" class="cancel-btn">Cancel</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
