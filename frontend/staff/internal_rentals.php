<?php
session_start();
include "../../web_includes/auth.php";
include "../../web_db/connection.php";

if (isset($_SESSION["Userpassword"]) && isset($_SESSION["username"])){
    $user_logged_username= $_SESSION["username"];
    $user_logged_password= $_SESSION["Userpassword"];
    $SqlforUser = "SELECT * FROM users WHERE password='$user_logged_password' AND username='$user_logged_username'";
    $resForUser = mysqli_query($conn, $SqlforUser);
    $user = mysqli_fetch_assoc($resForUser);
    $user_id=$user["user_id"];

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

    <title>GuestPro CMS| All Rented Cars</title>

    <!-- Custom fonts for this template-->
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link  href="../../css/custom.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="icon" href="../../img/GuestProLogoReal.JPG" type="image/png">
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
                        <h1 class="h6 mb-2"><strong style="color: dodgerblue;">All Internal Rentals Provided By:</strong>
                        <?php echo $user['full_name']?></h1>                        
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i                        
                            class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div><br>

                    <div class="card shadow mb-4">
                       <div class="card-header py-3">
                         <h6 class="m-0 font-weight-bold text-primary">Basic Info Click More to View More</h6>
                    </div>

                    <div class="card-body">                                            
                        <div class="table-responsive">                          
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>                                            
                                            <th>N#</th>
                                            <th>Renter Names</th>
                                            <th>Renter ID</th>
                                            <th>Telephone</th>
                                            <th>Car Name</th>
                                            <th>Plate Number</th>
                                            <th>Type</th>
                                            <th>Rented On</th>                                                                                      
                                            <th >Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                     <?php
                                     include "../../web_db/connection.php";                              
                                     $sql = "SELECT r.renter_full_name, r.id_number, r.telephone, r.price, r.rent_date, r.return_date, r.days_in_rent, r.total_fee,
                                    c.car_id, c.car_name, c.plate_number, c.type,u.user_id, u.full_name AS provider_name FROM rentals r
                                    INNER JOIN cars c ON r.car_id = c.car_id
                                    INNER JOIN users u ON r.user_id = u.user_id
                                    WHERE c.status='Rented' AND r.user_id='$user_id' ORDER BY r.rent_date DESC";
                                     $result=$conn->query($sql);
                                        if ($result->num_rows > 0){
                                            $count = 0;
                                            while ($row= $result->fetch_assoc()) {
                                                $count++;
                                            ?>                                       
                                        <!-- inside your while loop -->
                                            <tr>                                            
                                                <td><?php echo $count?></td>
                                                <td><?php echo $row["renter_full_name"]?></td>
                                                <td><?php echo $row["id_number"]?></td>
                                                <td><?php echo $row["telephone"]?></td>
                                                <td><?php echo $row["car_name"]?></td>
                                                <td><?php echo $row["plate_number"]?></td>
                                                <td><?php echo $row["type"]?></td>
                                                <td><?php echo $row["rent_date"]?></td>
                                                <td>
                                                    <button type="button" class="btn-primary return-btn" 
                                                    data-toggle="modal"
                                                    data-target="#returnCarModal_<?php echo $row['car_id']; ?>" 
                                                    style="background:green;border-radius:5px;border:none; font-size:10px;">
                                                    <i class="fas fa-car" style="font-size: 20px;"></i> More
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Record Return Car Modal -->
                                            <div class="modal fade" id="returnCarModal_<?php echo $row['car_id']; ?>" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Rental Details for <b style="color:dodgerblue;"><?php echo $row["car_name"]?></b></h5>
                                                    <button class="close" type="button" data-dismiss="modal"><span>×</span></button>
                                                </div>

                                                <div class="modal-body">
                                                    <!--Visible Labels Of Data-->
                                                    <p><b style="color: dodgerblue;">Renter:</b> <?php echo $row["renter_full_name"]?></p>
                                                    <p><b style="color: dodgerblue;">Mobile:</b> <?php echo $row["telephone"]?></p>
                                                    <p><b style="color: dodgerblue;">Rent Date:</b> <?php echo $row["rent_date"]?></p>
                                                    <p><b style="color: dodgerblue;">Original Days:</b> <?php echo $row["days_in_rent"]?></p>
                                                    <p><b style="color: dodgerblue;">Negotiated Price:</b> <?php echo $row["price"]?> FRW</p>
                                                    <p><b style="color: dodgerblue;">Expected Total Revenue:</b> <span class="expected-fee" data-fee="<?php echo $row["total_fee"]?>"><?php echo $row["total_fee"]?> FRW</span></p>
                                                    <p><b style="color: dodgerblue;">Original Return Date:</b> <?php echo $row["return_date"]?></p>
                                                    <p><b style="color: dodgerblue;">Rented By:</b> <?php echo $row["return_date"]?></p>
                                                    <p><b style="color: dodgerblue;">Rented By:</b> <?php echo $row["provider_name"]?></p>
                                                                                                                                                           
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
    <script src="../../vendor/chart.js/Chart.min.js"></script>
    <script src="../../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../../vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../../js/mycustomjs.js"></script>

    <!-- Page level custom scripts -->
    <script src="../../js/demo/datatables-demo.js"></script>
    
    <script>
        $(document).ready(function () {
    $('.return-btn').click(function () {
        var userId = $(this).data('id');
        var userName = $(this).data('name');

        // Set the name in the modal
        $('#carName').text(userName);

        // Set the hidden input value for deletion
        $('#hiddenCarId').val(userId);
        document.getElementById('NameOfCar').value = userName;

        var row = $(this).closest('tr');

        $('#carName').text($(this).data('name'));  // Display in modal

        // Fill hidden inputs with that row’s data
        $('#modalRenterName').val(row.find('td:eq(1)').text().trim());
        $('#modalIdNumber').val(row.find('td:eq(2)').text().trim());
        $('#modalTelephone').val(row.find('td:eq(3)').text().trim());
        $('#modalCarName').val(row.find('td:eq(4)').text().trim());
        $('#modalCarPlate').val(row.find('td:eq(5)').text().trim());
        $('#modalRentDate').val(row.find('td:eq(7)').text().trim());        
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