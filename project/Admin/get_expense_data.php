<?php
include 'db_connect.php';
session_start();

$userId = $_SESSION['admin_id'] ?? 1; // or however you identify admin session

// Grouping expense data by title instead of category
$query = "SELECT title, SUM(amount) as total FROM expenses GROUP BY title";
$result = $conn->query($query);

$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'category' => $row['title'], // using 'title' as the chart label
            'total' => $row['total']
        ];
    }
}

echo json_encode($data);
?>
