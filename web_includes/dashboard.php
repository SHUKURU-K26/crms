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
                                                  echo "<p style='font-size:20px;'>No Debts: $cars_available</p>";
                                                }
                                                 ?>
                                            </div>

                                            <div class="text-success" style="font-size:2.3em;font-weight:bold;">
                                                <?php                                                
                                                // Count of Cars Available for Rent
                                                $availableWithDebt="SELECT COUNT(car_id) AS available_cars_with_debt FROM cars WHERE status='available with Debt'";
                                                $resultForAvailable=$conn->query($availableWithDebt);
                                                if ($resultForAvailable->num_rows >0){
                                                  $available_rows=$resultForAvailable->fetch_assoc();
                                                  $cars_available_with_debt=$available_rows["available_cars_with_debt"];
                                                  echo "<p style='font-size:20px;color:red;'>With Debts: $cars_available_with_debt</p>";
                                                }
                                                 ?>
                                            </div>

                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-car fa-2x text-success"></i>
                                             <span style="font-size: 20px;" class="text-success">X<?php echo $cars_available + $cars_available_with_debt?></span> 
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
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Rented Cars</div>
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

                        <!-- Total Amount Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Revenue
                                            </div>

                                            <div class="text-success" style="font-size:2.3em;font-weight:bold;">
                                                <?php                                                
                                                //Count of All Total Revenue in the System
                                                $totalRevenue="SELECT SUM(revenue_received) AS total_revenue FROM rental_history";
                                                $resultForRevenue=$conn->query($totalRevenue);
                                                if ($resultForRevenue->num_rows >0){
                                                    $revenue_rows=$resultForRevenue->fetch_assoc();
                                                    $total_revenue=$revenue_rows["total_revenue"];
                                                    echo "<span id='total_revenue'>$total_revenue</span>";                                                    
                                                }
                                                 ?>
                                            </div>

                                        </div>                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                                                                        
                    </div>
                    <script>
                        // Format the total revenue with commas
                        document.addEventListener("DOMContentLoaded", function() {
                            var revenueElement = document.getElementById("total_revenue");
                            function formatRWF(amount){
                                   return Number(amount).toLocaleString('en-US') + ' RWF';
                            }
                            foreachmattedRevenue = formatRWF(revenueElement.textContent);
                            revenueElement.textContent = foreachmattedRevenue;
                            revenueElement.style.fontSize = ".8em";
                        });
                    </script>