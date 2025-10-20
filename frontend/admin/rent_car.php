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
    <title>GuestPro CMS | Renting Car</title>

    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css" />
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/custom.css">
    <link rel="icon" href="../../img/GuestProLogoReal.JPG" type="image/png">

</head>
<body id="page-top">
    <div id="wrapper">
        <?php include "../../web_includes/menu.php"; ?>
        
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include "../../web_includes/topbar.php"; ?>
                
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Renting Car Form</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
                        </a>
                    </div>

                    <?php include "../../web_includes/dashboard.php"?>

                    <div class="row">
                        <div class="col-12 grid-margin stretch-card">
                            <!-- Form Selection -->
                            <div class="p-4 shadow rounded bg-white mb-4">
                                <label for="formSelector" class="form-label">Renting Forms</label>
                                <select class="form-control" id="formSelector">
                                    <option value="">--Select Renting Form --</option>
                                    <option value="internal_car_rental">GuestPro Car Renting Form</option>
                                    <option value="external_car_rental">External Car Renting Form</option>
                                </select>
                            </div>
                            
                            <!-- INTERNAL CAR RENTAL FORM -->
                            <form class="p-4 shadow rounded bg-white" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST" id="internal_car_rentalForm" style="display: none;">
                                <h4 class="mb-3" style="color: dodgerblue;">Renter Details: </h4>
                                <hr />
                                <div class="mb-3">
                                    <label for="internal_renter_name" class="form-label">Renter Full Names:</label>
                                    <input type="text" class="form-control" id="internal_renter_name" name="renter_name" placeholder="Full Names" />
                                </div>
                                                        
                                <div class="mb-3">
                                    <label for="internal_phone" class="form-label">Cell Phone</label><br>
                                    <input type="tel" id="internal_phone" maxlength="10" pattern="^\d{10}$" class="form-control" name="phone_number" 
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);" placeholder="+250" />
                                </div>
                                
                                <div class="mb-3">
                                    <label for="internal_national_id" class="form-label">ID/Passport Number</label>
                                    <input type="text" class="form-control" id="internal_national_id" 
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);" 
                                    placeholder="Must be 16 Digits!" maxlength="16" pattern="^\d{16}$" name="Id_number" />
                                </div>

                                <h4 class="mt-5 mb-3" style="color: dodgerblue;">Vehicle Details </h4>
                                <hr />
                                <div class="mb-3">
                                    <label for="car_name" class="form-label">Car Name</label>
                                    <select name="car_id" id="car_name" class="form-control">
                                        <option value="">-- Select Car Name --</option>
                                        <?php 
                                        include "../../web_db/connection.php";
                                        $sql="SELECT * FROM cars WHERE status != 'rented'";
                                        $result = mysqli_query($conn, $sql);
                                        $categories_exist = mysqli_num_rows($result) > 0;                                
                                        if ($categories_exist): 
                                            while ($row = mysqli_fetch_assoc($result)) : ?>
                                                <option value="<?= $row['car_id']; ?>" 
                                                    data-booking-status="<?= $row['booking_status']; ?>"
                                                    data-booking-date="<?= $row['booking_date']; ?>">
                                                    <?= htmlspecialchars($row['car_name'] . " / Plate: " . $row["plate_number"]);?>
                                                    <?php if ($row['booking_status'] == 'booked'): ?>
                                                        [⚠️ Booked for <?= date('M d, Y', strtotime($row['booking_date'])); ?>]
                                                    <?php endif; ?>
                                                </option>
                                            <?php endwhile; 
                                        else: ?>
                                            <option value="" disabled selected>⚠ No Car Found. Please Register</option>
                                        <?php endif;?>
                                    </select>  
                                </div>  

                                <div id="rental-form-section" style="display: none;">
                                    <h4 class="mt-5 mb-3" style="color: dodgerblue;">Rent Length: </h4>
                                    <hr />
  
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price</label>
                                        <input type="number" class="form-control" id="price" name="price" placeholder="Enter Car Price to be * by Duration"/><br>
                                        <label for="rent-date" class="form-label"> Rent Date: <span style="color: dodgerblue;">Today by Default</span></label>
                                        <input type="date" class="form-control" id="rent-date" name="rent_date" value="<?= date('Y-m-d') ?>" /><br>
                                        <label for="return-date" class="form-label">Return Date:
                                            <span id="booking-restriction-display" style="color: #ff6b6b; font-weight: bold; font-size: 0.9em;"></span>
                                        </label>
                                        <input type="date" class="form-control" id="return-date" name="return_date" />
                                    </div>
  
                                    <div class="mb-3">
                                        <label for="days-of-rent" class="form-label">Days In Rent
                                            <span style="color: dodgerblue;">. Autogenerated Field</span>
                                        </label>
                                        <input type="text" class="form-control" id="days-of-rent" name="days_of_rent" placeholder="eg: 1, 3, 7" readonly />
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

                                    <!-- PAYMENT DETAILS SECTION -->
                                    <h4 class="mt-4 mb-3" style="color: dodgerblue;">Payment Details: </h4>
                                    <hr />

                                    <div class="mb-3">
                                        <label for="payment_status" class="form-label">Payment Status</label>
                                        <select class="form-control" id="payment_status" name="payment_status">
                                            <option value="">-- Select Payment Status --</option>
                                            <option value="Fully Paid">Fully Paid</option>
                                            <option value="Partial Paid">Partial Paid</option>
                                            <option value="Fully Unpaid">Fully Unpaid</option>
                                        </select>
                                    </div>

                                    <div id="partial_payment_section" style="display: none;">
                                        <div class="mb-3">
                                            <label for="amount_paid" class="form-label">Amount Paid</label>
                                            <input type="number" class="form-control" id="amount_paid" name="amount_paid" 
                                                placeholder="Enter amount customer has paid" min="0" step="0.01" />
                                            
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="alert alert-info" role="alert" style="background-color: #e7f3ff; border-left: 4px solid #2196F3;">
                                                <strong style="color: #1976D2;">Payment Summary:</strong>
                                                <div class="mt-2" style="font-size: 16px;">
                                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                                        <span>Total Fee:</span>
                                                        <span id="summary_total_fee" style="font-weight: bold; color: #333;">RWF 0</span>
                                                    </div>
                                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                                        <span>Amount Paid:</span>
                                                        <span id="summary_amount_paid" style="font-weight: bold; color: #4CAF50;">RWF 0</span>
                                                    </div>
                                                    <hr style="margin: 10px 0; border-top: 2px solid #2196F3;">
                                                    <div style="display: flex; justify-content: space-between;">
                                                        <span style="font-weight: bold;">Remaining Balance:</span>
                                                        <span id="remaining_balance" style="font-weight: bold; font-size: 18px; color: #f44336;">RWF 0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end mt-4">
                                        <input type="submit" value="Save" class="btn btn-primary w-100" name="Save_in_internal_car_rental">
                                    </div>
                                </div> 
                            </form>

                            <!-- EXTERNAL CAR RENTAL FORM -->
                            <form class="p-4 shadow rounded bg-white" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST" id="external_car_rentalForm" style="display: none;">
                                <h4 class="mb-3" style="color: dodgerblue;font-weight:bold;">External Renting Fields: </h4>
                                <hr />
                                <div class="mb-3">
                                    <label for="external_renter_name" class="form-label">Renter Full Names:</label>
                                    <input type="text" class="form-control" id="external_renter_name" name="renter_name" placeholder="Full Names" />
                                </div>
                                                        
                                <div class="mb-3">
                                    <label for="external_phone" class="form-label">Cell Phone</label><br>
                                    <input type="tel" id="external_phone" maxlength="10" pattern="^\d{10}$" class="form-control" name="phone_number" 
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);" placeholder="+250" />
                                </div>
                                
                                <div class="mb-3">
                                    <label for="external_national_id" class="form-label">ID/Passport Number</label>
                                    <input type="text" class="form-control" id="external_national_id" 
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);" 
                                    placeholder="Must be 16 Digits!" maxlength="16" pattern="^\d{16}$" name="Id_number" />
                                </div>

                                <h4 class="mt-5 mb-3" style="color: dodgerblue;">Vehicle Details </h4>
                                <hr />
                                <div class="mb-3">
                                    <label for="external_car_name" class="form-label">Car Name</label>
                                    <select name="car_id" id="external_car_name" class="form-control">
                                        <option value="">-- Select Car Name --</option>
                                        <?php 
                                        $sql="SELECT * FROM external_cars WHERE status in('available', 'available with Debt') AND lifecycle_status = 'active'";
                                        $result = mysqli_query($conn, $sql);                                
                                        $categories_exist = mysqli_num_rows($result) > 0;                                
                                        if ($categories_exist): 
                                            while ($row = mysqli_fetch_assoc($result)) : ?>
                                                <option value="<?= $row['car_id']; ?>" data-expected-return="<?= $row['expected_return_date']; ?>">
                                                    <?= htmlspecialchars($row['car_name'] . " / PN: " . $row["plate_number"])." ". $row["use_status"];?>
                                                </option>
                                            <?php endwhile; 
                                        else: ?>
                                            <option value="" disabled selected>⚠ No Car Found. Please Register</option>
                                        <?php endif; ?>
                                    </select>
                                </div>                                

                                <div id="external-rental-form-section" style="display: none;">
                                    <h4 class="mt-5 mb-3" style="color: dodgerblue;">Rent Length: </h4>
                                    <hr />
  
                                    <div class="mb-3">
                                        <label for="external_price" class="form-label">Price</label>
                                        <input type="number" class="form-control" id="external_price" name="price" placeholder="Enter Car Price to be * by Duration"/><br>
                                        <label for="external_rent-date" class="form-label"> Rent Date: <span style="color: dodgerblue;">Today by Default</span></label>
                                        <input type="date" class="form-control" id="external_rent-date" name="rent_date" value="<?= date('Y-m-d') ?>" /><br>
                                        <label for="external_return-date" class="form-label">Return Date: 
                                            <span id="external-max-date-display" style="color: red; font-weight: bold;"></span>
                                        </label>
                                        <input type="date" class="form-control" id="external_return-date" name="return_date" />                                      
                                    </div>
  
                                    <div class="mb-3">
                                        <label for="external_days-of-rent" class="form-label">Days In Rent
                                            <span style="color: dodgerblue;">. Autogenerated Field</span>
                                        </label>
                                        <input type="text" class="form-control" id="external_days-of-rent" name="days_of_rent" placeholder="eg: 1, 3, 7" readonly />
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

                                    <!-- EXTERNAL PAYMENT DETAILS SECTION -->
                                    <h4 class="mt-4 mb-3" style="color: dodgerblue;">Payment Details: </h4>
                                    <hr />

                                    <div class="mb-3">
                                        <label for="external_payment_status" class="form-label">Payment Status</label>
                                        <select class="form-control" id="external_payment_status" name="payment_status">
                                            <option value="">-- Select Payment Status --</option>
                                            <option value="Fully Paid">Fully Paid</option>
                                            <option value="Partial Paid">Partial Paid</option>
                                            <option value="Fully Unpaid">Fully Unpaid</option>
                                        </select>
                                    </div>

                                    <div id="external_partial_payment_section" style="display: none;">
                                        <div class="mb-3">
                                            <label for="external_amount_paid" class="form-label">Amount Paid</label>
                                            <input type="number" class="form-control" id="external_amount_paid" name="amount_paid" 
                                                placeholder="Enter amount customer has paid" min="0" step="0.01" />
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="alert alert-info" role="alert" style="background-color: #e7f3ff; border-left: 4px solid #2196F3;">
                                                <strong style="color: #1976D2;">Payment Summary:</strong>
                                                <div class="mt-2" style="font-size: 16px;">
                                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                                        <span>Total Fee:</span>
                                                        <span id="external_summary_total_fee" style="font-weight: bold; color: #333;">RWF 0</span>
                                                    </div>
                                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                                        <span>Amount Paid:</span>
                                                        <span id="external_summary_amount_paid" style="font-weight: bold; color: #4CAF50;">RWF 0</span>
                                                    </div>
                                                    <hr style="margin: 10px 0; border-top: 2px solid #2196F3;">
                                                    <div style="display: flex; justify-content: space-between;">
                                                        <span style="font-weight: bold;">Remaining Balance:</span>
                                                        <span id="external_remaining_balance" style="font-weight: bold; font-size: 18px; color: #f44336;">RWF 0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end mt-4">
                                        <input type="submit" value="Save" class="btn btn-primary w-100" name="Save_in_external_car_rental">
                                    </div>
                                </div> 
                            </form>

                            <?php
                            if ($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['Save_in_internal_car_rental'])){
                                include "../../web_includes/insertOfCarRental.php";
                            }
                            if ($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['Save_in_external_car_rental'])){
                                include "../../web_includes/insertOfExternalCarRental.php";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php include "../../web_includes/footer.php"; ?>
        </div>
    </div>

    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../../js/sb-admin-2.min.js"></script>
    <script src="../../js/mycustomjs.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    
    <script>
    // Form Selector with Dynamic Required Handling
    const formSelector = document.getElementById("formSelector");
    const internal_car_rentalForm = document.getElementById("internal_car_rentalForm");
    const external_car_rentalForm = document.getElementById("external_car_rentalForm");

    formSelector.addEventListener("change", function() {
        if (this.value === "internal_car_rental") {
            internal_car_rentalForm.style.display = "block";
            external_car_rentalForm.style.display = "none";
            
            // Enable internal form fields and set required
            setFormFieldsState(internal_car_rentalForm, false, true);
            // Disable external form fields and remove required
            setFormFieldsState(external_car_rentalForm, true, false);

        } else if (this.value === "external_car_rental") {
            external_car_rentalForm.style.display = "block";
            internal_car_rentalForm.style.display = "none";
            
            // Enable external form fields and set required
            setFormFieldsState(external_car_rentalForm, false, true);
            // Disable internal form fields and remove required
            setFormFieldsState(internal_car_rentalForm, true, false);

        } else {
            internal_car_rentalForm.style.display = "none";
            external_car_rentalForm.style.display = "none";
        }
    });

    // Helper function to set disabled and required states
    function setFormFieldsState(form, disabled, addRequired) {
        const elements = form.elements;
        for (let i = 0; i < elements.length; i++) {
            const el = elements[i];
            
            // Skip submit buttons
            if (el.type === 'submit') continue;
            
            el.disabled = disabled;
            
            // Handle required attribute based on field type and visibility
            if (addRequired) {
                // Only add required to visible, important fields
                if (el.name === 'renter_name' || el.name === 'phone_number' || 
                    el.name === 'Id_number' || el.name === 'car_id' || 
                    el.name === 'price' || el.name === 'rent_date' || 
                    el.name === 'return_date' || el.name === 'days_of_rent' ||
                    el.name === 'payment_status') {
                    el.setAttribute('required', 'required');
                }
            } else {
                // Remove required from all fields when form is hidden
                el.removeAttribute('required');
            }
        }
    }

    // Internal Form - Show rental section and booking restrictions
    document.addEventListener("DOMContentLoaded", function () {
        const carSelect = document.getElementById('car_name');
        const rentSection = document.getElementById('rental-form-section');
        const returnDateInput = document.getElementById('return-date');
        const rentDateInput = document.getElementById('rent-date');
        const bookingRestrictionDisplay = document.getElementById('booking-restriction-display');

        carSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const selectedValue = this.value;

            if (selectedValue !== "") {
                rentSection.style.display = "block";
                
                const bookingStatus = selectedOption.getAttribute('data-booking-status');
                const bookingDate = selectedOption.getAttribute('data-booking-date');
                
                if (bookingStatus === 'booked' && bookingDate && bookingDate !== '') {
                    const bookingDateObj = new Date(bookingDate);
                    const dayBeforeBooking = new Date(bookingDateObj);
                    dayBeforeBooking.setDate(bookingDateObj.getDate() - 1);
                    
                    const maxReturnDate = dayBeforeBooking.toISOString().split('T')[0];
                    returnDateInput.setAttribute('max', maxReturnDate);
                    
                    const formattedBookingDate = bookingDateObj.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                    const formattedMaxDate = dayBeforeBooking.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                    
                    bookingRestrictionDisplay.innerHTML = `<br>⚠️ This car is booked for ${formattedBookingDate}. Must return by ${formattedMaxDate}`;
                    bookingRestrictionDisplay.style.display = 'inline';
                } else {
                    returnDateInput.removeAttribute('max');
                    bookingRestrictionDisplay.textContent = '';
                    bookingRestrictionDisplay.style.display = 'none';
                }
            } else {
                rentSection.style.display = "none";
                returnDateInput.removeAttribute('max');
                bookingRestrictionDisplay.textContent = '';
                bookingRestrictionDisplay.style.display = 'none';
            }
        });
    });

    // External Form - Show rental section and expected return date restrictions
    document.addEventListener("DOMContentLoaded", function () {
        const carSelect = document.getElementById('external_car_name');
        const rentSection = document.getElementById('external-rental-form-section');    
        const returnDateInput = document.getElementById('external_return-date');
        const maxDateDisplay = document.getElementById('external-max-date-display');

        carSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const selectedValue = this.value;

            if (selectedValue !== "") {
                rentSection.style.display = "block";
                
                const expectedReturnDate = selectedOption.getAttribute('data-expected-return');
                
                if (expectedReturnDate) {
                    returnDateInput.setAttribute('max', expectedReturnDate);
                    
                    const formattedDate = new Date(expectedReturnDate).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                    maxDateDisplay.textContent = `(Must return by: ${formattedDate})`;
                } else {
                    returnDateInput.removeAttribute('max');
                    maxDateDisplay.textContent = '';
                }
            } else {
                rentSection.style.display = "none";
                returnDateInput.removeAttribute('max');
                maxDateDisplay.textContent = '';
            }
        });
    });

    // Calculate Days and Total Fee for INTERNAL Form
    document.addEventListener("DOMContentLoaded", function () {
        const internalForm = document.getElementById("internal_car_rentalForm");
        if (internalForm) {
            const rentDateInput = internalForm.querySelector("#rent-date");
            const returnDateInput = internalForm.querySelector("#return-date");
            const daysOfRentInput = internalForm.querySelector("#days-of-rent");
            const totalFeeInput = internalForm.querySelector("#totalFeeInput");
            const pricePerDayInput = internalForm.querySelector("#amountField");
            const price = internalForm.querySelector("#price");
            const totalFeeLabel = internalForm.querySelector("#totalFeeLabel");
            const carSelect = internalForm.querySelector("#car_name");

            function calculateInternalRentDetails() {
                const rentDate = new Date(rentDateInput.value);
                const returnDate = new Date(returnDateInput.value);
                const pricePerDay = parseFloat(pricePerDayInput.value) || 0;
                
                const maxReturnDate = returnDateInput.getAttribute('max');
                if (maxReturnDate && returnDate > new Date(maxReturnDate)) {
                    const selectedOption = carSelect.options[carSelect.selectedIndex];
                    const bookingDate = selectedOption.getAttribute('data-booking-date');
                    const formattedMaxDate = new Date(maxReturnDate).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                    const formattedBookingDate = new Date(bookingDate).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                    
                    alert(`⚠️ Invalid Return Date!\n\nThis car is booked for ${formattedBookingDate}.\nYou must return it by ${formattedMaxDate} (one day before the booking date).\n\nPlease select an earlier return date.`);
                    returnDateInput.value = maxReturnDate;
                    return;
                }

                if (returnDate >= rentDate) {
                    const timeDiff = returnDate - rentDate;
                    let dayDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
                    
                    let totalDays = dayDiff === 0 ? 1 : dayDiff + 1;
                    
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

        // EXTERNAL form calculation
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
                    
                    let totalDays = dayDiff === 0 ? 1 : dayDiff + 1;
                    
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

    // INTERNAL FORM - Payment Status Logic
    document.addEventListener("DOMContentLoaded", function () {
        const paymentStatusSelect = document.getElementById('payment_status');
        const partialPaymentSection = document.getElementById('partial_payment_section');
        const amountPaidInput = document.getElementById('amount_paid');
        const totalFeeInput = document.getElementById('totalFeeInput');
        
        const summaryTotalFee = document.getElementById('summary_total_fee');
        const summaryAmountPaid = document.getElementById('summary_amount_paid');
        const remainingBalance = document.getElementById('remaining_balance');

        paymentStatusSelect.addEventListener('change', function() {
            if (this.value === 'Partial Paid') {
                partialPaymentSection.style.display = 'block';
                amountPaidInput.setAttribute('required', 'required');
                calculateBalance();
            } else {
                partialPaymentSection.style.display = 'none';
                amountPaidInput.removeAttribute('required');
                amountPaidInput.value = '';
                
                if (this.value === 'Fully Paid') {
                    const totalFee = parseFloat(totalFeeInput.value) || 0;
                    amountPaidInput.value = totalFee;
                } else if (this.value === 'Fully Unpaid') {
                    amountPaidInput.value = 0;
                }
            }
        });

        amountPaidInput.addEventListener('input', function() {
            calculateBalance();
            validateAmountPaid();
        });

        const priceInput = document.getElementById('price');
        const rentDateInput = document.getElementById('rent-date');
        const returnDateInput = document.getElementById('return-date');
        
        if (priceInput) {
            priceInput.addEventListener('input', function() {
                setTimeout(() => {
                    if (paymentStatusSelect.value === 'Partial Paid') {
                        calculateBalance();
                    }
                }, 100);
            });
        }
        
        if (rentDateInput) {
            rentDateInput.addEventListener('change', function() {
                setTimeout(() => {
                    if (paymentStatusSelect.value === 'Partial Paid') {
                        calculateBalance();
                    }
                }, 100);
            });
        }
        
        if (returnDateInput) {
            returnDateInput.addEventListener('change', function() {
                setTimeout(() => {
                    if (paymentStatusSelect.value === 'Partial Paid') {
                        calculateBalance();
                    }
                }, 100);
            });
        }

        function calculateBalance() {
            const totalFee = parseFloat(totalFeeInput.value) || 0;
            const amountPaid = parseFloat(amountPaidInput.value) || 0;
            const balance = totalFee - amountPaid;

            summaryTotalFee.textContent = formatCurrency(totalFee);
            summaryAmountPaid.textContent = formatCurrency(amountPaid);
            remainingBalance.textContent = formatCurrency(balance);

            if (balance < 0) {
                remainingBalance.style.color = '#f44336';
                remainingBalance.textContent = 'Overpaid: ' + formatCurrency(Math.abs(balance));
            } else if (balance === 0) {
                remainingBalance.style.color = '#4CAF50';
            } else {
                remainingBalance.style.color = '#f44336';
            }
        }

        function validateAmountPaid() {
            const totalFee = parseFloat(totalFeeInput.value) || 0;
            const amountPaid = parseFloat(amountPaidInput.value) || 0;

            if (amountPaid > totalFee) {
                amountPaidInput.setCustomValidity('Amount paid cannot exceed total fee');
            } else if (amountPaid < 0) {
                amountPaidInput.setCustomValidity('Amount paid cannot be negative');
            } else if (amountPaid === 0 && paymentStatusSelect.value === 'Partial Paid') {
                amountPaidInput.setCustomValidity('Please enter an amount greater than 0 for partial payment');
            } else if (amountPaid === totalFee && paymentStatusSelect.value === 'Partial Paid') {
                amountPaidInput.setCustomValidity('Amount paid equals total fee. Please select "Fully Paid" instead');
            } else {
                amountPaidInput.setCustomValidity('');
            }
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('en-RW', {
                style: 'currency',
                currency: 'RWF',
                minimumFractionDigits: 0
            }).format(amount);
        }

        document.getElementById('internal_car_rentalForm').addEventListener('submit', function(e) {
            if (paymentStatusSelect.value === 'Partial Paid') {
                validateAmountPaid();
                if (!amountPaidInput.checkValidity()) {
                    e.preventDefault();
                    amountPaidInput.reportValidity();
                }
            }
        });
    });

    // EXTERNAL FORM - Payment Status Logic
    document.addEventListener("DOMContentLoaded", function () {
        const paymentStatusSelect = document.getElementById('external_payment_status');
        const partialPaymentSection = document.getElementById('external_partial_payment_section');
        const amountPaidInput = document.getElementById('external_amount_paid');
        const totalFeeInput = document.getElementById('external_totalFeeInput');
        
        const summaryTotalFee = document.getElementById('external_summary_total_fee');
        const summaryAmountPaid = document.getElementById('external_summary_amount_paid');
        const remainingBalance = document.getElementById('external_remaining_balance');

        paymentStatusSelect.addEventListener('change', function() {
            if (this.value === 'Partial Paid') {
                partialPaymentSection.style.display = 'block';
                amountPaidInput.setAttribute('required', 'required');
                calculateBalance();
            } else {
                partialPaymentSection.style.display = 'none';
                amountPaidInput.removeAttribute('required');
                amountPaidInput.value = '';
                
                if (this.value === 'Fully Paid') {
                    const totalFee = parseFloat(totalFeeInput.value) || 0;
                    amountPaidInput.value = totalFee;
                } else if (this.value === 'Fully Unpaid') {
                    amountPaidInput.value = 0;
                }
            }
        });

        amountPaidInput.addEventListener('input', function() {
            calculateBalance();
            validateAmountPaid();
        });

        const priceInput = document.getElementById('external_price');
        const rentDateInput = document.getElementById('external_rent-date');
        const returnDateInput = document.getElementById('external_return-date');
        
        if (priceInput) {
            priceInput.addEventListener('input', function() {
                setTimeout(() => {
                    if (paymentStatusSelect.value === 'Partial Paid') {
                        calculateBalance();
                    }
                }, 100);
            });
        }
        
        if (rentDateInput) {
            rentDateInput.addEventListener('change', function() {
                setTimeout(() => {
                    if (paymentStatusSelect.value === 'Partial Paid') {
                        calculateBalance();
                    }
                }, 100);
            });
        }
        
        if (returnDateInput) {
            returnDateInput.addEventListener('change', function() {
                setTimeout(() => {
                    if (paymentStatusSelect.value === 'Partial Paid') {
                        calculateBalance();
                    }
                }, 100);
            });
        }

        function calculateBalance() {
            const totalFee = parseFloat(totalFeeInput.value) || 0;
            const amountPaid = parseFloat(amountPaidInput.value) || 0;
            const balance = totalFee - amountPaid;

            summaryTotalFee.textContent = formatCurrency(totalFee);
            summaryAmountPaid.textContent = formatCurrency(amountPaid);
            remainingBalance.textContent = formatCurrency(balance);

            if (balance < 0) {
                remainingBalance.style.color = '#f44336';
                remainingBalance.textContent = 'Overpaid: ' + formatCurrency(Math.abs(balance));
            } else if (balance === 0) {
                remainingBalance.style.color = '#4CAF50';
            } else {
                remainingBalance.style.color = '#f44336';
            }
        }

        function validateAmountPaid() {
            const totalFee = parseFloat(totalFeeInput.value) || 0;
            const amountPaid = parseFloat(amountPaidInput.value) || 0;

            if (amountPaid > totalFee) {
                amountPaidInput.setCustomValidity('Amount paid cannot exceed total fee');
            } else if (amountPaid < 0) {
                amountPaidInput.setCustomValidity('Amount paid cannot be negative');
            } else if (amountPaid === 0 && paymentStatusSelect.value === 'Partial Paid') {
                amountPaidInput.setCustomValidity('Please enter an amount greater than 0 for partial payment');
            } else if (amountPaid === totalFee && paymentStatusSelect.value === 'Partial Paid') {
                amountPaidInput.setCustomValidity('Amount paid equals total fee. Please select "Fully Paid" instead');
            } else {
                amountPaidInput.setCustomValidity('');
            }
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('en-RW', {
                style: 'currency',
                currency: 'RWF',
                minimumFractionDigits: 0
            }).format(amount);
        }

        document.getElementById('external_car_rentalForm').addEventListener('submit', function(e) {
            if (paymentStatusSelect.value === 'Partial Paid') {
                validateAmountPaid();
                if (!amountPaidInput.checkValidity()) {
                    e.preventDefault();
                    amountPaidInput.reportValidity();
                }
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