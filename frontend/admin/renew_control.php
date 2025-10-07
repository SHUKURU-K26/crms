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

    <title>GuestPro CMS| Renew Techinical Control</title>

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
                        <h1 class="h3 mb-0 text-gray-800">Renew Techinical Control</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div>

                    <!-- Content Row -->
                    <?php
                    include "../../web_includes/dashboard.php";
                    ?>
                    <!-- Content Row -->

                    <div class="row">
                        <?php
                        include "../../web_db/connection.php";
                          if (isset($_GET['car_id'])) {
                              $car_id = intval($_GET['car_id']); // sanitize input
                              $sql = "SELECT * FROM cars WHERE car_id = ?";
                              $stmt = $conn->prepare($sql);
                              $stmt->bind_param("i", $car_id);
                              $stmt->execute();
                              $result = $stmt->get_result();
                              if ($result->num_rows > 0) {
                                  $row = $result->fetch_assoc();
                        ?>
                                    <div class="col-12 grid-margin stretch-card">
                                        <div class="card">
                                            <div class="card-body">
                                                <h4 class="card-title"><i class="fas fa-exclamation-triangle" style="color: red;"></i>
                                                    Control Techinc Renew of <?php echo $row["car_name"]?>
                                                </h4>
                                                <p class="card-description">
                                                    Set Renew & Expiration Dates Accordingly:
                                                </p>
                                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST" class="forms-sample">                                        

                                                    <div class="form-group">
                                                        <label for="exampleSelectGender">Car Name</label>
                                                        <input type="text" class="form-control" id="car_name" name="car_name" value="<?php echo $row['car_name']; ?>" readonly>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="exampleSelectGender">Car Plate Number</label>
                                                        <input type="text" class="form-control" id="plate_number" name="plate_number" value="<?php echo $row['plate_number']; ?>" readonly>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="exampleInputPassword4">Date of Renew:
                                                            <span style="color:red;font-weight:bold;">*</span>
                                                        </label>
                                                        <input type="date" class="form-control" id="renew_date" name="renew_date" required>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="exampleInputPassword4">Date of Expiration:
                                                        <span style="color:red;font-weight:bold;" id="invalid_input_message">*</span>
                                                        </label>
                                                        <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
                                                    </div>                                        
                                                                                                                                                                    
                                                    <button type="submit" class="save-btns" name="Save" ><i class="fa fa-check"></i> Done</button>
                                                    <button type="reset" class="btn btn-light"><i class="fa fas-close"></i> Reset</button>
                                                </form>

                                            </div>
                                        </div>
                                    </div> 
                                <?php                                      
                                    } else {
                                        echo "<h3>Car not found.</h3>";
                                    }
                                    $stmt->close();
                                                                    
                                }
      
                                ?>
                                
                                <?php
                                 if (isset($_POST['Save'])) {   
                                    $car_name = $_POST['car_name'];                                                                       
                                     $plate_number = $_POST['plate_number'];                        
                                     $renew_date = $_POST['renew_date'];
                                     $expiry_date = $_POST['expiry_date'];
                                     $updateSql = "UPDATE cars SET control_issued_date = ?, control_expiry_date = ? WHERE plate_number = ?";
                                     $updateStmt = $conn->prepare($updateSql);
                                     $updateStmt->bind_param("sss", $renew_date, $expiry_date, $plate_number);

                                     if ($updateStmt->execute()) {                                        
                                        echo "
                                        <div id='successAlertBox'>
                                          ✅ $car_name Techinical Control renewed <strong>Successfully. Valid till $expiry_date</strong>
                                        </div>
                                        
                                          <script>
                                          document.addEventListener('DOMContentLoaded', function() {
                                              const alertBox = document.getElementById('successAlertBox');
                                              if (!alertBox) return;
                                              setTimeout(() => {
                                              alertBox.style.opacity = 0;
                                              setTimeout(() => alertBox.remove(), 500);
                                                window.location.href = 'car_overview.php';
                                              }, 3000);
                                          });
                                          </script>
                                        ";
                                        
                                     } else {
                                         echo "<script>alert('Error Occured while Renewing Control Techinic. Please try again.');</script>";
                                     }
                                     $updateStmt->close();
                                    # code...
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

    <!--My Custom Js-->
    <script>
        let renew_date=document.getElementById("renew_date");
        let expiry_date=document.getElementById("expiry_date");

        expiry_date.addEventListener('input', function(){    
            if(expiry_date.value <= renew_date.value){
                document.getElementById("expiry_date").value="";
                document.getElementById("invalid_input_message").textContent="Must be Greater than Renew Date!";
                document.getElementById("invalid_input_message").style.color="red";
                document.getElementById("invalid_input_message").style.fontSize="12px";
                setTimeout(function(){
                document.getElementById("invalid_input_message").textContent="";
            }, 3000);
            }
            else if(expiry_date.value > renew_date.value){
                document.getElementById("invalid_input_message").textContent="Still in Use!";
                document.getElementById("invalid_input_message").style.color="green";

                setTimeout(function(){
                document.getElementById("invalid_input_message").textContent="";
            }, 2000); 
            }
        })
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