<?php
session_start();
include "../../staff_web_includes/staff_auth.php";
include "../../web_db/connection.php";

if (isset($_SESSION["Userpassword"]) && isset($_SESSION["username"])){
    $staff_password=$_SESSION["Userpassword"];
    $query="SELECT * FROM users WHERE password='$staff_password'";
    $result=$conn->query($query);
    $row=$result->fetch_assoc();
    $staff_name=$row["user_id"];

   if(isset($_POST['logout'])){
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
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include "../../staff_web_includes/staff_menu.php"; ?>
        
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include "../../staff_web_includes/staff_topbar.php"; ?>
                
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
                            <h6 class="m-0 font-weight-bold">All Cars Info with Booking Details</h6>
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
                            <li>Reset booking status to <strong>Unbooked</strong></li>
                            <li>Clear all booking dates and amount</li>
                            <li>Set car status back to <strong>Available</strong></li>
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
                        <input type="hidden" name="cancel_car_id" id="hiddenCancelCarId" />
                        <input type="hidden" name="cancel_car_name" id="hiddenCancelCarName" />                 
                        <input type="submit" name="cancel_booking" class="btn btn-danger" value="Yes, Cancel Booking"/>
                    </form>
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
                var carId = $(this).data('id');
                var carName = $(this).data('name');
                $('#cancelCarName').text(carName);
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
                
                $whereClause = " WHERE (
                    (booking_status = 'Unbooked' AND status = 'available')
                    OR (
                        booking_status = 'booked' 
                        AND (
                            booking_date > '$searchDate'
                            OR booking_return_date < '$searchDate'
                        )
                    )
                )";
            }
            
            $sql = "SELECT * FROM cars" . $whereClause;
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
                    
                    $isAvailable = false;
                    if ($isSearching) {
                        $isAvailable = true;
                    }
                    
                    $carDataJS[] = [
                        'id' => $row['car_id'],
                        'count' => $count,
                        'car_name' => $row['car_name'],
                        'category' => $rowCategory['category_name'] ?? 'N/A',
                        'plate_number' => $row['plate_number'],
                        'type' => $row['type'],
                        'fuel_type' => $row['fuel_type'],                        
                        'status' => $row['status'],
                        'booking_status' => $row['booking_status'],
                        'booking_date' => $row['booking_date'],
                        'booking_return_date' => $row['booking_return_date'],
                        'booking_amount' => $row['booking_amount'],
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
                        <th>Booking Details</th>
                        <th>Status</th>                        
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
                                <i class="fas fa-calendar-alt"></i>
                                <span class="booking-dates">From: ${formatDate(car.booking_date)}</span>
                            </div>
                            <div class="booking-detail">
                                <i class="fas fa-calendar-check"></i>
                                <span class="booking-dates">To: ${formatDate(car.booking_return_date)}</span>
                            </div>
                            
                        </div>
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
                `;                
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