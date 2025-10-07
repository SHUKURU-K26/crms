<?php
include "../../web_db/connection.php";  // DB connection file

    // Sanitize inputs
    $renter_name = htmlspecialchars($_POST['renter_name'] ?? '');
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone_number'] ?? '');
    $id_number = preg_replace('/[^0-9]/', '', $_POST['Id_number'] ?? '');
    $car_id = intval($_POST['car_id'] ?? 0);
    $rent_date = $_POST['rent_date'] ?? '';
    $return_date=$_POST["return_date"];
    $days_of_rent=$_POST["days_of_rent"];
    $price=$_POST["price"];
    $totalFee=$_POST["total_fee"];
    $rented_by=$_POST["rented_by"];
    
    // Sanitize total fee input (remove any currency symbols or commas)
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
        $errors[] = " Rent date is required.";
    }

    if (empty($return_date)) {
        $errors[] = "Return date is required.";
    }
    if (empty($days_of_rent)) {
        $errors[] = "Return date is required.";
    }

    if (!empty($errors)) {
        // Handle errors (display or log)
        foreach ($errors as $error) {
            echo "
                <div id='alertBox'>
                ⚠️ Error:'$error'.
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
    $sql="SELECT car_name, plate_number FROM external_cars WHERE car_id = $car_id";;
    $result = mysqli_query($conn, $sql);
    $categories_exist = mysqli_num_rows($result) > 0;
    $row= mysqli_fetch_assoc($result);
    $CarPlateNumber = $row['plate_number'];
    $car_name = $row['car_name'];
    // Prepared statement to insert data in Rental Table securely
    $stmt = $conn->prepare("INSERT INTO external_rentals (car_id, renter_full_name, id_number, telephone,negotiated_price, rent_date, return_date, days_in_rent, total_fee, user_id)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssdsssdi",$car_id, $renter_name, $id_number, $phone, $price, $rent_date, $return_date,$days_of_rent,$totalFee, $rented_by);
    //statement to Set the Car in Rent
    $setInRent=$conn->prepare("UPDATE external_cars SET status='rented' WHERE car_id= ?");
    $setInRent->bind_param('s', $car_id);
    if ($stmt->execute() && $setInRent->execute()){
        echo "
        <div id='successAlertBox'>
            ✅ External $car_name of Plate: $CarPlateNumber is Set in Rent Mode.
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
    } else {
        echo "<p style='color:red;'>Database error: " . $stmt->error . "</p>";
    }

    $stmt->close();
?>
