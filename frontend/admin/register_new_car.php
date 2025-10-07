<?php
session_start();
include "../../web_includes/auth.php";
if (isset($_SESSION["adminEmail"])){  
   if (isset($_POST['logout'])) {
      session_unset();
      session_destroy();
      header("Location: ../../index.php");
      exit();
   }
   
    include "../../web_db/connection.php" ;
    if ($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['Save_in_internal'])) {
                // Sanitize and validate inputs
                $Car_name=mysqli_real_escape_string($conn, $_POST["car_name"]);
                $category=mysqli_real_escape_string($conn, $_POST["category_id"]);
                $PlateNumber=mysqli_real_escape_string($conn, $_POST["plateNumber"]);
                $CarType=$_POST["car_type"];
                $fuel_type=$_POST["fuel_type"];
                $insurance_Issued_date=$_POST["insurance_issued_date"];
                $insurance_expiry_date=$_POST["insurance_expiry_date"];
                $control_issued_date=$_POST["control_issued_date"];
                $control_expiry_date=$_POST["control_expiry_date"];

                $check_query = "SELECT * FROM cars WHERE plate_number = '$PlateNumber'";
                $result = $conn->query($check_query);
                if ($result->num_rows > 0) {        
                    // Plate number already exists
                    include "../../system_messages/ErrorMessage.php";
                }
                
                else{
                    $stmt=$conn->prepare("INSERT INTO cars VALUES('', ?, ?, ?, ?, ?, 'available', ?, ?, ?, ?, CURDATE()) ");
                    $stmt->bind_param("sssssssss", $Car_name, $category, $PlateNumber, $CarType, $fuel_type, $insurance_Issued_date, $insurance_expiry_date,
                    $control_issued_date, $control_expiry_date);
                    $stmt->execute(); 
                    if ($stmt) {
                        include "../../system_messages/newCarMessage.php";
                        $stmt->close();
                    }
                }
    }

    if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['Save_in_external'])) {
        // Sanitize and validate inputs
        $Car_name=mysqli_real_escape_string($conn, $_POST["car_name"]);
        $provider=mysqli_real_escape_string($conn, $_POST["provider_name"]);
        $negotiated_price=mysqli_real_escape_string($conn, $_POST["negotiated_price"]);
        $days_in_service=mysqli_real_escape_string($conn, $_POST["days_in_service"]);
        $total_spending=mysqli_real_escape_string($conn, $_POST["total_spending"]);
        $PlateNumber=mysqli_real_escape_string($conn, $_POST["plateNumber"]);
        $CarType=$_POST["car_type"];
        $fuel_type=$_POST["fuel_type"];
        $user_id=$_POST["user_id"];
        $payment_status=$_POST["payment_status"];
        $payment_method=$_POST["payment_method"];
        $date_brought_on=$_POST["date_brought_on"];
        $expected_return_date=$_POST["expected_return_date"];
        
        // Calculate balance based on payment status
        $balance = 0;
        if ($payment_status === 'Fully Paid'){
            $balance = 0;
            //Inserting Into Expenses History Table statement            
            $stmForExpenseHistory = $conn->prepare("INSERT INTO expenses_history (car_name, plate, provider, amount_paid, payment_method, track_date) 
                VALUES (?, ?, ?, ?, ?, CURDATE())
            ");

            $stmForExpenseHistory->bind_param("sssis", $Car_name, $PlateNumber, $provider, $total_spending, $payment_method);
            $stmForExpenseHistory->execute();


        } 
        elseif ($payment_status === 'Half Paid'){
            $paid_amount = floatval($_POST["paid_amount"]);
            $balance = $total_spending - $paid_amount;
            //Inserting Into Expenses History Table statement            
            $stmForExpenseHistory=$conn->prepare("INSERT INTO expenses_history VALUES('', ?, ?, ?, ?, ?, CURDATE())");        
            $stmForExpenseHistory->bind_param("sssss", $Car_name, $PlateNumber, $provider, $paid_amount, $payment_method);
            $stmForExpenseHistory->execute();
            
        } else{ // Unpaid
            $balance = $total_spending;
            $payment_method = 'Not Paid Yet';
        }

        // Step 1: Check if plate exists with lifecycle_status = 'active'
        $check_active_query = "SELECT * FROM external_cars WHERE plate_number = '$PlateNumber' AND lifecycle_status='active'";
        $active_result = $conn->query($check_active_query);

        if ($active_result->num_rows > 0) {
            // Plate number already exists and is active
            include "../../system_messages/ErrorMessage.php";
        } 
        else {
            // Step 2: Check if plate exists with lifecycle_status = 'returned'
            $check_returned_query = "SELECT car_id FROM external_cars WHERE plate_number = '$PlateNumber' AND lifecycle_status='returned'";
            $returned_result = $conn->query($check_returned_query);

            if ($returned_result->num_rows > 0) {
                // Car was previously returned - UPDATE it (reactivate)
                $row = $returned_result->fetch_assoc();
                $existing_car_id = $row['car_id'];

                $update_stmt = $conn->prepare("UPDATE external_cars SET 
                    car_name = ?,
                    provider = ?,
                    negotiated_price = ?,
                    date_brought = ?,
                    expected_return_date = ?,
                    days_in_service = ?,
                    total_spending = ?,
                    type = ?,
                    fuel_type = ?,
                    user_id = ?,
                    status = 'available',
                    use_status = ?,
                    payment_method = ?,
                    balance = ?,
                    lifecycle_status = 'active'
                    WHERE plate_number = ? AND lifecycle_status = 'returned'
                ");

                $update_stmt->bind_param("ssissiissiisis", 
                    $Car_name, $provider, $negotiated_price, $date_brought_on, $expected_return_date,
                    $days_in_service, $total_spending, $CarType, $fuel_type, 
                    $user_id, $payment_status, $payment_method, $balance, $PlateNumber
                );

                $update_stmt->execute();

                if ($update_stmt->affected_rows > 0) {
                    $payment_msg = '';
                    if ($payment_status === 'Fully Paid'){
                        $payment_msg = ' (Fully Paid) via '.$payment_method.' Transaction';
                    } elseif ($payment_status === 'Half Paid') {
                        $payment_msg = ' (Partially Paid - Balance: ' . number_format($balance) . ' FRW) via '.$payment_method.' Transaction';
                    } else {
                        $payment_msg = ' (Unpaid)';
                    }
                
                    echo "
                    <div id='successAlertBox' style='position: fixed; top: 20px; right: 20px; z-index: 9999; background: linear-gradient(135deg, #1cc88a, #13855c); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.3);'>
                        <i class='fas fa-check-circle'></i> $Car_name Reactivated Successfully $payment_msg
                    </div>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const alertBox = document.getElementById('successAlertBox');
                        setTimeout(() => {
                            alertBox.style.transform = 'translateX(100%)';
                            alertBox.style.opacity = '0';
                            setTimeout(() => {
                                alertBox.remove();
                                window.location.href = '';
                            }, 500);
                        }, 4000);
                    });
                    </script>
                    ";
                } else {
                    echo "<script>alert('❌ Error reactivating car: " . mysqli_error($conn) . "');</script>";
                }

                $update_stmt->close();

            } else {
                // Plate doesn't exist at all - INSERT new record
                $stmt=$conn->prepare("INSERT INTO external_cars 
                (car_name, provider, negotiated_price, date_brought, expected_return_date, 
                days_in_service, total_spending, plate_number, type, fuel_type, user_id, 
                status, use_status, payment_method, balance) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'available', ?, ?, ?)");

                $stmt->bind_param("ssissiisssissi", 
                    $Car_name, $provider, $negotiated_price, $date_brought_on, $expected_return_date,
                    $days_in_service, $total_spending, $PlateNumber, $CarType, $fuel_type, 
                    $user_id, $payment_status, $payment_method, $balance);

                $stmt->execute();

                if ($stmt) {
                    $car_id = $conn->insert_id;
                    $stmt->close();

                    // Insert into External Car Expenses
                    $stmForExpense=$conn->prepare("INSERT INTO external_car_expenses (car_id) VALUES (?)"); 
                    $stmForExpense->bind_param("i", $car_id);
                    $stmForExpense->execute();
                    $stmForExpense->close();
                }

                if ($stmt && $stmForExpense){
                    $payment_msg = '';
                    if ($payment_status === 'Fully Paid'){
                        $payment_msg = ' (Fully Paid) via '.$payment_method.' Transaction';
                    } elseif ($payment_status === 'Half Paid') {
                        $payment_msg = ' (Partially Paid - Balance: ' . number_format($balance) . ' FRW) via '.$payment_method.' Transaction';
                    } else {
                        $payment_msg = ' (Unpaid)';
                    }
                
                    echo "
                    <div id='successAlertBox' style='position: fixed; top: 20px; right: 20px; z-index: 9999; background: linear-gradient(135deg, #1cc88a, #13855c); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.3);'>
                        <i class='fas fa-check-circle'></i> $Car_name Registered Successfully $payment_msg
                    </div>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const alertBox = document.getElementById('successAlertBox');
                        setTimeout(() => {
                            alertBox.style.transform = 'translateX(100%)';
                            alertBox.style.opacity = '0';
                            setTimeout(() => {
                                alertBox.remove();
                                window.location.href = '';
                            }, 500);
                        }, 4000);
                    });
                    </script>
                    ";
                }
            }
        }
    }                           
  
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>GuestPro CMS| Register New Car</title>

    <!-- Custom fonts for this template-->
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" href="../../img/GuestProLogoReal.JPG" type="image/png">
    <link rel="stylesheet" href="../../css/custom.css">
    <link rel="stylesheet" href="../../css/enhancedProviderPayementSelection.css">
    <link rel="stylesheet" href="../../css/registrationFormTypeSelection.css">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
         <?php 
          include "../../web_includes/menu.php";
         ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            
            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php
                  include "../../web_includes/topbar.php";
                ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">New Car Registration</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div>

                    <!-- Content Row -->
                    <?php include "../../web_includes/dashboard.php"?>
                    <!-- Content Row -->

                    <div class="row">

                        <!-- Area Chart -->
                        <div class="col-12 grid-margin stretch-card">
                            <!--Form Selection Whether the type of Form to be Displayed-->
                            <div class="registration-selector-container">
                                <div class="selector-header">
                                    <h3 class="selector-title">
                                        <i class="fas fa-clipboard-list"></i> Registration Type
                                    </h3>
                                    <p class="selector-subtitle">Pick either Internal or External Registration Form</p>
                                </div>

                                <div class="registration-options">
                                    <div class="option-card internal-option" data-value="internal">
                                        <div class="option-icon">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <h4 class="option-title">GuestPro</h4>
                                        <p class="option-description">Register cars under our internal management system</p>
                                        <ul class="option-features">
                                            <li><i class="fas fa-check-circle"></i> Company Owned Car</li>
                                            <li><i class="fas fa-check-circle"></i> Technical Control and Insurance Data are Required</li>
                                            <li><i class="fas fa-check-circle"></i>Registered By Both Admin & Staff</li>
                                        </ul>
                                        <div class="selection-indicator">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </div>

                                    <div class="option-card external-option" data-value="external">
                                        <div class="option-icon">
                                            <i class="fas fa-globe"></i>
                                        </div>
                                        <h4 class="option-title">External Registration</h4>
                                        <p class="option-description">Register cars from external partners or third-party providers</p>
                                        <ul class="option-features">
                                            <li><i class="fas fa-check-circle"></i> External Car Facility</li>
                                            <li><i style="color: red;">x</i> No Technical Control and Insurance Data Required</li>
                                            <li><i class="fas fa-check-circle"></i> Registered By both Admin & Staff</li>
                                        </ul>
                                        <div class="selection-indicator">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden traditional select for form submission -->
                                <select class="form-control hidden-select" id="formSelector" name="registration_type">
                                    <option value="">-- Choose Registration Type --</option>
                                    <option value="internal">GuestPro</option>
                                    <option value="external">External Car Registration</option>
                                </select>
                            </div>
                            <!--End of Form Selection Whether the type of Form to be Displayed-->

                            <!--Form of Registering  New External Car-->

                            <form class="p-4 shadow rounded bg-white" id="externalForm" style="display: none;" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST" >
                                <h4 class="mb-3" style="color: dodgerblue;font-weight:bold;">External Car Details: </h4>
                                <hr />
                                
                                <div class="mb-3">
                                    <label for="car_name" class="form-label">Car Name</label>
                                    <input type="text" class="form-control" name="car_name" placeholder="Enter Car Name" required />
                                </div>
                                
                                <div class="mb-3">
                                <label for="Plate_number" class="form-label">Provider / Company</label>
                                <input type="text" class="form-control" id="ProviderName" placeholder="Provider Name" name="provider_name" required />
                                </div>

                                <div class="mb-3">
                                <label for="Negotiated Price" class="form-label">Negotiated Price</label>
                                <input type="text" class="form-control" id="NegotiatedPrice" oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                    placeholder="Negotiated Price" name="negotiated_price" required />
                                </div>

                                <div class="mb-3">
                                <label for="date_brought_on" class="form-label">Date Brought On:
                                    <span style="color: dodgerblue;">Today by Default</span>
                                </label>
                                <input type="date" class="form-control" style="color: dodgerblue;" id="date_brought_on" value="<?php echo date("Y-m-d")?>"
                                    name="date_brought_on" required/>
                                </div>

                                <div class="mb-3">
                                <label for="expected_return_date" class="form-label">Expected Return Date: </label>
                                <input type="date" class="form-control" id="expected_return_date"
                                    name="expected_return_date" min="<?php echo date("Y-m-d")?>" required/>
                                <small class="text-muted">Return date cannot be before the brought date</small>
                                </div>

                                <div class="mb-3">
                                <label for="Days in Service" class="form-label">Days In Service</label>
                                <input type="text" class="form-control" id="daysInService" placeholder="Will be calculated automatically" name="days_in_service" readonly style="background-color: #e9ecef; font-weight: bold;" required />
                                <small class="text-success">Automatically calculated based on dates</small>
                                </div>
                                
                                <div class="total-spending-display">
                                    <div style="font-size: 16px; color: #155724; margin-bottom: 5px;">Total Spending:</div>
                                    <div class="total-amount" id="totalSpendingDisplay">0 FRW</div>
                                    <input type="hidden" id="TotalSpending" name="total_spending" />
                                </div>

                                <div class="mb-3">
                                <label for="Plate_number" class="form-label">Plate Number</label>
                                <input type="text" class="form-control" id="externalPlateNumber" placeholder="Enter Plate Number" name="plateNumber" required />
                                </div>

                                <div class="mb-3">
                                <label for="Plate_number" class="form-label">Car Type</label>
                                <select class="form-control" name="car_type" required>
                                    <option value="">--Select--</option>
                                    <option value="automatic">Automatic</option>
                                    <option value="manual">Manual</option>                              
                                </select>
                                </div>

                                <div class="mb-3">
                                <label for="Plate_number" class="form-label">Fuel Type</label>
                                <select class="form-control" name="fuel_type" id="" required>
                                    <option value="">--Select--</option>
                                    <option value="super">Super</option>
                                    <option value="Gasoil">Gasoil</option>
                                    <option value="Hybrid">Hybrid</option> 
                                    <option value="100% Electricity">100% Electricity</option>
                                </select>
                                </div>

                                <!-- Enhanced Payment Selection -->
                                <div class="payment-selection-container">
                                    <h5 style="color: #2c3e50; margin-bottom: 20px;">
                                        <i class="fas fa-credit-card"></i> Payment Status Selection
                                    </h5>
                                    
                                    <div class="payment-options">
                                        <label class="payment-option">
                                            <input type="radio" name="payment_status" value="Fully Paid" required>
                                            <div class="option-content">
                                                <div class="option-title">Fully Paid</div>
                                                <div class="option-description">Provider has been paid the complete amount</div>
                                            </div>
                                            <i class="fas fa-check-circle option-icon" style="color: #28a745;"></i>
                                        </label>

                                        <label class="payment-option">
                                            <input type="radio" name="payment_status" value="Half Paid" required>
                                            <div class="option-content">
                                                <div class="option-title">Partially Paid</div>
                                                <div class="option-description">Provider has received part of the payment</div>
                                            </div>
                                            <i class="fas fa-clock option-icon" style="color: #ffc107;"></i>
                                        </label>

                                        <label class="payment-option">
                                            <input type="radio" name="payment_status" value="Unpaid" required>
                                            <div class="option-content">
                                                <div class="option-title">Fully Unpaid</div>
                                                <div class="option-description">No payment made to provider yet</div>
                                            </div>
                                            <i class="fas fa-exclamation-circle option-icon" style="color: #dc3545;"></i>
                                        </label>
                                    </div>

                                    <!-- Partial Payment Input Section -->
                                    <div class="payment-input-section" id="partialPaymentSection">
                                        <h6 style="color: #ffc107; margin-bottom: 15px;"><i class="fas fa-dollar-sign"></i> Enter Partial Payment Amount</h6>
                                        <div class="form-group">
                                            <label>Amount Paid to Provider:</label>
                                            <input type="number" name="paid_amount" id="paidAmountInput" class="form-control" placeholder="Enter amount paid" min="0" step="0.01">
                                            <small class="text-muted">Enter the amount already paid to the provider</small>
                                        </div>

                                        <div class="balance-display" id="balanceDisplay">
                                            <div style="margin-bottom: 5px;">Remaining Balance:</div>
                                            <div class="balance-amount" id="balanceAmount">0 FRW</div>
                                            <input type="hidden" name="calculated_balance" id="calculatedBalance">
                                        </div>                                        
                                    </div>

                                    <!-- Payment Method Section (shows for both Fully Paid and Half Paid) -->
                                    <div class="payment-method-section" id="paymentMethodSection" style="display: none;">
                                        <div class="mb-3">
                                            <label for="Payment Method" class="form-label">Payment Method</label>
                                            <select class="form-control" name="payment_method" id="paymentMethodSelect" required>
                                                <option value="">--Select--</option>
                                                <option value="Momo">Momo</option>
                                                <option value="Bank">Bank</option>                                                
                                            </select>
                                        </div>
                                    </div>
                                </div>
                        
                                <div class="mb-3">
                                    <?php
                                        include "../../web_db/connection.php";
                                        $userEmail=$_SESSION["adminEmail"];                                     
                                        $userSql="SELECT * FROM users WHERE email='$userEmail'";
                                        $userQuery=$conn->query($userSql);
                                        $userData=$userQuery->fetch_assoc();
                                    ?>                                
                                <input type="hidden" class="form-control" id="user_id" value="<?php echo $userData['user_id']?>" name="user_id" required />
                                </div>

                                <div class="text-end mt-4">
                                <input type="submit" value="Save" class="btn btn-primary w-100" name="Save_in_external">                                
                                </div>
                            </form>
                            <!--End of External Car  Registration Form-->


                            

                            <!--Form of Registering  New GuestPro Car-->                            
                            <form class="p-4 shadow rounded bg-white" id="internalForm" style="display: none;" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST">
                                <h4 class="mb-3" style="color: dodgerblue;">Car Details: </h4>
                                <hr />
                                <div class="mb-3">
                                    <label for="car_name" class="form-label">Car Name</label>
                                    <input type="text" class="form-control" id="car_name" name="car_name" placeholder="Enter Car Name" required />
                                </div>

                                <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select name="category_id" id="category" class="form-control" required>
                                    <option value="">-- Select Category --</option>
                                    <?php 
                                    include "../../web_db/connection.php";
                                    $sql = "SELECT category_id, category_name FROM car_categories";
                                    $result = mysqli_query($conn, $sql);                                
                                    $categories_exist = mysqli_num_rows($result) > 0;                                
                                    if ($categories_exist): ?>                                            
                                            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                                <option value="<?php echo $row['category_id']; ?>">
                                                    <?php echo htmlspecialchars($row['category_name']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <option value="" disabled selected>⚠ First Register Category</option>
                                        <?php endif; ?>
                                </select>
                                </div>

                                <div class="mb-3">
                                <label for="Plate_number" class="form-label">Plate Number</label>
                                <input type="text" class="form-control" id="internalPlateNumber" placeholder="Enter Plate Number" name="plateNumber" required />
                                </div>

                                <div class="mb-3">
                                <label for="Plate_number" class="form-label">Car Type</label>
                                <select class="form-control" name="car_type" id="" required>
                                    <option value="">--Select--</option>
                                    <option value="Automatic">Automatic</option>
                                    <option value="Manual">Manual</option>                                    
                                </select>
                                </div>

                                <div class="mb-3">
                                <label for="Plate_number" class="form-label">Fuel Type</label>
                                <select class="form-control" name="fuel_type" id="" required>
                                    <option value="">--Select--</option>
                                    <option value="Super">Super</option>
                                    <option value="Gasoil">Gasoil</option>
                                    <option value="Hybrid">Hybrid</option> 
                                    <option value="100% Electricity">100% Electricity</option>
                                </select>
                                </div>

                                <h4 class="mt-5 mb-3" style="color: dodgerblue;">Insurance : </h4>
                                <hr />
                                <div class="mb-3">
                                <label for="insurance_issue_date" class="form-label">Issued Date:
                                    <span style="color: green;"> Today By Default</span>
                                </label>
                                <input type="date" class="form-control" id="insurance_issued_date" value="<?= date('Y-m-d') ?>" name="insurance_issued_date" required />
                                </div>
                                <div class="mb-3">
                                <label for="insurance_expiry_date" class="form-label">Expiry Date
                                    <span id="insurance_expiry_date_message"></span>
                                </label>
                                <input type="date" class="form-control" id="insurance_expiry_date" name="insurance_expiry_date" required />
                                </div>

                                <h4 class="mt-5 mb-3" style="color: dodgerblue;">Technical Control: </h4>
                                <hr />
                                <div class="mb-3">
                                <label for="technical_control_date" class="form-label">Issue Date:
                                    <span style="color:green;"> Today by Default</span>
                                </label>
                                <input type="date" class="form-control" id="control_issued_date" value="<?= date('Y-m-d') ?>" name="control_issued_date" required />
                                </div>
                                
                                <div class="mb-3">
                                    <label for="insurance_expiry_date" class="form-label">Expiry Date
                                    <span id="control_expiry_date_message"></span>
                                    </label>
                                <input type="date" class="form-control" id="control_expiry_date" name="control_expiry_date" required />

                                <div class="text-end mt-4">
                                <input type="submit" value="Save" class="btn btn-primary w-100" name="Save_in_internal">
                                </div>

                            </form>
                            <!--End of Guest Pro Car  Registration Form-->
                                                                                                            
                        </div>
                     <!-- Content Row -->                    
                    </div>
                                        
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->
            <?php
             include "../../web_includes/footer.php";
            ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>

    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you Sure?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" to Logout from your account.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form action="" method="POST">
                        <input type="submit" name="logout" class="btn btn-primary" style="background-color: red;border:none;" value="Logout"/>
                    </form>
                    <?php
                     if (isset($_POST['logout'])) {
                         session_destroy();
                         session_unset();
                        header("Location: ../../index.php");
                        exit();
                     }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../../js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="../../vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../../js/demo/chart-area-demo.js"></script>
    <script src="../../js/demo/chart-pie-demo.js"></script>
    <script src="../../js/mycustomjs.js"></script>
    <script>
    const formSelector = document.getElementById("formSelector");
    const internalForm = document.getElementById("internalForm");
    const externalForm = document.getElementById("externalForm");

    formSelector.addEventListener("change", function() {
        if (this.value === "internal") {
            internalForm.style.display = "block";
            externalForm.style.display = "none";

        // enable fields
        [...internalForm.elements].forEach(el => el.disabled = false);
        [...externalForm.elements].forEach(el => el.disabled = true);

        } else if (this.value === "external") {
            externalForm.style.display = "block";
            internalForm.style.display = "none";

        // enable fields
            [...externalForm.elements].forEach(el => el.disabled = false);
            [...internalForm.elements].forEach(el => el.disabled = true);

        } else {
            internalForm.style.display = "none";
            externalForm.style.display = "none";
       }
    });

    // AUTOMATIC DAYS CALCULATION SYSTEM
    document.addEventListener("DOMContentLoaded", function () {
        const dateBroughtInput = document.getElementById("date_brought_on");
        const expectedReturnInput = document.getElementById("expected_return_date");
        const daysOfRentInput = document.getElementById("daysInService");    
        const pricePerDayInput = document.getElementById("NegotiatedPrice");
        const totalSpendingInput = document.getElementById("TotalSpending");
        const totalSpendingDisplay = document.getElementById("totalSpendingDisplay");

        // Update return date minimum when brought date changes
        dateBroughtInput.addEventListener("change", function() {
            const broughtDate = this.value;
            expectedReturnInput.min = broughtDate;
            
            // If current return date is before new brought date, clear it
            if (expectedReturnInput.value && expectedReturnInput.value < broughtDate) {
                expectedReturnInput.value = '';
                daysOfRentInput.value = '';
                calculateTotalSpending();
            } else if (expectedReturnInput.value) {
                calculateDays();
            }
        });

        // Calculate days when return date changes
        expectedReturnInput.addEventListener("change", function() {
            calculateDays();
        });

        function calculateDays() {
            const broughtDate = new Date(dateBroughtInput.value);
            const returnDate = new Date(expectedReturnInput.value);
            
            if (broughtDate && returnDate && returnDate >= broughtDate) {
                // Calculate difference in days (+1 to include the first day)
                const timeDiff = returnDate.getTime() - broughtDate.getTime();
                const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;
                
                daysOfRentInput.value = daysDiff;
                calculateTotalSpending();
            } else if (returnDate < broughtDate) {
                // Show validation error
                expectedReturnInput.setCustomValidity("Return date cannot be before brought date");
                expectedReturnInput.reportValidity();
                daysOfRentInput.value = '';
                calculateTotalSpending();
            } else {
                expectedReturnInput.setCustomValidity("");
                daysOfRentInput.value = '';
                calculateTotalSpending();
            }
        }

        function calculateTotalSpending(){
            const days = parseInt(daysOfRentInput.value) || 0;
            const price = parseInt(pricePerDayInput.value) || 0;
            const totalFee = days * price;
            
            totalSpendingInput.value = totalFee;
            totalSpendingDisplay.textContent = totalFee.toLocaleString() + " FRW";
            
            // Recalculate balance if partial payment is selected
            calculateBalance();
        }

        // Update total spending when price changes
        pricePerDayInput.addEventListener("input", calculateTotalSpending);

        // Payment option selection
        const paymentOptions = document.querySelectorAll('input[name="payment_status"]');
        const partialPaymentSection = document.getElementById('partialPaymentSection');
        const paymentMethodSection = document.getElementById('paymentMethodSection');
        const paidAmountInput = document.getElementById('paidAmountInput');
        const paymentMethodSelect = document.getElementById('paymentMethodSelect');

    paymentOptions.forEach(option => {
        option.addEventListener('change', function() {
            // Remove selected class from all options
            document.querySelectorAll('.payment-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Add selected class to current option
            this.closest('.payment-option').classList.add('selected');
            
            if (this.value === 'Half Paid') {
                // Show both amount input AND payment method
                partialPaymentSection.classList.add('show');
                paymentMethodSection.style.display = 'block';
                paidAmountInput.required = true;
                paymentMethodSelect.required = true;
                calculateBalance();
            } else if (this.value === 'Fully Paid') {
                // Show ONLY payment method (no amount input)
                partialPaymentSection.classList.remove('show');
                paymentMethodSection.style.display = 'block';
                paidAmountInput.required = false;
                paidAmountInput.value = '';
                paymentMethodSelect.required = true;
            } else {
                // Unpaid - hide everything
                partialPaymentSection.classList.remove('show');
                paymentMethodSection.style.display = 'none';
                paidAmountInput.required = false;
                paymentMethodSelect.required = false;
                paidAmountInput.value = '';
                paymentMethodSelect.value = '';
            }
        });
    });
    
        // Balance calculation for partial payments
        function calculateBalance() {
            const totalSpending = parseInt(totalSpendingInput.value) || 0;
            const paidAmount = parseInt(paidAmountInput.value) || 0;
            const balance = Math.max(0, totalSpending - paidAmount);
            
            document.getElementById('balanceAmount').textContent = balance.toLocaleString() + ' FRW';
            document.getElementById('calculatedBalance').value = balance;
            
            // Validate that paid amount doesn't exceed total spending
            if (paidAmount > totalSpending && totalSpending > 0) {
                paidAmountInput.value = totalSpending;
                document.getElementById('balanceAmount').textContent = '0 FRW';
                document.getElementById('calculatedBalance').value = 0;
                
                // Show warning
                paidAmountInput.style.borderColor = '#dc3545';
                setTimeout(() => {
                    paidAmountInput.style.borderColor = '#ced4da';
                }, 2000);
            }
        }

        // Listen for paid amount input changes
        paidAmountInput.addEventListener('input', function() {
            // Only allow numbers
            this.value = this.value.replace(/[^0-9]/g, '');
            calculateBalance();
        });

        // Call once on load
        calculateTotalSpending();
    });
  

    //JS FOR ENHANCED REGISTRATION FORM TYPE SELECTION
    document.addEventListener('DOMContentLoaded', function() {
            const optionCards = document.querySelectorAll('.option-card');
            const hiddenSelect = document.getElementById('formSelector');

            optionCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove selected class from all cards
                    optionCards.forEach(c => c.classList.remove('selected'));
                    
                    // Add selected class to clicked card
                    this.classList.add('selected');
                    
                    // Update hidden select value
                    const selectedValue = this.getAttribute('data-value');
                    hiddenSelect.value = selectedValue;
                    
                    // Trigger change event for any form validation
                    hiddenSelect.dispatchEvent(new Event('change'));
                });
            });

            // Initialize with any pre-selected value
            if (hiddenSelect.value) {
                const selectedCard = document.querySelector(`[data-value="${hiddenSelect.value}"]`);
                if (selectedCard) {
                    selectedCard.classList.add('selected');
                }
            }
        });
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