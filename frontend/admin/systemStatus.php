<?php
session_start();
include "../../web_includes/auth.php";
include "../../web_db/connection.php";

// Handle account status toggle (Enable/Disable)
if (isset($_POST['toggle_status'])) {
    $user_id = intval($_POST['user_id']);
    $current_status = $_POST['current_status'];
    
    // Toggle status
    $new_status = ($current_status === 'active') ? 'disabled' : 'active';
    
    $update_sql = "UPDATE users SET account_status = '$new_status' WHERE user_id = $user_id";
    
    if ($conn->query($update_sql) === TRUE) {
        $action = ($new_status === 'disabled') ? 'disabled' : 'enabled';
        echo "
            <div id='successAlertBox' style='position: fixed; top: 20px; right: 20px; z-index: 9999; background: linear-gradient(135deg, #1cc88a, #13855c); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.3);'>
                <i class='fas fa-check-circle'></i> User account successfully $action!
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const alertBox = document.getElementById('successAlertBox');
                setTimeout(() => {
                    alertBox.style.transform = 'translateX(100%)';
                    alertBox.style.opacity = '0';
                    setTimeout(() => {
                        alertBox.remove();
                        window.location.href = '';
                    }, 500);
                }, 3000);
            });
            </script>
        ";
    } else {
        echo "<script>alert('Error updating account status.');</script>";
    }
}

// Logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../../index.php");
    exit();
}

if (isset($_SESSION["adminEmail"])){
    $adminEmail = $_SESSION["adminEmail"];
    $adminQuery = "SELECT * FROM users WHERE email='$adminEmail' AND role='admin'";
    $adminResult = mysqli_query($conn, $adminQuery);
    $adminData = mysqli_fetch_assoc($adminResult);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>User Management | GuestPro CMS</title>
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,900" rel="stylesheet" />
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet" />
    <link href="../../css/custom.css" rel="stylesheet">
    <link rel="icon" href="../../img/GuestProLogoReal.JPG" type="image/png">
    <style>
        .user-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .user-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-right: 15px;
        }
        
        .user-info {
            flex: 1;
        }
        
        .user-name {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }
        
        .user-role {
            font-size: 12px;
            opacity: 0.9;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-disabled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .user-body {
            padding: 20px;
        }
        
        .user-detail {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            font-size: 14px;
        }
        
        .user-detail i {
            width: 25px;
            color: #667eea;
            margin-right: 10px;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn-toggle {
            flex: 1;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-disable {
            background: #e74a3b;
            border: none;
            color: white;
        }
        
        .btn-disable:hover {
            background: #c0392b;
            color: white;
        }
        
        .btn-enable {
            background: #1cc88a;
            border: none;
            color: white;
        }
        
        .btn-enable:hover {
            background: #17a673;
            color: white;
        }
        
        .stats-card {
            border-radius: 15px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-3px);
        }
        
        .stats-icon {
            font-size: 40px;
            opacity: 0.3;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .stats-number {
            font-size: 32px;
            font-weight: 700;
            margin: 0;
        }
        
        .stats-label {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .page-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        
        .page-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 80px;
            opacity: 0.3;
            margin-bottom: 20px;
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include "../../web_includes/menu.php"; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include "../../web_includes/topbar.php"; ?>
                <div class="container-fluid">
                    
                    <!-- Page Header -->
                    <div class="page-header">
                        <h1><i class="fas fa-users-cog"></i> User Management</h1>
                        <p>Manage all system users and their account status</p>
                    </div>
                    
                    <!-- Statistics Cards -->
                    <div class="row">
                        <?php
                        // Get total users
                        $total_users_query = "SELECT COUNT(*) as total FROM users";
                        $total_result = $conn->query($total_users_query);
                        $total_users = $total_result->fetch_assoc()['total'];
                        
                        // Get active users
                        $active_users_query = "SELECT COUNT(*) as total FROM users WHERE account_status = 'active' OR account_status IS NULL";
                        $active_result = $conn->query($active_users_query);
                        $active_users = $active_result->fetch_assoc()['total'];
                        
                        // Get disabled users
                        $disabled_users_query = "SELECT COUNT(*) as total FROM users WHERE account_status = 'disabled'";
                        $disabled_result = $conn->query($disabled_users_query);
                        $disabled_users = $disabled_result->fetch_assoc()['total'];
                        
                        // Get staff count
                        $staff_query = "SELECT COUNT(*) as total FROM users WHERE role = 'staff'";
                        $staff_result = $conn->query($staff_query);
                        $staff_count = $staff_result->fetch_assoc()['total'];
                        ?>
                        
                        <div class="col-xl-3 col-md-6">
                            <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative;">
                                <p class="stats-label">Total Users</p>
                                <h2 class="stats-number"><?php echo $total_users; ?></h2>
                                <i class="fas fa-users stats-icon"></i>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6">
                            <div class="stats-card" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); position: relative;">
                                <p class="stats-label">Active Accounts</p>
                                <h2 class="stats-number"><?php echo $active_users; ?></h2>
                                <i class="fas fa-user-check stats-icon"></i>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6">
                            <div class="stats-card" style="background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%); position: relative;">
                                <p class="stats-label">Disabled Accounts</p>
                                <h2 class="stats-number"><?php echo $disabled_users; ?></h2>
                                <i class="fas fa-user-slash stats-icon"></i>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6">
                            <div class="stats-card" style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); position: relative;">
                                <p class="stats-label">Staff Members</p>
                                <h2 class="stats-number"><?php echo $staff_count; ?></h2>
                                <i class="fas fa-user-tie stats-icon"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Users List -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4 class="mb-3" style="color: #5a5c69; font-weight: 700;">
                                <i class="fas fa-list"></i> All System Users
                            </h4>
                        </div>
                        
                        <?php
                        $sql = "SELECT * FROM users ORDER BY 
                                CASE 
                                    WHEN role = 'admin' THEN 1 
                                    WHEN role = 'staff' THEN 2 
                                    ELSE 3 
                                END, 
                                full_name ASC";
                        $query = $conn->query($sql);
                        
                        if ($query->num_rows > 0) {
                            while ($row = $query->fetch_assoc()) {
                                // Set default status if null
                                $account_status = $row['account_status'] ?? 'active';
                                
                                // Get initials for avatar
                                $name_parts = explode(' ', $row['full_name']);
                                $initials = strtoupper(substr($name_parts[0], 0, 1));
                                if (count($name_parts) > 1) {
                                    $initials .= strtoupper(substr($name_parts[1], 0, 1));
                                }
                                
                                // Role badge color
                                $role_color = ($row['role'] === 'admin') ? '#f6c23e' : '#36b9cc';
                        ?>
                        
                        <div class="col-xl-4 col-md-6">
                            <div class="user-card">
                                <div class="user-header">
                                    <div class="d-flex align-items-center" style="flex: 1;">
                                        <div class="user-avatar">
                                            <?php echo $initials; ?>
                                        </div>
                                        <div class="user-info">
                                            <p class="user-name"><?php echo htmlspecialchars($row['full_name']); ?></p>
                                            <p class="user-role">
                                                <i class="fas fa-shield-alt"></i> <?php echo htmlspecialchars($row['role']); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <span class="status-badge status-<?php echo $account_status; ?>">
                                        <?php echo $account_status; ?>
                                    </span>
                                </div>
                                
                                <div class="user-body">
                                    <div class="user-detail">
                                        <i class="fas fa-envelope"></i>
                                        <span><?php echo htmlspecialchars($row['email']); ?></span>
                                    </div>
                                    
                                    <div class="user-detail">
                                        <i class="fas fa-phone"></i>
                                        <span><?php echo htmlspecialchars($row['phone']); ?></span>
                                    </div>
                                    
                                    <div class="user-detail">
                                        <i class="fas fa-user"></i>
                                        <span>@<?php echo htmlspecialchars($row['username']); ?></span>
                                    </div>
                                    
                                    <?php if ($row['role'] !== 'admin'): ?>
                                    <div class="action-buttons">
                                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="flex: 1;">
                                            <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>" />
                                            <input type="hidden" name="current_status" value="<?php echo $account_status; ?>" />
                                            
                                            <?php if ($account_status === 'active' || $account_status === NULL): ?>
                                                <button type="submit" name="toggle_status" class="btn btn-disable btn-toggle" 
                                                        onclick="return confirm('Are you sure you want to disable this account? The user will not be able to log in.')">
                                                    <i class="fas fa-ban"></i> Disable Account
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" name="toggle_status" class="btn btn-enable btn-toggle"
                                                        onclick="return confirm('Are you sure you want to enable this account? The user will be able to log in again.')">
                                                    <i class="fas fa-check"></i> Enable Account
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info mt-3 mb-0" style="font-size: 12px; padding: 8px;">
                                        <i class="fas fa-info-circle"></i> Admin accounts cannot be disabled
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <?php
                            }
                        } else {
                        ?>
                        <div class="col-12">
                            <div class="empty-state">
                                <i class="fas fa-users"></i>
                                <h4>No Users Found</h4>
                                <p>There are currently no users in the system.</p>
                            </div>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                    
                </div>
            </div>
            <?php include "../../web_includes/footer.php"; ?>
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