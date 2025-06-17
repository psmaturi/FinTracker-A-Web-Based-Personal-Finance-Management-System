<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}
include 'db_connect.php';

// Delete investment
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM investments WHERE id = $id");
    header("Location: admin_investments.php");
    exit();
}

// ✅ Fetch all investments with error handling
$sql = "SELECT investments.*, users.full_name 
        FROM investments 
        JOIN users ON investments.user_id = users.user_id 
        ORDER BY investments.id DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Query Failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Investments | Admin - FinTracker</title>
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
            color: #1E3A8A;
            margin-bottom: 20px;
            border-bottom: 2px solid #10B981;
            display: inline-block;
            padding-bottom: 5px;
        }

        .content-table {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }

        th, td {
            padding: 14px 12px;
            text-align: left;
        }

        thead {
            background-color: #1E3A8A;
            color: white;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tbody tr:hover {
            background-color: #eef2ff;
            transition: 0.2s ease-in-out;
        }

        a.edit-btn {
            color: #2563EB;
            text-decoration: none;
            font-weight: 500;
        }

        a.edit-btn:hover {
            text-decoration: underline;
        }

        a.delete-btn {
            color: #DC2626;
            text-decoration: none;
            font-weight: 500;
        }

        a.delete-btn:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .dashboard-title {
                font-size: 20px;
            }

            th, td {
                padding: 10px;
                font-size: 14px;
            }

            .content-table {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="logo.png" alt="FinTracker Logo" style="height: 40px;">
            </div>
            <h2>FinTracker</h2>
        </div>
        <ul class="nav-links">
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="admin_users.php">Users</a></li>
            <li><a href="admin_budgets.php">Budgets</a></li>
            <li><a href="admin_goals.php">Goals</a></li>
            <li class="active"><a href="admin_investments.php">Investments</a></li>
            <li><a href="logout1.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-nav">
            <div class="dashboard-title">Manage Investments</div>
        </div>

        <div class="content-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Current Value</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= htmlspecialchars($row['type']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td>₹<?= number_format($row['amount'], 2) ?></td>
                            <td>₹<?= number_format($row['current_value'], 2) ?></td>
                            <td>
                                <a href="edit_investment.php?id=<?= $row['id'] ?>" class="edit-btn">Edit</a> |
                                <a href="?delete=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Delete this investment?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($result->num_rows === 0): ?>
                        <tr><td colspan="7">No investments found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
