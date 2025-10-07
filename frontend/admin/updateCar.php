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
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>GuestPro CMS| Update Existing Car</title>

    <!-- Custom fonts for this template-->
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" href="../../img/GuestProLogoReal.JPG" type="image/png">
    <link rel="stylesheet" href="../../css/custom.css">

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
                        <h1 class="h3 mb-0 text-gray-800">Updating Existing Car</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Earnings (Monthly)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">$40,000</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                                                Earnings (Annual)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">$215,000</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tasks
                                            </div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">50%</div>
                                                </div>
                                                <div class="col">
                                                    <div class="progress progress-sm mr-2">
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            style="width: 50%" aria-valuenow="50" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Pending Requests</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->
                    
                    <div class="row">
                        <?php
                            if (isset($_GET["car_id"])) {  
                            
                                include "../../web_db/connection.php";
                                $car_id=$_GET["car_id"];
                                $sql="SELECT * FROM cars WHERE car_id='$car_id'";
                                $query=$conn->query($sql);
                                if ($query->num_rows>0) {
                                    $row=$query->fetch_assoc(); 
                        ?>
                        <!-- Area Chart -->
                        <div class="col-12 grid-margin stretch-card">
                            <form class="p-4 shadow rounded bg-white" action="" method="POST">
                                <h4 class="mb-3" style="color: dodgerblue;">Car Details: </h4>
                                <hr />
                                <div class="mb-3">
                                <label for="car_name" class="form-label">Car Name</label>
                                <input type="text" class="form-control" value="<?php echo $row["car_name"]?>" id="car_name" name="car_name" placeholder="Enter Car Name" required />
                                </div>

                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select name="category_id" id="category" class="form-control" required>
                                    <option value="<?php echo $row["category_id"]?>">--Select Category</option>
                                    <?php 
                                    include "../../web_db/connection.php";
                                    $sql = "SELECT category_id, category_name FROM car_categories";
                                    $result = mysqli_query($conn, $sql);                                
                                    $categories_exist = mysqli_num_rows($result) > 0;                                
                                    if ($categories_exist): ?>                                            
                                            <?php while ($fetchedData = mysqli_fetch_assoc($result)) : ?>
                                                <option value="<?php echo $fetchedData['category_id']; ?>">
                                                    <?php echo htmlspecialchars($fetchedData['category_name']); ?>
                                                </option>
                                                <?php endwhile; ?>
                                                <?php else: ?>
                                                <option value="" disabled selected>⚠ First Register Category</option>
                                            <?php endif; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                <label for="Plate_number" class="form-label">Plate Number</label>
                                <input type="text" class="form-control" value="<?php echo $row["plate_number"]?>" id="Enter Plate Number" placeholder="Enter Plate Number" name="plateNumber" required />
                                </div>

                                <div class="mb-3">
                                <label for="Car_Type" class="form-label">Car Type</label>
                                <select class="form-control" name="car_type" id="" required>
                                    <option value="<?php echo $row["type"]?>"><?php echo $row["type"]?></option>
                                    <option value="Automatic">Automatic</option>
                                    <option value="Manual">Manual</option>
                                    <option value="Hybrid">Hybrid</option>
                                </select>
                                </div>

                                <div class="mb-3">
                                <label for="Fuel_Type" class="form-label">Fuel Type</label>
                                <select class="form-control" name="fuel_type" id="" required>
                                    <option value="<?php echo $row["fuel_type"]?>"><?php echo $row["fuel_type"]?></option>
                                    <option value="Super">Super</option>
                                    <option value="Gasoil">Gasoil</option>
                                    <option value="Hybrid">Hybrid</option>
                                    <option value="100% Electricity">100% Electricity</option>
                                </select>
                                </div>

                                <h4 class="mt-5 mb-3" style="color: dodgerblue;">Insurance : </h4>
                                <hr />
                                <div class="mb-3">
                                <label for="insurance_issue_date" class="form-label">Issued Date</label>
                                <input type="date" class="form-control" value="<?php echo $row["insurance_issued_date"]?>" id="insurance_issued_date" name="insurance_issued_date" required />
                                </div>
                                <div class="mb-3">
                                <label for="insurance_expiry_date" class="form-label">Expiry Date
                                    <span id="insurance_expiry_date_message"></span>
                                </label>
                                <input type="date" class="form-control" value="<?php echo $row["insurance_expiry_date"]?>" id="insurance_expiry_date" name="insurance_expiry_date" required />
                                </div>

                                <h4 class="mt-5 mb-3" style="color: dodgerblue;">Technical Control: </h4>
                                <hr />
                                <div class="mb-3">
                                <label for="technical_control_date" class="form-label">Issue Date</label>
                                <input type="date" class="form-control" value="<?php echo $row["control_issued_date"]?>" id="control_issued_date" name="control_issued_date" required />
                                </div>
                                <div class="mb-3">
                                <label for="insurance_expiry_date" class="form-label">Expiry Date
                                <span id="control_expiry_date_message"></span>
                                </label>
                                <input type="date" class="form-control" value="<?php echo $row["control_expiry_date"]?>" id="control_expiry_date" name="control_expiry_date" required />

                                <div class="text-end mt-4">
                                <input type="submit" value="Update" style="background-color: green;" class="btn btn-primary w-100" name="update">
                                </div>
                            </form>
                            <?php
                                include "../../web_db/connection.php" ;
                                if ($_SERVER["REQUEST_METHOD"]=="POST") { 
                                    $car_id=$_GET["car_id"];
                                    $Car_name=mysqli_real_escape_string($conn, $_POST["car_name"]);
                                    $category=mysqli_real_escape_string($conn, $_POST["category_id"]);
                                    $PlateNumber=mysqli_real_escape_string($conn, $_POST["plateNumber"]);
                                    $CarType=$_POST["car_type"];
                                    $fuel_type=$_POST["fuel_type"];
                                    $insurance_Issued_date=$_POST["insurance_issued_date"];
                                    $insurance_expiry_date=$_POST["insurance_expiry_date"];
                                    $control_issued_date=$_POST["control_issued_date"];
                                    $control_expiry_date=$_POST["control_expiry_date"];

                                    $check_query = "SELECT * FROM cars WHERE plate_number = '$PlateNumber' AND car_id != '$car_id'";
                                    $result = $conn->query($check_query);

                                    if ($result->num_rows > 0) {        
                                            // Plate number already exists
                                            include "../../system_messages/ErrorMessage.php";
                                    } 
                                    else{
                                        $stmt=$conn->prepare("UPDATE cars SET car_name=?, category_id=?, plate_number=?, type=?, fuel_type=?, insurance_issued_date=?,
                                        insurance_expiry_date=?, control_issued_date=?, control_expiry_date=?  WHERE car_id='$car_id'");
                                        $stmt->bind_param("sssssssss", $Car_name, $category, $PlateNumber, $CarType,$fuel_type, $insurance_Issued_date, $insurance_expiry_date,
                                        $control_issued_date, $control_expiry_date);
                                        $stmt->execute(); 

                                      if ($stmt) {
                                        include "../../system_messages/updateMessage.php";
                                         
                                        echo "<script>
                                        setTimeout(()=>{
                                           window.location.href='car_overview.php';
                                           }, 2000)
                                           </script>";
                                        $stmt->close();
                                      }                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
                                    }

                                }
                            ?>
                            
                            <?php
                                }
                              }
                            ?>
                            
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
    <script src="../../js/mycustomjs.js"></script>

</body>

</html>
<?php
}
else {
    header("Location: ../../index.php");
    exit();
}
?>