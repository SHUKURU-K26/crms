<?php
// registration_codes.php
// Make sure $conn is your active MySQLi connection
$conn=new mysqli("localhost","root","","car_rental_management_system");
$countQuery = $conn->query("SELECT COUNT(*) AS All_codes FROM registration_codes WHERE status= 'unused'");
$countRow = $countQuery->fetch_assoc();
$badgeCountforcodes = $countRow['All_codes'];

$codeQuery = $conn->query("SELECT * FROM registration_codes WHERE status='unused'");
$allCodes = [];
while ($row = $codeQuery->fetch_assoc()) {
    $allCodes[] = $row;
}
