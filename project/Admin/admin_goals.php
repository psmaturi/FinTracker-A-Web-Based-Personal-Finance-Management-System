<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

include 'db_connect.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM goals WHERE id = $id");
    header("Location: admin_goals.php");
    exit();
}

// Fetch goals and join user info
$goals = $conn->query("
    SELECT goals.*, users.full_name 
    FROM goals 
    JOIN users ON goals.user_id = users.user_id 
    ORDER BY goals.id DESC
");

// Fetch savings grouped by goal_id
$savings_result = $conn->query("SELECT * FROM goal_savings ORDER BY date DESC");
$savings = [];
while ($row = $savings_result->fetch_assoc()) {
    $savings[$row['goal_id']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Goals | Admin - FinTracker</title>
    <link rel="stylesheet" href="admin-dashboard.css">
    <style>
        body {
            background-color: #F3F4F6;
            font-family: 'Segoe UI', sans-serif;
            color: #111827;
            margin: 0;
        }

        .dashboard-container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            background-color: #2c3e50;
            width: 250px;
            color: #fff;
            padding-top: 20px;
            flex-shrink: 0;
        }

        .sidebar-header {
            text-align: center;
            padding: 10px;
        }

        .sidebar h2 {
            font-size: 20px;
            margin-top: 10px;
            font-weight: bold;
        }

        .nav-links {
            list-style: none;
            padding: 0;
            margin-top: 20px;
        }

        .nav-links li {
            padding: 12px 20px;
        }

        .nav-links li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            display: block;
            transition: 0.3s;
        }

        .nav-links li a:hover,
        .nav-links .active a {
            background-color: #10B981;
            border-radius: 8px;
            color: #fff;
        }

        .main-content {
            flex-grow: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .dashboard-title {
            font-size: 24px;
            font-weight: 600;
            color:rgb(220, 224, 235);
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

        

        a.delete-btn {
            color: #DC2626;
            text-decoration: none;
            font-weight: 500;
        }

        a.delete-btn:hover {
            text-decoration: underline;
        }

        .saving-row {
            background-color: #f9f9f9;
            display: none;
        }

        .saving-row.open {
            display: table-row;
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

            .sidebar {
                width: 200px;
            }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
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
            <li class="active"><a href="admin_goals.php">Goals</a></li>
            <li><a href="admin_investments.php">Investments</a></li>
            <li><a href="logout1.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-nav">
            <div class="dashboard-title">Manage Goals</div>
        </div>

        <div class="content-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Goal Name</th>
                        <th>Target</th>
                        <th>Saved</th>
                        <th>Deadline</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $goals->fetch_assoc()): ?>
                        <tr class="goal-row" data-goal-id="<?= $row['id'] ?>">
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= htmlspecialchars($row['goal_name']) ?></td>
                            <td>₹<?= number_format($row['target_amount'], 2) ?></td>
                            <td>₹<?= number_format($row['saved_amount'], 2) ?></td>
                            <td><?= $row['deadline'] ?></td>
                            <td>
                               
                                <a href="?delete=<?= $row['id'] ?>" class="delete-btn" onclick="event.stopPropagation(); return confirm('Delete this goal?');">Delete</a>
                            </td>
                        </tr>
                        <tr class="saving-row" id="savings-<?= $row['id'] ?>">
                            <td colspan="7">
                                <strong>Savings:</strong>
                                <?php if (!empty($savings[$row['id']])): ?>
                                    <ul class="saving-list">
                                        <?php foreach ($savings[$row['id']] as $save): ?>
                                            <li>₹<?= number_format($save['amount'], 2) ?> on <?= $save['saved_date'] ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div>No savings records found.</div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($goals->num_rows === 0): ?>
                        <tr><td colspan="7">No goals found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.goal-row').forEach(row => {
        row.addEventListener('click', () => {
            const goalId = row.getAttribute('data-goal-id');
            const savingRow = document.getElementById('savings-' + goalId);
            if (savingRow) {
                savingRow.classList.toggle('open');
            }
        });
    });
</script>

</body>
</html>
