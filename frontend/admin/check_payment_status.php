<?php
session_start();
include "../../web_db/connection.php";

// Enable error logging for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response
ini_set('log_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_name = mysqli_real_escape_string($conn, $_POST['car_name']);
    $plate_number = mysqli_real_escape_string($conn, $_POST['plate_number']);
    $id_number = mysqli_real_escape_string($conn, $_POST['id_number']);
    $payment_type = strtolower(trim($_POST['payment_type'])); // 🔥 Force lowercase
    
    // 🔥 Log for debugging (check your PHP error log)
    error_log("Payment Check - Car: $car_name, Plate: $plate_number, ID: $id_number, Type: $payment_type");
    
    // Check if payment exists and is fully paid (case-insensitive status check)
    $stmt = $conn->prepare("SELECT status, balance FROM payments 
                           WHERE car_payed_for = ? 
                           AND plate = ? 
                           AND payer_national_id = ? 
                           AND LOWER(payment_type) = ? 
                           AND (LOWER(status) = 'full paid' OR balance = 0)");
    
    if (!$stmt) {
        error_log("SQL Error: " . $conn->error);
        echo json_encode([
            'is_fully_paid' => false,
            'error' => 'Database error'
        ]);
        exit;
    }
    
    $stmt->bind_param("ssss", $car_name, $plate_number, $id_number, $payment_type);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $is_fully_paid = ($result->num_rows > 0);
    
    // 🔥 Debug output
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        error_log("Found payment - Status: {$row['status']}, Balance: {$row['balance']}");
    } else {
        error_log("No payment record found for: $car_name ($payment_type)");
    }
    
    $response = [
        'is_fully_paid' => $is_fully_paid,
        'debug' => [
            'car_name' => $car_name,
            'plate' => $plate_number,
            'payment_type' => $payment_type,
            'records_found' => $result->num_rows
        ]
    ];
    
    echo json_encode($response);
    $stmt->close();
} else {
    echo json_encode([
        'is_fully_paid' => false,
        'error' => 'Invalid request'
    ]);
}
?>