<?php
session_start();
include "../../web_includes/auth.php";
include "../../web_db/connection.php";

// Cancel Booking Handler
if (isset($_POST['cancel_booking'])){
    $booking_id = $_POST['cancel_booking_id'];
    $car_id = $_POST['cancel_car_id'];
    $car_name = $_POST['cancel_car_name'];
    
    // Update booking status to cancelled in bookings table
    $cancelBookingSql = "UPDATE bookings SET booking_status='cancelled' WHERE booking_id=?";
    $stmt = $conn->prepare($cancelBookingSql);
    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        // Update car status back to available
        $updateCarSql = "UPDATE cars SET booking_status='Unbooked' WHERE car_id=?";
        $stmtCar = $conn->prepare($updateCarSql);
        $stmtCar->bind_param("i", $car_id);
        $stmtCar->execute();
        
        echo "
        <div id='successAlertBox' style='position: fixed; top: 20px; right: 20px; z-index: 9999; background: linear-gradient(135deg, #1cc88a, #13855c); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.3);'>
            <i class='fas fa-check-circle'></i> Booking for $car_name has been cancelled successfully!
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function(){
            const alertBox = document.getElementById('successAlertBox');
            setTimeout(() => {
                alertBox.style.transform = 'translateX(100%)';
                alertBox.style.opacity = '0';
                setTimeout(() => {
                    alertBox.remove();
                    window.location.href = '';
                }, 500);
            }, 3000);
        });
        </script>
        ";
    } else {
        echo "
        <div id='alertBox' style='position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; background: #f8d7da; color: red; padding: 15px 20px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);'>
            ⚠️ Error: Failed to cancel booking.
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertBox = document.getElementById('alertBox');
            setTimeout(() => {
                alertBox.style.opacity = 0;
                setTimeout(() => alertBox.remove(), 500);
            }, 3000);
        });
        </script>
        ";
    }
}


if (isset($_POST['delete'])){
    $car_id = $_POST['delete_id'];
    $sql = "SELECT status FROM cars WHERE car_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();

    if ($car['status'] === 'rented'){
        echo "
        <div id='alertBox' style='position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; background: #f8d7da; color: red; padding: 15px 20px; border-radius: 8px;'>
            ⚠️ Error: This Car Can't be Deleted Until It's marked as Available.
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertBox = document.getElementById('alertBox');
            setTimeout(() => {
                alertBox.style.opacity = 0;
                setTimeout(() => alertBox.remove(), 500);
                window.location.href='car_overview.php'
            }, 3000);
        });
        </script>
        ";
    } else {

        $deleteRentals = $conn->prepare("DELETE FROM rentals WHERE car_id = ?");
        $deleteRentals->bind_param("i", $car_id);
        $deleteRentals->execute();

        $deleteCar = $conn->prepare("DELETE FROM cars WHERE car_id = ?");
        $deleteCar->bind_param("i", $car_id);

        if ($deleteCar->execute()) {
            include "../../system_messages/deleteMessage.php";
        } else {
            echo "<script>alert('Failed to delete car');</script>";
        }
    }
}

if (isset($_POST["return_car"])){
    $carId=$_POST["carId"];
    $carName=$_POST["car_name"];
    $CarProvider=$_POST["car_provider"];
    
    $SqlCheck = "SELECT status FROM external_cars WHERE car_id = ?";
    $stmt = $conn->prepare($SqlCheck);
    $stmt->bind_param("i", $carId);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();
    
    if ($car["status"]=="rented"){
        echo "
        <div id='alertBox'>
            ⚠️:This Car Can't be Returned Back to the Provider until It's marked as Available.
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertBox = document.getElementById('alertBox');
            setTimeout(() => {
                alertBox.style.opacity = 0;
                setTimeout(() => alertBox.remove(), 500);
                window.location.href='car_overview.php'
            }, 3000);
        });
        </script>
        ";
    } else {
        $UpdateRecord=$conn->prepare("UPDATE external_cars SET lifecycle_status='returned' WHERE car_id = ? AND");
        $UpdateRecord->bind_param("i", $carId);
        $UpdateRecord->execute();
        if ($UpdateRecord->execute()){
            echo"
                <div id='successAlertBox' style='position: fixed; top: 20px; right: 20px; z-index: 9999; background: linear-gradient(135deg, #1cc88a, #13855c); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.3);'>
                    <i class='fas fa-check-circle'></i> $carName has Been Returned Back to the $CarProvider Successfully.
                </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function(){
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

if (isset($_POST["process_payment"])){
    $car_name=$_POST["car_name"];
    $car_id=$_POST["car_id"];
    $newBalance=$_POST["new_balance"];
    $paymentMethod=$_POST["payment_method"];

    $selectQuery=$conn->query("SELECT balance FROM external_cars WHERE car_id=$car_id");
    $row=$selectQuery->fetch_assoc();
    
    if ($newBalance==$row["balance"]){
        $UpdateQuery=$conn->prepare("UPDATE external_cars SET balance=?, payment_method=?, use_status='Fully Paid' WHERE car_id=?");
        $UpdateQuery->bind_param('isi', $newBalance,$paymentMethod,$car_id);
        $UpdateQuery->execute();
        if ($UpdateQuery->execute()){
            echo"
                <div id='successAlertBox' style='position: fixed; top: 20px; right: 20px; z-index: 9999; background: linear-gradient(135deg, #1cc88a, #13855c); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.3);'>
                    <i class='fas fa-check-circle'></i> $car_name External Debt Provision is Fully Cleared via $paymentMethod Transcation.
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
    }else{
        $UpdateQuery2=$conn->prepare("UPDATE external_cars SET balance=?, payment_method=?, use_status='Half Paid'  WHERE car_id=?");
        $UpdateQuery2->bind_param('isi', $newBalance,$paymentMethod,$car_id);
        $UpdateQuery2->execute();
        if ($UpdateQuery2->execute()){
            echo"
                <div id='successAlertBox' style='position: fixed; top: 20px; right: 20px; z-index: 9999; background: linear-gradient(135deg, #f6c23e, #d1a20a); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.3);'>
                    <i class='fas fa-check-circle'></i> $car_name External Rent is now Partially Paid via $paymentMethod Transcation.
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
    <title>GuestPro CMS| Car Overview</title>
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link  href="../../css/custom.css" rel="stylesheet">
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/overview.css">
    <link rel="icon" href="../../img/GuestProLogoReal.JPG" type="image/png">
    
    <style>
        .booking-search-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 25px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.booking-search-section h5 {
    color: white;
    font-weight: bold;
    margin-bottom: 15px;
}

.booking-search-section p {
    color: rgba(255,255,255,0.9);
    font-size: 14px;
    margin-bottom: 20px;
}

.booking-date-input-wrapper {
    display: flex;
    gap: 15px;
    align-items: end;
    flex-wrap: wrap; /* NEW: Allow wrapping on small screens */
}

.booking-date-input-wrapper > div {
    flex: 1;
    min-width: 200px; /* NEW: Ensure minimum width before wrapping */
}

.booking-date-input-wrapper input {
    width: 100%; /* NEW: Make input full width of container */
    padding: 12px 15px;
    border-radius: 8px;
    border: 2px solid rgba(255,255,255,0.3);
    font-size: 15px;
    background: rgba(255,255,255,0.95);
}

.booking-date-input-wrapper button {
    padding: 12px 20px; /* UPDATED: Reduced from 30px to 20px */
    background: white;
    color: #667eea;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    white-space: nowrap;
    flex-shrink: 0; /* NEW: Prevent buttons from shrinking */
}

.booking-date-input-wrapper button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.booking-indicator {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    margin-left: 8px;
    animation: pulse 2s infinite;
}

.booking-indicator.booked {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.booking-indicator.unbooked {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.booking-info-cell {
    font-size: 13px;
    padding: 8px !important;
}

.booking-info-cell .booking-detail {
    margin: 3px 0;
    display: flex;
    align-items: center;
    gap: 5px;
}

.booking-info-cell .booking-detail i {
    color: #667eea;
    width: 16px;
}

.booking-amount {
    color: #28a745;
    font-weight: bold;
}

.booking-dates {
    color: #6c757d;
    font-size: 12px;
}

.car-row.highlighted-available {
    background: linear-gradient(90deg, rgba(76, 175, 80, 0.1) 0%, rgba(255,255,255,0) 100%) !important;
    border-left: 4px solid #4caf50;
}

.availability-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    animation: glow 2s infinite;
}

@keyframes glow {
    0%, 100% { box-shadow: 0 0 10px rgba(56, 239, 125, 0.5); }
    50% { box-shadow: 0 0 20px rgba(56, 239, 125, 0.8); }
}

.search-result-info {
    background: #e3f2fd;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 15px;
    display: none;
    border-left: 4px solid #2196F3;
}

.search-result-info.show {
    display: block;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.clear-search-btn {
    background: #dc3545 !important;
    color: white !important;
}

.clear-search-btn:hover {
    background: #c82333 !important;
}

.cancel-booking-btn {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%) !important;
}

.cancel-booking-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
}

/* ===== NEW MOBILE RESPONSIVE STYLES ===== */
@media (max-width: 768px) {
    .booking-search-section {
        padding: 20px 15px;
    }
    
    .booking-search-section h5 {
        font-size: 18px;
    }
    
    .booking-search-section p {
        font-size: 13px;
    }
    
    .booking-date-input-wrapper {
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
    }
    
    .booking-date-input-wrapper > div {
        width: 100%;
        min-width: unset;
    }
    
    .booking-date-input-wrapper button {
        width: 100%;
        padding: 12px 15px;
        font-size: 14px;
    }
    
    .booking-date-input-wrapper input {
        font-size: 16px; /* Prevent zoom on iOS */
    }
    
    .search-result-info {
        padding: 12px;
        font-size: 13px;
    }
    
    .search-result-info span {
        display: block;
        float: none !important;
        margin-top: 8px;
    }
}

/* Extra small devices */
@media (max-width: 480px) {
    .booking-date-input-wrapper button {
        font-size: 13px;
        padding: 10px 12px;
    }
    
    .booking-search-section h5 {
        font-size: 16px;
    }
}

.car-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.detail-card {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.detail-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.detail-card-header {
    font-size: 14px;
    font-weight: bold;
    color: #667eea;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #667eea;
    display: flex;
    align-items: center;
    gap: 8px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #495057;
    display: flex;
    align-items: center;
    gap: 8px;
}

.detail-value {
    font-weight: 500;
    color: #212529;
    text-align: right;
}

.status-badge {
    display: inline-block;
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}

.status-available {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.status-rented {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.booking-status-booked {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    color: white;
}

.booking-status-unbooked {
    background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
    color: white;
}

.date-highlight {
    background: #fff3cd;
    padding: 3px 8px;
    border-radius: 5px;
    font-weight: bold;
    color: #856404;
}

.date-expired {
    background: #f8d7da;
    padding: 3px 8px;
    border-radius: 5px;
    font-weight: bold;
    color: #721c24;
}

.date-valid {
    background: #d4edda;
    padding: 3px 8px;
    border-radius: 5px;
    font-weight: bold;
    color: #155724;
}

.booking-details-section {
    background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);
    padding: 20px;
    border-radius: 15px;
    margin-top: 20px;
}

.booking-details-section h6 {
    color: #4a148c;
    font-weight: bold;
    margin-bottom: 15px;
}

.clickable-row {
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.clickable-row:hover {
    background-color: rgba(102, 126, 234, 0.1) !important;
}

/* Print styles */
@media print {
    body * {
        visibility: hidden;
    }
    #carDetailsContent, #carDetailsContent * {
        visibility: visible;
    }
    #carDetailsContent {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .modal-header, .modal-footer {
        display: none !important;
    }
    .detail-card {
        break-inside: avoid;
        page-break-inside: avoid;
    }
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
                    <div style="display: flex;justify-content:space-between;">
                        <h1 class="h3 mb-2 text-gray-800">All Cars Overview</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
                        </a>
                    </div><br>

                    <!-- Booking Availability Search Section -->
                    <div class="booking-search-section">
                        <h5><i class="fas fa-calendar-search"></i> Check Car Availability by Date</h5>
                        <p><i class="fas fa-info-circle"></i> Enter a date to find which cars will be available for booking on that specific day. The system will show cars that are either unbooked or will be returned by that date.</p>
                        
                        <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="booking-date-input-wrapper">
                                <div style="flex: 1;">
                                    <label style="color: white; font-size: 13px; margin-bottom: 5px; display: block;">
                                        <i class="fas fa-calendar-alt"></i> Select Date:
                                    </label>
                                    <input type="date" name="availability_date" id="availability_date" 
                                           min="<?php echo date('Y-m-d'); ?>" 
                                           value="<?php echo isset($_GET['availability_date']) ? $_GET['availability_date'] : ''; ?>"
                                           required>
                                </div>
                                <button type="submit" name="search_availability">
                                    <i class="fas fa-search"></i> Search Available Cars
                                </button>
                                <?php if(isset($_GET['availability_date'])): ?>
                                <button type="button" class="clear-search-btn" onclick="window.location.href='<?php echo $_SERVER['PHP_SELF']; ?>'">
                                    <i class="fas fa-times"></i> Clear
                                </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <?php if(isset($_GET['search_availability']) && isset($_GET['availability_date'])): ?>
                    <div class="search-result-info show">
                        <i class="fas fa-check-circle" style="color: #2196F3;"></i>
                        <strong>Search Results:</strong> Showing cars available on <strong><?php echo date('F d, Y', strtotime($_GET['availability_date'])); ?></strong>
                        <span style="float: right; color: #28a745;">
                            <i class="fas fa-car"></i> Available cars are highlighted in green
                        </span>
                    </div>
                    <?php endif; ?>

                    <div class="card shadow mb-4 enhanced-card">
                        <div class="card-header py-3 enhanced-header">
                            <h6 class="m-0 font-weight-bold">(Internal) GuestPro Cars Info with Booking Details</h6>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <div class="enhanced-table-container">
                                <div class="enhanced-controls">
                                    <div class="enhanced-search-container">
                                        <div class="enhanced-search-icon">🔍</div>
                                        <input type="text" class="enhanced-search-input" 
                                               placeholder="Search cars by any field..." 
                                               id="enhancedSearchInput">
                                    </div>

                                    <div class="enhanced-view-toggle">
                                        <button class="enhanced-toggle-btn active" data-view="internal">
                                            🏢 Internal Cars
                                        </button>
                                        <button class="enhanced-toggle-btn" data-view="external">
                                            🌐 External Cars
                                        </button>
                                    </div>

                                    <div class="enhanced-entries-info" id="enhancedEntriesInfo">
                                        Showing <span id="enhancedVisibleCount">0</span> of <span id="enhancedTotalCount">0</span> entries
                                    </div>
                                </div>

                                <div class="enhanced-loading" id="enhancedLoading">
                                    <div class="enhanced-spinner"></div>
                                    <p>Loading data...</p>
                                </div>

                                <div class="enhanced-table-wrapper" id="enhancedTableContainer">
                                    <table class="enhanced-data-table" id="enhancedDataTable">
                                        <thead id="enhancedTableHead"></thead>
                                        <tbody id="enhancedTableBody"></tbody>
                                    </table>
                                </div>

                                <div class="enhanced-no-results" id="enhancedNoResults">
                                    <div class="enhanced-no-results-icon">🔍</div>
                                    <h5>No results found</h5>
                                    <p>Try adjusting your search criteria</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include "../../web_includes/footer.php"; ?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <!--Delete Modal-->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Are you Sure to delete <b><strong id="carName"></strong></b>?</h5>                
                    <button class="close" type="button" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Confirm "Delete" to Permanently Delete: </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form action="" method="POST">       
                        <input type="hidden" name="delete_id" id="hiddenCarId" />                 
                        <input type="submit" name="delete" class="btn btn-primary" style="background-color: red;border:none;" value="Delete"/>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--Cancel Booking Modal-->
    <div class="modal fade" id="cancelBookingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Cancel Booking Confirmation
                </h5>                
                <button class="close" type="button" data-dismiss="modal" style="color: white;">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 25px;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <i class="fas fa-calendar-times" style="font-size: 3rem; color: #ff6b6b;"></i>
                </div>
                <h5 style="text-align: center; margin-bottom: 15px;">
                    Cancel booking for <strong id="cancelCarName" style="color: #ff6b6b;"></strong>?
                </h5>
                <div style="background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107; margin-bottom: 15px;">
                    <p style="margin: 0; color: #856404;">
                        <i class="fas fa-info-circle"></i> <strong>Warning:</strong> This action will:
                    </p>
                    <ul style="margin: 10px 0 0 20px; color: #856404;">
                        <li>Mark the booking as <strong>Cancelled</strong></li>
                        <li>Free up the car for new bookings</li>
                        <li>Set car status back to <strong>Unbooked</strong></li>
                    </ul>
                </div>
                <p style="text-align: center; color: #6c757d;">
                    Are you sure you want to proceed with cancelling this booking?
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">
                    <i class="fas fa-times"></i> No, Keep Booking
                </button>
                <form action="" method="POST">       
                    <input type="hidden" name="cancel_booking_id" id="hiddenCancelBookingId" />
                    <input type="hidden" name="cancel_car_id" id="hiddenCancelCarId" />
                    <input type="hidden" name="cancel_car_name" id="hiddenCancelCarName" />                 
                    <input type="submit" name="cancel_booking" class="btn btn-danger" value="Yes, Cancel Booking"/>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Car Details View Modal -->
<div class="modal fade" id="carDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-bottom: none; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title">
                    <i class="fas fa-car"></i> Complete Car Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.9;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="carDetailsContent" style="padding: 30px;">
                <!-- Content will be dynamically inserted here -->
            </div>
            <div class="modal-footer" style="border-top: 2px solid #f0f0f0; padding: 20px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-primary" onclick="printCarDetails()" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <i class="fas fa-print"></i> Print Details
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Are you Sure?</h5>
                    <button class="close" type="button" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
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
    <script src="../../vendor/chart.js/Chart.min.js"></script>
    <script src="../../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../../vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="../../js/mycustomjs.js"></script>

    <script>
        let currentView = 'internal';
        let internalCarsData = [];
        let externalCarsData = [];
        let allRows = [];
        let filteredRows = [];
        let searchDate = '<?php echo isset($_GET["availability_date"]) ? $_GET["availability_date"] : ""; ?>';

        $(document).ready(function() {
            loadInitialData();
            
            $(document).on('click', '.delete-btn', function () {
                var carId = $(this).data('id');
                var carName = $(this).data('name');
                $('#carName').text(carName);
                $('#hiddenCarId').val(carId);
            });
            
            $(document).on('click', '.cancel-booking-btn', function () {
                var bookingId = $(this).data('booking-id');
                var carId = $(this).data('car-id');
                var carName = $(this).data('name');
                $('#cancelCarName').text(carName);
                $('#hiddenCancelBookingId').val(bookingId);
                $('#hiddenCancelCarId').val(carId);
                $('#hiddenCancelCarName').val(carName);
            });
        });

        function loadInitialData() {
            loadInternalCars();
        }

        function loadInternalCars() {
            showLoading();
            loadInternalCarsFromDOM();
            hideLoading();
        }

        function loadInternalCarsFromDOM() {
            <?php
            $carDataJS = [];            
            $whereClause = "";
            $isSearching = false;
        
            if (isset($_GET['search_availability']) && isset($_GET['availability_date'])) {
                $searchDate = mysqli_real_escape_string($conn, $_GET['availability_date']);
                $isSearching = true;
                
                // Check cars that are either:
                // 1. Not booked at all (no active/pending bookings)
                // 2. Have bookings that don't overlap with the search date
                $whereClause = " WHERE c.car_id NOT IN (
                    SELECT car_id FROM bookings 
                    WHERE booking_status IN ('pending', 'active')
                    AND booking_date <= '$searchDate' 
                    AND booking_return_date >= '$searchDate'
                )";
            }
            
            // Join cars with bookings to get booking details
            $sql = "SELECT c.*, 
                    c.insurance_issued_date,
                    c.insurance_expiry_date,
                    c.control_issued_date,
                    c.control_expiry_date,
                    b.booking_id,
                    b.customer_name,
                    b.customer_national_id,
                    b.customer_phone,
                    b.booking_date,
                    b.booking_return_date,
                    b.booking_amount,
                    b.booking_status as current_booking_status
                    FROM cars c
                    LEFT JOIN bookings b ON c.car_id = b.car_id 
                    AND b.booking_status IN ('pending', 'active')
                    " . $whereClause . "
                    ORDER BY c.car_name ASC";
            
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $count = 0;
                while ($row = $result->fetch_assoc()) {
                    $count++;
                    $category_id = $row["category_id"];
                    $stmt = $conn->prepare("SELECT category_name FROM car_categories WHERE category_id = ?");
                    $stmt->bind_param("i", $category_id);
                    $stmt->execute();
                    $resultCategory = $stmt->get_result();
                    $rowCategory = $resultCategory->fetch_assoc();
                    $stmt->close();
                    
                    // Determine if car is available on search date
                    $isAvailable = false;
                    if ($isSearching && empty($row['booking_id'])) {
                        $isAvailable = true;
                    }
                    
                    // Determine booking status based on bookings table
                    $bookingStatus = empty($row['booking_id']) ? 'Unbooked' : 'booked';
                    
                    $carDataJS[] = [
                        'id' => $row['car_id'],
                        'count' => $count,
                        'car_name' => $row['car_name'],
                        'category' => $rowCategory['category_name'] ?? 'N/A',
                        'plate_number' => $row['plate_number'],
                        'type' => $row['type'],
                        'fuel_type' => $row['fuel_type'],                        
                        'status' => $row['status'],
                        'booking_status' => $bookingStatus,
                        // Added new fields:
                        'insurance_issued_date' => $row['insurance_issued_date'] ?? null,
                        'insurance_expiry_date' => $row['insurance_expiry_date'] ?? null,
                        'control_issued_date' => $row['control_issued_date'] ?? null,
                        'control_expiry_date' => $row['control_expiry_date'] ?? null,
                        // Existing booking fields:
                        'booking_id' => $row['booking_id'],
                        'booking_date' => $row['booking_date'],
                        'booking_return_date' => $row['booking_return_date'],
                        'booking_amount' => $row['booking_amount'],
                        'customer_name' => $row['customer_name'],
                        'customer_national_id' => $row['customer_national_id'],
                        'customer_phone' => $row['customer_phone'],
                        'is_available_on_search_date' => $isAvailable
                    ];
                }
            }
            echo "internalCarsData = " . json_encode($carDataJS) . ";";
            ?>
            
            renderTable();
        }

        function loadExternalCars() {
            showLoading();
            loadExternalCarsFromPHP();
        }

        function loadExternalCarsFromPHP() {
            <?php
            $externalCarDataJS = [];
            $sqlExternal = "SELECT ec.*, u.username as user_name FROM external_cars ec LEFT JOIN users u ON ec.user_id = u.user_id WHERE ec.lifecycle_status = 'active'";
            $resultExternal = $conn->query($sqlExternal);
            if ($resultExternal && $resultExternal->num_rows > 0) {
                $count = 0;
                while ($row = $resultExternal->fetch_assoc()) {
                    $count++;
                    $externalCarDataJS[] = [
                        'id' => $row['car_id'] ?? $count,
                        'count' => $count,
                        'car_name' => $row['car_name'],
                        'provider' => $row['provider'],
                        'negotiated_price' => $row['negotiated_price'],
                        'days_in_service' => $row['days_in_service'],
                        'total_spending' => $row['total_spending'],
                        'plate_number' => $row['plate_number'],
                        'type' => $row['type'],
                        'fuel_type' => $row['fuel_type'],
                        'user_name' => $row['user_name'] ?? 'N/A',
                        'status' => $row['status'],
                        'balance' => $row['balance'],
                        'use_status' => $row['use_status'],
                        'payment_method' => $row['payment_method']
                    ];
                }
            }
            echo "externalCarsData = " . json_encode($externalCarDataJS) . ";";
            ?>
            
            renderTable();
            hideLoading();
        }

        function showLoading() {
            document.getElementById('enhancedLoading').style.display = 'block';
            document.getElementById('enhancedTableContainer').style.display = 'none';
            document.getElementById('enhancedNoResults').classList.remove('show');
        }

        function hideLoading() {
            document.getElementById('enhancedLoading').style.display = 'none';
            document.getElementById('enhancedTableContainer').style.display = 'block';
        }

        function renderTable() {
            const currentData = currentView === 'internal' ? internalCarsData : externalCarsData;
            updateTableHeaders();
            
            const tableBody = document.getElementById('enhancedTableBody');
            tableBody.innerHTML = '';
            
            currentData.forEach((car, index) => {
                const row = createTableRow(car, index);
                tableBody.appendChild(row);
            });
            
            allRows = Array.from(document.querySelectorAll('#enhancedTableBody tr'));
            filteredRows = [...allRows];
            
            applyStatusStyling();
            addAnimationDelays();
            updateEntriesInfo();
            
            document.getElementById('enhancedSearchInput').value = '';
        }

        function updateTableHeaders() {
            const tableHead = document.getElementById('enhancedTableHead');
            
            if (currentView === 'internal') {
                tableHead.innerHTML = `
                    <tr>
                        <th>N#</th>
                        <th style="min-width:120px;">Name</th>
                        <th>Category</th>
                        <th style="width: 90px;">Plate</th>
                        <th>Type</th>
                        <th>Fuel</th>
                        <th>Booking Status</th>
                        <th style="min-width: 200px;">Booking & Customer Details</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                `;
            } else {
                tableHead.innerHTML = `
                    <tr>
                        <th>N#</th>
                        <th style="min-width:120px;">Car Name</th>
                        <th>Company</th>
                        <th>Price Negotiated</th>
                        <th>Duration</th>
                        <th>Total</th>
                        <th style="width: 90px;">Plate</th>
                        <th>Type</th>
                        <th>Fuel</th>
                        <th>Rented By</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                `;
            }
        }      
        
        function createTableRow(car, index) {
            const row = document.createElement('tr');
            row.className = 'car-row';
            row.style.animationDelay = `${index * 0.1}s`;
            
            if (searchDate && car.is_available_on_search_date) {
                row.classList.add('highlighted-available');
            }
            
            if (currentView === 'internal') {
                    const bookingBadge = car.booking_status === 'booked' 
                        ? '<span class="booking-indicator booked"><i class="fas fa-calendar-check"></i> BOOKED</span>'
                        : '<span class="booking-indicator unbooked"><i class="fas fa-calendar-plus"></i> UNBOOKED</span>';
                    
                    let bookingDetails = '-';
                    let cancelButton = '';
                    
                    if (car.booking_status === 'booked' && car.booking_date) {
                        bookingDetails = `
                            <div class="booking-info-cell">
                                <div class="booking-detail">
                                    <i class="fas fa-user"></i>
                                    <span><strong>${car.customer_name || 'N/A'}</strong></span>
                                </div>
                                <div class="booking-detail">
                                    <i class="fas fa-id-card"></i>
                                    <span>${car.customer_national_id || 'N/A'}</span>
                                </div>
                                <div class="booking-detail">
                                    <i class="fas fa-phone"></i>
                                    <span>${car.customer_phone || 'N/A'}</span>
                                </div>
                                <hr style="margin: 8px 0; border-color: #e3e6f0;">
                                <div class="booking-detail">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span class="booking-dates">From: ${formatDate(car.booking_date)}</span>
                                </div>
                                <div class="booking-detail">
                                    <i class="fas fa-calendar-check"></i>
                                    <span class="booking-dates">To: ${formatDate(car.booking_return_date)}</span>
                                </div>
                                <div class="booking-detail">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span class="booking-amount">${parseInt(car.booking_amount || 0).toLocaleString()} RWF</span>
                                </div>
                            </div>
                        `;
                        
                        cancelButton = `
                            <button type="button" class="enhanced-action-btn cancel-booking-btn" 
                                    title="Cancel Booking" data-toggle="modal"
                                    data-target="#cancelBookingModal" 
                                    data-booking-id="${car.booking_id}"
                                    data-car-id="${car.id}" 
                                    data-name="${car.car_name}"
                                    style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); color: white; margin-left: 5px;">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        `;
                    }
                    
                    const availabilityBadge = searchDate && car.is_available_on_search_date 
                        ? '<div class="availability-badge"><i class="fas fa-check-circle"></i> Available on Selected Date</div>'
                        : '';
                    
                    row.innerHTML = `
                        <td>${car.count}</td>
                        <td>
                            <strong>${car.car_name}</strong>
                            ${availabilityBadge}
                        </td>
                        <td>${car.category}</td>
                        <td style="font-size: 14px;"><code>${car.plate_number}</code></td>
                        <td>${car.type}</td>
                        <td>${car.fuel_type}</td>
                        <td>${bookingBadge}</td>
                        <td>${bookingDetails}</td>
                        <td class="status-cell">${car.status}</td>
                        <td style="white-space: nowrap;">
                            <form action="updateCar.php" method="GET" style="display: inline;">
                            <input type="hidden" value="${car.id}" name="car_id">
                            <button title="Edit ✍" type="submit" name="edit" class="enhanced-action-btn" style="background:green; color: white;">
                                <i class="fas fa-edit"></i></button>
                            </form>
                            <button type="button" class="enhanced-action-btn delete-btn" 
                                    title="⚠ Delete" data-toggle="modal"
                                    data-target="#deleteModal" data-id="${car.id}" 
                                    data-name="${car.car_name}"
                                    style="background:red; color: white;"><i class="fas fa-trash"></i>
                            </button>
                            ${cancelButton}
                        </td>
                    `;
                } else {
                    const balance = parseFloat(car.balance || 0);
                    const totalSpending = parseFloat(car.total_spending || 0);
                    let paymentStatus = '';
                    let statusClass = '';
                    let statusIcon = '';
                
                if (balance === 0) {
                    paymentStatus = 'FULLY PAID';
                    statusClass = 'payment-fully-paid';
                    statusIcon = 'fas fa-check-circle';
                } else if (balance > 0 && balance < totalSpending) {
                    paymentStatus = 'HALF PAID';
                    statusClass = 'payment-partial-paid';
                    statusIcon = 'fas fa-clock';
                } else {
                    paymentStatus = 'UNPAID';
                    statusClass = 'payment-unpaid';
                    statusIcon = 'fas fa-exclamation-circle';
                }

                const paymentButton = balance > 0 ? `
                    <button type="button" class="enhanced-action-btn payment-btn" 
                            title="Make Payment" data-toggle="modal"
                            data-target="#paymentModal_${car.id}" 
                            style="background: linear-gradient(135deg, #f39c12, #e67e22); color: white; margin-right: 5px;">
                        <i class="fas fa-dollar-sign"> Pay the Debts</i>
                    </button>
                ` : '';

                const ReturnButton = balance === 0 ? `
                    <button type="button" title="Return Back ${car.car_name}" class="enhanced-action-btn return-btn" 
                            title="Return Back ${car.car_name}" data-toggle="modal"
                            data-target="#returnModal_${car.id}" 
                            style="background: green; color: white;">
                        <i class="fas fa-undo"></i>
                    </button>
                ` : '';

                row.innerHTML = `
                    <td>${car.count}</td>
                    <td>
                        <strong>${car.car_name}</strong>
                        <div class="payment-indicator ${statusClass}">
                            <i class="${statusIcon}"></i> ${paymentStatus}
                        </div>
                    </td>
                    <td>${car.provider}</td>
                    <td>${parseFloat(car.negotiated_price).toLocaleString()} FRW</td>
                    <td>${car.days_in_service} days</td>
                    <td>${parseFloat(car.total_spending).toLocaleString()} FRW</td>
                    <td style="font-size: 14px;"><code>${car.plate_number}</code></td>
                    <td>${car.type}</td>
                    <td>${car.fuel_type}</td>
                    <td>${car.user_name}</td>
                    <td class="status-cell">${car.status}</td>
                    <td>
                        ${paymentButton}                        
                        ${ReturnButton}                     
                    </td>
                `;

                if (balance > 0){
                    setTimeout(() => createPaymentModal(car), 100);
                }
                
                if (balance === 0) {
                    setTimeout(() => createReturnModal(car), 100);
                }
            }
            
            return row;
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        function setupEventListeners() {
            const searchInput = document.getElementById('enhancedSearchInput');
            searchInput.addEventListener('input', (e) => {
                performSearch(e.target.value);
            });

            document.querySelectorAll('.enhanced-toggle-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    switchView(btn.dataset.view);
                });
            });
        }

        function performSearch(query) {
            if (!query.trim()) {
                filteredRows = [...allRows];
            } else {
                filteredRows = allRows.filter(row => {
                    const text = row.textContent.toLowerCase();
                    return text.includes(query.toLowerCase());
                });
            }
            
            renderFilteredRows();
        }

        function renderFilteredRows(){
            allRows.forEach(row => {
                row.style.display = 'none';
            });

            if (filteredRows.length === 0) {
                document.getElementById('enhancedNoResults').classList.add('show');
                document.getElementById('enhancedTableContainer').style.display = 'none';
            } else {
                document.getElementById('enhancedNoResults').classList.remove('show');
                document.getElementById('enhancedTableContainer').style.display = 'block';
                
                filteredRows.forEach((row, index) => {
                    row.style.display = 'table-row';
                    row.style.animationDelay = `${index * 0.05}s`;
                });
            }

            updateEntriesInfo();
        }

        function applyStatusStyling() {
            document.querySelectorAll('.status-cell').forEach(cell => {
                const statusText = cell.textContent.trim().toLowerCase();
                if (statusText === 'available') {
                    cell.classList.add('enhanced-status-available');
                } else if (statusText === 'rented') {
                    cell.classList.add('enhanced-status-rented');
                }
            });
        }

        function addAnimationDelays() {
            allRows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.1}s`;
            });
        }

        function updateEntriesInfo() {
            document.getElementById('enhancedVisibleCount').textContent = filteredRows.length;
            document.getElementById('enhancedTotalCount').textContent = allRows.length;
        }

        function switchView(view) {
            if (view === currentView) return;
            
            document.querySelectorAll('.enhanced-toggle-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.view === view);
            });

            currentView = view;
            
            if (view === 'external') {
                if (externalCarsData.length === 0) {
                    loadExternalCars();
                } else {
                    renderTable();
                }
            } else {
                renderTable();
            }
        }

        $(document).ready(function() {
            setupEventListeners();
            loadInitialData();
        });

        function createReturnModal(car){
            const modalId = `returnModal_${car.id}`;        
            $(`#${modalId}`).remove();

            const modal = `
                <div class="modal fade enhanced-modal" id="${modalId}" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
                            <div class="modal-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 25px; border-bottom: none; border-radius: 20px 20px 0 0;">
                                <h5 class="modal-title">
                                    <i class="fas fa-undo"></i> Return Car to Provider
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" style="padding: 30px; text-align: center;">
                                <div style="margin-bottom: 20px;">
                                    <i class="fas fa-car" style="font-size: 4rem; color: #28a745; margin-bottom: 20px;"></i>
                                </div>
                                <h4 style="color: #2c3e50; margin-bottom: 15px;">Return ${car.car_name} to Provider?</h4>
                                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                                    <p><strong>Provider:</strong> ${car.provider}</p>
                                    <p><strong>Plate Number:</strong> ${car.plate_number}</p>
                                    <p><strong>Total Paid:</strong> ${parseFloat(car.total_spending).toLocaleString()} FRW</p>
                                    <p style="color: #28a745; font-weight: bold;"><i class="fas fa-check-circle"></i> Payment Status: FULLY PAID</p>
                                    <p>Via: <strong>${car.payment_method}</strong> Transaction</p>
                                </div>
                                <p style="color: #6c757d;">Are you sure you want to return this car back to the provider? This action will mark the rental as complete.</p>
                                
                                <form method="POST" action="">
                                    <input type="hidden" name="carId" value="${car.id}">
                                    <input type="hidden" name="car_name" value="${car.car_name}">
                                    <input type="hidden" name="car_provider" value="${car.provider}">
                                    
                                    <div style="margin-top: 30px;">
                                        <button type="button" class="btn btn-secondary mr-3" data-dismiss="modal">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                        <button type="submit" name="return_car" class="btn btn-success btn-lg">
                                            <i class="fas fa-check"></i> Yes, Return Car
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('body').append(modal);
        }
        
        function createPaymentModal(car) {
            const modalId = `paymentModal_${car.id}`;
            $(`#${modalId}`).remove();

            const balance = parseFloat(car.balance || 0);
            const totalSpending = parseFloat(car.total_spending || 0);
            const paidAmount = totalSpending - balance;

            const modal = `
                <div class="modal fade payment-modal" id="${modalId}" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-dollar-sign"></i> Payment Details for <strong>${car.car_name}</strong>
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" style="color: white;">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="payment-detail-card">
                                            <h6><i class="fas fa-car text-primary"></i> Car Details</h6>
                                            <p><strong>Car Name:</strong> ${car.car_name}</p>                                            
                                            <p><strong>Provider:</strong> ${car.provider}</p>
                                            <p><strong>Plate Number:</strong> ${car.plate_number}</p>
                                            <p><strong>Use Status:</strong> ${car.use_status}</p>
                                        </div>
                                        <div class="payment-detail-card">
                                            <h6><i class="fas fa-info-circle text-info"></i> Service Details</h6>
                                            <p><strong>Days in Service:</strong> ${car.days_in_service} days</p>
                                            <p><strong>Daily Price:</strong> ${parseFloat(car.negotiated_price).toLocaleString()} FRW</p>
                                            <p><strong>Status:</strong> ${car.status}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="payment-detail-card">
                                            <h6><i class="fas fa-calculator text-success"></i> Financial Summary</h6>
                                            <p><strong>Total Spending:</strong> <span class="text-success">${totalSpending.toLocaleString()} FRW</span></p>
                                            <p><strong>Amount Already Paid:</strong> <span class="text-info">${paidAmount.toLocaleString()} FRW</span></p>
                                            <p><strong>Current Balance:</strong> <span class="text-danger">${balance.toLocaleString()} FRW</span></p>
                                        </div>
                                    </div>
                                </div>

                                <form method="POST" action="">
                                    <input type="hidden" name="car_id" value="${car.id}">
                                    <input type='hidden' value='${car.car_name}' name='car_name'>
                                    <input type="hidden" name="current_balance" value="${balance}">
                                    <input type="hidden" name="total_spending" value="${totalSpending}">

                                    <div class="payment-calculation">
                                        <h5 class="text-center mb-3">
                                            <i class="fas fa-money-bill-wave"></i> Make Payment
                                        </h5>
                                        
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-dollar-sign text-success"></i> Payment Amount:
                                            </label>
                                            <input type="number" name="payment_amount" class="form-control payment-input" 
                                                   placeholder="Enter payment amount" 
                                                   min="0" 
                                                   max="${balance}"
                                                   step="0.01"
                                                   data-balance="${balance}"
                                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                   required>
                                            <small class="text-muted">Maximum: ${balance.toLocaleString()} FRW</small>
                                        </div>

                                        <div class="balance-display">
                                            <div>Remaining Balance After Payment:</div>
                                            <div class="new-balance">${balance.toLocaleString()} FRW</div>
                                            <input type="hidden" name="new_balance" class="new-balance-hidden" value="${balance}">
                                        </div>
                                    </div>

                                    <div class="payment-method-section" id="paymentMethodSection">
                                        <div class="mb-3">
                                            <label for="Payment Method" class="form-label">Payment Method</label>
                                            <select class="form-control" name="payment_method" id="paymentMethodSelect" required>
                                                <option value="">--Select--</option>
                                                <option value="Momo">Momo</option>
                                                <option value="Bank">Bank</option>                                                
                                            </select>
                                        </div>
                                    </div>

                                    <div class="text-center mt-4">
                                        <button type="button" class="btn btn-secondary mr-3" data-dismiss="modal">
                                            <i class="fas fa-times"></i> Close
                                        </button>
                                        <button type="submit" name="process_payment" class="btn btn-success btn-lg">
                                            <i class="fas fa-credit-card"></i> Process Payment
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('body').append(modal);

            $(`#${modalId} .payment-input`).on('input', function() {
                const paymentAmount = parseFloat($(this).val()) || 0;
                const currentBalance = parseFloat($(this).data('balance'));
                const newBalance = Math.max(0, currentBalance - paymentAmount);
                
                $(this).closest('.modal-body').find('.new-balance').text(newBalance.toLocaleString() + ' FRW');
                $(this).closest('.modal-body').find('.new-balance-hidden').val(newBalance);
                
                if (paymentAmount > currentBalance) {
                    $(this).val(currentBalance);
                    $(this).closest('.modal-body').find('.new-balance').text('0 FRW');
                    $(this).closest('.modal-body').find('.new-balance-hidden').val(0);
                }
            });
        }


        // Function to open car details modal
function openCarDetailsModal(carData) {
    const modalContent = document.getElementById('carDetailsContent');
    
    // Check if dates are expired or valid
    const today = new Date();
    const insuranceExpiry = carData.insurance_expiry_date ? new Date(carData.insurance_expiry_date) : null;
    const controlExpiry = carData.control_expiry_date ? new Date(carData.control_expiry_date) : null;
    
    const insuranceStatus = insuranceExpiry ? (insuranceExpiry < today ? 'date-expired' : 'date-valid') : 'date-highlight';
    const controlStatus = controlExpiry ? (controlExpiry < today ? 'date-expired' : 'date-valid') : 'date-highlight';
    
    // Build booking section if car is booked
    let bookingSection = '';
    if (carData.booking_status === 'booked' && carData.booking_date) {
        bookingSection = `
            <div class="booking-details-section">
                <h6><i class="fas fa-calendar-check"></i> Active Booking Information</h6>
                <div class="car-details-grid">
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-user"></i> Customer Name:</span>
                        <span class="detail-value">${carData.customer_name || 'N/A'}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-id-card"></i> National ID:</span>
                        <span class="detail-value">${carData.customer_national_id || 'N/A'}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-phone"></i> Phone Number:</span>
                        <span class="detail-value">${carData.customer_phone || 'N/A'}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-calendar-alt"></i> Booking Date:</span>
                        <span class="detail-value">${formatDate(carData.booking_date)}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-calendar-check"></i> Return Date:</span>
                        <span class="detail-value">${formatDate(carData.booking_return_date)}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-money-bill-wave"></i> Booking Amount:</span>
                        <span class="detail-value" style="color: #28a745; font-size: 16px;">${parseInt(carData.booking_amount || 0).toLocaleString()} RWF</span>
                    </div>
                </div>
            </div>
        `;
    }
    
    modalContent.innerHTML = `
        <div style="text-align: center; margin-bottom: 30px;">
            <i class="fas fa-car" style="font-size: 4rem; color: #667eea; margin-bottom: 15px;"></i>
            <h3 style="color: #2c3e50; margin-bottom: 5px;">${carData.car_name}</h3>
            <p style="color: #6c757d; font-size: 18px;"><code>${carData.plate_number}</code></p>
        </div>

        <div class="car-details-grid">
            <!-- Basic Information Card -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fas fa-info-circle"></i> Basic Information
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="fas fa-hashtag"></i> Car ID:</span>
                    <span class="detail-value">#${carData.id}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="fas fa-tag"></i> Category:</span>
                    <span class="detail-value">${carData.category}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="fas fa-car-side"></i> Type:</span>
                    <span class="detail-value">${carData.type}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="fas fa-gas-pump"></i> Fuel Type:</span>
                    <span class="detail-value">${carData.fuel_type}</span>
                </div>
            </div>

            <!-- Status Information Card -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fas fa-signal"></i> Status Information
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="fas fa-toggle-on"></i> Car Status:</span>
                    <span class="detail-value">
                        <span class="status-badge ${carData.status.toLowerCase() === 'available' ? 'status-available' : 'status-rented'}">
                            ${carData.status}
                        </span>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="fas fa-calendar-alt"></i> Booking Status:</span>
                    <span class="detail-value">
                        <span class="status-badge ${carData.booking_status === 'booked' ? 'booking-status-booked' : 'booking-status-unbooked'}">
                            ${carData.booking_status === 'booked' ? 'BOOKED' : 'UNBOOKED'}
                        </span>
                    </span>
                </div>
            </div>

            <!-- Insurance Details Card -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fas fa-shield-alt"></i> Insurance Details
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="fas fa-calendar-plus"></i> Issued Date:</span>
                    <span class="detail-value ${insuranceStatus}">
                        ${carData.insurance_issued_date ? formatDate(carData.insurance_issued_date) : 'Not Available'}
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="fas fa-calendar-times"></i> Expiry Date:</span>
                    <span class="detail-value ${insuranceStatus}">
                        ${carData.insurance_expiry_date ? formatDate(carData.insurance_expiry_date) : 'Not Available'}
                        ${insuranceExpiry && insuranceExpiry < today ? ' <i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i>' : ''}
                    </span>
                </div>
                ${insuranceExpiry ? `
                <div class="detail-item">
                    <span class="detail-label"><i class="fas fa-hourglass-half"></i> Days Until Expiry:</span>
                    <span class="detail-value">
                        ${Math.ceil((insuranceExpiry - today) / (1000 * 60 * 60 * 24))} days
                    </span>
                </div>
                ` : ''}
            </div>

            <!-- Control/Technical Inspection Card -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fas fa-clipboard-check"></i> Technical Control
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="fas fa-calendar-plus"></i> Issued Date:</span>
                    <span class="detail-value ${controlStatus}">
                        ${carData.control_issued_date ? formatDate(carData.control_issued_date) : 'Not Available'}
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="fas fa-calendar-times"></i> Expiry Date:</span>
                    <span class="detail-value ${controlStatus}">
                        ${carData.control_expiry_date ? formatDate(carData.control_expiry_date) : 'Not Available'}
                        ${controlExpiry && controlExpiry < today ? ' <i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i>' : ''}
                    </span>
                </div>
                ${controlExpiry ? `
                <div class="detail-item">
                    <span class="detail-label"><i class="fas fa-hourglass-half"></i> Days Until Expiry:</span>
                    <span class="detail-value">
                        ${Math.ceil((controlExpiry - today) / (1000 * 60 * 60 * 24))} days
                    </span>
                </div>
                ` : ''}
            </div>
        </div>

        ${bookingSection}

        <div style="margin-top: 25px; padding: 20px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 15px; text-align: center;">
            <p style="margin: 0; color: #6c757d; font-size: 14px;">
                <i class="fas fa-clock"></i> Record generated on ${new Date().toLocaleString('en-US', { 
                    dateStyle: 'full', 
                    timeStyle: 'short' 
                })}
            </p>
        </div>
    `;
    
    $('#carDetailsModal').modal('show');
}

// Function to print car details
function printCarDetails() {
    window.print();
}

// Update the createTableRow function to add click event on rows
// Add this after the existing row creation code in your createTableRow function

// Modify your existing JavaScript to make rows clickable
$(document).ready(function() {
    // Existing code...
    
    // Add click event to table rows (delegate to handle dynamically created rows)
    $(document).on('click', '#enhancedTableBody tr.car-row', function(e) {
        // Don't trigger modal if clicking on buttons or form elements
        if ($(e.target).closest('button, form, .enhanced-action-btn').length) {
            return;
        }
        
        const rowIndex = $(this).index();
        const currentData = currentView === 'internal' ? internalCarsData : externalCarsData;
        const carData = currentData[rowIndex];
        
        if (carData) {
            openCarDetailsModal(carData);
        }
    });
    
    // Add hover effect hint
    $('#enhancedTableBody').on('mouseenter', 'tr.car-row', function() {
        if (!$(this).hasClass('clickable-row')) {
            $(this).addClass('clickable-row');
        }
    });
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