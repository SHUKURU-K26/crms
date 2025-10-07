<?php
// insurance_alerts.php
// Make sure $conn is your active MySQLi connection
$conn=new mysqli("localhost","root","","car_rental_management_system");
$today = date("Y-m-d");

// Get count of cars expiring in next 5 days
$countQuery = $conn->query("
    SELECT COUNT(*) AS expiring_count
    FROM cars
    WHERE insurance_expiry_date >= '$today'
      AND insurance_expiry_date <= DATE_ADD('$today', INTERVAL 14 DAY)
");
$countRow = $countQuery->fetch_assoc();
$badgeCount = $countRow['expiring_count'];

// Get first 3 cars for dropdown
$carsQuery = $conn->query("
    SELECT * FROM cars WHERE insurance_expiry_date >= '$today'AND insurance_expiry_date <= DATE_ADD('$today', INTERVAL 14 DAY)
    ORDER BY insurance_expiry_date ASC
");
$cars = [];
while ($row = $carsQuery->fetch_assoc()) {
    $cars[] = $row;
}
