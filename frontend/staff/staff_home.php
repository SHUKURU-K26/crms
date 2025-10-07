<?php
session_start();
include "../../staff_web_includes/staff_auth.php";
include "../../web_db/connection.php";
if (isset($_SESSION["Userpassword"]) && isset($_SESSION["username"])){
    $user_logged_password= $_SESSION["Userpassword"];
    $logged_in_username=$_SESSION["username"];
    $SqlforUser = "SELECT * FROM users WHERE password='$user_logged_password' AND username='$logged_in_username'";
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

    <title>Guest Pro Car Management System</title>

    <!-- Custom fonts for this template-->
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="icon" href="../../img/GuestProLogoReal.JPG" type="image/png">
    <!-- Custom styles for this template-->
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">

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

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0" style="color: #970000;font-weight:bold;">Dashboard Stats</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                            class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div>
                                    
                    <!-- Content Row -->
                     <p style="color: dodgeblue;font-weight:bold;font-size:20px;">Internal Cars Stats</p> 
                    <?php include "../../web_db/connection.php"?>
                    <div class="row">
                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">All Internal Cars</div>
                                            <div class="text-primary" style="font-size:2.3em;font-weight:bold;">
                                                <?php
                                                // Count of All Total Cars in the System                                                    
                                                    $totalCarsSql="SELECT COUNT(car_id) AS total_cars FROM cars";
                                                    $result=$conn->query($totalCarsSql);
                                                    if ($result->num_rows>0) {
                                                    $row=$result->fetch_assoc();
                                                    $totalCars=$row["total_cars"];
                                                    echo $totalCars;
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-car fa-2x" style="color: rgb(0, 95, 190);"></i>
                                            <i class="fas fa-car fa-2x" style="color: rgb(0, 95, 190);"></i>
                                            <i class="fas fa-car fa-2x" style="color: rgb(0, 95, 190);"></i>
                                            <i class="fas fa-car fa-2x" style="color: rgb(0, 95, 190);"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Available For Rent
                                            </div>

                                            <div class="text-success" style="font-size:2.3em;font-weight:bold;">
                                                <?php                                                
                                                // Count of Cars Available for Rent
                                                $availableCars="SELECT COUNT(car_id) AS available_cars FROM cars WHERE status='available'";
                                                $resultForAvailable=$conn->query($availableCars);
                                                if ($resultForAvailable->num_rows >0){
                                                  $available_rows=$resultForAvailable->fetch_assoc();
                                                  $cars_available=$available_rows["available_cars"];
                                                  echo $cars_available;
                                                }
                                                 ?>
                                            </div>

                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-car fa-2x text-success"></i>
                                             <span style="font-size: 20px;" class="text-success">X<?php echo $cars_available?></span> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">All Rented Cars</div>
                                            <div class="text-danger" style="font-size:2.3em;font-weight:bold;">
                                                <?php                                                
                                                //Count of Cars in Rent Mode
                                                $rentedCars="SELECT COUNT(car_id) AS rented_cars FROM cars WHERE status='rented'";
                                                $resultForRented=$conn->query($rentedCars);
                                                if ($resultForRented->num_rows >0){
                                                $rented_rows=$resultForRented->fetch_assoc();
                                                $cars_rented=$rented_rows["rented_cars"];
                                                echo $cars_rented;
                                                }
                                                 ?>
                                            </div>

                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-car fa-2x text-danger"></i>
                                            <span style="font-size: 20px;" class="text-danger">X<?php echo $cars_rented?></span> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                                        <!--All Cars Rented By Logged In Person-->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Rented By you: </div>
                                            <div class="text-danger" style="font-size:2.3em;font-weight:bold;">
                                                <?php                                                
                                                //Count of Cars in Rent Mode
                                                $rentedCars="SELECT COUNT(car_id) AS rented_cars 
                                                FROM rentals r JOIN users u ON r.user_id = u.user_id WHERE  u.full_name='$user_full_names'";
                                                $resultForRented=$conn->query($rentedCars);
                                                if ($resultForRented->num_rows >0){
                                                $rented_rows=$resultForRented->fetch_assoc();
                                                $cars_rented=$rented_rows["rented_cars"];
                                                echo $cars_rented;
                                                }
                                                 ?>
                                            </div>

                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-car fa-2x text-danger"></i>
                                            <span style="font-size: 20px;" class="text-danger">X<?php echo $cars_rented?></span> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                                                   
                    </div>
                    <!--End of Internal cars Stats-->

                    <!-----------///////////////////////////////////////////////////////////////////////---------------->
                    <div class="divider">
                        <hr>
                    </div>

                    <p style="color: dodgeblue;font-weight:bold;font-size:20px;">External Cars Stats</p> 



                    <!--Start of External Cars Stats-->

                    <div class="row">
                        <!-- All External Cars-->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">All External Cars</div>
                                            <div class="text-primary" style="font-size:2.3em;font-weight:bold;">
                                                <?php
                                                // Count of All Total Cars in the System                                                    
                                                    $totalCarsSql="SELECT COUNT(car_id) AS total_cars FROM external_cars";
                                                    $result=$conn->query($totalCarsSql);
                                                    if ($result->num_rows>0) {
                                                    $row=$result->fetch_assoc();
                                                    $totalCars=$row["total_cars"];
                                                    echo $totalCars;
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-car fa-2x" style="color: rgb(0, 95, 190);"></i>
                                            <i class="fas fa-car fa-2x" style="color: rgb(0, 95, 190);"></i>
                                            <i class="fas fa-car fa-2x" style="color: rgb(0, 95, 190);"></i>
                                            <i class="fas fa-car fa-2x" style="color: rgb(0, 95, 190);"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Available For Rent
                                            </div>

                                            <div class="text-success" style="font-size:2.3em;font-weight:bold;">
                                                <?php                                                
                                                // Count of Cars Available for Rent
                                                $availableCars="SELECT COUNT(car_id) AS available_cars FROM external_cars WHERE status='available'";
                                                $resultForAvailable=$conn->query($availableCars);
                                                if ($resultForAvailable->num_rows >0){
                                                  $available_rows=$resultForAvailable->fetch_assoc();
                                                  $cars_available=$available_rows["available_cars"];
                                                  echo $cars_available;
                                                }
                                                 ?>
                                            </div>

                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-car fa-2x text-success"></i>
                                             <span style="font-size: 20px;" class="text-success">X<?php echo $cars_available?></span> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">All Rented Cars</div>
                                            <div class="text-danger" style="font-size:2.3em;font-weight:bold;">
                                                <?php                                                
                                                //Count of Cars in Rent Mode
                                                $rentedCars="SELECT COUNT(car_id) AS rented_cars FROM external_cars WHERE status='rented'";
                                                $resultForRented=$conn->query($rentedCars);
                                                if ($resultForRented->num_rows >0){
                                                $rented_rows=$resultForRented->fetch_assoc();
                                                $cars_rented=$rented_rows["rented_cars"];
                                                echo $cars_rented;
                                                }
                                                 ?>
                                            </div>

                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-car fa-2x text-danger"></i>
                                            <span style="font-size: 20px;" class="text-danger">X<?php echo $cars_rented?></span> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                                 <!--All Cars Rented By Logged In Person-->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Rented By you: </div>
                                            <div class="text-danger" style="font-size:2.3em;font-weight:bold;">
                                                <?php                                                
                                                //Count of Cars in Rent Mode
                                                $rentedCars="SELECT COUNT(car_id) AS rented_cars 
                                                FROM external_rentals r JOIN users u ON r.user_id = u.user_id WHERE  u.full_name='$user_full_names'";
                                                $resultForRented=$conn->query($rentedCars);
                                                if ($resultForRented->num_rows >0){
                                                $rented_rows=$resultForRented->fetch_assoc();
                                                $cars_rented=$rented_rows["rented_cars"];
                                                echo $cars_rented;
                                                }
                                                 ?>
                                            </div>

                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-car fa-2x text-danger"></i>
                                            <span style="font-size: 20px;" class="text-danger">X<?php echo $cars_rented?></span> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                                                   
                    </div>
                    <!-- Content Row -->
                    
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
    <script src="../../vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../../js/demo/chart-area-demo.js"></script>
    <script src="../../js/demo/chart-pie-demo.js"></script>

</body>

</html>
<?php
}
else {
    header("Location: ../../index.php");
    exit();
}
?>