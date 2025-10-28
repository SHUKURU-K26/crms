<?php
include "../../web_db/connection.php";  // your DB connection file

    // Sanitize inputs
    $renter_name = htmlspecialchars($_POST['renter_name'] ?? '');
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone_number'] ?? '');
    $id_number = preg_replace('/[^0-9]/', '', $_POST['Id_number'] ?? '');
    $car_id = intval($_POST['car_id'] ?? 0);
    $rent_date = $_POST['rent_date'] ?? '';
    $return_date = $_POST["return_date"];
    $days_of_rent = $_POST["days_of_rent"];
    $price = $_POST["price"];
    $totalFee = $_POST["total_fee"];
    $rented_by = $_POST["rented_by"];
    $leftAmount = $totalFee - ($_POST["amount_paid"] ?? 0);
    $signedInUser = $adminData['full_name'];
    
    // Payment related fields
    $payment_status = $_POST['payment_status'] ?? '';
    $amount_paid = 0;
    
    // Determine amount paid based on payment status
    if ($payment_status == 'Fully Paid') {
        $amount_paid = $totalFee;
    } elseif ($payment_status == 'Partial Paid') {
        $amount_paid = floatval($_POST['amount_paid'] ?? 0);
    } elseif ($payment_status == 'Fully Unpaid') {
        $amount_paid = 0;
    }
    
    // Calculate balance
    $balance = $totalFee - $amount_paid;
    
    // Basic validation
    $errors = [];

    if (empty($renter_name)) {
        $errors[] = "Renter name is required.";
    }

    if (strlen($phone) !== 10) {
        $errors[] = "Phone number must be 10 digits.";
    }

    if (strlen($id_number) !== 16) {
        $errors[] = "ID number must be 16 digits.";
    }

    if ($car_id <= 0) {
        $errors[] = "Please select a valid car.";
    }
    
    if (empty($price)) {
        $errors[] = "Price Amount is required.";
    }

    if (empty($rent_date)) {
        $errors[] = "Rent date is required.";
    }

    if (empty($return_date)) {
        $errors[] = "Return date is required.";
    }
    
    if (empty($days_of_rent)) {
        $errors[] = "Days of rent is required.";
    }
    
    if (empty($payment_status)) {
        $errors[] = "Payment status is required.";
    }
    
    // Validate partial payment
    if ($payment_status == 'Partial Paid') {
        if ($amount_paid <= 0) {
            $errors[] = "Amount paid must be greater than 0 for partial payment.";
        }
        if ($amount_paid >= $totalFee) {
            $errors[] = "Amount paid cannot be equal to or greater than total fee for partial payment.";
        }
    }

    if (!empty($errors)) {
        // Handle errors (display or log)
        foreach ($errors as $error) {
            echo "
                <div id='alertBox'>
                ⚠️ Error: '$error'.
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const alertBox = document.getElementById('alertBox');
                    if (!alertBox) return;

                    setTimeout(() => {
                    alertBox.style.opacity = 0;
                    setTimeout(() => alertBox.remove(), 500);
                    }, 3000);
                });
                </script>
            ";
        }
        exit; // stop processing on error
    }
    
    // Get car details
    $sql = "SELECT car_name, plate_number FROM external_cars WHERE car_id = $car_id";
    $result = mysqli_query($conn, $sql);
    $categories_exist = mysqli_num_rows($result) > 0;
    $row = mysqli_fetch_assoc($result);
    $CarPlateNumber = $row['plate_number'];
    $car_name = $row['car_name'];

    
    // Start transaction for data integrity
    mysqli_begin_transaction($conn);
    
    try {        
        // 1. Insert into external_rentals table with payment info
        $stmt = $conn->prepare("INSERT INTO external_rentals(
            car_id, 
            renter_full_name, 
            id_number, 
            telephone, 
            negotiated_price, 
            rent_date, 
            return_date,
            days_in_rent,
            total_fee,
            payment_status,
            amount_paid,
            balance,
            user_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param(
            "isssdsssdsddi",
            $car_id,
            $renter_name,
            $id_number,
            $phone,
            $price,
            $rent_date,
            $return_date,
            $days_of_rent,
            $totalFee,
            $payment_status,
            $amount_paid,
            $balance,
            $rented_by
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error inserting external rental: " . $stmt->error);
        }
        $stmt->close();
        
        // 2. Update external car status to rented
        $setInRent = $conn->prepare("UPDATE external_cars SET status='rented' WHERE car_id = ?");
        $setInRent->bind_param('i', $car_id);
        
        if (!$setInRent->execute()){
            throw new Exception("Error updating external car status: " . $setInRent->error);
        }
        $setInRent->close();

        //Insert into Renting History Table
        $historyStmt = $conn->prepare("INSERT INTO renting_history(
            history_type,
            renter_names,
            phone,
            id_number,
            car_name,
            plate,
            rent_date,
            duration,
            rent_amount,
            rented_by
        ) VALUES ('external', ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $historyStmt->bind_param(
            "ssssssdss",
            $renter_name,
            $phone,
            $id_number,
            $car_name,
            $CarPlateNumber,
            $rent_date,
            $days_of_rent,
            $price,
            $signedInUser
        );
        if (!$historyStmt->execute()){
            throw new Exception("Error inserting renting history: " . $historyStmt->error);
        }
        $historyStmt->close();
        
        // 3. Handle payment/debt based on payment status (with EXTERNAL type)
        if ($payment_status == 'Fully Paid') {
            // Insert into PAYMENTS table only with type = 'external'
            $paymentStmt = $conn->prepare("INSERT INTO payments(
                payment_type,
                amount_paid,
                paid_by,
                payer_phone,
                payer_national_id,
                car_payed_for,
                plate,
                status,
                balance
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $payment_type = 'external';  // KEY DIFFERENCE: external instead of internal
            $status = 'Full paid';
            $zero_balance = 0;
            
            $paymentStmt->bind_param(
                "sdssssssd",
                $payment_type,
                $amount_paid,
                $renter_name,
                $phone,
                $id_number,
                $car_name,
                $CarPlateNumber,
                $status,
                $zero_balance
            );
            
            if (!$paymentStmt->execute()) {
                throw new Exception("Error inserting payment: " . $paymentStmt->error);
            }
            $paymentStmt->close();
            $successMessage = "✅ External $car_name (Plate: $CarPlateNumber) is set in Rent Mode. Payment: Fully Paid (RWF " . number_format($totalFee) . ")";
            
        } elseif ($payment_status == 'Partial Paid') {
            // Insert into BOTH payments and debts tables with type = 'external'
            // 🔥 UPDATE: After inserting the rental, we need to update total_fee to reflect only the balance
            // This matches how return_car.php reads the data
                $updateRentalStmt = $conn->prepare("UPDATE external_rentals 
                SET total_fee = balance 
                WHERE car_id = ? AND renter_full_name = ? AND id_number = ?");

            $updateRentalStmt->bind_param("iss", $car_id, $renter_name, $id_number);

            if (!$updateRentalStmt->execute()) {
                throw new Exception("Error updating rental balance: " . $updateRentalStmt->error);
            }
            $updateRentalStmt->close();
            
            // Insert into PAYMENTS table
            $paymentStmt = $conn->prepare("INSERT INTO payments(
                payment_type,
                amount_paid,
                paid_by,
                payer_phone,
                payer_national_id,
                car_payed_for,
                plate,
                status,
                balance
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $payment_type = 'external';  // KEY DIFFERENCE: external instead of internal
            $status = 'Half paid';
            
            $paymentStmt->bind_param(
                "sdssssssd",
                $payment_type,
                $amount_paid,
                $renter_name,
                $phone,
                $id_number,
                $car_name,
                $CarPlateNumber,
                $status,
                $balance
            );
            
            if (!$paymentStmt->execute()) {
                throw new Exception("Error inserting payment: " . $paymentStmt->error);
            }
            $paymentStmt->close();
            
            // Insert into DEBTS table
            $debtStmt = $conn->prepare("INSERT INTO debts(
                debt_type,
                car_name,
                car_plate,
                renter_names,
                national_id,
                phone_number,
                debt_amount,
                provider_names,
                debt_owner
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $debt_type = 'external';  // KEY DIFFERENCE: external instead of internal
            $provider = $adminData['full_name'];  // Could be dynamic based on car owner
            
            $debtStmt->bind_param(
                "ssssssdss",
                $debt_type,
                $car_name,
                $CarPlateNumber,
                $renter_name,
                $id_number,
                $phone,
                $balance,
                $provider,
                $renter_name
            );
            
            if (!$debtStmt->execute()) {
                throw new Exception("Error inserting debt: " . $debtStmt->error);
            }
            $debtStmt->close();
            
            $successMessage = "✅ External $car_name (Plate: $CarPlateNumber) is set in Rent Mode. Partial Payment: RWF " . number_format($amount_paid) . " paid. Balance: RWF " . number_format($balance);
            
        } elseif ($payment_status == 'Fully Unpaid') {
            // Insert into DEBTS table only with type = 'external'
            $debtStmt = $conn->prepare("INSERT INTO debts(
                debt_type,
                car_name,
                car_plate,
                renter_names,
                national_id,
                phone_number,
                debt_amount,
                provider_names,
                debt_owner
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $debt_type = 'external';  // KEY DIFFERENCE: external instead of internal
            $provider = $adminData['full_name'];  // Could be dynamic based on car owner
            
            $debtStmt->bind_param(
                "ssssssdss",
                $debt_type,
                $car_name,
                $CarPlateNumber,
                $renter_name,
                $id_number,
                $phone,
                $totalFee,
                $provider,
                $renter_name
            );
            
            if (!$debtStmt->execute()) {
                throw new Exception("Error inserting debt: " . $debtStmt->error);
            }
            $debtStmt->close();
            
            $successMessage = "⚠️ External $car_name (Plate: $CarPlateNumber) is set in Rent Mode. Full Amount (RWF " . number_format($totalFee) . ") recorded as debt.";
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Display success message
        echo "    
        <div id='successAlertBox'>
            $successMessage
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function(){
                const alertBox = document.getElementById('successAlertBox');
                if (!alertBox) return;
                setTimeout(() => {
                    alertBox.style.opacity = 0;
                    setTimeout(() => alertBox.remove(), 300);
                    window.location.href='car_overview.php';
                }, 3000);
            });
        </script>
        ";
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        
        echo "
        <div id='alertBox'>
            ❌ Database Error: " . $e->getMessage() . "
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const alertBox = document.getElementById('alertBox');
                if (!alertBox) return;
                setTimeout(() => {
                    alertBox.style.opacity = 0;
                    setTimeout(() => alertBox.remove(), 500);
                }, 4000);
            });
        </script>
        ";
    }
?>