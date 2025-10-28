<?php
// insurance_alerts.php
// Make sure $conn is your active MySQLi connection
$conn = new mysqli("localhost", "root", "", "car_rental_management_system");
$today = date("Y-m-d");

// Get count of cars that either:
// 1. Are expiring in next 30 days, OR
// 2. Have already expired (past due)
$countQuery = $conn->query("
    SELECT COUNT(*) AS expiring_count
    FROM cars
    WHERE insurance_expiry_date <= DATE_ADD('$today', INTERVAL 30 DAY)
");
$countRow = $countQuery->fetch_assoc();
$badgeCount = $countRow['expiring_count'];

// Get cars for dropdown - both upcoming and overdue
$carsQuery = $conn->query("
    SELECT *,
           CASE 
            WHEN insurance_expiry_date < '$today' THEN 'EXPIRED'
            ELSE 'EXPIRING SOON'
            END AS status
    FROM cars
    WHERE insurance_expiry_date <= DATE_ADD('$today', INTERVAL 30 DAY)
    ORDER BY 
        CASE WHEN insurance_expiry_date < '$today' THEN 0 ELSE 1 END,
        insurance_expiry_date ASC
");
$cars = [];
while ($row = $carsQuery->fetch_assoc()) {
    $cars[] = $row;
}

?>