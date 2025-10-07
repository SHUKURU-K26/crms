<?php include "../../web_db/connection.php"?>
                    <div class="row">
                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Cars</div>
                                            <div class="text-primary" style="font-size:2.3em;font-weight:bold;">
                                                <?php
                                                // Count of All Total Cars in the System                                                    
                                                    $totalCarsSql="SELECT COUNT(car_id) AS total_cars FROM cars";
                                                    $result=$conn->query($totalCarsSql);
                                                    if ($result->num_rows>0) {
                                                    $row=$result->fetch_assoc();
                                                    $totalCars=$row["total_cars"];
                                                    echo $totalCars;
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-car fa-2x" style="color: rgb(0, 95, 190);"></i>
                                            <i class="fas fa-car fa-2x" style="color: rgb(0, 95, 190);"></i>
                                            <i class="fas fa-car fa-2x" style="color: rgb(0, 95, 190);"></i>
                                            <i class="fas fa-car fa-2x" style="color: rgb(0, 95, 190);"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Available For Rent
                                            </div>

                                            <div class="text-success" style="font-size:2.3em;font-weight:bold;">
                                                <?php                                                
                                                // Count of Cars Available for Rent
                                                $availableCars="SELECT COUNT(car_id) AS available_cars FROM cars WHERE status='available'";
                                                $resultForAvailable=$conn->query($availableCars);
                                                if ($resultForAvailable->num_rows >0){
                                                  $available_rows=$resultForAvailable->fetch_assoc();
                                                  $cars_available=$available_rows["available_cars"];
                                                  echo $cars_available;
                                                }
                                                 ?>
                                            </div>

                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-car fa-2x text-success"></i>
                                             <span style="font-size: 20px;" class="text-success">X<?php echo $cars_available?></span> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">All Rented Cars</div>
                                            <div class="text-danger" style="font-size:2.3em;font-weight:bold;">
                                                <?php                                                
                                                //Count of Cars in Rent Mode
                                                $rentedCars="SELECT COUNT(car_id) AS rented_cars FROM cars WHERE status='rented'";
                                                $resultForRented=$conn->query($rentedCars);
                                                if ($resultForRented->num_rows >0){
                                                $rented_rows=$resultForRented->fetch_assoc();
                                                $cars_rented=$rented_rows["rented_cars"];
                                                echo $cars_rented;
                                                }
                                                 ?>
                                            </div>

                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-car fa-2x text-danger"></i>
                                            <span style="font-size: 20px;" class="text-danger">X<?php echo $cars_rented?></span> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                                            <!---->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Rented By you: </div>
                                            <div class="text-danger" style="font-size:2.3em;font-weight:bold;">
                                                <?php                                                
                                                //Count of Cars in Rent Mode
                                                $rentedCars="SELECT COUNT(car_id) AS rented_cars 
                                                FROM rentals r JOIN users u ON r.user_id = u.user_id WHERE  u.full_name='$user_full_names'";
                                                $resultForRented=$conn->query($rentedCars);
                                                if ($resultForRented->num_rows >0){
                                                $rented_rows=$resultForRented->fetch_assoc();
                                                $cars_rented=$rented_rows["rented_cars"];
                                                echo $cars_rented;
                                                }
                                                 ?>
                                            </div>

                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-car fa-2x text-danger"></i>
                                            <span style="font-size: 20px;" class="text-danger">X<?php echo $cars_rented?></span> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                                                   
                    </div>