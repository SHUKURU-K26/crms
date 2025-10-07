<?php
// externalCarsAlerts.php
// Make sure $conn is your active MySQLi connection
$conn=new mysqli("localhost","root","","car_rental_management_system");
$today = date("Y-m-d");

// Get count of cars expiring in next 5 days
$countQuery = $conn->query("
    SELECT COUNT(*) AS expiring_count
    FROM external_cars
    WHERE date_brought >= '$today'
    AND expected_return_date <= DATE_ADD('$today', INTERVAL 2 DAY) AND lifecycle_status='active'
");
$countRow = $countQuery->fetch_assoc();
$badgeCount = $countRow['expiring_count'];

// Fetch External cars for dropdown
$carsQuery = $conn->query("
    SELECT * FROM external_cars WHERE date_brought >= '$today' AND expected_return_date <= DATE_ADD('$today', INTERVAL 2 DAY) AND lifecycle_status='active'
    ORDER BY expected_return_date ASC
");

$cars = [];
while ($row = $carsQuery->fetch_assoc()) {
    $cars[] = $row;
}
