<?php
$conn = new mysqli("localhost", "root", "", "car_rental_management_system");
$today = date("Y-m-d");

// Get count of cars expiring in next 2 days
$countQuery = $conn->query("
    SELECT COUNT(*) AS expiring_count
    FROM rentals
    WHERE rent_date >= '$today'
      AND return_date <= DATE_ADD('$today', INTERVAL 2 DAY)
");
$countRow = $countQuery->fetch_assoc();
$badgeCountForInternalCars = $countRow['expiring_count'];

// Fetch car details JOINED with rentals that are expiring soon
$carsQuery = $conn->query("
    SELECT rentals.*, cars.car_name, cars.plate_number, cars.fuel_type, cars.type, cars.status
    FROM rentals
    JOIN cars ON rentals.car_id = cars.car_id
    WHERE rentals.rent_date >= '$today'
      AND rentals.return_date <= DATE_ADD('$today', INTERVAL 2 DAY)
    ORDER BY rentals.return_date ASC
");

$Internalcars = [];
while ($row = $carsQuery->fetch_assoc()) {
    $Internalcars[] = $row;
}
?>
