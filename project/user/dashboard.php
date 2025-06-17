<?php
session_start();
include 'db_connect.php'; // Include your database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user budget data
$query = "SELECT total_budget, total_expense FROM budget WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$total_budget = $row['total_budget'] ?? 0;
$total_expense = $row['total_expense'] ?? 0;
$budget_left = $total_budget - $total_expense;
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Dashboard</title>
    <link rel="stylesheet" href="dashboard.css" />
  </head>
  <body>
    <div class="sidebar">
      <img src="LOGO.png" alt="Logo" class="Logo" width="100" height="50" />
      <h2>USER DASHBOARD</h2>
      <ul>
        <li><a href="budget.php">💰 Budget Tracking</a></li>
        <li><a href="goaltracker.html">🎯 Goal Tracking</a></li>
        <li><a href="inv.html">📈 Investment</a></li>
        <li><a href="user_details.php">📊 Profile</a></li>
        <a href="logout.php" class="logout-btn">Logout</a>
      </ul>
    </div>

    <div class="main-content">
      <div class="section" id="budget-tracking">
        <h3>Budget Tracking</h3>
        <p>Monitor and manage your expenses and savings.</p>
        <button class="view-btn" onclick="navigate('budget.php')">
          View
        </button>
      </div>

      <div class="section" id="goal-tracking">
        <h3>Goal Tracking</h3>
        <p>Set and track your financial and personal goals.</p>
        <button class="view-btn" onclick="navigate('goaltracker.html')">
          View
        </button>
      </div>

      <div class="section" id="investment">
        <h3>Investment</h3>
        <p>Manage and analyze your investments effectively.</p>
        <button class="view-btn" onclick="navigate('inv.html')">
          View
        </button>
      </div>
      <div class="section" id="results">
        <h3>Results</h3>
        <p>View financial performance and insights.</p>
        <button class="view-btn" onclick="navigate('resultpage.html')">
          View
        </button>
      </div>
    </div>
    <script src="dashboard.js"></script>
  </body>
</html>

