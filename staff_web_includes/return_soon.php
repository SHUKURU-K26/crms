<?php
// Returning Soon Cars alerts.php
// Make sure $conn is your active MySQLi connection
$conn=new mysqli("localhost","root","","car_rental_management_system");
$today = date("Y-m-d");

// Get count of cars returning  in next 2 days
$countQuery = $conn->query("
    SELECT COUNT(*) AS returning_soon_cars
    FROM rentals
    WHERE return_date >= '$today'
      AND return_date <= DATE_ADD('$today', INTERVAL 2 DAY)
");
$countRow = $countQuery->fetch_assoc();
$badgeCount = $countRow['returning_soon_cars'];

// Get  all  cars for dropdown
$carsQuery = $conn->query("
    SELECT * FROM rentals WHERE return_date >= '$today'AND return_date <= DATE_ADD('$today', INTERVAL 2 DAY)
    ORDER BY return_date ASC
");
$cars = [];
while ($row = $carsQuery->fetch_assoc()) {
    $cars[] = $row;
}
