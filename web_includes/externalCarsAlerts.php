<?php
// externalCarsAlerts.php
// Make sure $conn is your active MySQLi connection
$conn = new mysqli("localhost", "root", "", "car_rental_management_system");
$today = date("Y-m-d");

// Get count of external cars that either:
// 1. Are expiring in next 2 days, OR
// 2. Have already passed their expected return date (overdue)
$countQuery = $conn->query("
    SELECT COUNT(*) AS expiring_count
    FROM external_cars
    WHERE expected_return_date <= DATE_ADD('$today', INTERVAL 2 DAY)
      AND lifecycle_status = 'active'
");
$countRow = $countQuery->fetch_assoc();
$badgeCount = $countRow['expiring_count'];

// Fetch External cars for dropdown - both upcoming and overdue
$carsQuery = $conn->query("
    SELECT *,
           CASE 
            WHEN expected_return_date < '$today' THEN 'OVERDUE'
            ELSE 'DUE SOON'
           END AS status
            FROM external_cars
            WHERE expected_return_date <= DATE_ADD('$today', INTERVAL 2 DAY)
            AND lifecycle_status = 'active'
            ORDER BY 
        CASE WHEN expected_return_date < '$today' THEN 0 ELSE 1 END,
        expected_return_date ASC
");

$cars = [];
while ($row = $carsQuery->fetch_assoc()) {
    $cars[] = $row;
}
?>