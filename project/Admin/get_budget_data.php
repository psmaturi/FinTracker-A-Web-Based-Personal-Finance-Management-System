<?php
include 'db_connect.php';
header('Content-Type: application/json');

$data = ["labels" => [], "totals" => []];

$result = $conn->query("SELECT DATE_FORMAT(created_at, '%b') AS month, SUM(total_budget) AS total 
                        FROM budget GROUP BY month ORDER BY MONTH(STR_TO_DATE(month, '%b'))");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data["labels"][] = $row["month"];
        $data["totals"][] = floatval($row["total"]);
    }
} else {
    $data["error"] = "Query failed";
}

echo json_encode($data);
?>
