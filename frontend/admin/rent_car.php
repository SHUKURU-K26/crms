<?php
session_start();
include "../../web_includes/auth.php";
include "../../web_db/connection.php";
if (isset($_SESSION["adminEmail"])){
    $adminEmail = $_SESSION["adminEmail"];
    $adminPassword = $_SESSION["password"];
    $adminQuery = "SELECT * FROM users WHERE email='$adminEmail' AND password='$adminPassword' AND role='admin'";
    $adminResult = mysqli_query($conn, $adminQuery);
    $adminData=mysqli_fetch_assoc($adminResult);

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

    <title>GuestPro CMS| Renting Car</title>

    <!-- Custom fonts for this template-->
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css" />

    <!-- Custom styles for this template-->
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/custom.css">
    <link rel="icon" href="../../img/GuestProLogoReal.JPG" type="image/png">

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
                        <h1 class="h3 mb-0 text-gray-800">Renting Car Form</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div>

                    <!-- Content Row -->
                    <?php include "../../web_includes/dashboard.php"?>

                    <!-- Content Row -->

                    <div class="row">

                        <!-- Area Chart -->
                        <div class="col-12 grid-margin stretch-card">
                            <!--Form Selection Whether the type of Form to be Displayed-->
                            <div class="p-4 shadow rounded bg-white mb-4">
                                <label for="carTypeSelect" class="form-label">Renting Forms</label>
                                <select class="form-control" id="formSelector">
                                    <option value="">--Select Renting Form --</option>
                                    <option value="internal_car_rental">GuestPro Car Renting Form</option>
                                    <option value="external_car_rental">External Car Renting Form</option>
                                </select>
                            </div>
                            <!--End of Form Selection Whether the type of Form to be Displayed-->
                            
                            <form class="p-4 shadow rounded bg-white" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST" id="internal_car_rentalForm" style="display: none;">
                                <h4 class="mb-3" style="color: dodgerblue;">Renter Details: </h4>
                                <hr />
                                <div class="mb-3">
                                    <label for="renter_name" class="form-label">Renter Full Names:</label>
                                    <input type="text" onclick="ToValidateChars(document.getElementById('renter_name'))" class="form-control" id="renter_name" name="renter_name" placeholder="Full Names" required />
                                </div>
                                                        
                                <div class="mb-3">
                                      <label for="phone-number" class="form-label">Cell Phone</label><br>
                                      <input type="tel" id="phone" maxlength="10" 
                                      pattern="^\d{10}$" class="form-control" name="phone_number" 
                                      oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);" 
                                      placeholder="+250"
                                      required />
                                </div>
                                
                                <div class="mb-3">
                                    <label for="Plate_number" class="form-label">ID/Passport Number</label>
                                     <input type="text" class="form-control" id="national-id" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);" 
                                            placeholder="Must be 16 Digits!" maxlength="16" pattern="^\d{16}$"
                                            name="Id_number" required/>
                                </div>

                                <h4 class="mt-5 mb-3" style="color: dodgerblue;">Vehicle Details </h4>
                                <hr />
                                <div class="mb-3">
                                    <label for="car_name" class="form-label">Car Name</label>
                                    <select name="car_id" id="car_name" class="form-control" required>
                                    <option value="">-- Select Car Name --</option>
                                    <?php 
                                    include "../../web_db/connection.php";
                                    $sql="SELECT * FROM cars WHERE status in('available', 'available with Debt')";
                                            $result = mysqli_query($conn, $sql);
                                            $categories_exist = mysqli_num_rows($result) > 0;                                
                                            if ($categories_exist): ?>                                          
                                                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                                <option value="<?= $row['car_id']; ?>" >
                                                    <?= htmlspecialchars($row['car_name'] . " / Plate: " . $row["plate_number"]);?>
                                                </option>
                                                <?php endwhile; ?>                                                    
                                    <?php else: ?>
                                            <option value="" disabled selected>⚠ No Car Found. Please Register</option>
                                    <?php endif;?>
                                </select>  
                                </div>  

                                <div id="rental-form-section" style="display: none;">
                                      <h4 class="mt-5 mb-3" style="color: dodgerblue;">Rent Length: </h4>
                                      <hr />
  
                                    <div class="mb-3">
                                    <label for="car_name" class="form-label">Price</label>
                                    <input type="number" class="form-control" id="price" name="price" required placeholder="Enter Car Price to be * by Duration"/><br>
                                      <label for="insurance_issued_date" class="form-label"> Rent Date: <span style="color: dodgerblue;">Today by Default</span></label>
                                      <input type="date" class="form-control" id="rent-date" name="rent_date" value="<?= date('Y-m-d') ?>" required /><br>
                                      <label for="return-date" class="form-label">Return Date:</label>
                                      <input type="date" class="form-control" id="return-date" name="return_date" required />
                                    </div>
  
                                    <div class="mb-3">
                                        <label for="days-of-rent" class="form-label">Days In Rent
                                          <span style="color: dodgerblue;">. Autogenerated Field</span>
                                        </label>
                                        <input type="text" class="form-control" id="days-of-rent" name="days_of_rent" placeholder="eg: 1, 3, 7" readonly required />
                                        <input type="hidden" value="<?php echo $adminData["user_id"]?>" class="form-control" name="rented_by" readonly/>
                                    </div>
  
                                    <div class="mb-3">
                                        <div style="display:flex; flex-direction:row; justify-content:space-around;">
                                            <label for="amountField">
                                            Price/Day:
                                            <input type="text" id="amountField" style="border: none; color:dodgerblue; font-weight:bold; outline:none;" readonly />
                                            </label>
                                            
                                            <label id="totalFeeLabel" for="totalFeeInput" style="color:green;font-weight:bold;">Total Fee:</label>
                                            <input type="hidden" id="totalFeeInput" name="total_fee" readonly style="color: green; font-weight: bold;outline: none; border: none;" />
                                        </div>
                                    </div>
                                    
                                    <div class="text-end mt-4">
                                       <input type="submit" value="Save" class="btn btn-primary w-100" name="Save_in_internal_car_rental">
                                    </div>
                                </div> 
                            </form>
                            <!--End of Internal_car_rental Car Rental Form-->

 
   
                            <!--External Form Rentig Car Form-->
                            <form class="p-4 shadow rounded bg-white" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST" id="external_car_rentalForm" style="display: none;">
                                <h4 class="mb-3" style="color: dodgerblue;font-weight:bold;">External Renting Fields: </h4>
                                <hr />
                                <div class="mb-3">
                                    <label for="renter_name" class="form-label">Renter Full Names:</label>
                                    <input type="text" onclick="ToValidateChars(document.getElementById('renter_name'))" class="form-control" id="renter_name" name="renter_name" placeholder="Full Names" required />
                                </div>
                                                        
                                <div class="mb-3">
                                      <label for="phone-number" class="form-label">Cell Phone</label><br>
                                      <input type="tel" id="phone" maxlength="10" 
                                      pattern="^\d{10}$" class="form-control" name="phone_number" 
                                      oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);" 
                                      placeholder="+250"
                                      required />
                                </div>
                                
                                <div class="mb-3">
                                    <label for="Plate_number" class="form-label">ID/Passport Number</label>
                                      <input type="text" class="form-control" id="national-id" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);" 
                                       placeholder="Must be 16 Digits!" maxlength="16" pattern="^\d{16}$"
                                       name="Id_number" required/>
                                </div>

                                <h4 class="mt-5 mb-3" style="color: dodgerblue;">Vehicle Details </h4>
                                <hr />
                                <div class="mb-3">
                                    <label for="car_name" class="form-label">Car Name</label>
                                    <select name="car_id" id="external_car_name" class="form-control" required>
                                    <option value="">-- Select Car Name --</option>
                                    <?php 
                                    include "../../web_db/connection.php";
                                    $sql="SELECT * FROM external_cars WHERE status in('available', 'available with Debt') AND lifecycle_status = 'active'";

                                            $result = mysqli_query($conn, $sql);                                
                                            $categories_exist = mysqli_num_rows($result) > 0;                                
                                            if ($categories_exist): ?>                                          
                                                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                                <option value="<?= $row['car_id']; ?>" data-expected-return="<?= $row['expected_return_date']; ?>">
                                                    <?= htmlspecialchars($row['car_name'] . " / PN: " . $row["plate_number"])." ". $row["use_status"];?>
                                                </option>
                                                <?php endwhile; ?>                                                    
                                    <?php else: ?>
                                            <option value="" disabled selected>⚠ No Car Found. Please Register</option>
                                    <?php endif; ?>
                                </select>
                                </div>                                

                                <div id="external-rental-form-section" style="display: none;">

                                    <h4 class="mt-5 mb-3" style="color: dodgerblue;">Rent Length: </h4>
                                    <hr />
  
                                    <div class="mb-3">
                                    <label for="car_name" class="form-label">Price</label>
                                      <input type="number" class="form-control" id="external_price" name="price" required placeholder="Enter Car Price to be * by Duration"/><br>
                                      <label for="insurance_issued_date" class="form-label"> Rent Date: <span style="color: dodgerblue;">Today by Default</span></label>
                                      <input type="date" class="form-control" id="external_rent-date" name="rent_date" value="<?= date('Y-m-d') ?>" required /><br>
                                      <label for="return-date" class="form-label">Return Date: 
                                        <span id="external-max-date-display" style="color: red; font-weight: bold;"></span>
                                      </label>
                                      <input type="date" class="form-control" id="external_return-date" name="return_date" required />                                      
                                    </div>
  
                                    <div class="mb-3">
                                        <label for="days-of-rent" class="form-label">Days In Rent
                                          <span style="color: dodgerblue;">. Autogenerated Field</span>
                                        </label>
                                        <input type="text" class="form-control" id="external_days-of-rent" name="days_of_rent" placeholder="eg: 1, 3, 7" readonly required />
                                        <input type="hidden" value="<?php echo $adminData["user_id"]?>" class="form-control" name="rented_by" readonly/>
                                    </div>
  
                                    <div class="mb-3">
                                        <div style="display:flex; flex-direction:row; justify-content:space-around;">
                                            <label for="external_amountField">
                                            Price/Day:
                                            <input type="text" id="external_amountField" style="border: none; color:dodgerblue; font-weight:bold; outline:none;" readonly />
                                            </label>
                                            
                                            <label id="external_totalFeeLabel" for="external_totalFeeInput" style="color:green;font-weight:bold;">Total Fee:</label>
                                            <input type="hidden" id="external_totalFeeInput" name="total_fee" readonly style="color: green; font-weight: bold;outline: none; border: none;" />
                                        </div>
                                    </div>
                                    
                                    <div class="text-end mt-4">
                                       <input type="submit" value="Save" class="btn btn-primary w-100" name="Save_in_external_car_rental">
                                    </div>
                                </div> 
                            </form>
                            <!--End of External Form Rentig Car Form-->
                            <?php
                                if ($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['Save_in_internal_car_rental'])){
                                    include "../../web_includes/insertOfCarRental.php";
                                    
                                }if ($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['Save_in_external_car_rental'])){
                                    include "../../web_includes/insertOfExternalCarRental.php";
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
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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

    // Form Selector Logic
    const formSelector = document.getElementById("formSelector");
    const internal_car_rentalForm = document.getElementById("internal_car_rentalForm");
    const external_car_rentalForm = document.getElementById("external_car_rentalForm");

    formSelector.addEventListener("change", function() {
        if (this.value === "internal_car_rental") {
            internal_car_rentalForm.style.display = "block";
            external_car_rentalForm.style.display = "none";

    // enable fields
        [...internal_car_rentalForm.elements].forEach(el => el.disabled = false);
        [...external_car_rentalForm.elements].forEach(el => el.disabled = true);

        } else if (this.value === "external_car_rental") {
            external_car_rentalForm.style.display = "block";
            internal_car_rentalForm.style.display = "none";

    // enable fields
            [...external_car_rentalForm.elements].forEach(el => el.disabled = false);
            [...internal_car_rentalForm.elements].forEach(el => el.disabled = true);

        } else {
            internal_car_rentalForm.style.display = "none";
            external_car_rentalForm.style.display = "none";
       }
});

// Show the rental form section when a car is selected
document.addEventListener("DOMContentLoaded", function () {
    const carSelect = document.getElementById('car_name');
    const rentSection = document.getElementById('rental-form-section');    

    carSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const selectedValue = this.value;

        if (selectedValue !== "") {
        // Show the section
        rentSection.style.display = "block";                
        } else {
        // Hide the section if the placeholder is selected
        rentSection.style.display = "none";        
        }
    });
})


// Show the rental form section of External available Cars when a car is selected
// AND set the max return date based on expected_return_date
document.addEventListener("DOMContentLoaded", function () {
    const carSelect = document.getElementById('external_car_name');
    const rentSection = document.getElementById('external-rental-form-section');    
    const returnDateInput = document.getElementById('external_return-date');
    const maxDateDisplay = document.getElementById('external-max-date-display');

    carSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const selectedValue = this.value;

        if (selectedValue !== "") {
            // Show the section
            rentSection.style.display = "block";
            
            // Get the expected_return_date from the data attribute
            const expectedReturnDate = selectedOption.getAttribute('data-expected-return');
            
            if (expectedReturnDate) {
                // Set the max attribute on the return date input
                returnDateInput.setAttribute('max', expectedReturnDate);
                
                // Display the max date to the user
                const formattedDate = new Date(expectedReturnDate).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
                maxDateDisplay.textContent = `(Must return by: ${formattedDate})`;
            } else{
                // Clear restrictions if no date is set
                returnDateInput.removeAttribute('max');
                maxDateDisplay.textContent = '';
            }
        } else {
            // Hide the section if the placeholder is selected
            rentSection.style.display = "none";
            returnDateInput.removeAttribute('max');
            maxDateDisplay.textContent = '';
        }
    });
})

// Calculate Days in Rent and Total Fee
document.addEventListener("DOMContentLoaded", function () {
    // Internal car rental calculation
    const internalForm = document.getElementById("internal_car_rentalForm");
    if (internalForm) {
        const rentDateInput = internalForm.querySelector("#rent-date");
        const returnDateInput = internalForm.querySelector("#return-date");
        const daysOfRentInput = internalForm.querySelector("#days-of-rent");
        const totalFeeInput = internalForm.querySelector("#totalFeeInput");
        const pricePerDayInput = internalForm.querySelector("#amountField");
        const price = internalForm.querySelector("#price");
        const totalFeeLabel = internalForm.querySelector("#totalFeeLabel");

        function calculateInternalRentDetails() {
            const rentDate = new Date(rentDateInput.value);
            const returnDate = new Date(returnDateInput.value);
            const pricePerDay = parseFloat(pricePerDayInput.value) || 0;

            if (returnDate >= rentDate) {
                const timeDiff = returnDate - rentDate;
                let dayDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
                
                let totalDays;
                if (dayDiff === 0) {
                    totalDays = 1;
                } else {
                    totalDays = dayDiff + 1;
                }
                
                daysOfRentInput.value = totalDays;
                const totalFee = totalDays * pricePerDay;
                
                totalFeeLabel.textContent = totalFee > 0
                    ? totalFee.toLocaleString("en-RW", { style: "currency", currency: "RWF" })
                    : "—";

                totalFeeInput.value = totalFee > 0 ? totalFee : "0";
            } else {
                daysOfRentInput.value = "";
                totalFeeInput.value = "—";
            }
        }

        price.addEventListener("input", () => {
            pricePerDayInput.value = price.value;
            calculateInternalRentDetails();
        });

        rentDateInput.addEventListener("change", () => {
            returnDateInput.min = rentDateInput.value;
            calculateInternalRentDetails();
        });

        returnDateInput.addEventListener("change", function () {
            if (this.value < rentDateInput.value) {
                alert("Return date cannot be earlier than the rent date.");
                this.value = rentDateInput.value;
            }
            calculateInternalRentDetails();
        });

        if (rentDateInput.value) {
            returnDateInput.min = rentDateInput.value;
        }

        calculateInternalRentDetails();
    }

    // External car rental calculation with expected_return_date validation
    const externalForm = document.getElementById("external_car_rentalForm");
    if (externalForm) {
        const rentDateInput = externalForm.querySelector("#external_rent-date");
        const returnDateInput = externalForm.querySelector("#external_return-date");
        const daysOfRentInput = externalForm.querySelector("#external_days-of-rent");
        const totalFeeInput = externalForm.querySelector("#external_totalFeeInput");
        const pricePerDayInput = externalForm.querySelector("#external_amountField");
        const price = externalForm.querySelector("#external_price");
        const totalFeeLabel = externalForm.querySelector("#external_totalFeeLabel");

        function calculateExternalRentDetails() {
            const rentDate = new Date(rentDateInput.value);
            const returnDate = new Date(returnDateInput.value);
            const pricePerDay = parseFloat(pricePerDayInput.value) || 0;
            const maxReturnDate = returnDateInput.getAttribute('max');

            // Validate against expected_return_date
            if (maxReturnDate && returnDate > new Date(maxReturnDate)) {
                alert(`⚠️ Cannot select a return date beyond ${new Date(maxReturnDate).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                })}. This car must be returned to its provider by that date.`);
                returnDateInput.value = maxReturnDate;
                return;
            }

            if (returnDate >= rentDate) {
                const timeDiff = returnDate - rentDate;
                let dayDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
                
                let totalDays;
                if (dayDiff === 0) {
                    totalDays = 1;
                } else {
                    totalDays = dayDiff + 1;
                }
                
                daysOfRentInput.value = totalDays;
                const totalFee = totalDays * pricePerDay;
                
                totalFeeLabel.textContent = totalFee > 0
                    ? totalFee.toLocaleString("en-RW", { style: "currency", currency: "RWF" })
                    : "—";

                totalFeeInput.value = totalFee > 0 ? totalFee : "0";
            } else {
                daysOfRentInput.value = "";
                totalFeeInput.value = "—";
            }
        }

        price.addEventListener("input", () => {
            pricePerDayInput.value = price.value;
            calculateExternalRentDetails();
        });

        rentDateInput.addEventListener("change", () => {
            returnDateInput.min = rentDateInput.value;
            calculateExternalRentDetails();
        });

        returnDateInput.addEventListener("change", calculateExternalRentDetails);

        if (rentDateInput.value) {
            returnDateInput.min = rentDateInput.value;
        }

        calculateExternalRentDetails();
    }
});
</script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
       
    <script>
    const phoneInput = document.getElementById("phone");
    const iti = window.intlTelInput(phoneInput, {
        initialCountry: "auto",
        nationalMode: false,
        formatOnDisplay: true,
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
        geoIpLookup: function(callback) {
        fetch("https://ipapi.co/json")
            .then(res => res.json())
            .then(data => callback(data.country_code))
            .catch(() => callback("RW"));
    }
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