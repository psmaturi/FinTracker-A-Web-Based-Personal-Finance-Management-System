<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}
include 'db_connect.php';

$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$totalBudget = $conn->query("SELECT SUM(total_budget) AS total FROM budget")->fetch_assoc()['total'] ?? 0;
$totalGoals = $conn->query("SELECT COUNT(*) AS total FROM goals")->fetch_assoc()['total'];
$totalInvestments = $conn->query("SELECT SUM(amount) AS total FROM investments")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | FinTracker</title>
    <link rel="stylesheet" href="admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.1/css/boxicons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", sans-serif;
            background-color: #ecf0f3;
            color: #333;
            height: 100vh;
            transition: background-color 0.3s, color 0.3s;
        }

        .sidebar {
            width: 260px;
            background-color: #2c3e50;
            color: white;
            height: 100vh;
            padding: 20px;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
            box-shadow: 4px 4px 8px rgba(255, 255, 255, 0.1);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .nav-links {
            list-style: none;
            padding: 0;
        }

        .nav-links li {
            padding: 15px;
            margin-bottom: 5px;
            cursor: pointer;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .nav-links li:hover {
            transform: scale(1.05);
            box-shadow: 2px 2px 8px rgba(255, 255, 255, 0.2);
        }

        .nav-links li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            display: block;
        }

        .nav-links li.active {
            background-color: #2c3e50;
            border-radius: 8px;
        }

        .main-content {
            margin-left: 260px;
            padding: 40px;
            background-color: #1f2f33;
            flex-grow: 1;
            opacity: 0.95;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .dashboard-title {
            font-size: 28px;
            font-weight: bold;
            color: rgb(27, 55, 111);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-profile img {
            height: 40px;
            width: 40px;
            border-radius: 50%;
        }

        .stats-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .stat-card {
            background: white;
            color: #2c3e50;
            padding: 20px;
            border-radius: 12px;
            flex: 1;
            min-width: 220px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        }

        .stat-icon i {
            font-size: 32px;
        }

        .charts-container {
    display: flex;
    gap: 20px;
    margin-top: 40px;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
}

.chart-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    flex: 1;
    min-width: 250px;
    max-width: 350px; /* reduced from 500px */
    height: 280px;     /* optional fixed height */
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}


        .chart-header h3 {
            margin-bottom: 15px;
            color: #2c3e50;
            font-size: 18px;
            text-align: center;
        }

        canvas {
            max-width: 100%;
            height: auto !important;
        }

        .toggle-btn {
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: #fff;
            transition: opacity 0.2s ease;
        }

        .toggle-btn:hover {
            opacity: 0.7;
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
            <li class="active"><a href="admin_dashboard.php"><i class="bx bx-grid-alt"></i><span>Dashboard</span></a></li>
            <li><a href="admin_users.php"><i class="bx bx-user"></i><span>Users</span></a></li>
            <li><a href="admin_budgets.php"><i class="bx bx-wallet"></i><span>Budgets</span></a></li>
            <li><a href="admin_goals.php"><i class="bx bx-target-lock"></i><span>Goals</span></a></li>
            <li><a href="admin_investments.php"><i class="bx bx-line-chart"></i><span>Investments</span></a></li>
            <li><a href="admin_security.php"><i class="bx bx-shield-quarter"></i><span>Security</span></a></li>
            <li><a href="logout1.php"><i class="bx bx-log-out"></i><span>Logout</span></a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-nav">
            <div class="dashboard-title">Admin Dashboard</div>
            <div class="user-profile">
                <img src="https://i.pravatar.cc/100?img=12" alt="Admin">
                <span><?php echo $_SESSION['admin_username']; ?></span>
                <div class="theme-toggle">
                    <button id="modeToggle" class="toggle-btn" title="Toggle Light/Dark Mode">🌙</button>
                </div>
            </div>
        </div>

        <div class="stats-container">
            <div class="stat-card"><div class="stat-info"><h3><?= $totalUsers ?></h3><p>Users</p></div><div class="stat-icon"><i class="bx bx-user"></i></div></div>
            <div class="stat-card"><div class="stat-info"><h3>$<?= number_format($totalBudget) ?></h3><p>Total Budget</p></div><div class="stat-icon"><i class="bx bx-wallet"></i></div></div>
            <div class="stat-card"><div class="stat-info"><h3><?= $totalGoals ?></h3><p>Goals Tracked</p></div><div class="stat-icon"><i class="bx bx-target-lock"></i></div></div>
            <div class="stat-card"><div class="stat-info"><h3>$<?= number_format($totalInvestments) ?></h3><p>Investments</p></div><div class="stat-icon"><i class="bx bx-line-chart"></i></div></div>
        </div>

        <div class="charts-container">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Monthly Budget Overview</h3>
                </div>
                <canvas id="budgetChart"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Expense Categories</h3>
                </div>
                <canvas id="expenseChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    const toggleBtn = document.getElementById('modeToggle');
    const savedMode = localStorage.getItem('mode') || 'light';
    document.body.classList.add(savedMode + '-mode');
    toggleBtn.innerText = savedMode === 'dark' ? '☀️' : '🌙';

    toggleBtn.addEventListener('click', () => {
        const isDark = document.body.classList.contains('dark-mode');
        document.body.classList.toggle('dark-mode');
        document.body.classList.toggle('light-mode');
        const newMode = isDark ? 'light' : 'dark';
        localStorage.setItem('mode', newMode);
        toggleBtn.innerText = isDark ? '🌙' : '☀️';
    });

    let budgetChartInstance = null;

    function loadBudgetChart() {
        fetch('get_budget_data.php')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('budgetChart').getContext('2d');
                if (budgetChartInstance) {
                    budgetChartInstance.data.labels = data.labels;
                    budgetChartInstance.data.datasets[0].data = data.totals;
                    budgetChartInstance.update();
                } else {
                    budgetChartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Total Budget',
                                data: data.totals,
                                backgroundColor: 'rgba(78, 115, 223, 0.2)',
                                borderColor: 'rgba(78, 115, 223, 1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: {
                            plugins: {
                                legend: {
                                    labels: {
                                        color: "#2c3e50"
                                    }
                                }
                            },
                            scales: {
                                x: { ticks: { color: "#2c3e50" }},
                                y: { ticks: { color: "#2c3e50" }}
                            }
                        }
                    });
                }
            });
    }

    function loadExpenseChart() {
        fetch('get_expense_data.php')
            .then(response => response.json())
            .then(data => {
                const categories = data.map(item => item.category);
                const amounts = data.map(item => item.total);

                const ctx = document.getElementById('expenseChart').getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: categories,
                        datasets: [{
                            data: amounts,
                            backgroundColor: [
                                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e',
                                '#e74a3b', '#858796', '#5a5c69'
                            ]
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                labels: {
                                    color: "#2c3e50"
                                }
                            }
                        }
                    }
                });
            });
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadBudgetChart();
        loadExpenseChart();
    });
</script>
</body>
</html>
