<?php
session_start();
include "../../web_includes/auth.php";
include "../../web_db/connection.php";

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

    <title>GuestPro CMS| Rental History</title>

    <!-- Custom fonts for this template-->
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link  href="../../css/custom.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" href="../../img/GuestProLogoReal.JPG" type="image/png">
    <link href="../../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
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
                        <h1 class="h3 mb-6" style="color: dodgerblue;font-weight:bold;font-family:Cambria;">External History Overall</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div><br>
                    <!-- Content Row -->
                    <?php include "../../web_includes/dashboard.php"?>

                    <!-- Content Row -->


                <div class="card shadow mb-4" >
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Rental History Dynamic Data</h6>
                    </div>
                    <div class="card-body" >                                            
                        <div class="table-responsive" >
                            
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" >
                                    <thead>
                                        <tr>                                            
                                            <th>N#</th>
                                            <th style="min-width: 120px;">Names</th>
                                            <th style="min-width: 100px;">Mobile</th>
                                            <th style="min-width: 100px;">National ID</th>
                                            <th style="min-width: 100px;">Car Name</th>
                                            <th style="min-width: 70px;">Plate</th>
                                            <th style="min-width: 80px;">Rent Date</th>
                                            <th style="min-width: 80px;">Exp: Return</th>
                                            <th style="min-width: 50px;">Length</th> 
                                            <th style="min-width: 80px;">Returned On</th>
                                            <th style="min-width: 100px;"> Fee</th>
                                            <th style="min-width: 120px;"> Provider</th>
                                            <th style="min-width: 100px;">Total Income</th>
                                            <th style="min-width: 100px;"> Status</th>
                                            <th>More</th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        <?php
                                        include "../../web_db/connection.php";
                                        $sql="SELECT * FROM external_rental_history WHERE lifecycle_status = 'active' ORDER BY date_rented_on DESC";
                                        $result=$conn->query($sql);
                                            if ($result->num_rows > 0) {
                                                $count = 0;
                                                while ($row= $result->fetch_assoc()){
                                                    $count++;
                                                ?>                                       
                                                <tr>                                            
                                                    <td><?php echo $count?></td>
                                                    <td>
                                                        <form action="" method="POST" value="<?php echo $row["external_history_id"]?>">
                                                            <input type="hidden" name="external_history_id">
                                                        </form>
                                                        <?php echo $row["renter_names"]?>
                                                    </td>
                                                    <td><?php echo $row["renter_phone"]?></td>
                                                    <td><?php echo $row["renter_national_id"]?></td>
                                                    <td><?php echo $row["car_name"]?></td>
                                                    <td><?php echo $row["car_plate"]?></td>
                                                    <td><?php echo $row["date_rented_on"]?></td>
                                                    <td><?php echo $row["expected_return_date"]?></td>
                                                    <td><?php echo $row["days_in_rent"]." Days"?></td>
                                                    <td><?php echo $row["date_returned_on"]?></td>
                                                    <td><?php echo $row["rental_fee"]." FRW"?></td>
                                                    <td><?php echo $row["provider_names"]?></td>
                                                    <td style="color: green;"><?php echo $row["revenue_received"]." FRW"?></td>
                                                    <td>
                                                        <?php if ($row["revenue_status"] === "Adjusted"){?>
                                                            <a href="#" class="btn btn-danger btn-circle btn-sm">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                            </a> Adjusted
                                                        <?php } else if($row["revenue_status"]=="Expected") { ?>
                                                            <a href="#" class="btn btn-success btn-circle btn-sm">
                                                                <i class="fas fa-check"></i>
                                                            </a> Expected
                                                        <?php } else{?>
                                                            <a href="#" class="btn btn-danger btn-circle btn-sm">
                                                                <i class="fas fa-wallet"></i>
                                                            </a> In Debt
                                                        <?php } ?>                                                        
                                                    </td>
                                                    <td>
                                                        <?php if ($row["revenue_status"] === "Adjusted"){?>
                                                            <button type="button" class="btn btn-warning btn-sm more-btn" 
                                                                data-toggle="modal"
                                                                data-target="#detailsModal"                                                                                                                                 
                                                                data-external_history_id="<?= $row['external_history_id']; ?>"
                                                                data-car_name="<?= $row['car_name']; ?>"
                                                                data-rent_date="<?= $row['date_rented_on']; ?>"
                                                                data-expected_return_date="<?= $row['expected_return_date']; ?>"
                                                                data-negotiated_price="<?= $row['rental_fee']; ?>"
                                                                data-total_revenue_expected="<?= $row["expected_revenue"]; ?>"
                                                                data-date_returned_on="<?= $row["date_returned_on"]; ?>"
                                                                data-total_revenue_received="<?= $row["revenue_received"]; ?>"
                                                                data-refund_due="<?= $row["refund_due"]; ?>"                                                                
                                                                style="min-width:105px;" title="About the Adjustment">
                                                                <i class="fas fa-info-circle"></i> More Info
                                                            </button>
                                                        <?php } else if($row["revenue_status"]=="Expected"){ ?>
                                                            <span class="text-muted">
                                                                Expected
                                                            </span>
                                                        <?php }else{?>
                                                            <span class="text-muted">
                                                                In Debt
                                                            </span>
                                                        <?php } ?>
                                                    </td>                                         
                                                </tr>
                                                <!-- Bootstrap Modal For Reason to Adjusted -->
                                                <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                    <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title" id="detailsModalLabel"><i class="fas fa-exclamation-triangle" style="color: yellow;"></i> Reason to Adjusted Income !</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">                                                       
                                                        <!-- Dynamic Details -->
                                                         <p><i class="fas fa-info-circle"></i>The Expected Total Revenue
                                                            was Adjusted Due to Change in Return Date as Follows:</p>
                                                        <ul class="list-group">
                                                            <li class="list-group-item"><strong>Car Name: </strong> <span id="car_name"></span></li>
                                                            <li class="list-group-item"><strong>Rent Date: </strong><span id="rent_date"></span></li>
                                                            <li class="list-group-item"><strong>Expected Return Date: </strong> <span id="expected_return_date"></span></li>
                                                            <li class="list-group-item"><strong>Negotiated Price: </strong> <span id="negotiated_price"></span></li>
                                                            <li class="list-group-item"><strong>Total Revenue Expected: </strong> <span id="total_revenue_expected"></span></li>
                                                            <li class="list-group-item"><strong>Date Returned On: </strong> <span id="date_returned_on"></span></li>
                                                            <li class="list-group-item"><strong>Revenue Received: </strong> <span id="revenue_received"></span></li>
                                                            <li class="list-group-item"><strong>Refund Due: </strong> <span id="refund_due"></span></li>
                                                        </ul>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                    </div>
                                                </div>
                                                </div>
                                            <?php 
                                                }
                                            } 
                                            ?> 
                                    </tbody>

                                </table>
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
    <script src="../../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../../vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../../js/mycustomjs.js"></script>

    <!-- Page level custom scripts -->
    <script src="../../js/demo/datatables-demo.js"></script>
    
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll("#dataTable tbody tr td:nth-child(10)").forEach(td => {
            const statusText = td.textContent.trim().toLowerCase();
            if (statusText === "available") {
                td.classList.add("status-available");
            } else if (statusText === "rented") {
                td.classList.add("status-rented");
            }
        });
    });

    function formatRWF(amount){
        return Number(amount).toLocaleString('en-US') + ' RWF';
    }
    document.getElementById('negotiated_price').style.color="orange";
    document.getElementById('total_revenue_expected').style.cssText="color:darkgreen;font-weight:bold;";
    document.getElementById('revenue_received').style.cssText="color:green;font-weight:bold;";
    document.getElementById('refund_due').style.cssText = "color:red; font-weight:bold";

    document.querySelectorAll('.more-btn').forEach(button => {
        button.addEventListener('click', function() {
        document.getElementById('car_name').textContent=this.dataset.car_name
        document.getElementById('rent_date').textContent = this.dataset.rent_date;
        document.getElementById('expected_return_date').textContent = this.dataset.expected_return_date;
        document.getElementById('negotiated_price').textContent = formatRWF(this.dataset.negotiated_price);
        document.getElementById('total_revenue_expected').textContent = formatRWF(this.dataset.total_revenue_expected);
        document.getElementById('date_returned_on').textContent = this.dataset.date_returned_on;
        document.getElementById('revenue_received').textContent = formatRWF(this.dataset.total_revenue_received);
        document.getElementById('refund_due').textContent = formatRWF(this.dataset.refund_due);
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