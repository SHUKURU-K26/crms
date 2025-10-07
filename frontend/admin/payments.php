<?php
session_start();
include "../../web_includes/auth.php";
include "../../web_db/connection.php";

if (isset($_POST['Re-pay'])){
    // Get the form data to Insert into Rental History Table
    $renter_name  = htmlspecialchars($_POST['renter_name']);
    $id_number    = htmlspecialchars($_POST['id_number']);
    $telephone    = htmlspecialchars($_POST['telephone']);
    $car_name     = htmlspecialchars($_POST['car_name']);
    $plate_number = htmlspecialchars($_POST['plate_number']);    
    $rent_date    = htmlspecialchars($_POST['rent_date']);
    $expected_return_date  = htmlspecialchars($_POST['expected_return_date']);
    $expected_revenue=$_POST["expected_revenue"];
    $date_returned_on=$_POST["date_returned_on"];
    $days_in_rent=$_POST["days_in_rent"];
    $adjusted_revenue = $_POST["adjusted_revenue"];
    $price=$_POST["price"];
    $refund_due=$_POST["refund_due"];
    $return_option=$_POST["return_option"];
    $partial_amount=$_POST["partial_amount"] ?? 0;
    $remaining_balance=$_POST["remaining_balance"] ?? 0;
    $provider_name=$_POST["provider_name"];

    //Checking if the Return Date was affected to Insert the adjusted Revenue as Total Revenue Received
    if ($date_returned_on !== $expected_return_date){
        $revenue_received  = $adjusted_revenue;
        $revenue_status="Adjusted";
    } 
    else {
        $revenue_received  = $expected_revenue;
        $revenue_status="Expected";
    }

    // Handle based on return option
    if ($return_option === "fully piad") {
        // Insert into Rental History Table
        $insert = $conn->prepare("INSERT INTO rental_history 
        (renter_names, renter_phone, renter_national_id, car_name, car_plate, date_rented_on, expected_return_date, date_returned_on,
        days_in_rent, rental_fee, revenue_received, revenue_status, expected_revenue, refund_due)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->bind_param("ssssssssiiisii", $renter_name, $telephone, $id_number, $car_name, $plate_number, $rent_date, $expected_return_date,
        $date_returned_on, $days_in_rent, $price, $revenue_received, $revenue_status, $expected_revenue, $refund_due);
        $insert->execute();
        $insert->close();
            
            // Get the car_id from the form
            $rental_id = $_POST['rental_id'];
            $car_name = htmlspecialchars($_POST['car_name'] ?? '');
            //Check if the car is in Rent Mode    
            $updateQuery=$conn->prepare("UPDATE cars SET status='Available' WHERE car_id = ?");
            $updateQuery->bind_param("i", $rental_id);
            
            if ($updateQuery->execute()) { 
                $deleteSql="DELETE FROM rentals WHERE car_id='$rental_id'";
                $query=$conn->query($deleteSql);

                if ($query) {
                    include "../../system_messages/returnCarMessage.php";
                }
           }
    } 
    
    elseif ($return_option === "partial paid") {
       $paymentInsert=$conn->prepare("INSERT INTO payments(amount_paid, paid_by, payer_phone, payer_national_id, car_payed_for, plate, status) 
       VALUES(?, ?, ?, ?, ?, ?, 'Half paid')");
       $paymentInsert->bind_param('isssss',$partial_amount, $renter_name, $telephone, $id_number, $car_name, $plate_number);
       $paymentInsert->execute();
       $paymentInsert->close();
    } 
    elseif ($return_option === "fully Unpaid") {
        $fullyUpaidInsert = $conn->prepare("INSERT INTO debts 
        (debt_type, car_name, car_plate, national_id, phone_number, debt_amount, provider_names) VALUES ('internal', ?, ?, ?, ?, ?, ?)");
        $fullyUpaidInsert->bind_param("ssssis", $car_name, $plate_number, $id_number, $telephone, $remaining_balance, $provider_name);
        $fullyUpaidInsert->execute();
        $fullyUpaidInsert->close();
        
        // Get the car_id from the form
        $rental_id = $_POST['rental_id'];
        $car_name = htmlspecialchars($_POST['car_name'] ?? '');
        //Check if the car is in Rent Mode    
        $updateQuery=$conn->prepare("UPDATE cars SET status='available with Debt' WHERE car_id = ?");
        $updateQuery->bind_param("i", $rental_id);
        
        if ($updateQuery->execute()) { 
            $deleteSql="DELETE FROM rentals WHERE car_id='$rental_id'";
            $query=$conn->query($deleteSql);

            if ($query) {
                include "../../system_messages/returnCarMessage.php";
            }
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

    <title>GuestPro CMS | All Payments</title>

    <!-- Custom fonts for this template-->
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="../../css/custom.css" rel="stylesheet">
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../../css/returnView.css" rel="stylesheet">
    <link rel="icon" href="../../img/GuestProLogoReal.JPG" type="image/png">
    <link href="../../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <?php include "../../web_includes/menu.php"; ?>
        
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <?php include "../../web_includes/topbar.php"; ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <div style="display: flex;justify-content:space-between;">
                        <h1 class="h3 mb-2" style="color: green;font-weight:bold;">
                            <i class="fas fa-check-circle"></i> All Payment Records
                        </h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
                        </a>
                    </div><br>

                    <div class="enhanced-container">
                        <!-- Controls -->
                        <div class="enhanced-controls">
                            <div class="search-container">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="search-input" placeholder="Search payments by any field..." id="searchInput">
                            </div>

                            <div class="view-toggle">
                                <button class="toggle-btn active" data-view="internal" id="internalBtn">
                                    <i class="fas fa-building"></i> Internal Payments
                                </button>
                                <button class="toggle-btn" data-view="external" id="externalBtn">
                                    <i class="fas fa-globe"></i> External Payments
                                </button>
                            </div>

                            <div class="entries-info">
                                Showing <span id="visibleCount">0</span> of <span id="totalCount">0</span> payments
                            </div>
                        </div>

                        <!-- Table Container -->
                        <div class="table-container" id="tableContainer">
                            <table class="enhanced-table" id="dataTable">
                                <thead id="tableHead">
                                    <!-- Headers will be set by JavaScript -->
                                </thead>
                                <tbody id="tableBody">
                                    <!-- Data will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>

                        <!-- No Results -->
                        <div class="no-results" id="noResults">
                            <i class="fas fa-search" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"></i>
                            <h4>No payments found</h4>
                            <p>Try adjusting your search criteria</p>
                        </div>
                    </div>

                    <!-- PHP Data for JavaScript -->
                    <script>
                        // Internal payments data
                        const internalPaymentsData = [
                            <?php
                            $sql = "SELECT * FROM payments WHERE payment_type='internal' ORDER BY p_id DESC";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                $paymentRecord = [];
                                while ($row = $result->fetch_assoc()) {
                                    $paymentRecord[] = json_encode($row);
                                }
                                echo implode(",\n", $paymentRecord);
                            }
                            ?>
                        ];

                        // External payments data
                        const externalPaymentsData = [
                            <?php
                            $sql = "SELECT * FROM payments WHERE payment_type='external' ORDER BY p_id DESC";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                $paymentRecord = [];
                                while ($row = $result->fetch_assoc()) {
                                    $paymentRecord[] = json_encode($row);
                                }
                                echo implode(",\n", $paymentRecord);
                            }
                            ?>
                        ];
                    </script>

                </div>
            </div>

            <!-- Footer -->
            <?php include "../../web_includes/footer.php"; ?>
        </div>
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
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

    <!-- Bootstrap core JavaScript-->
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
        let currentData = [];
        let filteredData = [];

        $(document).ready(function() {
            initializeTable();
            setupEventListeners();
        });

        function initializeTable() {
            currentData = internalPaymentsData;
            filteredData = [...currentData];
            renderTable();
        }

        function setupEventListeners(){
            // Search functionality
            $('#searchInput').on('input', function() {
                const query = $(this).val().toLowerCase();
                if (query === '') {
                    filteredData = [...currentData];
                } else {
                    filteredData = currentData.filter(item => 
                        Object.values(item).some(value => 
                            value && value.toString().toLowerCase().includes(query)
                        )
                    );
                }
                renderTable();
            });

            // View toggle
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
                currentData = internalPaymentsData;
            } else {
                currentData = externalPaymentsData;
            }

            filteredData = [...currentData];
            $('#searchInput').val('');
            renderTable();
        }

        function updateEntriesInfo() {
            $('#visibleCount').text(filteredData.length);
            $('#totalCount').text(currentData.length);
        }

        function renderTable() {
            const tableHead = $('#tableHead');
            const tableBody = $('#tableBody');

            // Clear existing content
            tableHead.empty();
            tableBody.empty();

            if (filteredData.length === 0) {
                $('#noResults').addClass('show');
                $('#tableContainer').hide();
            } else {
                $('#noResults').removeClass('show');
                $('#tableContainer').show();

                // Render headers
                tableHead.html(`
                    <tr>
                        <th>N#</th>
                        <th>Amount Paid</th>
                        <th>Paid By</th>
                        <th>Mobile</th>
                        <th>Payer ID</th>
                        <th>Car Paid For</th>
                        <th>Plate</th>
                        <th>Status</th>
                        <th>Balance</th>
                        <th>Payment Type</th>                      
                    </tr>
                `);

                // Render data rows
                filteredData.forEach((payment, index) => {
                    // Decide color based on status
                    let statusColor = (payment.status && payment.status.toLowerCase() === "half paid") ? "red" : "green";
                    let balanceColor = (payment.balance == 0) ? "green" : "red";
                    
                    // Determine payment type badge color
                    let paymentTypeBadge = '';
                    if (payment.payment_type) {
                        paymentTypeBadge = payment.payment_type === 'internal' ? 
                            `<span class="badge badge-info">${payment.payment_type.charAt(0).toUpperCase() + payment.payment_type.slice(1)}</span>` :
                            `<span class="badge badge-warning">${payment.payment_type.charAt(0).toUpperCase() + payment.payment_type.slice(1)}</span>`;
                    } else {
                        paymentTypeBadge = '<span class="badge badge-secondary">Unknown</span>';
                    }

                    const row = `
                        <tr style="animation-delay: ${index * 0.1}s">
                            <td>${index + 1}</td>
                            <td><strong>${payment.amount_paid || 0} FRW</strong></td>
                            <td ><code>${payment.paid_by || 'N/A'}</code></td>
                            <td>${payment.payer_phone || 'N/A'}</td>
                            <td><strong style="color: #4e73df;">${payment.payer_national_id || 'N/A'}</strong></td>
                            <td><code>${payment.car_payed_for || 'N/A'}</code></td>
                            <td style='min-width:115px;'>${payment.plate || 'N/A'}</td>
                            <td style="color:${statusColor};min-width:110px; font-weight:bold;">
                              ${payment.status || 'N/A'}
                            </td>
                            <td style="color:${balanceColor}; font-weight:bold;">
                              ${payment.balance || 0} FRW
                            </td>
                            <td>${paymentTypeBadge}</td>
                        </tr>
                    `;
                    tableBody.append(row);
                });
            }
            updateEntriesInfo();
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