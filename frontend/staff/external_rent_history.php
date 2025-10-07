<?php
session_start();
include "../../staff_web_includes/staff_auth.php";
include "../../web_db/connection.php";

if (isset($_SESSION["Userpassword"]) && isset($_SESSION["username"])){
    $user_logged_password= $_SESSION["Userpassword"];
    $SqlforUser = "SELECT * FROM users WHERE password='$user_logged_password'";
    $resForUser = mysqli_query($conn, $SqlforUser);
    $user = mysqli_fetch_assoc($resForUser);
    $user_full_names=$user['full_name'];


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
    <link rel="icon" href="../../img/GuestProLogoReal.JPG" type="image/png">

    <!-- Custom styles for this template-->
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php
         include "../../staff_web_includes/staff_menu.php";
         ?>
         
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            
            <!-- Main Content -->
            <div id="content">
                        
                <!-- Topbar -->
                <?php
                  include "../../staff_web_includes/staff_topbar.php";
                ?>
                <!-- End of Topbar -->
                
                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <div style="display: flex;justify-content:space-between;">
                        <h1 class="h4 mb-6" style="color: dodgerblue;font-weight:bold;font-family:Cambria;">Your External Renting History</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div><br>
                    <!-- Content Row -->
                    <?php include "../../staff_web_includes/staff_dashboard.php"?>

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
                                            <th>More</th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        <?php
                                        include "../../web_db/connection.php";
                                        $sql="SELECT * FROM external_rental_history WHERE provider_names='$user_full_names' ORDER BY date_rented_on DESC";
                                        $result=$conn->query($sql);
                                            if ($result->num_rows > 0) {
                                                $count = 0;
                                                while ($row= $result->fetch_assoc()){
                                                    $count++;
                                                ?>                                       
                                                <tr>                                            
                                                    <td><?php echo $count?></td>
                                                    <td>
                                                        <form action="" method="POST" value="<?php echo $row["history_id"]?>">
                                                            <input type="hidden" name="history_id">
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
                                                    <td>                                                        
                                                            <button type="button" class="btn btn-success btn-sm more-btn" 
                                                                data-toggle="modal"
                                                                data-target="#detailsModal"                                                                                                                                 
                                                                data-history_id="<?= $row['history_id']; ?>"
                                                                data-car_name="<?= $row['car_name']; ?>"
                                                                data-plate="<?= $row['car_plate']; ?>"
                                                                data-rent_date="<?= $row['date_rented_on']; ?>"
                                                                data-expected_return_date="<?= $row['expected_return_date']; ?>"                                                                                                                               
                                                                data-date_returned_on="<?= $row["date_returned_on"]; ?>"                                                                                                                     
                                                                data-revenue_status="<?= $row["revenue_status"]; ?>"                                                                                                                     
                                                                style="min-width:105px;" title="More Info">
                                                                <i class="fas fa-info-circle"></i> More Info
                                                            </button>                                                                                                                    
                                                    </td>                                         
                                                </tr>
                                                <!-- Bootstrap Modal For Statement -->
                                                <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-md modal-dialog-centered">
                                                    <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title" id="detailsModalLabel"><i class="fas fa-check-circle"></i> Record statement.</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <!-- Dynamic Details -->
                                                         <p><i class="fas fa-info-circle"></i>Statement
                                                            <li class="list-group-item"><strong>Car Name: </strong> <span id="car_name"></span></li>
                                                            <li class="list-group-item"><strong>Plate: </strong> <span id="car_plate"></span></li>
                                                            <li class="list-group-item"><strong>Rent Date: </strong><span id="rent_date"></span></li>
                                                            <li class="list-group-item"><strong>Expected Return Date: </strong> <span id="expected_return_date"></span></li>                                                            
                                                            <li class="list-group-item"><strong>Date Returned On: </strong> <span id="date_returned_on"></span></li>                                            
                                                            <li class="list-group-item"><strong>Status Statement: </strong> <span id="revenue_status"></span> Due to Date of Return.</li>                                                            
                                                            <p style="font-size: 4em;color:green; margin-left:40%;" id="indicator"><i class="fas fa-check-circle"></i></p>
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
    
    document.querySelectorAll('.more-btn').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('car_name').textContent = this.dataset.car_name;
        document.getElementById('car_plate').textContent = this.dataset.plate;
        document.getElementById('rent_date').textContent = this.dataset.rent_date;
        document.getElementById('expected_return_date').textContent = this.dataset.expected_return_date;
        document.getElementById('date_returned_on').textContent = this.dataset.date_returned_on;
        document.getElementById('revenue_status').textContent = this.dataset.revenue_status;
        
        // Debugging: log the revenue status to console
        console.log('Revenue Status: ', this.dataset.revenue_status);

        // Check if 'Adjusted' and change icon
        if (this.dataset.revenue_status === "Adjusted") {
            console.log('Changing icon to exclamation!');
            document.getElementById("indicator").innerHTML = '<i class="fas fa-exclamation-triangle" style="color: yellow;"></i>';
        } else {
            console.log('Changing icon to checkmark');
            document.getElementById("indicator").innerHTML = '<i class="fas fa-check-circle" style="color: green;"></i>';
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