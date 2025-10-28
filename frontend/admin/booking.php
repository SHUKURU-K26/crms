<?php
session_start();
include "../../web_includes/auth.php";
include "../../web_db/connection.php";

// Handle booking submission
if (isset($_POST['book_car'])){
    $car_id = mysqli_real_escape_string($conn, $_POST["car_id"]) ?? '';
    $customer_name = mysqli_real_escape_string($conn, $_POST["customer_name"]) ?? '';
    $customer_national_id = mysqli_real_escape_string($conn, $_POST["customer_national_id"]) ?? '';
    $customer_phone = mysqli_real_escape_string($conn, $_POST["customer_phone"]) ?? '';
    $booking_date = mysqli_real_escape_string($conn, $_POST["booking_date"]) ?? '';
    $booking_return_date = mysqli_real_escape_string($conn, $_POST["booking_return_date"]) ?? '';
    $booking_amount = mysqli_real_escape_string($conn, $_POST["booking_amount"]) ?? '';
    
    // Validate dates
    $today = date('Y-m-d');
    if ($booking_date < $today) {
        echo "<div id='deleteSuccessBox'>Error: Booking date cannot be in the past!</div>";
    } elseif ($booking_return_date <= $booking_date) {
        echo "<div id='deleteSuccessBox'>Error: Return date must be after booking date!</div>";
    } elseif (empty($car_id)) {
        echo "<div id='deleteSuccessBox'>Error: Please select a car to book!</div>";
    } elseif (empty($customer_name) || empty($customer_national_id) || empty($customer_phone)) {
        echo "<div id='deleteSuccessBox'>Error: Please fill in all customer information!</div>";
    } else {
        // Check if car is already booked for these dates
        $checkQuery = "SELECT * FROM bookings 
                       WHERE car_id='$car_id' 
                       AND booking_status IN ('pending', 'active')
                       AND (
                           (booking_date <= '$booking_date' AND booking_return_date >= '$booking_date')
                           OR (booking_date <= '$booking_return_date' AND booking_return_date >= '$booking_return_date')
                           OR (booking_date >= '$booking_date' AND booking_return_date <= '$booking_return_date')
                       )";
        $checkResult = $conn->query($checkQuery);
        
        if ($checkResult->num_rows > 0) {
            echo "<div id='deleteSuccessBox'>Error: This car is already booked for the selected dates!</div>";
        } else {
            // Insert booking into bookings table
            $insertSql = "INSERT INTO bookings (car_id, customer_name, customer_national_id, customer_phone, 
                          booking_date, booking_return_date, booking_amount, booking_status) 
                          VALUES ('$car_id', '$customer_name', '$customer_national_id', '$customer_phone',
                          '$booking_date', '$booking_return_date', '$booking_amount', 'pending')";
            
            if ($conn->query($insertSql) === TRUE) {
                $booking_id = $conn->insert_id;
                
                // Update car status to indicate it's booked
                $updateCarSql = "UPDATE cars SET booking_status='booked' WHERE car_id='$car_id'";
                $conn->query($updateCarSql);
                
                echo "<div id='successBox'>Booking created successfully! Reference: #BOOK-" . str_pad($booking_id, 4, '0', STR_PAD_LEFT) . "</div>";
                echo"<script>
                        setTimeout(function() {
                            window.location.href = 'car_overview.php';
                        }, 2000);
                     </script>";
            } else {
                echo "<div id='deleteSuccessBox'>Error creating booking: " . $conn->error . "</div>";
            }
        }
    }
}

// Fetch available cars for booking
$carsQuery = "SELECT * FROM cars 
              WHERE status='available'
              ORDER BY car_name ASC";
$availableCars = $conn->query($carsQuery);

// Logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../../index.php");
    exit();
}

if (isset($_SESSION["adminEmail"])){
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Guest Pro Car Management System</title>
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,900" rel="stylesheet" />
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet" />
    <link href="../../css/custom.css" rel="stylesheet">
    <link rel="icon" href="../../img/GuestProLogoReal.JPG" type="image/png">
    <style>
        #deleteSuccessBox, #successBox {
            max-width: 90%;
            margin: 10px auto;
            padding: 12px 20px;
            font-weight: bold;
            font-family: 'Segoe UI', sans-serif;
            font-size: 16px;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transition: opacity 0.5s ease-in-out;
            z-index: 9999;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
        }
        
        #deleteSuccessBox {
            background-color: #f8d7da;
            color: red;
            border: 1px solid #f5c6cb;
        }
        
        #successBox {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        @media (max-width: 600px) {
            #deleteSuccessBox, #successBox {
                font-size: 14px;
                padding: 10px 15px;
            }
        }
        
        #submitBtn:hover {
            opacity: 0.8;
            transform: scale(1.02);
            transition: all 0.3s;
        }
        
        .info-badge {
            background-color: #e7f3ff;
            padding: 12px;
            border-left: 4px solid #2196F3;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .car-preview {
            background: #f8f9fc;
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #e3e6f0;
            margin-top: 15px;
            margin-bottom: 15px;
            display: none;
        }
        
        .car-preview.active {
            display: block;
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .car-detail-item {
            padding: 8px 0;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .car-detail-item:last-child {
            border-bottom: none;
        }
        
        .car-detail-item i {
            color: #970000;
            width: 20px;
        }
        
        .form-control:focus {
            border-color: #970000;
            box-shadow: 0 0 0 0.2rem rgba(151, 0, 0, 0.25);
        }
        
        select.form-control {
            cursor: pointer;
        }
        
        .section-divider {
            border-top: 2px solid #e3e6f0;
            margin: 25px 0;
            padding-top: 20px;
        }
        
        .section-title {
            color: #970000;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 18px;
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include "../../web_includes/menu.php"; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include "../../web_includes/topbar.php"; ?>
                <div class="container-fluid">
                    <div class="row page-titles">
                        <div class="col-md-5 align-self-center">
                            <h3 class="text-themecolor">Initiate Car Booking</h3>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                                <li class="breadcrumb-item active">Book a Car</li>
                            </ol>
                        </div>
                        <div class="col-md-7 align-self-center">
                            <p style="font-weight: bold;">
                                <a href="#" class="btn btn-info btn-circle btn-sm" data-toggle="tooltip" data-placement="top">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                Complete the form below to initiate a new car booking.
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <center class="m-t-30">
                                        <img src="../../img/GuestProLogoReal.JPG" class="img-circle" width="150" />
                                        <h4 class="card-title m-t-10">Car Booking</h4>
                                        <h6 class="card-subtitle">Reserve a Vehicle</h6>
                                    </center>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-9 col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="info-badge">
                                        <i class="fas fa-lightbulb"></i> <strong>Note:</strong> Select an available car, enter customer details, and set the booking dates. All fields are required.
                                    </div>
                                    
                                    <form class="form-horizontal form-material" id="bookingForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST">
                                        
                                        <!-- CAR SELECTION SECTION -->
                                        <div class="section-title">
                                            <i class="fas fa-car"></i> Vehicle Selection
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-md-12"><i class="fas fa-car"></i> Select Car</label>
                                            <div class="col-md-12">
                                                <select class="form-control form-control-line" id="car_id" name="car_id" required onchange="showCarDetails()">
                                                    <option value="">-- Choose an Available Car --</option>
                                                    <?php 
                                                    if ($availableCars && $availableCars->num_rows > 0) {
                                                        while($car = $availableCars->fetch_assoc()) {
                                                            echo "<option value='{$car['car_id']}' 
                                                                    data-name='{$car['car_name']}' 
                                                                    data-plate='{$car['plate_number']}' 
                                                                    data-type='{$car['type']}' 
                                                                    data-fuel='{$car['fuel_type']}'>
                                                                    {$car['car_name']} - {$car['plate_number']}
                                                                  </option>";
                                                        }
                                                    } else {
                                                        echo "<option value=''>No cars available for booking</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Car Details Preview -->
                                        <div id="carPreview" class="car-preview col-md-12">
                                            <h6 class="font-weight-bold" style="color: #970000;">
                                                <i class="fas fa-info-circle"></i> Selected Car Details:
                                            </h6>
                                            <div class="car-detail-item">
                                                <i class="fas fa-car"></i> <strong>Car Name:</strong> <span id="preview-name"></span>
                                            </div>
                                            <div class="car-detail-item">
                                                <i class="fas fa-id-card"></i> <strong>Plate Number:</strong> <span id="preview-plate"></span>
                                            </div>
                                            <div class="car-detail-item">
                                                <i class="fas fa-cog"></i> <strong>Transmission:</strong> <span id="preview-type"></span>
                                            </div>
                                            <div class="car-detail-item">
                                                <i class="fas fa-gas-pump"></i> <strong>Fuel Type:</strong> <span id="preview-fuel"></span>
                                            </div>
                                        </div>

                                        <!-- CUSTOMER INFORMATION SECTION -->
                                        <div class="section-divider">
                                            <div class="section-title">
                                                <i class="fas fa-user"></i> Customer Information
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-12"><i class="fas fa-user"></i> Customer Name <span style="color: red;">*</span></label>
                                            <div class="col-md-12">
                                                <input type="text" class="form-control form-control-line" id="customer_name" name="customer_name" 
                                                       placeholder="Enter customer full name" required>
                                                <small class="form-text text-muted">Enter the full name of the customer</small>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-12"><i class="fas fa-id-card"></i> National ID <span style="color: red;">*</span></label>
                                            <div class="col-md-12">
                                                <input type="text" class="form-control form-control-line" id="customer_national_id" name="customer_national_id" 
                                                       placeholder="Enter national ID number" required maxlength="16">
                                                <small class="form-text text-muted">Enter the customer's national ID or passport number</small>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-12"><i class="fas fa-phone"></i> Phone Number <span style="color: red;">*</span></label>
                                            <div class="col-md-12">
                                                <input type="tel" class="form-control form-control-line" id="customer_phone" name="customer_phone" 
                                                       placeholder="Enter phone number (e.g., 0781234567)" required pattern="[0-9]{10,15}">
                                                <small class="form-text text-muted">Enter a valid phone number (10-15 digits)</small>
                                            </div>
                                        </div>

                                        <!-- BOOKING DETAILS SECTION -->
                                        <div class="section-divider">
                                            <div class="section-title">
                                                <i class="fas fa-calendar-alt"></i> Booking Details
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-12"><i class="fas fa-calendar-alt"></i> Booking Date (Pick-up)</label>
                                            <div class="col-md-12">
                                                <input type="date" class="form-control form-control-line" id="booking_date" name="booking_date" 
                                                       min="<?php echo date('Y-m-d'); ?>" required onchange="validateDates()">
                                                <small class="form-text text-muted">Select the date when the customer will pick up the car</small>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-12"><i class="fas fa-calendar-check"></i> Return Date (Drop-off)</label>
                                            <div class="col-md-12">
                                                <input type="date" class="form-control form-control-line" id="booking_return_date" name="booking_return_date" 
                                                       required onchange="validateDates()">
                                                <small class="form-text text-muted">Select the date when the customer will return the car</small>
                                            </div>
                                        </div>

                                        <div id="dateValidationMessage" class="col-md-12" style="display: none;">
                                            <div class="alert alert-danger">
                                                <i class="fas fa-exclamation-triangle"></i> <span id="validationText"></span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-12"><i class="fas fa-money-bill-wave"></i> Booking Amount (RWF)</label>
                                            <div class="col-md-12">
                                                <input type="number" class="form-control form-control-line" id="booking_amount" name="booking_amount" 
                                                       placeholder="Enter total rental amount in Rwandan Francs" min="1" required
                                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                <small class="form-text text-muted">Enter the total rental fee for this booking</small>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <button type="submit" name="book_car" id="submitBtn" 
                                                        style="background-color: #970000; border:none;" 
                                                        class="btn btn-success">
                                                    <i class="fas fa-check-circle"></i> Confirm Booking
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include "../../web_includes/footer.php"; ?>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Are you Sure?</h5>
                    <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">Select "Logout" to Logout from your account.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form action="" method="POST">
                        <input type="submit" name="logout" class="btn btn-primary" style="background-color: red;border:none;" value="Logout" />
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../../js/sb-admin-2.min.js"></script>

    <script>
        // Auto-hide success/error messages after 5 seconds
        setTimeout(function() {
            $('#deleteSuccessBox, #successBox').fadeOut('slow');
        }, 5000);

        // Show car details when selected
        function showCarDetails() {
            var select = document.getElementById('car_id');
            var selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value) {
                document.getElementById('preview-name').textContent = selectedOption.getAttribute('data-name');
                document.getElementById('preview-plate').textContent = selectedOption.getAttribute('data-plate');
                document.getElementById('preview-type').textContent = selectedOption.getAttribute('data-type').charAt(0).toUpperCase() + selectedOption.getAttribute('data-type').slice(1);
                document.getElementById('preview-fuel').textContent = selectedOption.getAttribute('data-fuel');
                
                document.getElementById('carPreview').classList.add('active');
            } else {
                document.getElementById('carPreview').classList.remove('active');
            }
        }

        // Date validation function
        function validateDates() {
            var bookingDate = document.getElementById('booking_date').value;
            var returnDate = document.getElementById('booking_return_date').value;
            var today = new Date().toISOString().split('T')[0];
            var messageDiv = document.getElementById('dateValidationMessage');
            var messageText = document.getElementById('validationText');
            
            var isValid = true;
            var message = '';
            
            if (bookingDate && bookingDate < today) {
                message = 'Booking date cannot be in the past! Please select today or a future date.';
                isValid = false;
            }
            
            if (bookingDate && returnDate && returnDate <= bookingDate) {
                message = 'Return date must be after the booking date! Please select a later date.';
                isValid = false;
            }
            
            if (message) {
                messageText.textContent = message;
                messageDiv.style.display = 'block';
                document.getElementById('submitBtn').disabled = true;
            } else {
                messageDiv.style.display = 'none';
                document.getElementById('submitBtn').disabled = false;
            }
        }

        // Update return date minimum when booking date changes
        document.getElementById('booking_date').addEventListener('change', function() {
            var bookingDate = this.value;
            if (bookingDate) {
                var nextDay = new Date(bookingDate);
                nextDay.setDate(nextDay.getDate() + 1);
                var minReturnDate = nextDay.toISOString().split('T')[0];
                document.getElementById('booking_return_date').setAttribute('min', minReturnDate);
            }
        });

        // Initialize tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
</body>
</html>
<?php
} else {
    header("Location: ../../index.php");
    exit();
}
?>