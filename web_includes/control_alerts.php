<?php
// control_expiration_alerts.php
// Make sure $conn is your active MySQLi connection
$conn = new mysqli("localhost", "root", "", "car_rental_management_system");
$today = date("Y-m-d");

// Get count of cars that either:
// 1. Are expiring in next 30 days, OR
// 2. Have already expired (past due)
$countQuery = $conn->query("
    SELECT COUNT(*) AS expiring_count_control
    FROM cars
    WHERE control_expiry_date <= DATE_ADD('$today', INTERVAL 30 DAY)
");
$countRow = $countQuery->fetch_assoc();
$badgeCountforControl = $countRow['expiring_count_control'];

// Get cars for dropdown - both upcoming and overdue
$carsQuery = $conn->query("
    SELECT *,
           CASE 
               WHEN control_expiry_date < '$today' THEN 'EXPIRED'
               ELSE 'EXPIRING SOON'
           END AS status
           FROM cars
           WHERE control_expiry_date <= DATE_ADD('$today', INTERVAL 30 DAY)
           ORDER BY 
           CASE WHEN control_expiry_date < '$today' THEN 0 ELSE 1 END,
         control_expiry_date ASC
");
$control_expiration_cars = [];
while ($row = $carsQuery->fetch_assoc()) {
    $control_expiration_cars[] = $row;
}
?>