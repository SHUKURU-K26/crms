-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: car_rental_management_system
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `car_categories`
--

DROP TABLE IF EXISTS `car_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `car_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `created_at` date DEFAULT curdate(),
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `car_categories`
--

LOCK TABLES `car_categories` WRITE;
/*!40000 ALTER TABLE `car_categories` DISABLE KEYS */;
INSERT INTO `car_categories` VALUES (2,'Economy','2025-08-17'),(3,'VIP','2025-08-17'),(6,'Vans','2025-08-23'),(8,'Classic','2025-09-04');
/*!40000 ALTER TABLE `car_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cars`
--

DROP TABLE IF EXISTS `cars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cars` (
  `car_id` int(11) NOT NULL AUTO_INCREMENT,
  `car_name` varchar(100) NOT NULL,
  `category_id` int(11) NOT NULL,
  `plate_number` varchar(100) DEFAULT NULL,
  `type` enum('automatic','manual') DEFAULT NULL,
  `fuel_type` enum('Super','Gasoil','Hybrid','100% Electricity') DEFAULT NULL,
  `status` enum('available','rented','available with Debt') DEFAULT NULL,
  `insurance_issued_date` date DEFAULT NULL,
  `insurance_expiry_date` date DEFAULT NULL,
  `control_issued_date` date DEFAULT NULL,
  `control_expiry_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`car_id`),
  UNIQUE KEY `plate_number` (`plate_number`),
  KEY `fk_category` (`category_id`),
  CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `car_categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cars`
--

LOCK TABLES `cars` WRITE;
/*!40000 ALTER TABLE `cars` DISABLE KEYS */;
INSERT INTO `cars` VALUES (3,'Ford',2,'RAD 966 I','automatic','Super','available','2025-08-28','2026-08-28','2025-08-26','2026-08-27','2025-08-25 22:00:00'),(5,'V8 Land Cruiser',3,'RAG 100 V','automatic','Super','available','2025-08-27','2026-08-27','2025-08-27','2026-08-27','2025-08-26 22:00:00'),(9,'KIA Sorent',2,'RAI 182 Z','automatic','Hybrid','available','2025-09-03','2026-09-03','2025-09-03','2026-09-03','2025-09-02 22:00:00'),(20,'Hyudai Tucson',2,'RAI 083 I','automatic','Super','available','2025-09-16','2026-09-16','2025-09-16','2026-09-16','2025-09-15 22:00:00'),(21,'Hyudai Sentafe',2,'RAG 128 J','automatic','Super','available','2025-09-16','2026-09-16','2025-09-16','2026-09-16','2025-09-15 22:00:00'),(23,'Toyota Hiance',6,'RAG 400 K','manual','Gasoil','available','2025-09-16','2026-09-16','2025-09-16','2026-09-16','2025-09-15 22:00:00'),(24,'Benz',8,'RAJ 100 S','manual','Super','available','2025-09-18','2026-09-18','2025-09-18','2026-09-18','2025-09-17 22:00:00'),(25,'Toyota Carina',2,'RAH 403 H','manual','Super','available','2025-09-18','2026-09-18','2025-09-18','2026-09-18','2025-09-17 22:00:00'),(26,'Hyundai sonata ',2,'RAJ 349 K','automatic','Super','available','2025-09-18','2026-09-18','2025-09-18','2026-09-18','2025-09-17 22:00:00'),(27,'Volkswagen T-Cross',2,'RAH 289 F','automatic','Super','available','2025-09-26','2026-10-19','2025-09-26','2026-09-19','2025-09-25 22:00:00');
/*!40000 ALTER TABLE `cars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `debts`
--

DROP TABLE IF EXISTS `debts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `debts` (
  `debt_id` int(11) NOT NULL AUTO_INCREMENT,
  `debt_type` enum('internal','external') DEFAULT NULL,
  `car_name` varchar(20) DEFAULT NULL,
  `car_plate` varchar(15) DEFAULT NULL,
  `renter_names` varchar(25) DEFAULT NULL,
  `national_id` varchar(16) DEFAULT NULL,
  `phone_number` varchar(16) DEFAULT NULL,
  `debt_amount` int(11) DEFAULT NULL,
  `provider_names` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`debt_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `debts`
--

LOCK TABLES `debts` WRITE;
/*!40000 ALTER TABLE `debts` DISABLE KEYS */;
/*!40000 ALTER TABLE `debts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses_history`
--

DROP TABLE IF EXISTS `expenses_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expenses_history` (
  `eid` int(11) NOT NULL AUTO_INCREMENT,
  `car_name` varchar(20) DEFAULT NULL,
  `plate` varchar(16) DEFAULT NULL,
  `provider` varchar(25) DEFAULT NULL,
  `amount_paid` int(11) DEFAULT NULL,
  `payment_method` enum('Momo','Bank','Not Paid Yet') DEFAULT NULL,
  `track_date` date DEFAULT NULL,
  PRIMARY KEY (`eid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses_history`
--

LOCK TABLES `expenses_history` WRITE;
/*!40000 ALTER TABLE `expenses_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `external_car_expenses`
--

DROP TABLE IF EXISTS `external_car_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `external_car_expenses` (
  `eid` int(11) NOT NULL AUTO_INCREMENT,
  `car_id` int(11) NOT NULL,
  PRIMARY KEY (`eid`),
  KEY `fk_car_id` (`car_id`),
  CONSTRAINT `fk_car_id` FOREIGN KEY (`car_id`) REFERENCES `external_cars` (`car_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `external_car_expenses`
--

LOCK TABLES `external_car_expenses` WRITE;
/*!40000 ALTER TABLE `external_car_expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `external_car_expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `external_cars`
--

DROP TABLE IF EXISTS `external_cars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `external_cars` (
  `car_id` int(11) NOT NULL AUTO_INCREMENT,
  `car_name` varchar(100) DEFAULT NULL,
  `provider` varchar(100) DEFAULT NULL,
  `negotiated_price` int(11) DEFAULT NULL,
  `date_brought` date DEFAULT NULL,
  `expected_return_date` date DEFAULT NULL,
  `days_in_service` int(11) DEFAULT NULL,
  `total_spending` int(11) DEFAULT NULL,
  `plate_number` varchar(100) DEFAULT NULL,
  `type` enum('automatic','manual') DEFAULT NULL,
  `fuel_type` enum('super','Gasoil','Hybrid','100% Electricity') DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('available','rented') DEFAULT NULL,
  `use_status` enum('Fully Paid','Half Paid','Unpaid') DEFAULT NULL,
  `payment_method` enum('Momo','Bank','Not Paid Yet') DEFAULT NULL,
  `balance` int(11) DEFAULT NULL,
  `lifecycle_status` enum('active','returned') DEFAULT 'active',
  PRIMARY KEY (`car_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `external_cars_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `external_cars`
--

LOCK TABLES `external_cars` WRITE;
/*!40000 ALTER TABLE `external_cars` DISABLE KEYS */;
/*!40000 ALTER TABLE `external_cars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `external_rental_history`
--

DROP TABLE IF EXISTS `external_rental_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `external_rental_history` (
  `external_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `renter_names` varchar(30) NOT NULL,
  `renter_phone` varchar(14) NOT NULL,
  `renter_national_id` varchar(16) NOT NULL,
  `car_name` varchar(20) NOT NULL,
  `car_plate` varchar(15) NOT NULL,
  `date_rented_on` date NOT NULL,
  `expected_return_date` date NOT NULL,
  `date_returned_on` date NOT NULL,
  `days_in_rent` int(11) NOT NULL,
  `rental_fee` int(11) NOT NULL,
  `revenue_received` int(11) NOT NULL,
  `revenue_status` varchar(20) NOT NULL,
  `expected_revenue` int(11) NOT NULL,
  `lifecycle_status` enum('active','returned') DEFAULT NULL,
  `refund_due` int(11) NOT NULL,
  `provider_names` varchar(20) NOT NULL,
  PRIMARY KEY (`external_history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `external_rental_history`
--

LOCK TABLES `external_rental_history` WRITE;
/*!40000 ALTER TABLE `external_rental_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `external_rental_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `external_rentals`
--

DROP TABLE IF EXISTS `external_rentals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `external_rentals` (
  `external_rental_id` int(11) NOT NULL AUTO_INCREMENT,
  `car_id` int(11) NOT NULL,
  `renter_full_name` varchar(35) NOT NULL,
  `id_number` varchar(16) NOT NULL,
  `telephone` varchar(16) NOT NULL,
  `negotiated_price` int(11) NOT NULL,
  `rent_date` date NOT NULL,
  `return_date` date NOT NULL,
  `days_in_rent` int(11) NOT NULL,
  `total_fee` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`external_rental_id`),
  KEY `car_id` (`car_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `external_rentals_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `external_cars` (`car_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `external_rentals_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `external_rentals`
--

LOCK TABLES `external_rentals` WRITE;
/*!40000 ALTER TABLE `external_rentals` DISABLE KEYS */;
/*!40000 ALTER TABLE `external_rentals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `p_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_type` enum('internal','external') DEFAULT NULL,
  `amount_paid` int(11) DEFAULT NULL,
  `paid_by` varchar(20) DEFAULT NULL,
  `payer_phone` varchar(16) DEFAULT NULL,
  `payer_national_id` varchar(16) DEFAULT NULL,
  `car_payed_for` varchar(20) DEFAULT NULL,
  `plate` varchar(15) DEFAULT NULL,
  `status` enum('Full paid','Half paid') DEFAULT NULL,
  `balance` int(11) DEFAULT NULL,
  PRIMARY KEY (`p_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,'internal',50000,'Didier Bana','0789238392','1087349388333223','Hyudai Tucson','RAI 083 I','Full paid',0),(2,'external',50000,'Shukuru Kamanzi','0792989892','1087349388333223','Dinar','RAD 132 X','Full paid',0),(3,'external',250000,'Shukuru Kamanzi','0792989892','1087349388333223','Mercedez Benz','RAG 791 X','Full paid',0);
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registration_codes`
--

DROP TABLE IF EXISTS `registration_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registration_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `status` enum('unused','used') NOT NULL DEFAULT 'unused',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registration_codes`
--

LOCK TABLES `registration_codes` WRITE;
/*!40000 ALTER TABLE `registration_codes` DISABLE KEYS */;
INSERT INTO `registration_codes` VALUES (5,'165175','unused'),(6,'787714','unused'),(7,'012525','unused');
/*!40000 ALTER TABLE `registration_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rental_history`
--

DROP TABLE IF EXISTS `rental_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rental_history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `renter_names` varchar(30) DEFAULT NULL,
  `renter_phone` varchar(14) DEFAULT NULL,
  `renter_national_id` varchar(16) DEFAULT NULL,
  `car_name` varchar(20) DEFAULT NULL,
  `car_plate` varchar(15) DEFAULT NULL,
  `date_rented_on` date DEFAULT NULL,
  `expected_return_date` date DEFAULT NULL,
  `date_returned_on` date DEFAULT NULL,
  `days_in_rent` int(11) DEFAULT NULL,
  `rental_fee` int(11) DEFAULT NULL,
  `revenue_received` int(11) DEFAULT NULL,
  `revenue_status` varchar(20) DEFAULT NULL,
  `expected_revenue` int(11) DEFAULT NULL,
  `refund_due` int(11) DEFAULT NULL,
  `provider_names` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rental_history`
--

LOCK TABLES `rental_history` WRITE;
/*!40000 ALTER TABLE `rental_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `rental_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rentals`
--

DROP TABLE IF EXISTS `rentals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rentals` (
  `rental_id` int(11) NOT NULL AUTO_INCREMENT,
  `car_id` int(11) DEFAULT NULL,
  `renter_full_name` varchar(35) DEFAULT NULL,
  `id_number` varchar(16) DEFAULT NULL,
  `telephone` varchar(16) DEFAULT NULL,
  `price` decimal(11,0) DEFAULT NULL,
  `rent_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `days_in_rent` int(11) DEFAULT NULL,
  `total_fee` decimal(11,0) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`rental_id`),
  KEY `car_id` (`car_id`),
  KEY `fk_rentals_user` (`user_id`),
  CONSTRAINT `fk_rentals_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`car_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rentals`
--

LOCK TABLES `rentals` WRITE;
/*!40000 ALTER TABLE `rentals` DISABLE KEYS */;
/*!40000 ALTER TABLE `rentals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `phone` varchar(14) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Giraneza Sylvie','GuestPro','fake123@gmail.com','0788888888','$2y$10$P0CsEHoJmTPDMI9lM4JHXOeE0woamxcyY5djZCLTCzQsKQtGVT/jm','admin'),(4,'Shukuru Kamanzi','Shukuru26','ShukuruKamanzi26@gmail.com','0795088463','$2y$10$lLGTngeAPmKul7Fkde2tpeCffkpfJ27vbSpIVKUTzvlewMk3OB/lW','staff'),(5,'Didier Bana','Didier','DidierBana123@gmail.com','0789698036','$2y$10$Xz70uMwnCihm7xwrQCuj1ey5I5.Wlv0B5PC8ATQ8uEUHB/F4ewaF.','staff');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-07  9:50:50
