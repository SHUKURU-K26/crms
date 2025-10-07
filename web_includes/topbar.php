<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>                    
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>
                                  
                                <!--External Cars alerts-->
                                <?php include __DIR__ . "/externalCarsAlerts.php"; ?>
                                <li class="nav-item dropdown no-arrow mx-1">
                                    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-clock fa-fw" title="External Car"></i>
                                        <!-- Counter - Alerts -->
                                        <span class="badge badge-danger badge-counter"><?php echo $badgeCount; ?></span>
                                    </a>
                                    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                        aria-labelledby="alertsDropdown">
                                        <h6 class="dropdown-header">External Rental Falling off Alerts</h6>

                                        <?php if (!empty($cars)): ?>
                                            <?php foreach ($cars as $car): ?>
                                                <a class="dropdown-item d-flex align-items-center" href="renew_insurance.php?car_id=<?php echo $car['car_id']; ?>">
                                                    <div class="mr-3">
                                                        <div class="icon-circle bg-warning">
                                                            <i class="fas fa-info text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div  class="medium text-danger"><?php echo $car['expected_return_date']; ?> Rental is Falling off</div>
                                                        <span class="font-weight-bold"><?php echo $car['car_name'] . " (" . $car['plate_number'] . ") External Rent is Falling Off!"; ?></span>                                                        
                                                    </div>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <a class="dropdown-item d-flex align-items-center" href="#">
                                                <div class="mr-3">
                                                    <div class="icon-circle bg-success">
                                                        <i class="fas fa-check text-white"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="font-weight-bold">No External Rent about to Fall off</span>
                                                </div>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </li>

                                <!--Insurance Alerts-->
                                <?php include __DIR__ . "/insurance_alerts.php"; ?>
                                <li class="nav-item dropdown no-arrow mx-1">
                                    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-handshake fa-fw" title="Insurance alerts"></i>
                                        <!-- Counter - Alerts -->
                                        <span class="badge badge-danger badge-counter"><?php echo $badgeCount; ?></span>
                                    </a>
                                    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                        aria-labelledby="alertsDropdown">
                                        <h6 class="dropdown-header">Insurance Expiration Alerts</h6>

                                        <?php if (!empty($cars)): ?>
                                            <?php foreach ($cars as $car): ?>
                                                <a class="dropdown-item d-flex align-items-center" href="renew_insurance.php?car_id=<?php echo $car['car_id']; ?>">
                                                    <div class="mr-3">
                                                        <div class="icon-circle bg-warning">
                                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div  class="medium text-danger"><?php echo $car['insurance_expiry_date']; ?> Expiring Soon!</div>
                                                        <span class="font-weight-bold"><?php echo $car['car_name'] . " (" . $car['plate_number'] . ") insurance expiring soon!"; ?></span>                                                        
                                                    </div>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <a class="dropdown-item d-flex align-items-center" href="#">
                                                <div class="mr-3">
                                                    <div class="icon-circle bg-success">
                                                        <i class="fas fa-check text-white"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="font-weight-bold">Insurance of All Cars is still Valid</span>
                                                </div>
                                            </a>
                                        <?php endif; ?>                                        
                                    </div>
                                </li> 

                            <!-- Control Alerts -->
                            <?php include __DIR__ . "/control_alerts.php"; ?>
                            <li class="nav-item dropdown no-arrow mx-1">
                                <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-tools" title="Technical Control ALerts"></i>
                                    <!-- Counter - Messages -->
                                    <span class="badge badge-danger badge-counter"><?php echo $badgeCountforControl; ?></span>
                                </a>
                                <!-- Dropdown - Messages -->
                                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="messagesDropdown">
                                    <h6 class="dropdown-header">
                                        Techinical Control Expiration Alerts
                                    </h6>
                                    <?php if (!empty($control_expiration_cars)): ?>
                                                <?php foreach ($control_expiration_cars as $control): ?>
                                                    <a class="dropdown-item d-flex align-items-center" href="renew_control.php?car_id=<?php echo $control['car_id']; ?>">
                                                        <div class="mr-3">
                                                            <div class="icon-circle bg-warning">
                                                                <i class="fas fa-exclamation-triangle text-white"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="medium text-danger"><?php echo $control['control_expiry_date']; ?> Expiring Soon</div>
                                                            <span class="font-weight-bold"><?php echo $control['car_name'] . " (" . $control['plate_number'] . ") expiring soon!"; ?></span>
                                                        </div>
                                                    </a>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <a class="dropdown-item d-flex align-items-center" href="#">
                                                    <div class="mr-3">
                                                        <div class="icon-circle bg-success">
                                                            <i class="fas fa-check text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span class="font-weight-bold">Techincal Control of all Cars is Up to Date.</span>
                                                    </div>
                                                </a>
                                            <?php endif; ?>                                
                                </div>
                            </li>


                            <!--Registration Codes--> 
                            <?php include __DIR__ . "/registration_codes.php"; ?>
                            <li class="nav-item dropdown no-arrow mx-1">
                                <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-key" title="Technical Control ALerts"></i>
                                    <!-- Counter - Messages -->
                                    <span class="badge badge-danger badge-counter"><?php echo $badgeCountforcodes; ?></span>
                                </a>
                                <!-- Dropdown - Messages -->
                                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="messagesDropdown">
                                    <h6 class="dropdown-header">
                                        Registration Codes
                                    </h6>
                                    <?php if (!empty($allCodes)): ?>
                                                <?php 
                                                    $codeCount = 0;
                                                    foreach ($allCodes as $code):$codeCount++ ?>
                                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                                        <div class="mr-3">
                                                            <div class="icon-circle bg-success">
                                                                <i class="fas fa-check text-white"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="medium text-success"><?php echo $code['code']; ?> Available</div>
                                                            <span class="font-weight-bold"><?php echo $codeCount . " (" . $code['code'] . ") Share It"; ?></span>
                                                        </div>
                                                    </a>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <a class="dropdown-item d-flex align-items-center" href="#">
                                                    <div class="mr-3">
                                                        <div class="icon-circle bg-success">
                                                            <i class="fas fa-check text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span class="font-weight-bold">No Registration PassKeys. Generate New one</span>
                                                    </div>
                                                </a>
                                            <?php endif; ?>                                
                                </div>
                            </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">SK </span>
                                <img class="img-profile rounded-circle" src="../../img/GuestProLogoReal.JPG" alt="Profile Picture">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="change_password.php">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Change password
                                </a>                                
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>