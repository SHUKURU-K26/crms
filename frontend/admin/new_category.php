<?php
session_start();
include "../../web_includes/auth.php";
include "../../web_db/connection.php";
if ( isset($_POST['delete'])){
    $category_id = $_POST['category_id'];
    //Check if there's a Car that has the Same Category in Rent
    $sql="SELECT c.car_name,cc.category_name FROM cars c INNER JOIN car_categories cc ON c.category_id = cc.category_id WHERE cc.category_id = '$category_id'";
    $checkquery=$conn->query($sql);
    if ($checkquery->num_rows > 0) {
        echo "
        <div id='alertBox'>
        ⚠️ Error: This Category Can't be Deleted because there're Some Car(s) Registered in It. Please delete the Car(s) First.
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertBox = document.getElementById('alertBox');
            if (!alertBox) return;
            setTimeout(() => {
            alertBox.style.opacity = 0;
            setTimeout(() => alertBox.remove(), 500);
            window.location.href='new_category.php'
            }, 3000);
        });
        </script>
    ";

    }else{
        $deleteQuery = "DELETE FROM car_categories WHERE category_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $category_id);
        if ($stmt->execute()) {
            include "../../system_messages/deleteCategoryMessage.php";
        } else {
            echo "<script>alert('Failed to delete');</script>";
        }
    }
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
    <meta name="description" content="">
    <meta name="author" content="">

    <title>GuestPro CMS| Category Registration</title>

    <!-- Custom fonts for this template-->
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" href="../../img/GuestProLogoReal.JPG" type="image/png">
    <link rel="stylesheet" href="../../css/custom.css">

    <style>
        /* Center the specific card in its row */
        .centered-form-row {
            min-height: calc(100vh - 200px); /* Adjust for header/footer height */
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>

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
                        <h1 class="h3 mb-0 text-gray-800">New Category Registration</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div>

                    <!-- Cards Row -->
                    <?php include "../../web_includes/dashboard.php"?>

                            <!-- Centered Form Row -->                     
                                                <?php
                            // Fetch car categories from DB
                            include "../../web_db/connection.php";
                            $sql = "SELECT * FROM car_categories";
                            $result = mysqli_query($conn, $sql);
                            ?>
                            <div class="container mt-4">
                                <h4 class="mb-3" style="color: dodgerblue;">Car Categories</h4>
                                <table class="table table-bordered table-striped">
                                    <thead style="background-color: rgb(0, 95, 190);color:white;">
                                        <tr>
                                            <th>#</th>
                                            <th> Name</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $count = 0;
                                        if(mysqli_num_rows($result) > 0){
                                            $count++;
                                            while($row = mysqli_fetch_assoc($result)){
                                                ?>

                                                <tr>
                                                    <td><?php echo $count++?></td>
                                                    <td><?php echo $row['category_name']?></td>

                                                    <td>                                                        
                                                        <input type="hidden" name="category_id" value="<?php echo $row["category_id"]?>" id="category_id">                                                    
                                                        <button type="button" id="delete" class='btn btn-danger btn-sm delete-btn'
                                                            title="⚠ Delete" data-toggle="modal"
                                                            data-target="#deleteModal" data-id="<?php echo $row['category_id']; ?>" 
                                                            data-name="<?php echo htmlspecialchars($row['category_name']); ?>"
                                                            style="background:red;border-radius:5px;border:none; font-size:10px;"><i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='4' class='text-center'>No categories found</td></tr>";
                                        }
                                        ?>
                                        <!-- Last row with button -->                                        
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                <button type="button" style="width: 100%;background-color: rgb(0, 95, 190);border:none;" class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal">
                                                    Add New Category
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        
                             <!-- Add a Category Modal -->
                            <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        
                                        <div class="modal-header">
                                                <h5 class="modal-title" id="addCategoryModalLabel">Add New Car Category</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                        </div>
                                            
                                        <div class="modal-body">
                                                <form action="" method="POST">
                                                    <div class="mb-3">
                                                        <label class="form-label">Category Name</label>
                                                        <input type="text" name="category_name" class="form-control" required autofocus placeholder="Enter category name">
                                                    </div>
                                                    <button  type="submit" name="Save" class="btn btn-success">Save</button>
                                                </form>
                                                <?php
                                                if (isset($_POST['Save'])) {
                                                    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
                                                    // Insert the new category into the database
                                                    $insertQuery = "INSERT INTO car_categories (category_name) VALUES (?)";
                                                    $stmt = $conn->prepare($insertQuery);
                                                    $stmt->bind_param("s", $category_name);

                                                    if ($stmt->execute()) {
                                                        include "../../system_messages/newCategoryMessage.php";                                                        
                                                        exit();
                                                    } else {
                                                        echo "<script>alert('Failed to add category.');</script>";
                                                    }
                                                }
                                                ?>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Confirmation Modal -->
                            <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Are you Sure to delete <b><strong id="category_name"></strong> </b>Category?</h5>                
                                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>

                                        </div>
                                        <div class="modal-body">Click "Delete" to Permanently Delete: </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                            <form action="" method="POST">       
                                                <input type="hidden" name="category_id" id="hidden_category_id" />                 
                                                <input type="submit" name="delete" class="btn btn-primary" style="background-color: red;border:none;" value="Delete"/>
                                            </form>
                                        </div>
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

    <!-- Page level custom scripts -->
    <script src="../../js/demo/chart-area-demo.js"></script>
    <script src="../../js/demo/chart-pie-demo.js"></script>
    <script src="../../js/mycustomjs.js"></script>

     <script>
        $(document).ready(function () {
            $('.delete-btn').click(function () {
                var category_id = $(this).data('id');
                var category_name = $(this).data('name');

                // Set the name in the modal
                $('#category_name').text(category_name);

                // Set the hidden input value for deletion
                $('#hidden_category_id').val(category_id);
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
