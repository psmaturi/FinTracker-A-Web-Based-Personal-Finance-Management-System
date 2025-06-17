<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

include 'db_connect.php';
$admin_id = $_SESSION['admin_id']; // assume this exists from login

// Fetch logs from system_logs table
$logs = $conn->query("SELECT * FROM system_logs ORDER BY timestamp DESC LIMIT 50");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Security | Admin - FinTracker</title>
    <link rel="stylesheet" href="admin-dashboard.css">
    <style>
        .main-content { padding: 30px; }
        .dashboard-title {
            font-size: 26px;
            font-weight: 700;
            color: #1E3A8A;
            border-bottom: 3px solid #10B981;
            margin-bottom: 25px;
            display: inline-block;
            padding-bottom: 8px;
        }
        .security-box {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        .card {
            flex: 1;
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .card h3 {
            color: #1E3A8A;
            margin-bottom: 20px;
        }
        input[type="password"], button {
            padding: 10px;
            width: 100%;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background: #10B981;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #0d946f;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        th {
            background: #1E3A8A;
            color: white;
            text-align: left;
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo"><img src="logo.png" alt="Logo" style="height: 40px;"></div>
            <h2>FinTracker</h2>
        </div>
        <ul class="nav-links">
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="admin_users.php">Users</a></li>
            <li><a href="admin_budgets.php">Budgets</a></li>
            <li><a href="admin_goals.php">Goals</a></li>
            <li><a href="admin_investments.php">Investments</a></li>
            <li class="active"><a href="admin_security.php">Security</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="dashboard-title">Admin Security</div>

        <div class="security-box">
            <!-- Password Change -->
            <div class="card">
                <h3>Change Password</h3>
                <form action="update_admin_password.php" method="POST">
                    <input type="password" name="current_password" placeholder="Current Password" required>
                    <input type="password" name="new_password" placeholder="New Password" required>
                    <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                    <button type="submit">Update Password</button>
                </form>
            </div>

            <!-- Logs -->
            <div class="card">
                <h3>System Logs</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Action</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($log = $logs->fetch_assoc()): ?>
                            <tr>
                                <td><?= $log['timestamp'] ?></td>
                                <td><?= htmlspecialchars($log['action']) ?></td>
                                <td><?= htmlspecialchars($log['description']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if ($logs->num_rows === 0): ?>
                            <tr><td colspan="3">No logs found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
