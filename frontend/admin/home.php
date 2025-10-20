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
   
   // ADD THE CODE GENERATION HANDLING HERE
   if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['use_code'])) {
       include "../../web_db/connection.php";
       
       $code = trim($_POST['code']);
       
       if (!empty($code)) {
           // Check if code already exists
           $check_stmt = $conn->prepare("SELECT * FROM registration_codes WHERE code = ?");
           $check_stmt->bind_param("s", $code);
           $check_stmt->execute();
           $result = $check_stmt->get_result();
           
           if ($result->num_rows == 0) {
               // Code doesn't exist, insert it
               $stmt = $conn->prepare("INSERT INTO registration_codes (code, status, created_at) VALUES (?, 'Unused', NOW())");
               $stmt->bind_param("s", $code);
               
               if ($stmt->execute()) {
                   echo "<script>
                       $(document).ready(function() {
                           $('#successModal').modal('show');
                       });
                   </script>";
               } else {
                   echo "<script>
                       alert('Database error: Could not save code. Error: " . addslashes($stmt->error) . "');
                   </script>";
               }
               $stmt->close();
           } else {
               echo "<script>
                   alert('This code already exists. Please generate a new one.');
               </script>";
           }
           
           $check_stmt->close();
       } else {
           echo "<script>
               alert('Please generate a code first.');
           </script>";
       }
       
       $conn->close();
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

    <title>Guest Pro Car Management System</title>

    <!-- Custom fonts for this template-->
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" href="../../img/GuestProLogoReal.JPG" type="image/png">
    <link rel="stylesheet" href="../../css/homeDashboard.css">
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <?php include "../../web_includes/menu.php"; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <?php include "../../web_includes/topbar.php"; ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="dashboard-header">
                        <div class="d-sm-flex align-items-center justify-content-between">
                            <div>
                                <h1 class="mb-2">Dashboard Analytics</h1>
                                <p class="mb-0">Comprehensive overview of your car rental management system</p>
                            </div>
                            <a href="#" class="btn btn-light shadow-sm">
                                <i class="fas fa-download fa-sm text-gray-600 mr-2"></i>Generate Report
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <?php include "../../web_db/connection.php"; ?>

                        <!-- FLEET OVERVIEW SECTION -->
                        <div class="col-12">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-tachometer-alt section-icon"></i>
                                    Fleet Overview
                                </h3>
                            </div>
                        </div>

                        <!-- Total Internal Cars -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card dashboard-card card-primary h-100">
                                <div class="card-body">
                                    <div class="stat-label">Internal Fleet</div>
                                    <div class="stat-number">
                                        <?php
                                        $totalInternalSql = "SELECT COUNT(car_id) AS total_internal FROM cars";
                                        $result = $conn->query($totalInternalSql);
                                        $totalInternal = 0;
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $totalInternal = $row["total_internal"];
                                        }
                                        echo $totalInternal;
                                        ?>
                                        <span class="metric-badge">Cars</span>
                                    </div>
                                    <i class="fas fa-building stat-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Total External Cars -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card dashboard-card card-info h-100">
                                <div class="card-body">
                                    <div class="stat-label">External Fleet</div>
                                    <div class="stat-number">
                                        <?php
                                        $totalExternalSql = "SELECT COUNT(car_id) AS total_external FROM external_cars WHERE lifecycle_status = 'active'";
                                        $result = $conn->query($totalExternalSql);
                                        $totalExternal = 0;
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $totalExternal = $row["total_external"];
                                        }
                                        echo $totalExternal;
                                        ?>
                                        <span class="metric-badge">Cars</span>
                                    </div>
                                    <i class="fas fa-handshake stat-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Total Fleet -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card dashboard-card card-dark h-100">
                                <div class="card-body">
                                    <div class="stat-label">Total Fleet</div>
                                    <div class="stat-number">
                                        <?php
                                        $totalFleet = $totalInternal + $totalExternal;
                                        echo $totalFleet;
                                        ?>
                                        <span class="metric-badge">Cars</span>
                                    </div>
                                    <i class="fas fa-car stat-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Fleet Utilization -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card dashboard-card card-secondary h-100">
                                <div class="card-body">
                                    <div class="stat-label">Fleet Utilization</div>
                                    <div class="stat-number">
                                        <?php
                                        $rentedInternalSql = "SELECT COUNT(car_id) AS rented_internal FROM cars WHERE status='rented'";
                                        $rentedExternalSql = "SELECT COUNT(car_id) AS rented_external FROM external_cars WHERE status='rented' AND lifecycle_status = 'active'";
                                        
                                        $result1 = $conn->query($rentedInternalSql);
                                        $result2 = $conn->query($rentedExternalSql);
                                        
                                        $rentedInternal = 0;
                                        $rentedExternal = 0;
                                        
                                        if ($result1->num_rows > 0) {
                                            $row1 = $result1->fetch_assoc();
                                            $rentedInternal = $row1["rented_internal"];
                                        }
                                        if ($result2->num_rows > 0) {
                                            $row2 = $result2->fetch_assoc();
                                            $rentedExternal = $row2["rented_external"];
                                        }
                                        
                                        $totalRented = $rentedInternal + $rentedExternal;
                                        $utilizationRate = $totalFleet > 0 ? round(($totalRented / $totalFleet) * 100, 1) : 0;
                                        
                                        $utilizationClass = $utilizationRate >= 70 ? 'utilization-high' : 
                                                          ($utilizationRate >= 40 ? 'utilization-medium' : 'utilization-low');
                                        ?>
                                        <span class="<?php echo $utilizationClass; ?>"><?php echo $utilizationRate; ?>%</span>
                                    </div>
                                    <div class="sub-stat"><?php echo $totalRented; ?> of <?php echo $totalFleet; ?> cars rented</div>
                                    <i class="fas fa-chart-pie stat-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- INTERNAL CARS SECTION -->
                        <div class="col-12">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-building section-icon"></i>
                                    Internal Fleet Management
                                </h3>
                            </div>
                        </div>

                        <!-- Available Internal Cars -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card dashboard-card card-success h-100">
                                <div class="card-body">
                                    <div class="stat-label">Available Internal</div>
                                    <div class="stat-number">
                                        <?php
                                        $availableInternalSql = "SELECT COUNT(car_id) AS available_internal FROM cars WHERE status='available'";
                                        $availableInternalDebtSql = "SELECT COUNT(car_id) AS available_internal_debt FROM cars WHERE status='available with Debt'";
                                        
                                        $result1 = $conn->query($availableInternalSql);
                                        $result2 = $conn->query($availableInternalDebtSql);
                                        
                                        $availableClean = 0;
                                        $availableWithDebt = 0;
                                        
                                        if ($result1->num_rows > 0) {
                                            $row1 = $result1->fetch_assoc();
                                            $availableClean = $row1["available_internal"];
                                        }
                                        if ($result2->num_rows > 0) {
                                            $row2 = $result2->fetch_assoc();
                                            $availableWithDebt = $row2["available_internal_debt"];
                                        }
                                        
                                        $totalAvailable = $availableClean + $availableWithDebt;
                                        echo $totalAvailable;
                                        ?>
                                    </div>
                                    <div class="sub-stat">Clean: <?php echo $availableClean; ?> | With Debt: <?php echo $availableWithDebt; ?></div>
                                    <i class="fas fa-check-circle stat-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Rented Internal Cars -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card dashboard-card card-danger h-100">
                                <div class="card-body">
                                    <div class="stat-label">Rented Internal</div>
                                    <div class="stat-number">
                                        <?php echo $rentedInternal; ?>
                                    </div>
                                    <div class="sub-stat">
                                        <?php 
                                        $internalUtilization = $totalInternal > 0 ? round(($rentedInternal / $totalInternal) * 100, 1) : 0;
                                        echo "Utilization: " . $internalUtilization . "%";
                                        ?>
                                    </div>
                                    <i class="fas fa-car-side stat-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Internal Revenue -->
                        <div class="col-xl-6 col-md-12 mb-4">
                            <div class="card dashboard-card card-success h-100">
                                <div class="card-body">
                                    <div class="stat-label">Internal Revenue</div>
                                    <div class="stat-number revenue-number">
                                        <?php
                                        $internalRevenueSql = "SELECT SUM(amount_paid) AS internal_revenue FROM payments";
                                        $result = $conn->query($internalRevenueSql);
                                        $internalRevenue = 0;
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $internalRevenue = $row["internal_revenue"] ?? 0;
                                        }
                                        echo number_format($internalRevenue);
                                        ?>
                                        <span class="metric-badge">RWF</span>
                                    </div>
                                    <i class="fas fa-money-bill-wave stat-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- EXTERNAL CARS SECTION -->
                        <div class="col-12">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-handshake section-icon"></i>
                                    External Fleet Management
                                </h3>
                            </div>
                        </div>

                        <!-- Available External Cars -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card dashboard-card card-success h-100">
                                <div class="card-body">
                                    <div class="stat-label">Available External</div>
                                    <div class="stat-number">
                                        <?php
                                        $availableExternalSql = "SELECT COUNT(car_id) AS available_external FROM external_cars WHERE status='available' AND lifecycle_status = 'active'";
                                        $result = $conn->query($availableExternalSql);
                                        $availableExternal = 0;
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $availableExternal = $row["available_external"];
                                        }
                                        echo $availableExternal;
                                        ?>
                                    </div>
                                    <i class="fas fa-clipboard-check stat-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Rented External Cars -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card dashboard-card card-danger h-100">
                                <div class="card-body">
                                    <div class="stat-label">Rented External</div>
                                    <div class="stat-number">
                                        <?php echo $rentedExternal; ?>
                                    </div>
                                    <div class="sub-stat">
                                        <?php 
                                        $externalUtilization = $totalExternal > 0 ? round(($rentedExternal / $totalExternal) * 100, 1) : 0;
                                        echo "Utilization: " . $externalUtilization . "%";
                                        ?>
                                    </div>
                                    <i class="fas fa-exchange-alt stat-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- External Revenue -->
                        <div class="col-xl-6 col-md-12 mb-4">
                            <div class="card dashboard-card card-success h-100">
                                <div class="card-body">
                                    <div class="stat-label">External Revenue</div>
                                    <div class="stat-number revenue-number">
                                        <?php
                                        $externalRevenueSql = "SELECT SUM(revenue_received) AS external_revenue FROM external_rental_history";
                                        $result = $conn->query($externalRevenueSql);
                                        $externalRevenue = 0;
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $externalRevenue = $row["external_revenue"] ?? 0;
                                        }
                                        echo number_format($externalRevenue);
                                        ?>
                                        <span class="metric-badge">RWF</span>
                                    </div>
                                    <i class="fas fa-chart-line stat-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- DEBT ANALYSIS SECTION -->
                        <div class="col-12">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-exclamation-triangle section-icon"></i>
                                    Debt Analysis
                                </h3>
                            </div>
                        </div>

                        <!-- Internal Debt -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card dashboard-card card-warning h-100">
                                <div class="card-body">
                                    <div class="stat-label">Internal Debt</div>
                                    <div class="stat-number debt-number">
                                        <?php
                                        $internalDebtSql = "SELECT SUM(debt_amount) AS internal_debt FROM debts WHERE debt_type='internal'";
                                        $result = $conn->query($internalDebtSql);
                                        $internalDebt = 0;
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $internalDebt = $row["internal_debt"] ?? 0;
                                        }
                                        echo number_format($internalDebt);
                                        ?>
                                        <span class="metric-badge">RWF</span>
                                    </div>
                                    <i class="fas fa-exclamation-circle stat-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- External Debt -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card dashboard-card card-warning h-100">
                                <div class="card-body">
                                    <div class="stat-label">External Debt</div>
                                    <div class="stat-number debt-number">
                                        <?php
                                        $externalDebtSql = "SELECT SUM(debt_amount) AS external_debt FROM debts WHERE debt_type='external'";
                                        $result = $conn->query($externalDebtSql);
                                        $externalDebt = 0;
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $externalDebt = $row["external_debt"] ?? 0;
                                        }
                                        echo number_format($externalDebt);
                                        ?>
                                        <span class="metric-badge">RWF</span>
                                    </div>
                                    <i class="fas fa-ban stat-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Highest Internal Debt Car -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card dashboard-card card-danger h-100">
                                <div class="card-body">
                                    <div class="stat-label">Highest Internal Debt</div>
                                    <div style="font-size: 1rem; font-weight: 600; color: #2d3436;">
                                        <?php
                                        $highestInternalDebtSql = "SELECT car_name, car_plate, debt_amount FROM debts WHERE debt_type='internal' ORDER BY debt_amount DESC LIMIT 1";
                                        $result = $conn->query($highestInternalDebtSql);
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            echo $row["car_name"];
                                            echo "<div class='sub-stat'>" . $row["car_plate"] . "</div>";
                                            echo "<div class='mt-2 debt-number' style='font-size: 1.5rem; font-weight: 700;'>" . number_format($row["debt_amount"]) . " RWF</div>";
                                        } else {
                                            echo "No debts";
                                        }
                                        ?>
                                    </div>
                                    <i class="fas fa-arrow-up stat-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Highest External Debt Car -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card dashboard-card card-danger h-100">
                                <div class="card-body">
                                    <div class="stat-label">Highest External Debt</div>
                                    <div style="font-size: 1rem; font-weight: 600; color: #2d3436;">
                                        <?php
                                        $highestExternalDebtSql = "SELECT car_name, car_plate, debt_amount FROM debts WHERE debt_type='external' ORDER BY debt_amount DESC LIMIT 1";
                                        $result = $conn->query($highestExternalDebtSql);
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            echo $row["car_name"];
                                            echo "<div class='sub-stat'>" . $row["car_plate"] . "</div>";
                                            echo "<div class='mt-2 debt-number' style='font-size: 1.5rem; font-weight: 700;'>" . number_format($row["debt_amount"]) . " RWF</div>";
                                        } else {
                                            echo "No debts";
                                        }
                                        ?>
                                    </div>
                                    <i class="fas fa-arrow-up stat-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- FINANCIAL SUMMARY SECTION -->
                        <div class="col-12">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-chart-bar section-icon"></i>
                                    Financial Summary
                                </h3>
                            </div>
                        </div>

                        <!-- Total Revenue -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card dashboard-card card-success h-100">
                                <div class="card-body">
                                    <div class="stat-label">Total Revenue</div>
                                    <div class="stat-number revenue-number">
                                        <?php
                                        $totalRevenue = $internalRevenue + $externalRevenue;
                                        echo number_format($totalRevenue);
                                        ?>
                                        <span class="metric-badge">RWF</span>
                                    </div>
                                    <div class="financial-highlight">
                                        <div class="sub-stat">Internal: <?php echo number_format($internalRevenue); ?> RWF</div>
                                        <div class="sub-stat">External: <?php echo number_format($externalRevenue); ?> RWF</div>
                                    </div>
                                    <i class="fas fa-coins stat-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Total Debt -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card dashboard-card card-warning h-100">
                                <div class="card-body">
                                    <div class="stat-label">Total Outstanding Debt</div>
                                    <div class="stat-number debt-number">
                                        <?php
                                        $totalDebt = $internalDebt + $externalDebt;
                                        echo number_format($totalDebt);
                                        ?>
                                        <span class="metric-badge">RWF</span>
                                    </div>
                                    <div class="financial-highlight">
                                        <div class="sub-stat">Internal: <?php echo number_format($internalDebt); ?> RWF</div>
                                        <div class="sub-stat">External: <?php echo number_format($externalDebt); ?> RWF</div>
                                    </div>
                                    <i class="fas fa-credit-card stat-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Net Financial Position -->
                        <div class="col-xl-4 col-md-12 mb-4">
                            <div class="card dashboard-card card-primary h-100">
                                <div class="card-body">
                                    <div class="stat-label">Net Financial Position</div>
                                    <div class="stat-number">
                                        <?php
                                        $netPosition = $totalRevenue - $totalDebt;
                                        $isPositive = $netPosition >= 0;
                                        echo ($isPositive ? "+" : "") . number_format($netPosition);
                                        ?>
                                        <span class="metric-badge">RWF</span>
                                    </div>
                                    <div class="financial-highlight">
                                        <div class="sub-stat">Revenue - Debt = Net Position</div>
                                        <div class="sub-stat" style="color: <?php echo $isPositive ? '#1cc88a' : '#e74a3b'; ?>">
                                            <?php echo $isPositive ? 'Positive' : 'Negative'; ?> Financial Health
                                        </div>
                                    </div>
                                    <i class="fas fa-calculator stat-icon"></i>
                                </div>
                            </div>
                        </div>


                        <!-- Expenses ANALYSIS SECTION -->
                        <div class="col-12">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-bill section-icon"></i>
                                    Expenses Summary
                                </h3>
                            </div>
                        </div>

                        <!--Expenses Cards-->

                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card dashboard-card card-success h-100">
                                <div class="card-body">
                                    <div class="stat-label">Cleared Total Expenses</div>
                                    <div class="stat-number revenue-number">
                                        <?php
                                        include "../../web_db/connection.php";
                                        $totalExpense="SELECT SUM(amount_paid) AS total_expense FROM expenses_history";
                                        $result = $conn->query($totalExpense);
                                        $totalExpenseAmount = 0;
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $totalExpenseAmount = $row["total_expense"] ?? 0;
                                        }
                                        echo number_format($totalExpenseAmount);
                                                                                                                        
                                        ?>
                                        <span class="metric-badge">RWF</span>
                                    </div>
                                    <div class="financial-highlight">
                                        <div class="sub-stat">External Cars: <?php echo number_format($totalExpenseAmount); ?> RWF</div>                                        
                                    </div>
                                    <i class="fas fa-coins stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    
                        <!-- Uncleared balance  -->
                        <div class="col-xl-4 col-md-12 mb-4">
                            <div class="card dashboard-card card-primary h-100">
                                <div class="card-body">
                                    <div class="stat-label">Total Uncleared Balance</div>
                                    <div class="stat-number">
                                    <?php
                                        $unclearedBalance="SELECT SUM(ec.balance) AS total_balance
                                        FROM external_cars ec
                                        INNER JOIN external_car_expenses ece 
                                        ON ec.car_id = ece.car_id";
                                        $result = $conn->query($unclearedBalance);
                                        $unclearedBalanceAmount = 0; 
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $unclearedBalanceAmount = $row["total_balance"] ?? 0;
                                        }
                                        echo number_format($unclearedBalanceAmount);
                                    ?>
                                        <span class="metric-badge">RWF</span>
                                    </div>
                                    <div class="financial-highlight">
                                        <div class="sub-stat">Revenue - Debt = Net Position</div>
                                        <div class="sub-stat" style="color: <?php echo $isPositive ? '#1cc88a' : '#e74a3b'; ?>">
                                            <?php echo $isPositive ? 'Positive' : 'Negative'; ?> Financial Health
                                        </div>
                                    </div>
                                    <i class="fas fa-calculator stat-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Total Debt -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card dashboard-card card-warning h-100">
                                <div class="card-body">
                                    <div class="stat-label">Highest Expense Facility</div>
                                    <div class="stat-number debt-number">
                                        <?php
                                        $HighestExpense="SELECT * FROM expenses_history WHERE amount_paid = (SELECT MAX(amount_paid) FROM expenses_history)";
                                        $result = $conn->query($HighestExpense);
                                        $HighestExpenseAmount = 0;
                                        $carName="";
                                        $carPlate="";
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $HighestExpenseAmount = $row["amount_paid"] ?? 0;
                                            $carName=$row["car_name"];
                                            $carPlate=$row["plate"];
                                        }
                                        echo number_format($HighestExpenseAmount);
                                        ?>
                                        <span class="metric-badge">RWF</span>
                                    </div>
                                    <div class="financial-highlight">
                                        <div class="sub-stat">Name: <?php echo $carName; ?> RWF</div>
                                        <div class="sub-stat">Plate: <?php echo $carPlate ?> RWF</div>
                                    </div>
                                    <i class="fas fa-fw fa-car stat-icon"></i>
                                </div>
                            </div>
                        </div>
                        <!-- End of Expenses Cards -->
                    </div>

                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php include "../../web_includes/footer.php"; ?>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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

    <script>
        // Add some interactive animations
        document.addEventListener("DOMContentLoaded", function() {
            // Animate numbers on scroll
            const animateNumbers = () => {
                const numbers = document.querySelectorAll('.animate-number');
                numbers.forEach(num => {
                    num.style.opacity = '0';
                    num.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        num.style.transition = 'all 0.6s ease';
                        num.style.opacity = '1';
                        num.style.transform = 'translateY(0)';
                    }, Math.random() * 200);
                });
            };
            
            // Trigger animations
            setTimeout(animateNumbers, 500);
            
            // Add hover effects for cards
            const cards = document.querySelectorAll('.dashboard-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>

</body>

</html>
<?php
}
else{
    header("Location: ../../index.php");
    exit();
}

?>