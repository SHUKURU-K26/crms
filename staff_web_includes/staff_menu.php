<!-- Original Menu with Modal Integration -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="staff_home.php">
        <div class="sidebar-brand-icon">
            <i class="fas fa-car"></i>
        </div>
        <div class="sidebar-brand-text mx-3">CRMS<sup>&copy;</sup></div>                
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="staff_home.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Status
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
            aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-car"></i>
            <span>Cars</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">All Cars</h6>                                
                <a class="collapse-item" href="internal_status.php">Internal Status</a>
                <a class="collapse-item" href="external_status.php">External Status</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Utilities Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
            aria-expanded="true" aria-controls="collapseUtilities">
            <i class="fas fa-fw fa-key"></i>
            <span>Renting</span>
        </a>
        <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Renting Menus:</h6>
                <a class="collapse-item" href="staff_rent_car.php">Rent</a>
                <a class="collapse-item" href="internal_rentals.php">Internal Rentals</a>
                <a class="collapse-item" href="external_rentals.php">External Rentals</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">
    <div class="sidebar-heading">
        History
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#history"
            aria-expanded="true" aria-controls="history">
            <i class="fas fa-fw fa-folder"></i>
            <span>Renting History</span>
        </a>
        <div id="history" class="collapse" aria-labelledby="rental History" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">External & Internal</h6>
                <a class="collapse-item" href="internal_rent_history.php">Internal Rental History</a>
                <a class="collapse-item" href="external_rent_history.php">External rental History</a>
            </div>
        </div>
    </li>

    <!-- Divider-->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Account Settings
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
            aria-expanded="true" aria-controls="collapsePages">
            <i class="fas fa-toolbox"></i>
            <span>Settings</span>
        </a>
        <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Settings</h6>
                <a class="collapse-item" href="staff_profile.php">Profile</a>
                <a class="collapse-item" href="staff_change_password.php">Change Password</a>
                <a class="collapse-item" href="#">Logout</a>                        
            </div>
        </div>
    </li>
    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block"><br><br>
        <?php
        include "../../web_db/connection.php";
        $user_logged_password = $_SESSION["Userpassword"];
        $stmt = $conn->prepare("SELECT * FROM users WHERE password = ? AND role='staff'");
        $stmt->bind_param("s", $user_logged_password);
        $stmt->execute();
        $result = $stmt->get_result();
         $row = $result->fetch_assoc();                        
        ?>
    <p style="text-align: center;">
        <?php
         echo "Logged in as: <br><strong style='color:white;'>" .$row["full_name"] . "</strong>";
         $stmt->close();              
        ?>
    </p>
    
    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>           

</ul>
