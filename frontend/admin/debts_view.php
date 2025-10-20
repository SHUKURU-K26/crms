<?php
session_start();
include "../../web_includes/auth.php";
include "../../web_db/connection.php";

if (isset($_POST['re_pay'])) {
    // Start transaction for data consistency
    mysqli_begin_transaction($conn);
    
    try {
        // Get and sanitize form data
        $debt_id = intval($_POST['rental_id']); // This is actually debt_id from your form
        $renter_name = htmlspecialchars($_POST['renter_name']);
        $id_number = htmlspecialchars($_POST['id_number']);
        $telephone = htmlspecialchars($_POST['telephone']);
        $car_name = htmlspecialchars($_POST['car_name']);
        $plate_number = htmlspecialchars($_POST['plate_number']);
        $provider_name = htmlspecialchars($_POST['provider_names']);
        $return_option = $_POST['return_option'];
        $partial_amount = floatval($_POST['partial_amount'] ?? 0);
        $remaining_balance = floatval($_POST['remaining_balance'] ?? 0);
        $debt_type = $_POST['debt_type']; // Get debt type from form
        
        // Get current debt amount from database
        $debt_stmt = $conn->prepare("SELECT debt_amount FROM debts WHERE debt_id = ?");
        $debt_stmt->bind_param("i", $debt_id);
        $debt_stmt->execute();
        $debt_result = $debt_stmt->get_result();
        $debt_row = $debt_result->fetch_assoc();
        $current_debt_amount = floatval($debt_row['debt_amount']);
        $debt_stmt->close();
        
        if (!$debt_row) {
            throw new Exception("Debt record not found.");
        }
        
        // Process based on repayment option
        switch ($return_option) {
            case "fully piad": // Note: keeping your typo for compatibility
                processFullDebtPayment($conn, [
                    'debt_id' => $debt_id,
                    'renter_name' => $renter_name,
                    'telephone' => $telephone,
                    'id_number' => $id_number,
                    'car_name' => $car_name,
                    'plate_number' => $plate_number,
                    'provider_name' => $provider_name,
                    'debt_amount' => $current_debt_amount,
                    'debt_type' => $debt_type
                ]);
                break;
                
            case "partial paid":
                processPartialDebtPayment($conn, [
                    'debt_id' => $debt_id,
                    'renter_name' => $renter_name,
                    'telephone' => $telephone,
                    'id_number' => $id_number,
                    'car_name' => $car_name,
                    'plate_number' => $plate_number,
                    'provider_name' => $provider_name,
                    'partial_amount' => $partial_amount,
                    'remaining_balance' => $remaining_balance,
                    'current_debt_amount' => $current_debt_amount,
                    'debt_type' => $debt_type
                ]);
                break;
                
            default:
                throw new Exception("Invalid payment option selected.");
        }
        
        // Commit transaction
        mysqli_commit($conn);
        showDebtPaymentSuccessMessage($car_name, $plate_number, $return_option, $partial_amount ?? $current_debt_amount);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        showDebtPaymentErrorMessage("Error processing payment: " . $e->getMessage());
    }
}

function processFullDebtPayment($conn, $data) {
    // Determine payment type based on debt type
    $payment_type = $data['debt_type'];
    
    // 1. Update payments table - mark as fully paid
    $stmt = $conn->prepare("UPDATE payments SET amount_paid = amount_paid + ?, status = 'Full paid', balance = 0 
    WHERE car_payed_for = ? AND plate = ? AND paid_by = ? AND status = 'Half paid' AND payment_type = ?");
    $stmt->bind_param("dssss", $data['debt_amount'], $data['car_name'], $data['plate_number'], $data['renter_name'], $payment_type);
    
    // If no existing payment record, create a new one
    if (!$stmt->execute() || $stmt->affected_rows == 0) {
        $stmt->close();
        
        // Create new payment record
        $stmt = $conn->prepare("INSERT INTO payments 
            (amount_paid, paid_by, payer_phone, payer_national_id, car_payed_for, plate, status, balance, payment_type)
            VALUES (?, ?, ?, ?, ?, ?, 'Full paid', 0, ?)");
        
        $stmt->bind_param("dssssss", 
            $data['debt_amount'], $data['renter_name'], $data['telephone'], 
            $data['id_number'], $data['car_name'], $data['plate_number'], $payment_type
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to create payment record: " . $stmt->error);
        }
    }
    $stmt->close();
    
    // 🔥 NEW SECTION 2: Update rental records to reflect zero balance
    // This is critical so return_car.php shows 0 balance
    if ($data['debt_type'] === 'internal') {
        // Update INTERNAL rental: set total_fee and balance to 0
        $updateRentalStmt = $conn->prepare("UPDATE rentals 
            INNER JOIN cars ON rentals.car_id = cars.car_id 
            SET rentals.total_fee = 0, rentals.balance = 0 
            WHERE cars.car_name = ? AND cars.plate_number = ? AND rentals.renter_full_name = ?");
        
        $updateRentalStmt->bind_param("sss", $data['car_name'], $data['plate_number'], $data['renter_name']);
        
        if (!$updateRentalStmt->execute()) {
            throw new Exception("Failed to update internal rental balance: " . $updateRentalStmt->error);
        }
        $updateRentalStmt->close();
        
    } else {
        // Update EXTERNAL rental: set total_fee and balance to 0
        $updateRentalStmt = $conn->prepare("UPDATE external_rentals 
            INNER JOIN external_cars ON external_rentals.car_id = external_cars.car_id 
            SET external_rentals.total_fee = 0, external_rentals.balance = 0 
            WHERE external_cars.car_name = ? AND external_cars.plate_number = ? AND external_rentals.renter_full_name = ?");
        
        $updateRentalStmt->bind_param("sss", $data['car_name'], $data['plate_number'], $data['renter_name']);
        
        if (!$updateRentalStmt->execute()) {
            throw new Exception("Failed to update external rental balance: " . $updateRentalStmt->error);
        }
        $updateRentalStmt->close();
    }
    
    // 3. Delete the debt record (debt is fully cleared)
    $stmt = $conn->prepare("DELETE FROM debts WHERE debt_id = ?");
    $stmt->bind_param("i", $data['debt_id']);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to delete debt record: " . $stmt->error);
    }
    $stmt->close();
    
    // 4. Update rental history based on debt type (if record exists)
    if ($data['debt_type'] === 'internal') {
        // Update internal rental history
        $stmt = $conn->prepare("UPDATE rental_history 
            SET revenue_received = revenue_received + ?, revenue_status = 'Expected'
            WHERE car_name = ? AND car_plate = ? AND renter_names = ?");
        
        $stmt->bind_param("dsss", 
            $data['debt_amount'], $data['car_name'], $data['plate_number'], $data['renter_name']
        );
    } else {
        // Update external rental history
        $stmt = $conn->prepare("UPDATE external_rental_history 
            SET revenue_received = revenue_received + ?, revenue_status = 'Expected'
            WHERE car_name = ? AND car_plate = ? AND renter_names = ?");
        
        $stmt->bind_param("dsss", 
            $data['debt_amount'], $data['car_name'], $data['plate_number'], $data['renter_name']
        );
    }
    
    $stmt->execute(); // This might not affect rows if no matching record exists, which is okay
    $stmt->close();
}

function processPartialDebtPayment($conn, $data) {
    // Validation: ensure partial amount doesn't exceed debt
    if ($data['partial_amount'] > $data['current_debt_amount']) {
        throw new Exception("Partial amount cannot exceed total debt amount.");
    }
    
    $new_debt_amount = $data['current_debt_amount'] - $data['partial_amount'];
    $payment_type = $data['debt_type'];

    $checkPaymentStmt = $conn->prepare("SELECT * FROM payments 
       WHERE car_payed_for = ? AND plate = ? AND paid_by = ? AND status = 'Half paid' AND payment_type = ?");
    $checkPaymentStmt->bind_param("ssss", $data['car_name'], $data['plate_number'], $data['renter_name'], $payment_type);
    $checkPaymentStmt->execute();
    $existingPayment = $checkPaymentStmt->get_result()->fetch_assoc();
    $checkPaymentStmt->close();
    
    if ($existingPayment) {
        // 1. Update payments table - add to amount paid, reduce balance
        $stmt = $conn->prepare("UPDATE payments 
            SET amount_paid = amount_paid + ?, balance = balance - ? 
            WHERE car_payed_for = ? AND plate = ? AND paid_by = ? AND status = 'Half paid' AND payment_type = ?");
        
        $stmt->bind_param("ddssss", 
            $data['partial_amount'], $data['partial_amount'], $data['car_name'], $data['plate_number'], $data['renter_name'], $payment_type
        );
        
        $stmt->execute();
        $stmt->close();
    } else {
        // Create new payment record
        $stmt = $conn->prepare("INSERT INTO payments 
        (amount_paid, paid_by, payer_phone, payer_national_id, car_payed_for, plate, status, balance, payment_type)
        VALUES (?, ?, ?, ?, ?, ?, 'Half paid', ?, ?)");
        
        $stmt->bind_param("dsssssds", 
            $data['partial_amount'], $data['renter_name'], $data['telephone'], 
            $data['id_number'], $data['car_name'], $data['plate_number'], $new_debt_amount, $payment_type
        );

        $stmt->execute();
        $stmt->close();
    }

    // 2. Update Rental Record Balance based on debt type
    if ($data['debt_type'] === 'internal') {
        // Update INTERNAL rental balance
        $updateRentalStmt = $conn->prepare("UPDATE rentals INNER JOIN cars ON rentals.car_id = cars.car_id 
            SET rentals.balance = rentals.balance - ?, rentals.total_fee = rentals.total_fee - ?
            WHERE cars.car_name = ? AND cars.plate_number = ? AND rentals.renter_full_name = ?");
        
        $updateRentalStmt->bind_param("ddsss", 
            $data['partial_amount'], $data['partial_amount'], $data['car_name'], $data['plate_number'], $data['renter_name']
        );
        $updateRentalStmt->execute();
        $updateRentalStmt->close();
        
    } else {
        // Update EXTERNAL rental balance
        $updateRentalStmt = $conn->prepare("UPDATE external_rentals INNER JOIN external_cars ON external_rentals.car_id = external_cars.car_id 
            SET external_rentals.balance = external_rentals.balance - ?, external_rentals.total_fee = external_rentals.total_fee - ?
            WHERE external_cars.car_name = ? AND external_cars.plate_number = ? AND external_rentals.renter_full_name = ?");
        
        $updateRentalStmt->bind_param("ddsss", 
            $data['partial_amount'], $data['partial_amount'], $data['car_name'], $data['plate_number'], $data['renter_name']
        );
        $updateRentalStmt->execute();
        $updateRentalStmt->close();
    }
    
    // 3. Update debt amount
    if ($new_debt_amount > 0) {
        $stmt = $conn->prepare("UPDATE debts SET debt_amount = ? WHERE debt_id = ?");
        $stmt->bind_param("di", $new_debt_amount, $data['debt_id']);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update debt amount: " . $stmt->error);
        }
        $stmt->close();
        
        // Car remains 'available with Debt' since balance still exists
    } else {
        // If somehow the remaining balance is 0, treat as full payment
        processFullDebtPayment($conn, array_merge($data, ['debt_amount' => $data['partial_amount']]));
        return;
    }
    
    // 4. Update rental history based on debt type
    if ($data['debt_type'] === 'internal') {
        // Update internal rental history
        $stmt = $conn->prepare("UPDATE rental_history 
            SET revenue_received = revenue_received + ?
            WHERE car_name = ? AND car_plate = ? AND renter_names = ?");
        
        $stmt->bind_param("dsss", 
            $data['partial_amount'], $data['car_name'], $data['plate_number'], $data['renter_name']
        );
    } else {
        // Update external rental history
        $stmt = $conn->prepare("UPDATE external_rental_history 
            SET revenue_received = revenue_received + ?
            WHERE car_name = ? AND car_plate = ? AND renter_names = ?");
        
        $stmt->bind_param("dsss", 
            $data['partial_amount'], $data['car_name'], $data['plate_number'], $data['renter_name']
        );
    }
    
    $stmt->execute(); // This might not affect rows if no matching record exists, which is okay
    $stmt->close();
}

function showDebtPaymentSuccessMessage($car_name, $plate_number, $return_option, $amount) {
    $message = "";
    switch ($return_option) {
        case "fully piad": // keeping your typo for compatibility
            $message = "✅ Full debt payment of " . number_format($amount) . " FRW received for $car_name (Plate: $plate_number). Debt cleared!";
            break;
        case "partial paid":
            $message = "💰 Partial payment of " . number_format($amount) . " FRW received for $car_name (Plate: $plate_number). Remaining balance updated.";
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

function showDebtPaymentErrorMessage($message) {
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
                        <h1 class="h3 mb-2" style="color: red;font-weight:bold;">
                            <i class="fas fa-exclamation-triangle"></i> All Cars with Debts
                        </h1>
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
                                <button class="toggle-btn active" data-view="internal" id="internalBtn">
                                    <i class="fas fa-building"></i> Internal Rentals
                                </button>

                                <button class="toggle-btn" data-view="external" id="externalBtn">
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
                        const internalRentalsData=[
                            <?php
                            $sql = "SELECT * FROM debts WHERE debt_type='internal' ORDER BY debt_id DESC";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                $debt = [];
                                while ($row = $result->fetch_assoc()) {
                                    $debt[] = json_encode($row);
                                }
                                echo implode(",\n", $debt);
                            }
                            ?>
                        ];

                        const externalRentalsData = [
                            <?php
                            $sql = "SELECT * FROM debts WHERE debt_type='external' ORDER BY debt_id DESC";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                $debt = [];
                                while ($row = $result->fetch_assoc()) {
                                    $debt[] = json_encode($row);
                                }
                                echo implode(",\n", $debt);
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
        let currentData = [];
        let filteredData = [];

        $(document).ready(function() {
            initializeTable();
            setupEventListeners();
        });

        function initializeTable() {
            currentData = internalRentalsData;
            filteredData = [...currentData];
            renderTable();
        }

        function setupEventListeners(){
            $('#searchInput').on('input', function() {
                const query = $(this).val().toLowerCase();
                if (query === '') {
                    filteredData = [...currentData];
                } else {
                    filteredData = currentData.filter(item => 
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
                currentData = internalRentalsData;
            } else {
                currentData = externalRentalsData;
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
                        <th>Car Name</th>
                        <th style='min-width:105px;'>Plate</th>
                        <th>Renter Names</th>
                        <th>National ID</th>
                        <th>Mobile</th>
                        <th>Debt Amount</th>
                        <th>Debt Onwer</th>
                        <th>Provided By</th>
                        <th style='min-width:105px;'>Action</th>
                    </tr>
                `);

                filteredData.forEach((rental, index) => {
                    const row = `
                        <tr style="animation-delay: ${index * 0.1}s">
                            <td>${index + 1}</td>
                            <td><strong>${rental.car_name}</strong></td>
                            <td><code>${rental.car_plate}</code></td>
                            <td>${rental.renter_names}</td>
                            <td><strong style="color: #4e73df;">${rental.national_id}</strong></td>
                            <td><code>${rental.phone_number}</code></td>
                            <td style='color:red;font-weight:bold;'>${rental.debt_amount} FRW</td>
                            <td style='color:green;font-weight:bold;'>${rental.debt_owner}</td>
                            <td>${rental.provider_names}</td>
                            <td>
                                <button type="button" class="action-btn return-btn" 
                                        data-toggle="modal" data-target="#returnModal_${rental.debt_id}">
                                    <i class="fas fa-info-circle"></i> Re-pay
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
            const modalId = `returnModal_${rental.debt_id}`;
            $(`#${modalId}`).remove();

            const modal = `
                <div class="modal fade enhanced-modal" id="${modalId}" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-car"></i> Re-Payment for <strong>${rental.car_name} with Plate of ${rental.car_plate}</strong>
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
                                                ${rental.renter_names}
                                            </div>
                                        </div>
                                        <div class="rental-detail">
                                            <i class="fas fa-id-card"></i>
                                            <div>
                                                <strong>ID Number:</strong><br>
                                                ${rental.national_id}
                                            </div>
                                        </div>
                                        <div class="rental-detail">
                                            <i class="fas fa-phone"></i>
                                            <div>
                                                <strong>Mobile:</strong><br>
                                                ${rental.phone_number}
                                            </div>
                                        </div>
                                        <div class="rental-detail">
                                            <i class="fas fa-money-bill"></i>
                                            <div>
                                                <strong>Debt Amount:</strong><br>
                                                ${rental.debt_amount} FRW
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="rental-detail">
                                            <i class="fas fa-user"></i>
                                            <div>
                                                <strong>Provided By:</strong><br>
                                                ${rental.provider_names}
                                            </div>
                                        </div>
                                        <div class="rental-detail">
                                            <i class="fas fa-tags"></i>
                                            <div>
                                                <strong>Rental Type:</strong><br>
                                                <span class="badge ${rental.debt_type === 'internal' ? 'badge-info' : 'badge-warning'}">
                                                    ${rental.debt_type.charAt(0).toUpperCase() + rental.debt_type.slice(1)}
                                                </span>
                                            </div>
                                        </div>                                                                                      
                                    </div>
                                </div>

                                <form method="POST" action="">
                                     <input type="hidden" name="rental_id" value="${rental.debt_id}">
                                     <input type="hidden" name="car_name" value="${rental.car_name}">
                                     <input type="hidden" name="plate_number" value="${rental.car_plate}">
                                    <input type="hidden" name="renter_name" value="${rental.renter_names}">
                                    <input type="hidden" name="id_number" value="${rental.national_id}">
                                    <input type="hidden" name="telephone" value="${rental.phone_number}">
                                    <input type="hidden" name="provider_names" value="${rental.provider_names}">
                                    <input type="hidden" name="debt_type" value="${rental.debt_type}">
                                    <input type="hidden" name="debt_amount" class="adjusted-revenue-hidden" value="${rental.debt_amount}">
                                    <div class="text-center mt-4">
                                            <label>Select Payment Options</label>
                                            <select name='return_option' class='form-control' required>
                                                <option value="">--Select If Portion or Full Paid--</option>
                                                <option value="fully piad">Full Paying</option>
                                                <option value="partial paid">Portion Paying</option>                                                
                                            </select><br>                            

                                            <div id="partial-payment-box" style="display:none; margin-top:10px;">
                                                <label>Enter Partial Amount Paid</label>
                                                <input type="number" name="partial_amount" class="form-control partial-input" min="1" placeholder="Enter amount customer paid"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);">
                                                    <small class="text-info d-block mt-2">
                                                        Remaining Balance: <span class="remaining-balance font-weight-bold">0 FRW</span>
                                                    </small>
                                                <!-- Hidden input for DB -->
                                                    <input type="hidden" name="remaining_balance" class="remaining-balance-hidden" value="0">
                                            </div><br>

                                            <button type="submit" name="re_pay" class="btn btn-success btn-sml">
                                                <i class="fas fa-check-circle"></i> Done
                                            </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(modal);
            
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

            $(`#${modalId} .return-date-input`).on('change', function() {    
                const partial = parseInt($(`#${modalId} .partial-input`).val()) || 0;
                const adjustedRevenue = parseInt($(`#${modalId} .adjusted-revenue-hidden`).val());
                const remaining = Math.max(0, adjustedRevenue - partial);
                $(`#${modalId} .remaining-balance`).text(remaining + " FRW");
                $(`#${modalId} .remaining-balance-hidden`).val(remaining);
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