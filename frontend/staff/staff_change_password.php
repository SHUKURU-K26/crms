<?php
session_start();
include "../../staff_web_includes/staff_auth.php";
include "../../web_db/connection.php";

// Handle profile update submission (example, you can expand validation)
if (isset($_POST['change_password'])){
    $OldPassword =  mysqli_real_escape_string($conn, $_POST["old_password"]) ?? '';

    // Fetch the single user (since there's only one account)
    $user_logged_password=$_SESSION["Userpassword"];
    $sql = "SELECT password FROM users WHERE password='$user_logged_password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($OldPassword, $row['password'])) {
            $NewPassword = mysqli_real_escape_string($conn, $_POST["new_password"]) ?? '';
            $ConfirmPassword = mysqli_real_escape_string($conn, $_POST["confirm_password"]) ?? '';
            if ($NewPassword === $ConfirmPassword) {
                // Hash the new password
                $hashedPassword = password_hash($NewPassword, PASSWORD_DEFAULT);

                // Update the password in the database
                $updateSql = "UPDATE users SET password='$hashedPassword' WHERE password='$user_logged_password'";
                if($conn->query($updateSql) === TRUE) {                    
                    session_unset();
                    session_destroy();
                    // Show success message
                    echo "
                        <div id='successAlertBox'>
                            <a href='#' class='btn btn-success btn-circle'>
                                <i class='fas fa-check'></i>
                            </a> 
                            Password Updated <strong>Successfully.</strong>Session Expired, Please Login Back
                        </div>
                        
                        ";
                    echo"<script>
                            setTimeout(function(){
                                window.location.href='../staff_web_includes/staff_logout.php';
                            },3000);
                        </script>";
                                                         
                } else {
                    echo "<div id='deleteSuccessBox'>Error updating password: " . $conn->error . "</div>";
                }
            } else {
                echo "<div id='deleteSuccessBox'>Mismatched Password Detected!</div>";
            }            
        }else {
            include "../../system_messages/wrongCurrentPasswordMessage.php";
        }
    }
}

// Logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../../index.php");
    exit();
}

if (isset($_SESSION["Userpassword"]) && isset($_SESSION["username"])){
    $user_logged_password=$_SESSION["Userpassword"];
    $query="SELECT * FROM users WHERE password='$user_logged_password'";
    $result=$conn->query($query);
    $row=$result->fetch_assoc();
    $staff_name=$row["full_name"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Password Change | GPTS</title>
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,900" rel="stylesheet" />
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet" />
    <link  href="../../css/custom.css" rel="stylesheet">
    <link rel="icon" href="../../img/GuestProLogoReal.JPG" type="image/png">
    <style>
        #deleteSuccessBox {
            max-width: 90%;
            margin: 10px auto;
            padding: 12px 20px;
            background-color: #f8d7da;
            color: red;
            font-weight: bold;
            font-family: 'Segoe UI', sans-serif;
            font-size: 16px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transition: opacity 0.5s ease-in-out;
            z-index: 9999;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
        }

        @media (max-width: 600px) {
            #deleteSuccessBox {
            font-size: 14px;
            padding: 10px 15px;
            }
        }
        #submitBtn:hover{
            opacity: 0.7;
        }
        .valid { 
         color: green; 
         font-weight: bold; 
         }
         .invalid { 
          color: #970000; 
         }
        </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include "../../staff_web_includes/staff_menu.php"; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include "../../staff_web_includes/staff_topbar.php"; ?>
                <div class="container-fluid">
                    <div class="row page-titles">
                        <div class="col-md-5 align-self-center">
                            <h3 class="text-themecolor">Profile</h3>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="staff_home.php">Home</a></li>
                                <li class="breadcrumb-item active">Change Account Password</li>
                            </ol>
                        </div>
                        <div class="col-md-7 align-self-center">
                            <p style="font-weight: bold;">
                                    <a href="#" class="btn btn-info btn-circle btn-sm" data-toggle="tooltip" data-placement="top">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                   It's advisable to use a strong password to protect your account.
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-5">
                            <div class="card">
                                <div class="card-body">
                                    <center class="m-t-30">
                                        <img src="../../img/GuestProLogoReal.JPG" class="img-circle" width="150" />
                                        <h4 class="card-title m-t-10" style="color:#970000;font-weight:bold;"><?php echo $staff_name;?></h4>
                                        <h6 class="card-subtitle">Account Management</h6>
                                    </center>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-7">
                            <div class="card">
                                <div class="card-body">                                    
                                    <form class="form-horizontal form-material" id="myform" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST">
                                        <div class="form-group">
                                            <label class="col-md-12">Current Password</label>
                                            <div class="col-md-12">
                                                <input type="password" placeholder="Old Password" id="password" required class="form-control form-control-line" name="old_password">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-12">New Password</label>
                                            <div class="col-md-12">
                                                <input type="password" placeholder="New Password" id="NewPassword" required class="form-control form-control-line" name="new_password">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-12">Confirm New Password</label>
                                            <div class="col-md-12">
                                                <input type="password" placeholder="Re-type to Confirm" id="ConfirmPassword" required class="form-control form-control-line" name="confirm_password">
                                            </div>

                                            <div>
                                                <input type="checkbox" onclick="ToShowPassword()" id="showPassword">
                                                <label for="showPassword">Show Password</label>
                                            </div>                            
                                            <p id="checkPasswordMatch"></p>
                                        </div>
                                            <!--Password Strength Rules-->                                            
                                            <i class="fas fa-exclamation-triangle" style="color:darkcyan;"> Rules for Strength </i>
                                            <p id="rule-length">Must be atleast 8+ Characters</p>
                                            <p id="rule-uppercase">Must Contain UpperCase</p>
                                            <p id="rule-lowercase">Must Contain LowerCase</p>
                                            <p id="rule-number">Must Contain at least one Number (0-9)</p>
                                            <p id="rule-special">Must Contain at least one Character</p>

                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <button type="submit" name="change_password" id="submitBtn" style="background-color: #970000;border:none;" class="btn btn-success">Change Password</button>
                                            </div>
                                        </div>
                                    </form>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include "../../web_includes/footer.php"; ?>
        </div>
    </div>

    <!-- Password Modal -->
    <div class="modal fade" id="passwordCheckModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">You're about to Modify Profile Account.</h5>
                    <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST">
                    <div class="modal-body">Enter your Password to Verify:</div>
                    <input type="password" class="form-control" name="pswd" required />
                    <div class="modal-footer">
                        <input type="submit" name="check" class="btn btn-primary" style="background-color: red;border:none;" value="Done" />
                        <button type="submit" name="cancel" class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Are you Sure?</h5>
                    <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">Select "Logout" to Logout from your account.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form action="" method="POST">
                        <input type="submit" name="logout" class="btn btn-primary" style="background-color: red;border:none;" value="Logout" />
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../../js/sb-admin-2.min.js"></script>
    <script src="../../js/passwordStrengthening.js"></script>
</body>
</html>
<?php
} else {
    header("Location: ../../index.php");
    exit();
}
?>
