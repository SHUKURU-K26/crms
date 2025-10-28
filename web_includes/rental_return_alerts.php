<?php
// rental_return_alerts.php
// Make sure $conn is your active MySQLi connection
$conn = new mysqli("localhost", "root", "", "car_rental_management_system");
$today = date("Y-m-d");

// Get combined count from both rental tables
$countQuery = $conn->query("
    SELECT 
        (SELECT COUNT(*) 
         FROM rentals r
         INNER JOIN cars c ON r.car_id = c.car_id
         WHERE r.return_date <= DATE_ADD('$today', INTERVAL 2 DAY)
           AND r.return_date >= '$today'
        ) +
        (SELECT COUNT(*) 
         FROM external_rentals er
         INNER JOIN external_cars ec ON er.car_id = ec.car_id
         WHERE er.return_date <= DATE_ADD('$today', INTERVAL 2 DAY)
           AND er.return_date >= '$today'
        ) AS total_returning_count
");
$countRow = $countQuery->fetch_assoc();
$badgeCountForRentals = $countRow['total_returning_count'];

// Fetch internal rentals
$rentalsQuery = $conn->query("
    SELECT r.*,
           c.car_name,
           c.plate_number,
           'Internal' AS rental_type,
           CASE 
            WHEN r.return_date < '$today' THEN 'OVERDUE'
            ELSE 'DUE SOON'
           END AS status
    FROM rentals r
    INNER JOIN cars c ON r.car_id = c.car_id
    WHERE r.return_date <= DATE_ADD('$today', INTERVAL 2 DAY)
");

// Fetch external rentals
$externalRentalsQuery = $conn->query("
    SELECT er.*,
           ec.car_name,
           ec.plate_number,
           'External' AS rental_type,
           CASE 
            WHEN er.return_date < '$today' THEN 'OVERDUE'
            ELSE 'DUE SOON'
           END AS status
    FROM external_rentals er
    INNER JOIN external_cars ec ON er.car_id = ec.car_id
    WHERE er.return_date <= DATE_ADD('$today', INTERVAL 2 DAY)
");

// Combine both results into a single array
$rentals = [];
while ($row = $rentalsQuery->fetch_assoc()) {
    $rentals[] = $row;
}
while ($row = $externalRentalsQuery->fetch_assoc()) {
    $rentals[] = $row;
}

// Sort the combined array by status (OVERDUE first) then by return_date
usort($rentals, function($a, $b) {
    // First sort by status (OVERDUE = 0, DUE SOON = 1)
    $statusA = ($a['status'] == 'OVERDUE') ? 0 : 1;
    $statusB = ($b['status'] == 'OVERDUE') ? 0 : 1;
    
    if ($statusA != $statusB) {
        return $statusA - $statusB;
    }
    
    // Then sort by return_date ascending
    return strcmp($a['return_date'], $b['return_date']);
});
?>