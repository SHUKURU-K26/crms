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
   
   // Handle the extend submission
   if(isset($_POST['extend'])) {
       $car_id = $_POST['car_id'];
       $new_return_date = $_POST['new_return_date'];
       $date_brought = $_POST['date_brought'];
       $negotiated_price = $_POST['negotiated_price'];
       $car_name = $_POST['car_name'];
       
       // First, get the current record to know what was already paid
       $check_sql = "SELECT total_spending, balance, use_status FROM external_cars WHERE car_id = ?";
       $check_stmt = $conn->prepare($check_sql);
       $check_stmt->bind_param("i", $car_id);
       $check_stmt->execute();
       $check_result = $check_stmt->get_result();
       $current_data = $check_result->fetch_assoc();
       
       $old_total_spending = $current_data['total_spending'];
       $old_balance = $current_data['balance'];
       $old_use_status = $current_data['use_status'];
       
       // Calculate how much was already paid
       $amount_already_paid = $old_total_spending - $old_balance;
       
       // Calculate new days and total spending
       $date1 = new DateTime($date_brought);
       $date2 = new DateTime($new_return_date);
       $interval = $date1->diff($date2);
       $new_days = $interval->days;
       $new_total_spending = $new_days * $negotiated_price;
       
       // Calculate new balance (new total - amount already paid)
       $new_balance = $new_total_spending - $amount_already_paid;
       
       // Determine new use_status
       $new_use_status = '';
       if($new_balance <= 0) {
           $new_use_status = 'Fully Paid';
           $new_balance = 0; // Make sure balance doesn't go negative
       } elseif($amount_already_paid > 0 && $new_balance > 0) {
           $new_use_status = 'Half Paid';
       } else {
           $new_use_status = 'Unpaid';
       }
       
       // Update the database
       $update_sql = "UPDATE external_cars SET 
                      expected_return_date = ?,
                      days_in_service = ?,
                      total_spending = ?,
                      balance = ?,
                      use_status = ?
                      WHERE car_id = ?";
       
       $stmt = $conn->prepare($update_sql);
       $stmt->bind_param("siiisi", $new_return_date, $new_days, $new_total_spending, $new_balance, $new_use_status, $car_id);
       
       if($stmt->execute()) {
           echo "
               <div id='successAlertBox' style='position: fixed; top: 20px; right: 20px; z-index: 9999; background: linear-gradient(135deg, #1cc88a, #13855c); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.3); max-width: 400px;'>
                   <i class='fas fa-check-circle'></i> <strong>$car_name</strong> Retention Period Extended!<br>
                   <small style='display: block; margin-top: 10px;'>
                       Valid until: <strong>$new_return_date</strong><br>
                       Already Paid: <strong>" . number_format($amount_already_paid) . " RWF</strong><br>
                       New Balance: <strong>" . number_format($new_balance) . " RWF</strong><br>
                       Status: <strong>$new_use_status</strong>
                   </small>
               </div>
               <script>
               document.addEventListener('DOMContentLoaded', function() {
                   const alertBox = document.getElementById('successAlertBox');
                   setTimeout(() => {
                       alertBox.style.transition = 'all 0.5s ease';
                       alertBox.style.transform = 'translateX(100%)';
                       alertBox.style.opacity = '0';
                       setTimeout(() => {
                           alertBox.remove();
                           window.location.href = window.location.href;
                       }, 500);
                   }, 4000);
               });
               </script>
           ";
       } else {
           echo "<script>alert('Error extending retention period!');</script>";
       }
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

    <title>GuestPro CMS| Extend Retention</title>

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
                        <h1 class="h3 mb-6" style="color: dodgerblue;font-weight:bold;font-family:Cambria;">Retention Period Extention:</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div><br>
                    <!-- Content Row -->
                    <?php include "../../web_includes/dashboard.php"?>

                    <!-- Content Row -->


                <div class="card shadow mb-4" >
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Basic Info</h6>
                    </div>
                    <div class="card-body" >                                            
                        <div class="table-responsive" >
                            
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" >
                                    <thead>
                                        <tr>
                                            <th>N#</th>
                                            <th>Name</th>
                                            <th >Plate</th>
                                            <th>Provider</th>
                                            <th>Price</th>
                                            <th>Date Brought On</th>
                                            <th>Rent Expiry Date</th>
                                            <th>Days</th>                                            
                                            <th>Extend</th>
                                        </tr>
                                        
                                    </thead>
                                    <tbody>
                                        <?php
                                        include "../../web_db/connection.php";
                                        $sql="SELECT * FROM external_cars WHERE lifecycle_status = 'active' ORDER BY date_brought DESC";
                                        $result=$conn->query($sql);
                                            if ($result->num_rows > 0){
                                                $count = 0;
                                                while ($row= $result->fetch_assoc()){
                                                    $count++;
                                                ?>                                       
                                                <tr>                                            
                                                    <td><?php echo $count?></td>
                                                    <td><?php echo $row["car_name"]?></td>
                                                    <td><?php echo $row["plate_number"]?></td>
                                                    <td><?php echo $row["provider"]?></td>
                                                    <td><?php echo number_format($row["negotiated_price"])." RWF"?></td>
                                                    <td><?php echo $row["date_brought"]?></td>                                                    
                                                    <td><?php echo $row["expected_return_date"]?></td>
                                                    <td><?php echo $row["days_in_service"]." Days"?></td>                                                                                                       
                                                   
                                                    <td>                                                        
                                                        <button type="button" class="btn btn-success btn-sm more-btn" 
                                                            data-toggle="modal"
                                                            data-target="#detailsModal"
                                                            data-car_id="<?= $row['car_id']; ?>"
                                                            data-car_name="<?= $row['car_name']; ?>"
                                                            data-plate_number="<?= $row['plate_number']; ?>"
                                                            data-provider="<?= $row['provider']; ?>"
                                                            data-negotiated_price="<?= $row['negotiated_price']; ?>"
                                                            data-date_brought="<?= $row['date_brought']; ?>"
                                                            data-expected_return_date="<?= $row['expected_return_date']; ?>"                                                            
                                                            data-days_in_service="<?= $row["days_in_service"]; ?>"
                                                            data-total_spending="<?= $row['total_spending']; ?>"                                                  
                                                            style="min-width:105px;" title="About the Adjustment">
                                                                <i class="fas fa-plus"></i> Extend
                                                        </button>                                                                                                                    
                                                    </td>
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

    <!-- Bootstrap Modal To Extend the Retention Period (OUTSIDE THE LOOP) -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detailsModalLabel"><i class="fas fa-exclamation-triangle" style="color: yellow;"></i> Extend the Retention Period</h5>
                    <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">                                                       
                    <!-- Dynamic Details -->
                    <p>Pick the Return Date to Extend the Retention Period</p>
                    <p><i class="fas fa-info-circle"></i> Keep in Mind that the More Period the More Expense.</p>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Car Name: </strong> <span id="car_name"></span></li>
                        <li class="list-group-item"><strong>Plate: </strong><span id="plate_number"></span></li>
                        <li class="list-group-item"><strong>Provider: </strong><span id="provider"></span></li>
                        <li class="list-group-item"><strong>Negotiated Price/Day: </strong> <span id="negotiated_price" style="color:orange;font-weight:bold;"></span></li>
                        <li class="list-group-item"><strong>Date Brought On: </strong> <span id="date_brought"></span></li>
                        <li class="list-group-item"><strong>Current Return Date: </strong> <span id="expected_return_date"></span></li>                                                            
                        <li class="list-group-item"><strong>Current Days: </strong> <span id="days_in_service"></span></li> 
                        <li class="list-group-item"><strong>Current Total Spending: </strong> <span id="total_spending" style="color:green;font-weight:bold;"></span></li>                                                                                                                     
                    </ul>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="extendForm" onsubmit="return validateExtension();">
                        <input type="hidden" name="car_id" id="car_id">
                        <input type="hidden" name="date_brought" id="date_brought_hidden">
                        <input type="hidden" name="negotiated_price" id="negotiated_price_hidden">
                        <input type="hidden" name="car_name" id="car_name_hidden">
                        
                        <div class="form-group" style="margin:10px;">
                            <label for="new_return_date"><strong>New Return Date:</strong></label>
                            <input type="date" class="form-control" id="new_return_date" name="new_return_date" required>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> You must select a date AFTER the current expected return date to extend the retention period.
                            </small>
                            
                            <div id="calculation_preview" style="margin-top:15px; padding:10px; background-color:#f8f9fc; border-radius:5px; display:none;">
                                <h6 style="color:#4e73df; font-weight:bold;">📊 New Calculation Preview:</h6>
                                <p style="margin:5px 0;"><strong>Additional Days:</strong> <span id="additional_days" style="color:#1cc88a;">0</span> days</p>
                                <p style="margin:5px 0;"><strong>New Total Days:</strong> <span id="new_total_days" style="color:#36b9cc;">0</span> days</p>
                                <p style="margin:5px 0;"><strong>New Total Spending:</strong> <span id="new_total_spending" style="color:#e74a3b; font-weight:bold; font-size:1.1em;">0 RWF</span></p>
                            </div>
                            
                            <br>
                            <input type="submit" class="btn btn-success btn-block" name="extend" value="✓ Confirm Extension"/>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

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

    // Form submission validation
    function validateExtension() {
        const newReturnDate = document.getElementById('new_return_date').value;
        const expectedReturnDate = window.currentCarData.expectedReturnDate;
        
        if(!newReturnDate) {
            alert('Please select a new return date!');
            return false;
        }
        
        // Check if date is same as expected return date (no extension)
        if(newReturnDate === expectedReturnDate) {
            alert('You cannot submit without extending the retention period!\n\nThe selected date (' + newReturnDate + ') is the same as the current expected return date.\n\nPlease select a date AFTER ' + expectedReturnDate + ' to extend the retention period.');
            return false;
        }
        
        // Check if date is before expected return date
        const newDate = new Date(newReturnDate);
        const expectedDate = new Date(expectedReturnDate);
        
        if(newDate <= expectedDate) {
            alert('Invalid date selection!\n\nThe new return date must be AFTER the current expected return date (' + expectedReturnDate + ').');
            return false;
        }
        
        // If validation passes, confirm with user
        const additionalDays = document.getElementById('additional_days').textContent;
        const newTotalSpending = document.getElementById('new_total_spending').textContent;
        
        return confirm('Are you sure you want to extend the retention period?\n\n' +
                      'Additional Days: ' + additionalDays + ' days\n' +
                      'New Total Spending: ' + newTotalSpending + '\n\n' +
                      'Click OK to confirm or Cancel to go back.');
    }

    // Populate modal when button is clicked
    document.querySelectorAll('.more-btn').forEach(button => {
        button.addEventListener('click', function() {
            // Set all the values
            document.getElementById('car_id').value = this.dataset.car_id;
            document.getElementById('car_name').textContent = this.dataset.car_name;
            document.getElementById('plate_number').textContent = this.dataset.plate_number;
            document.getElementById('provider').textContent = this.dataset.provider;
            document.getElementById('date_brought').textContent = this.dataset.date_brought;
            document.getElementById('expected_return_date').textContent = this.dataset.expected_return_date;
            document.getElementById('negotiated_price').textContent = formatRWF(this.dataset.negotiated_price);
            document.getElementById('days_in_service').textContent = this.dataset.days_in_service + ' Days';
            document.getElementById('total_spending').textContent = formatRWF(this.dataset.total_spending);
            
            // Set hidden fields for calculation and form submission
            document.getElementById('date_brought_hidden').value = this.dataset.date_brought;
            document.getElementById('negotiated_price_hidden').value = this.dataset.negotiated_price;
            document.getElementById('car_name_hidden').value = this.dataset.car_name;
            
            // Store current values for calculation
            window.currentCarData = {
                dateBrought: this.dataset.date_brought,
                expectedReturnDate: this.dataset.expected_return_date,
                negotiatedPrice: parseInt(this.dataset.negotiated_price),
                currentDays: parseInt(this.dataset.days_in_service)
            };
            
            // Set the new_return_date input to expected_return_date as default
            const dateInput = document.getElementById('new_return_date');
            dateInput.value = this.dataset.expected_return_date;
            
            // Set minimum date to be one day after expected_return_date
            const expectedDate = new Date(this.dataset.expected_return_date);
            expectedDate.setDate(expectedDate.getDate() + 1);
            const minDate = expectedDate.toISOString().split('T')[0];
            dateInput.setAttribute('min', minDate);
            
            // Hide preview initially
            document.getElementById('calculation_preview').style.display = 'none';
        });
    });

    // Live calculation when date is selected
    document.getElementById('new_return_date').addEventListener('change', function() {
        const newReturnDate = this.value;
        
        if(!newReturnDate || !window.currentCarData) return;
        
        const dateBrought = new Date(window.currentCarData.dateBrought);
        const expectedDate = new Date(window.currentCarData.expectedReturnDate);
        const returnDate = new Date(newReturnDate);
        
        // Check if new date is same as or before expected return date
        if(returnDate <= expectedDate) {
            alert('New return date must be AFTER the current expected return date (' + window.currentCarData.expectedReturnDate + ')!\nPlease select a date that extends the retention period.');
            this.value = window.currentCarData.expectedReturnDate;
            document.getElementById('calculation_preview').style.display = 'none';
            return;
        }
        
        // Calculate difference in days
        const timeDiff = returnDate.getTime() - dateBrought.getTime();
        const newTotalDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
        const additionalDays = newTotalDays - window.currentCarData.currentDays;
        
        // Calculate new total spending
        const newTotalSpending = newTotalDays * window.currentCarData.negotiatedPrice;
        
        // Display the calculation
        document.getElementById('additional_days').textContent = additionalDays;
        document.getElementById('new_total_days').textContent = newTotalDays;
        document.getElementById('new_total_spending').textContent = formatRWF(newTotalSpending);
        document.getElementById('calculation_preview').style.display = 'block';
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