<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}
include 'db_connect.php';
if (isset($_GET['delete_budget'])) {
    $budget_id = $_GET['delete_budget'];
    $delete_query = "DELETE FROM budget WHERE budget_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $budget_id);

    if ($stmt->execute()) {
        echo "<script>alert('Budget deleted successfully'); window.location.href='admin_budgets.php';</script>";
    } else {
        echo "<script>alert('Failed to delete budget');</script>";
    }
}

// Fetch all budgets
$budgets = $conn->query("SELECT budget.*, users.full_name FROM budget JOIN users ON budget.user_id = users.user_id ORDER BY budget.user_id DESC");

// Fetch all expenses grouped by user_id
$expenses = [];
$expenseResult = $conn->query("SELECT * FROM expenses");
while ($exp = $expenseResult->fetch_assoc()) {
    $expenses[$exp['user_id']][] = $exp;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Budgets | Admin - FinTracker</title>
    <link rel="stylesheet" href="admin-dashboard.css">
    <style>
        body {
            background-color: #F3F4F6;
            font-family: 'Segoe UI', sans-serif;
            color: #111827;
            margin: 0;
        }

        .main-content {
            padding: 30px;
        }

        .top-nav .dashboard-title {
            font-size: 28px;
            font-weight: 700;
            color: #1E3A8A;
            margin-bottom: 24px;
            border-bottom: 3px solid #10B981;
            display: inline-block;
            padding-bottom: 8px;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }

        .styled-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .styled-table thead {
            background-color: #1E3A8A;
            color: #ffffff;
            text-align: left;
        }

        .styled-table th,
        .styled-table td {
            padding: 14px 18px;
            border-bottom: 1px solid #E5E7EB;
            vertical-align: top;
        }

        .styled-table tbody tr:hover {
            background-color: #F0FDF4;
        }

        .styled-table tbody tr:last-child td {
            border-bottom: none;
        }

        .styled-table ul {
            list-style: disc;
            padding-left: 18px;
            margin: 0;
            color: #374151;
            font-size: 14px;
        }

        .delete-btn {
            background-color: #DC2626;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease;
            display: inline-block;
        }

        .delete-btn:hover {
            background-color: #B91C1C;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }

            .styled-table th,
            .styled-table td {
                font-size: 13px;
                padding: 12px;
            }

            .styled-table ul {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo"><img src="logo.png" alt="FinTracker Logo" style="height: 40px;"></div>
            <h2>FinTracker</h2>
        </div>
        <ul class="nav-links">
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="admin_users.php">Users</a></li>
            <li class="active"><a href="admin_budgets.php">Budgets</a></li>
            <li><a href="admin_goals.php">Goals</a></li>
            <li><a href="admin_investments.php">Investments</a></li>
            <li><a href="logout1.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-nav">
            <div class="dashboard-title">Manage Budgets</div>
        </div>

        <div class="table-container">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Budget Amount</th>
                        <th>Month</th>
                        <th>Expenses</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($budgets->num_rows === 0): ?>
                        <tr>
                            <td colspan="5">No budgets found.</td>
                        </tr>
                    <?php else: ?>
                        <?php while ($row = $budgets->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['full_name']) ?></td>
                                <td>₹<?= $row['total_budget'] ?? $row['amount'] ?></td>
                                <td><?= $row['created_at'] ?></td>
                                <td>
                                    <?php if (!empty($expenses[$row['user_id']])): ?>
                                        <ul>
                                            <?php foreach ($expenses[$row['user_id']] as $exp): ?>
                                                <li><?= htmlspecialchars($exp['title']) ?> - ₹<?= $exp['amount'] ?> on <?= $exp['created_at'] ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <em>No expenses found.</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                <a href="?delete_budget=<?= $row['budget_id'] ?>" onclick="event.stopPropagation(); return confirm('Delete this budget?');" style="color:red;">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
