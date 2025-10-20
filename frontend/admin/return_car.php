<?php
session_start();
include "../../web_includes/auth.php";
include "../../web_db/connection.php";

if (isset($_POST['return_btn'])) {
    $view_type = $_POST['view_type'] ?? 'internal';
    $was_fully_paid_during_rental = isset($_POST['was_fully_paid_during_rental']) && $_POST['was_fully_paid_during_rental'] === 'yes';
    
    mysqli_begin_transaction($conn);
    
    try {
        $rental_id = intval($_POST['rental_id']);
        $car_name = htmlspecialchars($_POST['car_name']);
        $plate_number = htmlspecialchars($_POST['plate_number']);

        if ($was_fully_paid_during_rental) {
            processAlreadyPaidReturn($conn, [
                'rental_id' => $rental_id,
                'car_name' => $car_name,
                'plate_number' => $plate_number,
                'view_type' => $view_type
            ]);            
            
            mysqli_commit($conn);
            showSuccessMessage($car_name, $plate_number, 'already_paid');
            
        } else {
            $renter_name = htmlspecialchars($_POST['renter_name']);
            $id_number = htmlspecialchars($_POST['id_number']);
            $telephone = htmlspecialchars($_POST['telephone']);
            $rent_date = $_POST['rent_date'];
            $expected_return_date = $_POST['expected_return_date'];
            $date_returned_on = $_POST['date_returned_on'];
            $days_in_rent = intval($_POST['days_in_rent']);
            $price_per_day = floatval($_POST['price']);
            $expected_revenue = floatval($_POST['expected_revenue']);
            $lifecycle_status = $_POST["lifecycle_status"];
            $adjusted_revenue = floatval($_POST['adjusted_revenue']);
            $refund_due = floatval($_POST['refund_due']);
            $return_option = $_POST['return_option'];
            $partial_amount = floatval($_POST['partial_amount'] ?? 0);
            $remaining_balance = floatval($_POST['remaining_balance'] ?? 0);
            $provider_name = htmlspecialchars($_POST['provider_name']);

            $final_revenue = ($date_returned_on !== $expected_return_date) ? $adjusted_revenue : $expected_revenue;
            $revenue_status = ($date_returned_on !== $expected_return_date) ? "Adjusted" : "Expected";
            
            switch ($return_option) {
                case "fully paid":
                    processFullyPaidReturn($conn, [
                        'rental_id' => $rental_id,
                        'renter_name' => $renter_name,
                        'telephone' => $telephone,
                        'id_number' => $id_number,
                        'car_name' => $car_name,
                        'plate_number' => $plate_number,
                        'rent_date' => $rent_date,
                        'expected_return_date' => $expected_return_date,
                        'date_returned_on' => $date_returned_on,
                        'days_in_rent' => $days_in_rent,
                        'price_per_day' => $price_per_day,
                        'final_revenue' => $final_revenue,
                        'revenue_status' => $revenue_status,
                        'expected_revenue' => $expected_revenue,
                        'lifecycle_status' => $lifecycle_status,
                        'refund_due' => $refund_due,
                        'provider_name' => $provider_name,
                        'view_type' => $view_type
                    ]);
                    break;
                
                case "partial paid":
                    processPartialPaidReturn($conn, [
                        'rental_id' => $rental_id,
                        'renter_name' => $renter_name,
                        'telephone' => $telephone,
                        'id_number' => $id_number,
                        'car_name' => $car_name,
                        'plate_number' => $plate_number,
                        'rent_date' => $rent_date,
                        'expected_return_date' => $expected_return_date,
                        'date_returned_on' => $date_returned_on,
                        'days_in_rent' => $days_in_rent,
                        'price_per_day' => $price_per_day,
                        'final_revenue' => $final_revenue,
                        'revenue_status' => $revenue_status,
                        'expected_revenue' => $expected_revenue,
                        'lifecycle_status' => $lifecycle_status,                    
                        'refund_due' => $refund_due,
                        'provider_name' => $provider_name,
                        'partial_amount' => $partial_amount,
                        'remaining_balance' => $remaining_balance,
                        'view_type' => $view_type
                    ]);
                    break;
                    
                case "fully Unpaid":
                    processFullyUnpaidReturn($conn, [
                        'rental_id' => $rental_id,
                        'renter_name' => $renter_name,
                        'telephone' => $telephone,
                        'id_number' => $id_number,
                        'car_name' => $car_name,
                        'plate_number' => $plate_number,
                        'rent_date' => $rent_date,
                        'expected_return_date' => $expected_return_date,
                        'date_returned_on' => $date_returned_on,
                        'days_in_rent' => $days_in_rent,
                        'price_per_day' => $price_per_day,
                        'final_revenue' => $final_revenue,
                        'revenue_status' => $revenue_status,
                        'expected_revenue' => $expected_revenue,
                        'lifecycle_status' => $lifecycle_status,
                        'refund_due' => $refund_due,
                        'provider_name' => $provider_name,
                        'debt_amount' => $final_revenue,
                        'view_type' => $view_type
                    ]);
                    break;
                    
                default:
                    throw new Exception("Invalid return option selected.");
            }
        }
        
        mysqli_commit($conn);
        
        if ($was_fully_paid_during_rental) {
            showSuccessMessage($car_name, $plate_number, 'already_paid');
        } else {
            showSuccessMessage($car_name, $plate_number, $return_option);
        }
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        showErrorMessage("Error processing return: " . $e->getMessage());
    }
}

function processAlreadyPaidReturn($conn, $data) {
    if ($data['view_type'] === 'external') {
        $stmt = $conn->prepare("UPDATE external_cars SET status = 'available' WHERE car_id = ?");
    } else {
        $stmt = $conn->prepare("UPDATE cars SET status = 'available' WHERE car_id = ?");
    }
    $stmt->bind_param("i", $data['rental_id']);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to update car status: " . $stmt->error);
    }
    $stmt->close();
    
    if ($data['view_type'] === 'external') {
        $stmt = $conn->prepare("DELETE FROM external_rentals WHERE car_id = ?");
    } else {
        $stmt = $conn->prepare("DELETE FROM rentals WHERE car_id = ?");
    }
    $stmt->bind_param("i", $data['rental_id']);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to delete rental record: " . $stmt->error);
    }
    $stmt->close();
}

function processFullyPaidReturn($conn, $data) {
    // 1. Insert into rental history
    if ($data['view_type'] === 'external') {
        $stmt = $conn->prepare("INSERT INTO external_rental_history 
            (renter_names, renter_phone, renter_national_id, car_name, car_plate, 
             date_rented_on, expected_return_date, date_returned_on, days_in_rent, 
             rental_fee, revenue_received, revenue_status, expected_revenue, lifecycle_status, refund_due, provider_names)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssssssidisdsds", 
            $data['renter_name'], $data['telephone'], $data['id_number'],
            $data['car_name'], $data['plate_number'], $data['rent_date'],
            $data['expected_return_date'], $data['date_returned_on'], $data['days_in_rent'],
            $data['price_per_day'], $data['final_revenue'], $data['revenue_status'],
            $data['expected_revenue'], $data['lifecycle_status'],
            $data['refund_due'], $data['provider_name']
        );
        
        // For external: Delete any existing debt records
        $deleteDebtStmt = $conn->prepare("DELETE FROM debts WHERE car_name=? AND car_plate=? AND renter_names=? AND national_id=? AND phone_number=? AND debt_type='external'");
        $deleteDebtStmt->bind_param("sssss", $data['car_name'], $data['plate_number'], $data['renter_name'], $data['id_number'], $data['telephone']);
        if (!$deleteDebtStmt->execute()) {
            throw new Exception("Failed to delete existing external debt record: " . $deleteDebtStmt->error);
        }
        $deleteDebtStmt->close();

    } else {
        $stmt = $conn->prepare("INSERT INTO rental_history 
            (renter_names, renter_phone, renter_national_id, car_name, car_plate, 
             date_rented_on, expected_return_date, date_returned_on, days_in_rent, 
             rental_fee, revenue_received, revenue_status, expected_revenue, refund_due, provider_names)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssssssidisdds", 
            $data['renter_name'], $data['telephone'], $data['id_number'],
            $data['car_name'], $data['plate_number'], $data['rent_date'],
            $data['expected_return_date'], $data['date_returned_on'], $data['days_in_rent'],
            $data['price_per_day'], $data['final_revenue'], $data['revenue_status'],
            $data['expected_revenue'], $data['refund_due'], $data['provider_name']
        );

        // For internal: Delete any existing debt records
        $deleteDebtStmt = $conn->prepare("DELETE FROM debts WHERE car_name=? AND car_plate=? AND renter_names=? AND national_id=? AND phone_number=? AND debt_type='internal'");
        $deleteDebtStmt->bind_param("sssss", $data['car_name'], $data['plate_number'], $data['renter_name'], $data['id_number'], $data['telephone']);
        if (!$deleteDebtStmt->execute()) {
            throw new Exception("Failed to delete existing internal debt record: " . $deleteDebtStmt->error);
        }
        $deleteDebtStmt->close();
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert rental history: " . $stmt->error);
    }
    $stmt->close();
    
    // 2. Handle payments table
    $payment_type = ($data['view_type'] === 'external') ? 'external' : 'internal';
    
    $checkStmt = $conn->prepare("SELECT * FROM payments WHERE car_payed_for=? AND plate=? AND payer_national_id=? AND status='Half paid' AND payment_type=?");
    $checkStmt->bind_param("ssss", $data['car_name'], $data['plate_number'], $data['id_number'], $payment_type);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        // Update existing payment
        $updateStmt = $conn->prepare("UPDATE payments SET amount_paid=amount_paid+?, status='Full paid', balance=0 WHERE car_payed_for=? AND plate=? AND payer_national_id=? AND status='Half paid' AND payment_type=?");
        $updateStmt->bind_param("dssss", $data['final_revenue'], $data['car_name'], $data['plate_number'], $data['id_number'], $payment_type);
        if (!$updateStmt->execute()) {
            throw new Exception("Failed to update existing payment record: " . $updateStmt->error);
        }
        $updateStmt->close();
    } else {
        // Insert new payment
        $stmt = $conn->prepare("INSERT INTO payments
            (payment_type, amount_paid, paid_by, payer_phone, payer_national_id, car_payed_for, plate, status, balance)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Full paid', 0)");
        
        $stmt->bind_param("sdsssss", 
            $payment_type, $data['final_revenue'], $data['renter_name'], $data['telephone'], 
            $data['id_number'], $data['car_name'], $data['plate_number']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert payment record: " . $stmt->error);
        }
        $stmt->close();
    }
    $checkStmt->close();
    
    // 3. Update car status
    if ($data['view_type'] === 'external') {
        $stmt = $conn->prepare("UPDATE external_cars SET status = 'available' WHERE car_id = ?");
    } else {
        $stmt = $conn->prepare("UPDATE cars SET status = 'available' WHERE car_id = ?");
    }
    $stmt->bind_param("i", $data['rental_id']);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to update car status: " . $stmt->error);
    }
    $stmt->close();
    
    // 4. Delete rental record
    if ($data['view_type'] === 'external') {
        $stmt = $conn->prepare("DELETE FROM external_rentals WHERE car_id = ?");
    } else {
        $stmt = $conn->prepare("DELETE FROM rentals WHERE car_id = ?");
    }
    $stmt->bind_param("i", $data['rental_id']);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to delete rental record: " . $stmt->error);
    }
    $stmt->close();
}

function processPartialPaidReturn($conn, $data) {
    // 1. Insert into rental history
    if ($data['view_type'] === 'external') {
        $stmt = $conn->prepare("INSERT INTO external_rental_history 
            (renter_names, renter_phone, renter_national_id, car_name, car_plate, 
            date_rented_on, expected_return_date, date_returned_on, days_in_rent, 
            rental_fee, revenue_received, revenue_status, expected_revenue, lifecycle_status, refund_due, provider_names)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssssssidisdsds", 
            $data['renter_name'], $data['telephone'], $data['id_number'],
            $data['car_name'], $data['plate_number'], $data['rent_date'],
            $data['expected_return_date'], $data['date_returned_on'], $data['days_in_rent'],
            $data['price_per_day'], $data['partial_amount'], $data['revenue_status'],
            $data['expected_revenue'], $data['lifecycle_status'],
            $data['refund_due'], $data['provider_name']
        );
        
    } else {
        $stmt = $conn->prepare("INSERT INTO rental_history 
            (renter_names, renter_phone, renter_national_id, car_name, car_plate, 
             date_rented_on, expected_return_date, date_returned_on, days_in_rent, 
             rental_fee, revenue_received, revenue_status, expected_revenue, refund_due, provider_names)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssssssidisdds", 
            $data['renter_name'], $data['telephone'], $data['id_number'],
            $data['car_name'], $data['plate_number'], $data['rent_date'],
            $data['expected_return_date'], $data['date_returned_on'], $data['days_in_rent'],
            $data['price_per_day'], $data['partial_amount'], $data['revenue_status'],
            $data['expected_revenue'], $data['refund_due'], $data['provider_name']
        );
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert rental history: " . $stmt->error);
    }
    $stmt->close();

    // 2. Handle payments table
    $payment_type = ($data['view_type'] === 'external') ? 'external' : 'internal';

    $checkStmt = $conn->prepare("SELECT * FROM payments WHERE car_payed_for=? AND plate=? AND payer_national_id=? AND status='Half paid' AND payment_type=?");
    $checkStmt->bind_param("ssss", $data['car_name'], $data['plate_number'], $data['id_number'], $payment_type);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        // Update existing payment
        $updateStmt = $conn->prepare("UPDATE payments SET amount_paid=amount_paid + ?, status='Half Paid', balance=balance - ? WHERE car_payed_for=? AND plate=? AND payer_national_id=? AND status='Half paid' AND payment_type=?");
        $updateStmt->bind_param("ddssss", $data['partial_amount'], $data["partial_amount"], $data['car_name'], $data['plate_number'], $data['id_number'], $payment_type);
        if (!$updateStmt->execute()) {
            throw new Exception("Failed to update existing payment record: " . $updateStmt->error);
        }
        $updateStmt->close();
    } else {
        // Insert new payment
        $stmt = $conn->prepare("INSERT INTO payments(payment_type, amount_paid, paid_by, payer_phone, payer_national_id, car_payed_for, plate, status, balance)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Half paid', ?)");
    
        $stmt->bind_param("sdsssssd", 
            $payment_type, $data['partial_amount'], $data['renter_name'], $data['telephone'], 
            $data['id_number'], $data['car_name'], $data['plate_number'], $data['remaining_balance']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert payment record: " . $stmt->error);
        }
        $stmt->close();
    }
    $checkStmt->close();
    
    // 3. Handle debts table
    $debt_type = ($data['view_type'] === 'external') ? 'external' : 'internal';
    
    $updateDebtStmt = $conn->prepare("SELECT * FROM debts WHERE car_name=? AND car_plate=? AND renter_names=? AND national_id=? AND phone_number=? AND debt_amount>0 AND debt_type=?");
    $updateDebtStmt->bind_param("ssssss", $data['car_name'], $data['plate_number'], $data['renter_name'], $data['id_number'], $data['telephone'], $debt_type);
    $updateDebtStmt->execute();
    $debtResult = $updateDebtStmt->get_result();
    
    if ($debtResult->num_rows > 0) {
        // Update existing debt
        $updateStmt = $conn->prepare("UPDATE debts SET debt_amount=debt_amount-? WHERE car_name=? AND car_plate=? AND renter_names=? AND national_id=? AND phone_number=? AND debt_type=?");
        $updateStmt->bind_param("dssssss", $data['partial_amount'], $data['car_name'], $data['plate_number'], $data['renter_name'], $data['id_number'], $data['telephone'], $debt_type);
        if (!$updateStmt->execute()) {
            throw new Exception("Failed to update existing debt record: " . $updateStmt->error);
        }
        $updateStmt->close();
    } else {
        // Insert new debt
        $stmt = $conn->prepare("INSERT INTO debts (debt_type, debt_amount, car_name, car_plate, renter_names, phone_number, national_id, provider_names)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssssss", 
            $debt_type, $data['remaining_balance'], $data['car_name'], 
            $data['plate_number'], $data['renter_name'], 
            $data['telephone'], $data['id_number'], $data['provider_name']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert debt record: " . $stmt->error);
        }
        $stmt->close();
    }
    $updateDebtStmt->close();
    
    // 4. Update car status
    if ($data['view_type'] === 'external') {
        $stmt = $conn->prepare("UPDATE external_cars SET status = 'available' WHERE car_id = ?");
    } else {
        $stmt = $conn->prepare("UPDATE cars SET status = 'available' WHERE car_id = ?");
    }
    $stmt->bind_param("i", $data['rental_id']);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to update car status: " . $stmt->error);
    }
    $stmt->close();
    
    // 5. Delete rental record
    if ($data['view_type'] === 'external') {
        $stmt = $conn->prepare("DELETE FROM external_rentals WHERE car_id = ?");
    } else {
        $stmt = $conn->prepare("DELETE FROM rentals WHERE car_id = ?");
    }
    $stmt->bind_param("i", $data['rental_id']);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to delete rental record: " . $stmt->error);
    }
    $stmt->close();
}

function processFullyUnpaidReturn($conn, $data) {
    // 1. Insert into rental history with 0 revenue_received
    if ($data['view_type'] === 'external') {
        $stmt = $conn->prepare("INSERT INTO external_rental_history 
            (renter_names, renter_phone, renter_national_id, car_name, car_plate, 
            date_rented_on, expected_return_date, date_returned_on, days_in_rent, 
            rental_fee, revenue_received, revenue_status, expected_revenue, lifecycle_status, refund_due, provider_names)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssssssidisdsds", 
            $data['renter_name'], $data['telephone'], $data['id_number'],
            $data['car_name'], $data['plate_number'], $data['rent_date'],
            $data['expected_return_date'], $data['date_returned_on'], $data['days_in_rent'],
            $data['price_per_day'], $data['revenue_status'],
            $data['expected_revenue'], $data['lifecycle_status'],
            $data['refund_due'], $data['provider_name']
        );
        
    } else {
        $stmt = $conn->prepare("INSERT INTO rental_history 
            (renter_names, renter_phone, renter_national_id, car_name, car_plate, 
             date_rented_on, expected_return_date, date_returned_on, days_in_rent, 
             rental_fee, revenue_received, revenue_status, expected_revenue, refund_due, provider_names)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssssssidisdds", 
            $data['renter_name'], $data['telephone'], $data['id_number'],
            $data['car_name'], $data['plate_number'], $data['rent_date'],
            $data['expected_return_date'], $data['date_returned_on'], $data['days_in_rent'],
            $data['price_per_day'], $data['revenue_status'],
            $data['expected_revenue'], $data['refund_due'], $data['provider_name']
        );
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert rental history: " . $stmt->error);
    }
    $stmt->close();
    
    // 2. Insert/Update debt record
    $debt_type = ($data['view_type'] === 'external') ? 'external' : 'internal';
    
    $checkDebtStmt = $conn->prepare("SELECT * FROM debts WHERE car_name=? AND car_plate=? AND renter_names=? AND national_id=? AND phone_number=? AND debt_type=?");
    $checkDebtStmt->bind_param("ssssss", $data['car_name'], $data['plate_number'], $data['renter_name'], $data['id_number'], $data['telephone'], $debt_type);
    $checkDebtStmt->execute();
    $debtResult = $checkDebtStmt->get_result();
    
    if ($debtResult->num_rows > 0) {
        // Update existing debt
        $updateDebtStmt = $conn->prepare("UPDATE debts SET debt_amount=debt_amount+? WHERE car_name=? AND car_plate=? AND renter_names=? AND national_id=? AND phone_number=? AND debt_type=?");
        $updateDebtStmt->bind_param("dssssss", $data['debt_amount'], $data['car_name'], $data['plate_number'], $data['renter_name'], $data['id_number'], $data['telephone'], $debt_type);
        if (!$updateDebtStmt->execute()) {
            throw new Exception("Failed to update existing debt record: " . $updateDebtStmt->error);
        }
        $updateDebtStmt->close();
    } else {
        // Insert new debt
        $insertDebtStmt = $conn->prepare("INSERT INTO debts (debt_type, debt_amount, car_name, car_plate, renter_names, phone_number, national_id, provider_names)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insertDebtStmt->bind_param("sdssssss", 
            $debt_type, $data['debt_amount'], $data['car_name'], 
            $data['plate_number'], $data['renter_name'], 
            $data['telephone'], $data['id_number'], $data['provider_name']
        );
        
        if (!$insertDebtStmt->execute()) {
            throw new Exception("Failed to insert debt record: " . $insertDebtStmt->error);
        }
        $insertDebtStmt->close();
    }
    $checkDebtStmt->close();
    
    // 3. Update car status to 'available with Debt'
    if ($data['view_type'] === 'external') {
        $stmt = $conn->prepare("UPDATE external_cars SET status = 'available with Debt' WHERE car_id = ?");
    } else {
        $stmt = $conn->prepare("UPDATE cars SET status = 'available with Debt' WHERE car_id = ?");
    }
    $stmt->bind_param("i", $data['rental_id']);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to update car status: " . $stmt->error);
    }
    $stmt->close();    
    
    // 4. Delete from rentals table
    if ($data['view_type'] === 'external') {
        $stmt = $conn->prepare("DELETE FROM external_rentals WHERE car_id = ?");
    } else {
        $stmt = $conn->prepare("DELETE FROM rentals WHERE car_id = ?");
    }
    $stmt->bind_param("i", $data['rental_id']);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to delete rental record: " . $stmt->error);
    }
    $stmt->close();
}

function showSuccessMessage($car_name, $plate_number, $return_option) {
    $message = "";
    switch ($return_option) {
        case "already_paid":
            $message = "✅ $car_name (Plate: $plate_number) returned successfully. Payment was already completed during rental.";
            break;
        case "fully paid":
            $message = "✅ $car_name (Plate: $plate_number) returned with full payment. Car is now available.";
            break;
        case "partial paid":
            $message = "⚠️ $car_name (Plate: $plate_number) returned with partial payment. Car is available but debt recorded.";
            break;
        case "fully Unpaid":
            $message = "🔴 $car_name (Plate: $plate_number) returned unpaid. Debt recorded, car available with debt status.";
            break;
    }
    
    echo "
    <div id='successAlertBox' style='position: fixed; top: 20px; right: 20px; z-index: 9999; 
         background: linear-gradient(135deg, #1cc88a, #13855c); color: white; padding: 20px; 
         border-radius: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.3); 
         animation: slideIn 0.5s ease; max-width: 400px;'>
        $message
    </div>
    <style>
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1;}
        }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const alertBox = document.getElementById('successAlertBox');
        setTimeout(() => {
            alertBox.style.transform = 'translateX(100%)';
            alertBox.style.opacity = '0';
            setTimeout(() => {
                alertBox.remove();
                window.location.href='';
            }, 500);
        }, 4000);
    });
    </script>";
}

function showErrorMessage($message) {
    echo "
    <div id='errorAlertBox' style='position: fixed; top: 20px; right: 20px; z-index: 9999; 
         background: linear-gradient(135deg, #e74a3b, #c0392b); color: white; padding: 20px; 
         border-radius: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.3); 
         animation: slideIn 0.5s ease; max-width: 400px;'>
        ⚠️ $message
    </div>
    <style>
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1;}
        }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const alertBox = document.getElementById('errorAlertBox');
        setTimeout(() => {
            alertBox.style.opacity = '0';
            setTimeout(() => alertBox.remove(), 500);
        }, 5000);
    });
    </script>";
}

if (isset($_SESSION["adminEmail"])){ 
   if (isset($_POST['logout'])) {
      session_unset();
      session_destroy();
      header("Location: ../../index.php");
      exit();
   }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>GuestPro CMS | All Rented Cars</title>
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="../../css/custom.css" rel="stylesheet">
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../../css/returnView.css" rel="stylesheet">
    <link rel="icon" href="../../img/GuestProLogoReal.JPG" type="image/png">
    <link href="../../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include "../../web_includes/menu.php"; ?>
        
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include "../../web_includes/topbar.php"; ?>

                <div class="container-fluid">
                    <div style="display: flex;justify-content:space-between;">
                        <h1 class="h3 mb-2 text-gray-800">Overview of All Rented Cars</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
                        </a>
                    </div><br>

                    <div class="enhanced-container">
                        <div class="enhanced-controls">
                            <div class="search-container">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="search-input" placeholder="Search rentals by any field..." id="searchInput">
                            </div>

                            <div class="view-toggle">                                
                                <button type="submit" class="toggle-btn active" data-view="internal" id="internalBtn">
                                    <i class="fas fa-building"></i> Internal Rentals
                                </button>
                                <button type="submit" class="toggle-btn" data-view="external" id="externalBtn">
                                    <i class="fas fa-globe"></i> External Rentals
                                </button>
                            </div>

                            <div class="entries-info">
                                Showing <span id="visibleCount">0</span> of <span id="totalCount">0</span> rentals
                            </div>
                        </div>

                        <div class="table-container" id="tableContainer">
                            <table class="enhanced-table" id="dataTable">
                                <thead id="tableHead"></thead>
                                <tbody id="tableBody"></tbody>
                            </table>
                        </div>

                        <div class="no-results" id="noResults">
                            <i class="fas fa-search" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"></i>
                            <h4>No rentals found</h4>
                            <p>Try adjusting your search criteria</p>
                        </div>
                    </div>

                    <script>
                        const internalRentalsData = [
                            <?php
                            $sql = "SELECT r.renter_full_name, r.id_number, r.telephone, r.price, r.rent_date, r.return_date, 
                                    r.days_in_rent, r.total_fee, r.payment_status, r.amount_paid,
                                    c.car_id, c.car_name, c.plate_number, c.type, u.user_id, u.full_name AS provider_name 
                                    FROM rentals r 
                                    INNER JOIN cars c ON r.car_id = c.car_id
                                    INNER JOIN users u ON r.user_id = u.user_id
                                    WHERE c.status='Rented' 
                                    ORDER BY r.rent_date DESC";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                $rentals = [];
                                while ($row = $result->fetch_assoc()) {
                                    $rentals[] = json_encode($row);
                                }
                                echo implode(",\n", $rentals);
                            }
                            ?>
                        ];
                        
                        const externalRentalsData = [
                            <?php
                            $sqlForExternal = "SELECT er.renter_full_name, er.id_number, er.telephone, er.negotiated_price, 
                                              er.rent_date, er.return_date, er.days_in_rent, er.total_fee, er.payment_status, er.amount_paid,
                                              ec.car_id, ec.car_name, ec.plate_number, ec.type, ec.lifecycle_status, u.user_id, u.full_name AS provider_name 
                                              FROM external_rentals er
                                              INNER JOIN external_cars ec ON er.car_id = ec.car_id
                                              INNER JOIN users u ON er.user_id = u.user_id
                                              WHERE ec.status='Rented' AND ec.lifecycle_status = 'active'
                                              ORDER BY er.rent_date DESC";
                            $result = $conn->query($sqlForExternal);
                            if ($result->num_rows > 0) {
                                $External_rentals = [];
                                while ($row = $result->fetch_assoc()) {
                                    $External_rentals[] = json_encode($row);
                                }
                                echo implode(",\n", $External_rentals);
                            }
                            ?>
                        ];
                    </script>

                </div>
            </div>

            <?php include "../../web_includes/footer.php"; ?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Are you Sure?</h5>
                    <button class="close" type="button" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" to Logout from your account.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form action="" method="POST">
                        <input type="submit" name="logout" class="btn btn-primary" style="background-color: red;border:none;" value="Logout"/>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../../js/sb-admin-2.min.js"></script>
    <script src="../../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../../vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="../../js/mycustomjs.js"></script>

    <script>
        let currentView = 'internal';
        let internalData = [];
        let externalData = [];
        let filteredData = [];

        $(document).ready(function() {
            initializeTable();
            setupEventListeners();
        });

        function initializeTable() {
            internalData = internalRentalsData;
            externalData = externalRentalsData;
            filteredData = [...internalData];
            renderTable();
        }

        function setupEventListeners(){
            $('#searchInput').on('input', function() {
                const query = $(this).val().toLowerCase();
                const sourceData = currentView === 'internal' ? internalData : externalData;
                
                if (query === '') {
                    filteredData = [...sourceData];
                } else{
                    filteredData = sourceData.filter(item => 
                        Object.values(item).some(value => 
                            value.toString().toLowerCase().includes(query)
                        )
                    );
                }
                renderTable();
            });

            $('.toggle-btn').on('click', function() {
                const view = $(this).data('view');
                switchView(view);
            });
        }

        function switchView(view) {
            currentView = view;
            $('.toggle-btn').removeClass('active');
            $(`[data-view="${view}"]`).addClass('active');

            if (view === 'internal') {
                filteredData = [...internalData];
            } else if (view === 'external') {
                filteredData = [...externalData];
            }

            $('#searchInput').val('');
            renderTable();
        }

        function renderTable() {
            const tableHead = $('#tableHead');
            const tableBody = $('#tableBody');

            tableHead.empty();
            tableBody.empty();

            if (filteredData.length === 0) {
                $('#noResults').addClass('show');
                $('#tableContainer').hide();
            } else {
                $('#noResults').removeClass('show');
                $('#tableContainer').show();

                tableHead.html(`
                    <tr>
                        <th>N#</th>
                        <th>Renter Names</th>
                        <th>Renter ID</th>
                        <th>Telephone</th>
                        <th>Car Name</th>
                        <th>Plate Number</th>
                        <th>Type</th>
                        <th>Rented On</th>
                        <th>Action</th>
                    </tr>
                `);

                filteredData.forEach((rental, index) => {
                    const row = `
                        <tr style="animation-delay: ${index * 0.1}s">
                            <td>${index + 1}</td>
                            <td><strong>${rental.renter_full_name}</strong></td>
                            <td><code>${rental.id_number}</code></td>
                            <td>${rental.telephone}</td>
                            <td><strong style="color: #4e73df;">${rental.car_name}</strong></td>
                            <td><code>${rental.plate_number}</code></td>
                            <td>${rental.type}</td>
                            <td>${rental.rent_date}</td>
                            <td>
                                <button type="button" class="action-btn return-btn" 
                                        data-toggle="modal" data-target="#returnModal_${rental.car_id}">
                                    <i class="fas fa-info-circle"></i> More
                                </button>
                            </td>
                        </tr>
                    `;
                    tableBody.append(row);
                    createRentalModal(rental);
                });
            }

            updateEntriesInfo();
        }

function createRentalModal(rental) {
            const modalId = `returnModal_${rental.car_id}`;
            $(`#${modalId}`).remove();

            const paymentStatus = rental.payment_status || 'Fully Unpaid';
            const isFullyPaid = paymentStatus === 'Fully Paid';
            const amountPaid = parseFloat(rental.amount_paid) || 0;

            // 🔥 NEW: Check if debt was already fully paid in debts.php
            const payment_type = currentView.toLowerCase(); // Force lowercase: 'internal' or 'external'
            let isDebtFullyPaid = false;
    
    // Check payments table via AJAX to see if this rental's debt is fully paid
    $.ajax({
        url: 'check_payment_status.php',
        method: 'POST',
        data: {
            car_name: rental.car_name,
            plate_number: rental.plate_number,
            id_number: rental.id_number,
            payment_type: payment_type
        },
        async: false, // Make synchronous for modal creation
        success: function(response) {
            const paymentData = JSON.parse(response);
            isDebtFullyPaid = paymentData.is_fully_paid;
        },
        error: function() {
            isDebtFullyPaid = false; // Default to false on error
        }
    });
    let paymentSectionHTML = '';
    
    // Priority 1: Check if paid during rental process (original logic)
    if (isFullyPaid) {
        paymentSectionHTML = `
            <div class="alert alert-success mt-4" role="alert">
                <i class="fas fa-check-circle"></i> <strong>Payment Complete</strong><br>
                This rental was fully paid during the rental process (${amountPaid.toLocaleString()} FRW).<br>
                No additional payment needed upon return.
            </div>
            <input type="hidden" name="was_fully_paid_during_rental" value="yes">
            <input type="hidden" name="return_option" value="fully paid">
            <input type="hidden" name="partial_amount" value="0">
            <input type="hidden" name="remaining_balance" value="0">
        `;
    } 
    // Priority 2: Check if debt was cleared in debts.php
    else if (isDebtFullyPaid) {
        paymentSectionHTML = `
            <div class="alert alert-success mt-4" role="alert">
                <i class="fas fa-check-circle"></i> <strong>Debt Cleared!</strong><br>
                This customer has already paid their debt in full.<br>
                Simply click the button below to return the car.
            </div>
            <input type="hidden" name="was_fully_paid_during_rental" value="yes">
            <input type="hidden" name="return_option" value="fully paid">
            <input type="hidden" name="partial_amount" value="0">
            <input type="hidden" name="remaining_balance" value="0">
        `;
    }
    // Priority 3: Standard payment flow - customer needs to pay
    else {
        paymentSectionHTML = `
            <div class="alert alert-warning mt-4" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <strong>Payment Required</strong><br>                       
            </div>
            
            <div class="text-center mt-4">
                <label>Select Returning Option</label>
                <select name='return_option' class='form-control' required>
                    <option value="">--Select Return Decision--</option>
                    <option value="fully paid">Fully Paid</option>
                    <option value="partial paid">Partial Paid</option>
                    <option value="fully Unpaid">Fully Unpaid</option>
                </select><br>                            

                <div id="partial-payment-box" style="display:none; margin-top:10px;">
                    <label>Enter Partial Amount Paid</label>
                    <input type="number" name="partial_amount" class="form-control partial-input" min="1" placeholder="Enter amount customer paid"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);">
                    <small class="text-info d-block mt-2">
                        Remaining Balance: <span class="remaining-balance font-weight-bold">0 FRW</span>
                    </small>
                    <input type="hidden" name="remaining_balance" class="remaining-balance-hidden" value="0">
                </div><br>
            </div>
        `;
    }

    const modal = `
        <div class="modal fade enhanced-modal" id="${modalId}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-car"></i> Rental Details for <strong>${rental.car_name}</strong>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="rental-detail">
                                    <i class="fas fa-user"></i>
                                    <div>
                                        <strong>Renter:</strong><br>
                                        ${rental.renter_full_name}
                                    </div>
                                </div>
                                <div class="rental-detail">
                                    <i class="fas fa-id-card"></i>
                                    <div>
                                        <strong>ID Number:</strong><br>
                                        ${rental.id_number}
                                    </div>
                                </div>
                                <div class="rental-detail">
                                    <i class="fas fa-phone"></i>
                                    <div>
                                        <strong>Mobile:</strong><br>
                                        ${rental.telephone}
                                    </div>
                                </div>
                                <div class="rental-detail">
                                    <i class="fas fa-calendar"></i>
                                    <div>
                                        <strong>Rent Date:</strong><br>
                                        ${rental.rent_date}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="rental-detail">
                                    <i class="fas fa-clock"></i>
                                    <div>
                                        <strong>Original Days:</strong><br>
                                        ${rental.days_in_rent} days
                                    </div>
                                </div>
                                <div class="rental-detail">
                                    <i class="fas fa-dollar-sign"></i>
                                    <div>
                                        <strong>Daily Price:</strong><br>
                                        ${rental.price || rental.negotiated_price} FRW
                                    </div>
                                </div>
                                <div class="rental-detail">
                                    <i class="fas fa-chart-line"></i>
                                    <div>
                                        <strong>Unpaid Balance:</strong><br>
                                        <span class="text-success font-weight-bold">${rental.total_fee} FRW</span>
                                    </div>
                                </div>                                        
                                <div class="rental-detail">
                                    <i class="fas fa-calendar-check"></i>
                                    <div>
                                        <strong>Expected Return:</strong><br>
                                        ${rental.return_date}
                                    </div>
                                </div>
                                <strong>Provider:</strong><br>
                                <span class="text-danger font-weight-bold">${rental.provider_name}</span>                                                
                            </div>
                        </div>

                        <form method="POST" action="">                                
                            <input type="hidden" name="rental_id" value="${rental.car_id}">
                            <input type="hidden" name="renter_name" value="${rental.renter_full_name}">
                            <input type="hidden" name="id_number" value="${rental.id_number}">
                            <input type="hidden" name="telephone" value="${rental.telephone}">
                            <input type="hidden" name="car_name" value="${rental.car_name}">
                            <input type="hidden" name="plate_number" value="${rental.plate_number}">
                            <input type="hidden" name="price" value="${rental.price || rental.negotiated_price}">
                            <input type="hidden" name="rent_date" value="${rental.rent_date}">
                            <input type="hidden" name="days_in_rent" value="${rental.days_in_rent}">
                            <input type="hidden" name="expected_return_date" value="${rental.return_date}">
                            <input type="hidden" name="expected_revenue" value="${rental.total_fee}">
                            <input type="hidden" name="provider_name" value="${rental.provider_name}">                                    
                            <input type="hidden" name="lifecycle_status" value="${rental.lifecycle_status || ''}">                                    
                            <input type="hidden" name="view_type" value="${currentView}">                                    

                            <div class="form-group mt-4">
                                <label class="font-weight-bold">
                                    <i class="fas fa-calendar-alt text-primary"></i> Actual Return Date:
                                </label>
                                <input type="date" class="form-control return-date-input"
                                       name="date_returned_on"
                                       value="${rental.return_date}"
                                       min="${rental.rent_date}"
                                       data-rentdate="${rental.rent_date}"
                                       data-price="${rental.price || rental.negotiated_price}"
                                       data-expected="${rental.total_fee}"
                                       data-originalreturn="${rental.return_date}">
                            </div>

                            <div class="calculation-box">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="text-center p-3" style="background: #d4edda; border-radius: 10px;">
                                            <i class="fas fa-calculator text-success"></i>
                                            <h6 class="text-success mt-2">Balance Left</h6>
                                            <div class="adjusted-revenue font-weight-bold h5 text-success">${rental.total_fee} FRW</div>
                                            <input type="hidden" name="adjusted_revenue" class="adjusted-revenue-hidden" value="${rental.total_fee}">
                                        </div>
                                    </div> 
                                    <div class="col-md-6">
                                        <div class="text-center p-3" style="background: #f8d7da; border-radius: 10px;">
                                            <i class="fas fa-money-bill-wave text-danger"></i>
                                            <h6 class="text-danger mt-2">Refund Due</h6>
                                            <div class="refund-amount font-weight-bold h5 text-danger">0 FRW</div>
                                            <input type="hidden" name="refund_due" class="refund-hidden" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            ${paymentSectionHTML}

                            <div class="text-center mt-4">
                                <button type="submit" name="return_btn" class="btn btn-success btn-sml">
                                    <i class="fas fa-check-circle"></i> Return
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modal);

    // Only attach payment option handlers if payment is NOT fully paid
    if (!isFullyPaid && !isDebtFullyPaid) {
        $(`#${modalId} select[name='return_option']`).on('change', function() {
            const selected = $(this).val();
            const partialInput = $(`#${modalId} .partial-input`);

            if (selected === "partial paid") {
                $(`#${modalId} #partial-payment-box`).show();
                partialInput.attr("required", true);
            } else {
                $(`#${modalId} #partial-payment-box`).hide();
                partialInput.removeAttr("required").val("");
                $(`#${modalId} .remaining-balance`).text("0 FRW");
            }
        });

        $(`#${modalId} .partial-input`).on('input', function() {
            let partial = parseInt($(this).val()) || 0;
            const adjustedRevenue = parseInt($(`#${modalId} .adjusted-revenue-hidden`).val());

            if (partial > adjustedRevenue) {
                alert("Partial amount cannot exceed total debt amount!");
                $(this).val(0);
                partial = 0;
            }

            const remaining = Math.max(0, adjustedRevenue - partial);
            $(`#${modalId} .remaining-balance`).text(remaining + " FRW");
            $(`#${modalId} .remaining-balance-hidden`).val(remaining);
        });
    }

    $(`#${modalId} .return-date-input`).on('change', function() {
        const rentDate = new Date($(this).data('rentdate'));
        const dailyPrice = parseInt($(this).data('price'));
        const expectedTotal = parseInt($(this).data('expected'));
        const selectedDate = new Date($(this).val());

        const days = Math.max(1, Math.ceil((selectedDate - rentDate) / (1000 * 60 * 60 * 24)));
        const adjustedRevenue = days * dailyPrice;

        $(`#${modalId} .adjusted-revenue`).text(adjustedRevenue + " FRW");
        $(`#${modalId} .adjusted-revenue-hidden`).val(adjustedRevenue);

        let refund = 0;
        if (adjustedRevenue < expectedTotal) {
            refund = expectedTotal - adjustedRevenue;
        }
        $(`#${modalId} .refund-amount`).text(refund + " FRW");
        $(`#${modalId} .refund-hidden`).val(refund);

        // Only update remaining balance if payment options are shown
        if (!isFullyPaid && !isDebtFullyPaid) {
            const partial = parseInt($(`#${modalId} .partial-input`).val()) || 0;
            const remaining = Math.max(0, adjustedRevenue - partial);
            $(`#${modalId} .remaining-balance`).text(remaining + " FRW");
            $(`#${modalId} .remaining-balance-hidden`).val(remaining);
        }
    });
}

        function updateEntriesInfo() {
            $('#visibleCount').text(filteredData.length);
            const totalData = currentView === 'internal' ? internalData : externalData;
            $('#totalCount').text(totalData.length);
        }
    </script>

</body>
</html>
<?php
}
else {
    header("Location: ../../index.php");
    exit();
}
?>