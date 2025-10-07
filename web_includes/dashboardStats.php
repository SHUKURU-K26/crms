<?php
 include "../web_db/connection.php";

 // Count of All Total Cars in the System
 $totalCarsSql="SELECT COUNT(car_id) AS total_cars FROM cars";
 $result=$conn->query($totalCarsSql);
 if ($result->num_rows>0) {
    $row=$result->fetch_assoc();
    $totalCars=$row["total_cars"];
 }


 // Count of Cars Available for Rent
 $availableCars="SELECT COUNT(car_id) AS available_cars FROM cars WHERE status='available'";
 $resultForAvailable=$conn->query($availableCars);
 if ($resultForAvailable->num_rows >0){
   $available_rows=$resultForAvailable->fetch_assoc();
   $cars_available=$available_rows["available_cars"];
 }

 //Count of Cars in Rent Mode
 $rentedCars="SELECT COUNT(car_id) AS rented_cars FROM cars WHERE status='rented'";
 $resultForRented=$conn->query($rentedCars);
 if ($resultForRented->num_rows >0){
   $rented_rows=$resultForRented->fetch_assoc();
   $cars_rented=$rented_rows["rented_cars"];
 }


 //Count of All Categories on System
$categories_registered="SELECT COUNT(category_id) AS all_categories FROM car_categories";
 $resultForcategories=$conn->query($categories_registered);
 if ($resultForcategories->num_rows >0){
   $category_rows=$resultForcategories->fetch_assoc();
   $All_categories=$category_rows["all_categories"];
 }


 //Count of All Total Revenue in the System
  $totalRevenue="SELECT SUM(revenue_received) AS total_revenue FROM rental_history";
  $resultForRevenue=$conn->query($totalRevenue);
  if ($resultForRevenue->num_rows >0){
    $revenue_rows=$resultForRevenue->fetch_assoc();
    $total_revenue=$revenue_rows["total_revenue"];
  }
?> 