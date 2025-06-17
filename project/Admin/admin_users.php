<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}
include 'db_connect.php';

// Delete user
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE user_id = $id");
    header("Location: admin_users.php");
    exit();
}

// Fetch all users
$result = $conn->query("SELECT * FROM users ORDER BY user_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users | Admin - FinTracker</title>
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

.top-nav .dashboard-title {
    font-size: 26px;
    font-weight: 700;
    color: rgb(30, 58, 138);
    margin-bottom: 25px;
    border-bottom: 3px solid #10B981;
    display: inline-block;
    padding-bottom: 8px;
}

.content-table {
    overflow-x: auto;
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

thead {
    background-color: #1E3A8A;
    color: #ffffff;
    position: sticky;
    top: 0;
    z-index: 1;
}

th, td {
    padding: 14px 20px;
    text-align: left;
    font-size: 15px;
    border-bottom: 1px solid #e5e7eb;
    vertical-align: middle;
}

th {
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

tr:hover {
    background-color: #F9FAFB;
    transition: all 0.2s ease;
}

.action-btn {
    padding: 7px 14px;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    margin-right: 6px;
    transition: all 0.2s ease-in-out;
}

.edit-btn {
    background-color: #1E3A8A;
    color: #ffffff;
}

.edit-btn:hover {
    background-color: #2745aa;
    box-shadow: 0 0 5px rgba(30, 58, 138, 0.5);
}

.delete-btn {
    background-color: #dc2626;
    color: #ffffff;
}

.delete-btn:hover {
    background-color: #b91c1c;
    box-shadow: 0 0 5px rgba(220, 38, 38, 0.5);
}

@media (max-width: 768px) {
    table, thead, tbody, th, td, tr {
        display: block;
    }

    thead {
        display: none;
    }

    tr {
        margin-bottom: 15px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    td {
        position: relative;
        padding-left: 50%;
        text-align: right;
        border: none;
        border-bottom: 1px solid #e5e7eb;
    }

    td::before {
        content: attr(data-label);
        position: absolute;
        left: 16px;
        width: 45%;
        padding-right: 10px;
        white-space: nowrap;
        text-align: left;
        font-weight: 600;
        color: #1E3A8A;
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
            <li class="active"><a href="admin_users.php">Users</a></li>
            <li><a href="admin_budgets.php">Budgets</a></li>
            <li><a href="admin_goals.php">Goals</a></li>
            <li><a href="admin_investments.php">Investments</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-nav">
            <div class="dashboard-title">Manage Users</div>
        </div>

        <div class="content-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td data-label="ID"><?= $row['user_id'] ?></td>
                                <td data-label="Username"><?= htmlspecialchars($row['username']) ?></td>
                                <td data-label="Name"><?= htmlspecialchars($row['full_name']) ?></td>
                                <td data-label="Email"><?= htmlspecialchars($row['email']) ?></td>
                                <td data-label="Phone"><?= htmlspecialchars($row['phone']) ?></td>
                                <td data-label="Registered"><?= $row['created_at'] ?></td>
                                <td data-label="Actions">
                                    <a href="admin_edit_user.php?id=<?= $row['user_id'] ?>" class="action-btn edit-btn">Edit</a>
                                    <a href="?delete=<?= $row['user_id'] ?>" class="action-btn delete-btn" onclick="return confirm('Delete this user?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
