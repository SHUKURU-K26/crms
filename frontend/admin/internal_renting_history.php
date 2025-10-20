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

    <title>GuestPro CMS| Internal Renting History</title>

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
                        <h1 class="h3 mb-6" style="color: dodgerblue;font-weight:bold;font-family:Cambria;">Internal Renting History</h1>
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
                                            <th style="min-width: 120px;">Renter</th>
                                            <th style="min-width: 100px;">Mobile</th>
                                            <th style="min-width: 100px;">National ID</th>
                                            <th style="min-width: 100px;">Car Name</th>
                                            <th style="min-width: 70px;">Plate</th>
                                            <th style="min-width: 80px;">Rent Date</th>                                            
                                            <th style="min-width: 50px;">Days</th>                                             
                                            <th style="min-width: 100px;">N/Fee</th>   
                                            <th style="min-width: 90px;">Provider</th>                                                                      
                                        </tr>

                                    </thead>
                                    <tbody>
                                        <?php
                                        include "../../web_db/connection.php";
                                        $sql="SELECT * FROM renting_history  WHERE history_type='internal' ORDER BY rent_date ASC";
                                        $result=$conn->query($sql);
                                            if ($result->num_rows > 0) {
                                                $count = 0;
                                                while ($row= $result->fetch_assoc()){
                                                    $count++;
                                                ?>                                       
                                                <tr>                                            
                                                    <td><?php echo $count?></td>                                                    
                                                    <td><?php echo $row["renter_names"]?></td>
                                                    <td><?php echo $row["phone"]?></td>
                                                    <td><?php echo $row["id_number"]?></td>
                                                    <td><?php echo $row["car_name"]?></td>
                                                    <td><?php echo $row["plate"]?></td>
                                                    <td><?php echo $row["rent_date"]?></td>
                                                    <td><?php echo $row["duration"]." Days"?></td>
                                                    <td style="color: green; font-weight:bold;"><?php echo $row["rent_amount"]." FRW"?></td>
                                                    <td style="color: red;font-weight:bold;"><?php echo $row["rented_by"]?></td>                                                                               
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
    <script src="../../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../../vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../../js/mycustomjs.js"></script>

    <!-- Page level custom scripts -->
    <script src="../../js/demo/datatables-demo.js"></script>   
</body>
</html>
<?php
}
else {
    header("Location: ../../index.php");
    exit();
}
?>