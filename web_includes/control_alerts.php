<?php
// control_expiration_alerts.php
// Make sure $conn is your active MySQLi connection
$conn=new mysqli("localhost","root","","car_rental_management_system");
$today = date("Y-m-d");

// Get count of cars expiring in next 5 days
$countQuery = $conn->query("
    SELECT COUNT(*) AS expiring_count_control
    FROM cars
    WHERE control_expiry_date >= '$today'
      AND control_expiry_date <= DATE_ADD('$today', INTERVAL 14 DAY)
");
$countRow = $countQuery->fetch_assoc();
$badgeCountforControl = $countRow['expiring_count_control'];

// Get first 3 cars for dropdown
$carsQuery = $conn->query("
    SELECT *
    FROM cars
    WHERE control_expiry_date >= '$today'
      AND control_expiry_date <= DATE_ADD('$today', INTERVAL 14 DAY)
    ORDER BY control_expiry_date ASC
");
$control_expiration_cars = [];
while ($row = $carsQuery->fetch_assoc()) {
    $control_expiration_cars[] = $row;
}
