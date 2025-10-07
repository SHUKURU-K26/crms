<?php
session_start();
include "../../web_includes/auth.php";
include "../../web_db/connection.php";

if (isset($_POST['delete'])){
    $car_id = $_POST['delete_id'];
    // Step 1: Check if car is rented
    $sql = "SELECT status FROM cars WHERE car_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();

    if ($car['status'] === 'rented'){
        echo "
        <div id='alertBox'>
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
        // Step 2: Remove rentals referencing this car (to avoid FK error)
        $deleteRentals = $conn->prepare("DELETE FROM rentals WHERE car_id = ?");
        $deleteRentals->bind_param("i", $car_id);
        $deleteRentals->execute();

        // Step 3: Delete the car
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

    
    //Check if the Car is Rent Mode

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
    }    
    else{
        //Set the Car Lifecycle Status to returned
        $UpdateRecord=$conn->prepare("UPDATE external_cars SET lifecycle_status='returned' WHERE car_id = ?");
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

//Re-pay For the Providers Debt Money
if (isset($_POST["process_payment"])){
    $car_name=$_POST["car_name"];
    $car_id=$_POST["car_id"];
    $newBalance=$_POST["new_balance"];
    $paymentMethod=$_POST["payment_method"];

    //Select the Balance to Check if the Balance in Db table is Equal to Entered Balance
    $selectQuery=$conn->query("SELECT balance FROM external_cars WHERE car_id=$car_id");
    $row=$selectQuery->fetch_assoc();
    
    if ($newBalance==$row["balance"]){
        $UpdateQuery=$conn->prepare("UPDATE external_cars SET balance=?, payment_method=?, use_status='Fully Paid' WHERE car_id=?");

        $UpdateQuery->bind_param('isi', $newBalance,$paymentMethod,$car_id);
        $UpdateQuery->execute();
        if ($UpdateQuery->execute()){
            # code...
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
            # code...
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
    <meta name="description" content="">
    <meta name="author" content="">

    <title>GuestPro CMS| Register New Car</title>

    <!-- Custom fonts for this template-->
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link  href="../../css/custom.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/overview.css">
    <link rel="icon" href="../../img/GuestProLogoReal.JPG" type="image/png">
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
                    <div style="display: flex;justify-content:space-between;">
                        <h1 class="h3 mb-2 text-gray-800">All Cars Over view</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div><br>

                <div class="card shadow mb-4 enhanced-card">
                    <div class="card-header py-3 enhanced-header">
                        <h6 class="m-0 font-weight-bold">(Internal) GuestPro Cars Info</h6>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <div class="enhanced-table-container">
                            <div class="enhanced-controls">
                                <div class="enhanced-search-container">
                                    <div class="enhanced-search-icon">🔍</div>
                                    <input 
                                        type="text" 
                                        class="enhanced-search-input" 
                                        placeholder="Search cars by any field..." 
                                        id="enhancedSearchInput"
                                    >
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
                                    <thead id="enhancedTableHead">
                                        <!-- Headers will be dynamically updated based on view -->
                                    </thead>
                                    <tbody id="enhancedTableBody">
                                        <!-- Table body will be populated by JavaScript -->
                                    </tbody>
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
                                        
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php
             include "../../web_includes/footer.php";
            ?>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>

    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <!--Delete Modal-->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                 <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Are you Sure to delete <b><strong id="carName"></strong></b>?</h5>                
                  <button class="close" type="button" data-dismiss="modal" aria-label="Close">
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
    <script src="../../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../../vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../../js/mycustomjs.js"></script>

    <script>
        // Enhanced Table JavaScript
        let currentView = 'internal';
        let internalCarsData = [];
        let externalCarsData = [];
        let allRows = [];
        let filteredRows = [];

        // Initialize
        $(document).ready(function() {
            // Load initial data and initialize table
            loadInitialData();
            
            // Existing delete modal functionality
            $(document).on('click', '.delete-btn', function () {
                var carId = $(this).data('id');
                var carName = $(this).data('name');
                $('#carName').text(carName);
                $('#hiddenCarId').val(carId);
            });
        });

        function loadInitialData() {
            // Load internal cars data
            loadInternalCars();
        }

        function loadInternalCars() {
            // Show loading
            showLoading();
            
            $.ajax({
                url: 'get_cars_data.php',
                method: 'GET',
                data: { view: 'internal' },
                dataType: 'json',
                success: function(data) {
                    internalCarsData = data;
                    if (currentView === 'internal') {
                        renderTable();
                    }
                    hideLoading();
                },
                error: function() {
                    // Fallback to existing PHP data if AJAX fails
                    loadInternalCarsFromDOM();
                    hideLoading();
                }
            });
        }

        function loadInternalCarsFromDOM() {
            // Fallback method - get data from existing PHP-rendered content
            <?php
            $carDataJS = [];
            $sql="SELECT * FROM cars";
            $result=$conn->query($sql);
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
                    
                    $carDataJS[] = [
                        'id' => $row['car_id'],
                        'count' => $count,
                        'car_name' => $row['car_name'],
                        'category' => $rowCategory['category_name'] ?? 'N/A',
                        'plate_number' => $row['plate_number'],
                        'type' => $row['type'],
                        'fuel_type' => $row['fuel_type'],
                        'insurance_issued_date' => $row['insurance_issued_date'],
                        'insurance_expiry_date' => $row['insurance_expiry_date'],
                        'control_issued_date' => $row['control_issued_date'],
                        'control_expiry_date' => $row['control_expiry_date'],
                        'status' => $row['status']
                    ];
                }
            }
            echo "internalCarsData = " . json_encode($carDataJS) . ";";
            ?>
            
            renderTable();
        }

        function loadExternalCars() {
            // Show loading
            showLoading();
            
            $.ajax({
                url: 'get_cars_data.php',
                method: 'GET',
                data: { view: 'external' },
                dataType: 'json',
                success: function(data) {
                    externalCarsData = data;
                    renderTable();
                    hideLoading();
                },
                error: function() {
                    // Fallback to direct PHP query
                    loadExternalCarsFromPHP();
                }
            });
        }

        function loadExternalCarsFromPHP() {
            <?php
            // Load external cars data with use_status
            $externalCarDataJS = [];
            $sqlExternal = "SELECT ec.*, u.username as user_name FROM external_cars ec LEFT JOIN users u ON ec.user_id = u.user_id
            WHERE ec.lifecycle_status = 'active'";
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
            
            // Update table headers
            updateTableHeaders();
            
            // Clear existing rows
            const tableBody = document.getElementById('enhancedTableBody');
            tableBody.innerHTML = '';
            
            // Render new rows
            currentData.forEach((car, index) => {
                const row = createTableRow(car, index);
                tableBody.appendChild(row);
            });
            
            // Update rows arrays for search functionality
            allRows = Array.from(document.querySelectorAll('#enhancedTableBody tr'));
            filteredRows = [...allRows];
            
            // Apply styling and animations
            applyStatusStyling();
            addAnimationDelays();
            updateEntriesInfo();
            
            // Clear search
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
                        <th style="width: 100px;">IID</th>
                        <th style="width: 100px;">IED</th>
                        <th style="width: 100px;">CID</th>
                        <th style="width: 100px;">CED</th>
                        <th>Status</th>
                        <th>Edit</th>
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
                        <th>Edit</th>
                    </tr>
                `;
            }
        }      
        function createTableRow(car, index) {
            const row = document.createElement('tr');
            row.className = 'car-row';
            row.style.animationDelay = `${index * 0.1}s`;
            
            if (currentView === 'internal') {
                row.innerHTML = `
                    <td>${car.count}</td>
                    <td><strong>${car.car_name}</strong></td>
                    <td>${car.category}</td>
                    <td style="font-size: 14px;"><code>${car.plate_number}</code></td>
                    <td>${car.type}</td>
                    <td>${car.fuel_type}</td>
                    <td>${car.insurance_issued_date}</td>
                    <td>${car.insurance_expiry_date}</td>
                    <td>${car.control_issued_date}</td>
                    <td>${car.control_expiry_date}</td>
                    <td class="status-cell">${car.status}</td>
                    <td>
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
                    </td>
                `;
            } else {
                // Get payment status based on balance
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

                // Only show payment button if there's a balance to pay
                const paymentButton = balance > 0 ? `
                    <button type="button" class="enhanced-action-btn payment-btn" 
                            title="Make Payment" data-toggle="modal"
                            data-target="#paymentModal_${car.id}" 
                            style="background: linear-gradient(135deg, #f39c12, #e67e22); color: white; margin-right: 5px;">
                        <i class="fas fa-dollar-sign"> Pay the Debts</i>
                    </button>
                ` : '';

                const ReturnButton=balance === 0 ? `
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

                // Only create payment modal if there's a balance to pay
                if (balance > 0){
                    setTimeout(() => createPaymentModal(car), 100);
                }
                
                // Create return modal if balance is 0 (fully paid)
                if (balance === 0) {
                    setTimeout(() => createReturnModal(car), 100);
                }
            }
            
            return row;
        }

        function setupEventListeners() {
            // Search functionality
            const searchInput = document.getElementById('enhancedSearchInput');
            searchInput.addEventListener('input', (e) => {
                performSearch(e.target.value);
            });

            // View toggle functionality
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
            // Hide all rows first
            allRows.forEach(row => {
                row.style.display = 'none';
            });

            // Show filtered rows
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
            
            // Update toggle buttons
            document.querySelectorAll('.enhanced-toggle-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.view === view);
            });

            currentView = view;
            
            // Load data for the selected view
            if (view === 'external') {
                // Load external cars if not already loaded
                if (externalCarsData.length === 0) {
                    loadExternalCars();
                } else {
                    renderTable();
                }
            } else {
                renderTable();
            }
        }

        // Initialize everything when DOM is ready
        $(document).ready(function() {
            setupEventListeners();
            loadInitialData();
        });

        // Create return modal for fully paid external cars
        function createReturnModal(car){
            const modalId = `returnModal_${car.id}`;        
            // Remove existing modal if it exists
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
            
            // Remove existing modal if it exists
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

            // Add payment calculation functionality
            $(`#${modalId} .payment-input`).on('input', function() {
                const paymentAmount = parseFloat($(this).val()) || 0;
                const currentBalance = parseFloat($(this).data('balance'));
                const newBalance = Math.max(0, currentBalance - paymentAmount);
                
                $(this).closest('.modal-body').find('.new-balance').text(newBalance.toLocaleString() + ' FRW');
                $(this).closest('.modal-body').find('.new-balance-hidden').val(newBalance);
                
                // Validate payment amount
                if (paymentAmount > currentBalance) {
                    $(this).val(currentBalance);
                    $(this).closest('.modal-body').find('.new-balance').text('0 FRW');
                    $(this).closest('.modal-body').find('.new-balance-hidden').val(0);
                }
            });
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