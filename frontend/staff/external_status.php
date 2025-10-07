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
    <meta name="description" content="">
    <meta name="author" content="">
    <title>GuestPro CMS| External Cars Overview</title>

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
                        <h1 class="h3 mb-2 text-gray-800">External Status Over view</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div><br>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Dynamic Table View</h6>
                    </div>
                    <div class="card-body">                                            
                        <div class="table-responsive">
                            
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>                                            
                                            <th>N#</th>
                                            <th>Name</th>
                                            <th>Plate</th>
                                            <th>Type</th>
                                            <th>Fuel</th>
                                            <th>Provider</th>
                                            <th>Brought On:</th>
                                            <th>Date to Return: </th>
                                            <th>Days in Service</th>
                                            <th>Status</th>                                            
                                        </tr>

                                    </thead>
                                    <tbody>
                                     <?php
                                     include "../../web_db/connection.php";
                                     $sql="SELECT * FROM external_cars";
                                     $result=$conn->query($sql);
                                        if ($result->num_rows > 0) {
                                            $count = 0;
                                            while ($row= $result->fetch_assoc()) {
                                                $count++;
                                    ?>                                       
                                        <tr>                                            
                                            <td><?php echo $count?></td>
                                            <td><?php echo $row["car_name"]?></td>
                                            <td><?php echo $row["plate_number"]?></td>
                                            <td><?php echo $row["type"]?></td>
                                            <td><?php echo $row["fuel_type"]?></td>
                                            <td><?php echo $row["provider"];?></td>
                                            <td><?php echo $row["date_brought"]?></td>
                                            <td><?php echo $row["expected_return_date"]?></td>
                                            <td><?php echo $row["days_in_service"]?></td>                                            
                                            <td><?php echo $row["status"]?></td>                                            
                                        </tr>
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
    <script src="../../vendor/chart.js/Chart.min.js"></script>
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