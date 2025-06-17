<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch budget details
$query = "SELECT total_budget, total_expense, budget_left FROM budget WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$total_budget = $row['total_budget'] ?? 0;
$total_expense = $row['total_expense'] ?? 0;
$budget_left = $row['budget_left'] ?? 0;

// Fetch expenses
$expenseQuery = "SELECT id, title, amount FROM expenses WHERE user_id = ?";
$expenseStmt = $conn->prepare($expenseQuery);
$expenseStmt->bind_param("i", $user_id);
$expenseStmt->execute();
$expenseResult = $expenseStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Budget Tracker</title>
    <link rel="stylesheet" href="budget.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<!-- Sidebar Start -->
<div class="sidebar">
    <img src="LOGO.png" alt="Logo" class="Logo" width="100" height="50" />
    <h2>USER DASHBOARD</h2>
    <ul>
        <li><a href="budget.php">💰 Budget Tracking</a></li>
        <li><a href="goaltracker.html">🎯 Goal Tracking</a></li>
        <li><a href="inv.html">📈 Investment</a></li>
        <li><a href="user_details.php">📊 Profile</a></li>
    </ul>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>
<!-- Sidebar End -->

<!-- Main Content Start -->
<div class="main-content">
    <div class="container">
        <div class="header">
            <div class="budget-section">
                <h2>Budget Tracker</h2>
                <form action="add_budget.php" method="POST" class="input-group">
                    <input type="number" name="budgetAmount" placeholder="Enter budget amount" required />
                    <button type="submit">Add Budget</button>
                </form>
            </div>

            <div class="summary-cards">
                <div class="card">
                    <h3>Total Budget</h3>
                    <p id="totalBudget">$<?php echo number_format($total_budget, 2); ?></p>
                </div>
                <div class="card">
                    <h3>Total Expense</h3>
                    <p id="totalExpense">$<?php echo number_format($total_expense, 2); ?></p>
                </div>
                <div class="card">
                    <h3>Budget Left</h3>
                    <p id="budgetLeft">$<?php echo number_format($budget_left, 2); ?></p>
                </div>
            </div>
        </div>

        <div class="expense-section">
            <div class="add-expense">
                <div class="budget-section">
                    <h2>Add Expense</h2>
                    <form action="add_expense.php" method="POST" class="input-group-column">
                        <input type="text" name="expenseTitle" placeholder="Expense Title" required />
                        <input type="number" name="expenseAmount" placeholder="Amount" required />
                        <button type="submit">Add Expense</button>
                    </form>
                </div>
            </div>

            <div class="expense-history">
                <h2>Expense History</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Expense Name</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($expense = $expenseResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($expense['title']); ?></td>
                                <td>$<?php echo number_format($expense['amount'], 2); ?></td>
                                <td>
                                    <form action="delete_expense.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                        <input type="hidden" name="expense_id" value="<?php echo $expense['id']; ?>">
                                        <button type="submit" class="delete-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Main Content End -->

</body>
</html>