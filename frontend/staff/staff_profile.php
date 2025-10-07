<?php
session_start();
include "../../staff_web_includes/staff_auth.php";
include "../../web_db/connection.php";

// Handle password check
if (isset($_POST['check'])) {
    $entered_password = $_POST['pswd'] ?? '';
    
    // Verify the entered password
    $user_logged_password= $_SESSION["Userpassword"];
    $SqlforUser = "SELECT * FROM users WHERE password='$user_logged_password'";
    $resForUser = mysqli_query($conn, $SqlforUser);
    $user = mysqli_fetch_assoc($resForUser);
    $user_full_names=$user['full_name'];
    $old_username=$user['username'];
    $user_logged_password=$user['password'];
    $sql = "SELECT password FROM users WHERE full_name='$user_full_names' AND username='$old_username' AND password='$user_logged_password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($entered_password, $row['password'])){
            $_SESSION['can_edit_profile'] = true;
        } else {
            $_SESSION['can_edit_profile'] = false;
            echo "
                <div id='deleteSuccessBox'>
                    Incorrect Password!
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const alertBox = document.getElementById('deleteSuccessBox');
                        if (!alertBox) return;
                        setTimeout(() => {
                         alertBox.style.opacity = 0;
                         setTimeout(() => alertBox.remove(), 500);
                         window.location.href = 'staff_profile.php';
                        }, 3000);
                    });
                </script>
           ";
        }
    }
}

// Handle profile update submission (example, you can expand validation)
if (isset($_POST['update_profile'])) {
    $user_logged_password= $_SESSION["Userpassword"];
    $SqlforUser = "SELECT * FROM users WHERE password='$user_logged_password'";
    $resForUser = mysqli_query($conn, $SqlforUser);
    $user = mysqli_fetch_assoc($resForUser);
    $user_full_names=$user['full_name'];
    $old_username=$user['username'];

    if (isset($_SESSION['can_edit_profile']) && $_SESSION['can_edit_profile'] === true) {
        // Sanitize inputs
        $full_name = $conn->real_escape_string($_POST['full_name']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $new_username = $conn->real_escape_string($_POST['username']);

        $update_sql = "UPDATE users SET full_name='$full_name', email='$email', phone='$phone', username='$new_username' 
        WHERE full_name='$user_full_names' AND username='$old_username' AND password='$user_logged_password' AND role='staff'";

        if ($conn->query($update_sql) === TRUE){
            // Clear session right away (logout)
            session_unset();
            session_destroy();
            echo "
            <div id='successAlertBox'>
            Profile Info Updated <strong>Successfully.</strong>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const alertBox = document.getElementById('successAlertBox');
                if (!alertBox) return;

                setTimeout(() => {
                alertBox.style.opacity = 0;
                setTimeout(() => alertBox.remove(), 500);
                window.location.href = '../../index.php';
                }, 3000);
            });
            </script>
            ";
            // After successful update, clear the session flag to require password again later
            unset($_SESSION['can_edit_profile']);
        } else {
            echo "<script>alert('Error updating profile.');</script>";
        }
    } else {
        echo "<script>alert('You must verify your password to update profile.');</script>";
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
    $user_logged_password= $_SESSION["Userpassword"];
    $SqlforUser = "SELECT * FROM users WHERE password='$user_logged_password'";
    $resForUser = mysqli_query($conn, $SqlforUser);
    $user = mysqli_fetch_assoc($resForUser);
    $user_full_names=$user['full_name'];
    $old_username=$user['username'];   

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Profile View</title>
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,900" rel="stylesheet" />
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet" />
    <link rel="icon" href="../../img/GuestProLogoReal.JPG" type="image/png">
    <link  href="../../css/custom.css" rel="stylesheet">
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
                                <li class="breadcrumb-item active">Profile</li>
                            </ol>
                        </div>
                        <div class="col-md-7 align-self-center">
                            <button data-toggle="modal" data-target="#passwordCheckModal" class="btn btn-info pull-right">Edit Profile</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-5">
                            <div class="card">
                                <div class="card-body">
                                    <center class="m-t-30">
                                        <img src="../../img/GuestProLogoReal.JPG" class="img-circle" width="150" />
                                        <h4 class="card-title m-t-10" style="color: #970000;font-weight:bold;"><?php echo $user_full_names?></h4>
                                        <h6 class="card-subtitle">Signed in</h6>
                                    </center>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-7">
                            <div class="card">
                                <div class="card-body">
                                    <?php
                                    $sql="SELECT * FROM users WHERE full_name='$user_full_names' AND username='$old_username' AND password='$user_logged_password'";
                                    $query=$conn->query($sql);
                                    if ($query->num_rows > 0) {
                                        $row = $query->fetch_assoc();
                                        $readonly = !(isset($_SESSION['can_edit_profile']) && $_SESSION['can_edit_profile'] === true);
                                    ?>
                                    <form class="form-horizontal form-material" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST">
                                        <div class="form-group">
                                            <label class="col-md-12">Full Name</label>
                                            <div class="col-md-12">
                                                <input type="text" value="<?php echo htmlspecialchars($row["full_name"]) ?>" <?php echo $readonly ? 'readonly' : ''; ?> class="form-control form-control-line" name="full_name">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12">Email</label>
                                            <div class="col-md-12">
                                                <input type="email" value="<?php echo htmlspecialchars($row["email"]) ?>" <?php echo $readonly ? 'readonly' : ''; ?> class="form-control form-control-line" name="email">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12">Phone No</label>
                                            <div class="col-md-12">
                                                <input type="text" value="<?php echo htmlspecialchars($row["phone"]) ?>" <?php echo $readonly ? 'readonly' : ''; ?> class="form-control form-control-line" name="phone">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12">Username@</label>
                                            <div class="col-md-12">
                                                <input type="text" value="<?php echo htmlspecialchars($row["username"]) ?>" <?php echo $readonly ? 'readonly' : ''; ?> class="form-control form-control-line" name="username">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <button type="submit" name="update_profile" class="btn btn-success" <?php echo $readonly ? 'disabled' : ''; ?>>Update Profile</button>
                                            </div>
                                        </div>
                                    </form>
                                    <?php
                                       
                                    } 
                                    ?>
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
</body>
</html>
<?php
} else {
    header("Location: ../../index.php");
    exit();
}
?>
