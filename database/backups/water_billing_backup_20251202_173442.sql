-- MySQL dump 10.13  Distrib 5.7.44, for Linux (x86_64)
--
-- Host: localhost    Database: water_billing
-- ------------------------------------------------------
-- Server version	5.7.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `AreaAssignment`
--

DROP TABLE IF EXISTS `AreaAssignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AreaAssignment` (
  `area_assignment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `area_id` bigint(20) unsigned NOT NULL,
  `meter_reader_id` bigint(20) unsigned NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  PRIMARY KEY (`area_assignment_id`),
  KEY `areaassignment_area_id_meter_reader_id_effective_from_index` (`area_id`,`meter_reader_id`,`effective_from`),
  KEY `areaassignment_meter_reader_id_foreign` (`meter_reader_id`),
  CONSTRAINT `areaassignment_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `area` (`a_id`),
  CONSTRAINT `areaassignment_meter_reader_id_foreign` FOREIGN KEY (`meter_reader_id`) REFERENCES `meter_readers` (`mr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AreaAssignment`
--

LOCK TABLES `AreaAssignment` WRITE;
/*!40000 ALTER TABLE `AreaAssignment` DISABLE KEYS */;
/*!40000 ALTER TABLE `AreaAssignment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BillAdjustment`
--

DROP TABLE IF EXISTS `BillAdjustment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BillAdjustment` (
  `bill_adjustment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bill_id` bigint(20) unsigned NOT NULL,
  `bill_adjustment_type_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`bill_adjustment_id`),
  KEY `bill_adjustment_bill_index` (`bill_id`),
  KEY `bill_adjustment_created_at_index` (`created_at`),
  KEY `billadjustment_bill_adjustment_type_id_foreign` (`bill_adjustment_type_id`),
  KEY `billadjustment_user_id_foreign` (`user_id`),
  CONSTRAINT `billadjustment_bill_adjustment_type_id_foreign` FOREIGN KEY (`bill_adjustment_type_id`) REFERENCES `BillAdjustmentType` (`bill_adjustment_type_id`),
  CONSTRAINT `billadjustment_bill_id_foreign` FOREIGN KEY (`bill_id`) REFERENCES `water_bill_history` (`bill_id`) ON DELETE CASCADE,
  CONSTRAINT `billadjustment_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BillAdjustment`
--

LOCK TABLES `BillAdjustment` WRITE;
/*!40000 ALTER TABLE `BillAdjustment` DISABLE KEYS */;
/*!40000 ALTER TABLE `BillAdjustment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BillAdjustmentType`
--

DROP TABLE IF EXISTS `BillAdjustmentType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BillAdjustmentType` (
  `bill_adjustment_type_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direction` enum('debit','credit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`bill_adjustment_type_id`),
  KEY `billadjustmenttype_stat_id_foreign` (`stat_id`),
  CONSTRAINT `billadjustmenttype_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BillAdjustmentType`
--

LOCK TABLES `BillAdjustmentType` WRITE;
/*!40000 ALTER TABLE `BillAdjustmentType` DISABLE KEYS */;
INSERT INTO `BillAdjustmentType` VALUES (1,'Meter Reading Error','credit',2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(2,'Billing Error','credit',2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(3,'Penalty Waiver','credit',2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(4,'Surcharge','debit',2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(5,'Other','debit',2,'2025-11-06 01:46:14','2025-11-06 01:46:14');
/*!40000 ALTER TABLE `BillAdjustmentType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ChargeItem`
--

DROP TABLE IF EXISTS `ChargeItem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ChargeItem` (
  `charge_item_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `default_amount` decimal(10,2) NOT NULL,
  `charge_type` enum('one_time','recurring','usage_based') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_taxable` tinyint(1) NOT NULL DEFAULT '0',
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`charge_item_id`),
  UNIQUE KEY `chargeitem_code_unique` (`code`),
  KEY `charge_item_code_index` (`code`),
  KEY `charge_item_type_index` (`charge_type`),
  KEY `chargeitem_stat_id_foreign` (`stat_id`),
  CONSTRAINT `chargeitem_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ChargeItem`
--

LOCK TABLES `ChargeItem` WRITE;
/*!40000 ALTER TABLE `ChargeItem` DISABLE KEYS */;
INSERT INTO `ChargeItem` VALUES (1,'Connection Fee','CONN_FEE','Initial connection fee for new water service',500.00,'one_time',0,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(2,'Service Deposit','SERVICE_DEP','Refundable security deposit',300.00,'one_time',0,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(3,'Meter Deposit','METER_DEP','Water meter deposit',200.00,'one_time',0,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(4,'Application Processing Fee','APP_PROC','Non-refundable application processing fee',50.00,'one_time',0,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(5,'Installation Fee','INSTALL_FEE','Meter installation fee',800.00,'one_time',0,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(6,'Monthly Service Charge','MONTHLY_SVC','Monthly service maintenance charge',50.00,'recurring',0,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(7,'Reconnection Fee','RECONN_FEE','Fee for reconnecting disconnected service',300.00,'one_time',0,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(8,'Late Payment Penalty','LATE_PENALTY','Penalty for late payment',50.00,'one_time',0,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(9,'Meter Transfer Fee','METER_TRANSFER','Fee for transferring meter to new location',250.00,'one_time',0,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(10,'Meter Replacement Fee','METER_REPLACE','Fee for replacing damaged meter',500.00,'one_time',0,2,'2025-11-06 01:46:14','2025-11-06 01:46:14');
/*!40000 ALTER TABLE `ChargeItem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Consumer`
--

DROP TABLE IF EXISTS `Consumer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Consumer` (
  `c_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cust_id` bigint(20) unsigned DEFAULT NULL,
  `cm_id` bigint(20) unsigned DEFAULT NULL,
  `a_id` bigint(20) unsigned DEFAULT NULL,
  `stat_id` bigint(20) unsigned DEFAULT NULL,
  `chng_mtr_stat` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`c_id`),
  UNIQUE KEY `consumer_customer_meter_unique` (`cust_id`,`cm_id`),
  KEY `consumer_cm_id_foreign` (`cm_id`),
  KEY `consumer_a_id_foreign` (`a_id`),
  KEY `consumer_stat_id_foreign` (`stat_id`),
  CONSTRAINT `consumer_a_id_foreign` FOREIGN KEY (`a_id`) REFERENCES `area` (`a_id`),
  CONSTRAINT `consumer_cm_id_foreign` FOREIGN KEY (`cm_id`) REFERENCES `consumer_meters` (`cm_id`),
  CONSTRAINT `consumer_cust_id_foreign` FOREIGN KEY (`cust_id`) REFERENCES `customer` (`cust_id`),
  CONSTRAINT `consumer_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Consumer`
--

LOCK TABLES `Consumer` WRITE;
/*!40000 ALTER TABLE `Consumer` DISABLE KEYS */;
/*!40000 ALTER TABLE `Consumer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CustomerCharge`
--

DROP TABLE IF EXISTS `CustomerCharge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CustomerCharge` (
  `charge_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `application_id` bigint(20) unsigned DEFAULT NULL,
  `connection_id` bigint(20) unsigned DEFAULT NULL,
  `charge_item_id` bigint(20) unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `unit_amount` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) GENERATED ALWAYS AS ((`quantity` * `unit_amount`)) STORED,
  `due_date` date NOT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`charge_id`),
  KEY `customer_charge_customer_index` (`customer_id`),
  KEY `customer_charge_due_date_index` (`due_date`),
  KEY `customercharge_application_id_foreign` (`application_id`),
  KEY `customercharge_connection_id_foreign` (`connection_id`),
  KEY `customercharge_charge_item_id_foreign` (`charge_item_id`),
  KEY `customercharge_stat_id_foreign` (`stat_id`),
  CONSTRAINT `customercharge_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `ServiceApplication` (`application_id`) ON DELETE SET NULL,
  CONSTRAINT `customercharge_charge_item_id_foreign` FOREIGN KEY (`charge_item_id`) REFERENCES `ChargeItem` (`charge_item_id`),
  CONSTRAINT `customercharge_connection_id_foreign` FOREIGN KEY (`connection_id`) REFERENCES `ServiceConnection` (`connection_id`) ON DELETE SET NULL,
  CONSTRAINT `customercharge_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`cust_id`),
  CONSTRAINT `customercharge_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CustomerCharge`
--

LOCK TABLES `CustomerCharge` WRITE;
/*!40000 ALTER TABLE `CustomerCharge` DISABLE KEYS */;
/*!40000 ALTER TABLE `CustomerCharge` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CustomerLedger`
--

DROP TABLE IF EXISTS `CustomerLedger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CustomerLedger` (
  `ledger_entry_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `connection_id` bigint(20) unsigned DEFAULT NULL,
  `period_id` bigint(20) unsigned DEFAULT NULL,
  `txn_date` date NOT NULL,
  `post_ts` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `source_type` enum('BILL','CHARGE','ADJUST','PAYMENT','REFUND','WRITE_OFF','TRANSFER','REVERSAL') COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_id` bigint(20) unsigned NOT NULL,
  `source_line_no` int(11) DEFAULT NULL,
  `source_line_no_nz` int(11) GENERATED ALWAYS AS (ifnull(`source_line_no`,0)) STORED,
  `description` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `debit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ledger_entry_id`),
  KEY `customerledger_customer_id_index` (`customer_id`),
  KEY `customerledger_connection_id_index` (`connection_id`),
  KEY `customerledger_period_id_index` (`period_id`),
  KEY `customerledger_source_type_index` (`source_type`),
  KEY `customerledger_source_id_index` (`source_id`),
  KEY `customerledger_source_type_source_id_index` (`source_type`,`source_id`),
  KEY `customerledger_txn_date_index` (`txn_date`),
  KEY `customerledger_user_id_foreign` (`user_id`),
  KEY `customerledger_stat_id_foreign` (`stat_id`),
  CONSTRAINT `customerledger_connection_id_foreign` FOREIGN KEY (`connection_id`) REFERENCES `ServiceConnection` (`connection_id`) ON DELETE CASCADE,
  CONSTRAINT `customerledger_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`cust_id`) ON DELETE CASCADE,
  CONSTRAINT `customerledger_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `period` (`per_id`) ON DELETE SET NULL,
  CONSTRAINT `customerledger_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`),
  CONSTRAINT `customerledger_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CustomerLedger`
--

LOCK TABLES `CustomerLedger` WRITE;
/*!40000 ALTER TABLE `CustomerLedger` DISABLE KEYS */;
/*!40000 ALTER TABLE `CustomerLedger` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MeterAssignment`
--

DROP TABLE IF EXISTS `MeterAssignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MeterAssignment` (
  `assignment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection_id` bigint(20) unsigned NOT NULL,
  `meter_id` bigint(20) unsigned NOT NULL,
  `installed_at` date NOT NULL,
  `removed_at` date DEFAULT NULL,
  `install_read` decimal(12,3) NOT NULL DEFAULT '0.000',
  `removal_read` decimal(12,3) DEFAULT NULL,
  PRIMARY KEY (`assignment_id`),
  KEY `meterassignment_connection_id_foreign` (`connection_id`),
  KEY `meterassignment_meter_id_foreign` (`meter_id`),
  CONSTRAINT `meterassignment_connection_id_foreign` FOREIGN KEY (`connection_id`) REFERENCES `ServiceConnection` (`connection_id`) ON DELETE CASCADE,
  CONSTRAINT `meterassignment_meter_id_foreign` FOREIGN KEY (`meter_id`) REFERENCES `meter` (`mtr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MeterAssignment`
--

LOCK TABLES `MeterAssignment` WRITE;
/*!40000 ALTER TABLE `MeterAssignment` DISABLE KEYS */;
/*!40000 ALTER TABLE `MeterAssignment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MeterReading`
--

DROP TABLE IF EXISTS `MeterReading`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MeterReading` (
  `reading_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assignment_id` bigint(20) unsigned NOT NULL,
  `period_id` bigint(20) unsigned NOT NULL,
  `reading_date` date NOT NULL,
  `reading_value` decimal(10,3) NOT NULL,
  `is_estimated` tinyint(1) NOT NULL DEFAULT '0',
  `meter_reader_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`reading_id`),
  KEY `meter_reading_assignment_period_index` (`assignment_id`,`period_id`),
  KEY `meterreading_period_id_foreign` (`period_id`),
  KEY `meterreading_meter_reader_id_foreign` (`meter_reader_id`),
  CONSTRAINT `meterreading_assignment_id_foreign` FOREIGN KEY (`assignment_id`) REFERENCES `MeterAssignment` (`assignment_id`),
  CONSTRAINT `meterreading_meter_reader_id_foreign` FOREIGN KEY (`meter_reader_id`) REFERENCES `users` (`id`),
  CONSTRAINT `meterreading_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `period` (`per_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MeterReading`
--

LOCK TABLES `MeterReading` WRITE;
/*!40000 ALTER TABLE `MeterReading` DISABLE KEYS */;
/*!40000 ALTER TABLE `MeterReading` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Payment`
--

DROP TABLE IF EXISTS `Payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Payment` (
  `payment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `receipt_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payer_id` bigint(20) unsigned NOT NULL,
  `payment_date` date NOT NULL,
  `amount_received` decimal(10,2) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  UNIQUE KEY `payment_receipt_no_unique` (`receipt_no`),
  KEY `payment_receipt_no_index` (`receipt_no`),
  KEY `payment_date_index` (`payment_date`),
  KEY `payment_payer_id_foreign` (`payer_id`),
  KEY `payment_user_id_foreign` (`user_id`),
  KEY `payment_stat_id_foreign` (`stat_id`),
  CONSTRAINT `payment_payer_id_foreign` FOREIGN KEY (`payer_id`) REFERENCES `customer` (`cust_id`),
  CONSTRAINT `payment_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`),
  CONSTRAINT `payment_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Payment`
--

LOCK TABLES `Payment` WRITE;
/*!40000 ALTER TABLE `Payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `Payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PaymentAllocation`
--

DROP TABLE IF EXISTS `PaymentAllocation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PaymentAllocation` (
  `payment_allocation_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint(20) unsigned NOT NULL,
  `target_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_id` bigint(20) unsigned NOT NULL,
  `amount_applied` decimal(10,2) NOT NULL,
  `period_id` bigint(20) unsigned NOT NULL,
  `connection_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`payment_allocation_id`),
  KEY `payment_allocation_index` (`payment_id`,`target_type`,`target_id`),
  KEY `payment_allocation_period_index` (`period_id`),
  KEY `paymentallocation_connection_id_foreign` (`connection_id`),
  CONSTRAINT `paymentallocation_connection_id_foreign` FOREIGN KEY (`connection_id`) REFERENCES `ServiceConnection` (`connection_id`),
  CONSTRAINT `paymentallocation_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `Payment` (`payment_id`) ON DELETE CASCADE,
  CONSTRAINT `paymentallocation_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `period` (`per_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PaymentAllocation`
--

LOCK TABLES `PaymentAllocation` WRITE;
/*!40000 ALTER TABLE `PaymentAllocation` DISABLE KEYS */;
/*!40000 ALTER TABLE `PaymentAllocation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ServiceApplication`
--

DROP TABLE IF EXISTS `ServiceApplication`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ServiceApplication` (
  `application_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `address_id` bigint(20) unsigned NOT NULL,
  `application_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `submitted_at` datetime NOT NULL,
  `approved_at` datetime DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`application_id`),
  UNIQUE KEY `serviceapplication_application_number_unique` (`application_number`),
  KEY `service_application_number_index` (`application_number`),
  KEY `service_application_date_index` (`submitted_at`),
  KEY `serviceapplication_customer_id_foreign` (`customer_id`),
  KEY `serviceapplication_address_id_foreign` (`address_id`),
  KEY `serviceapplication_approved_by_foreign` (`approved_by`),
  KEY `serviceapplication_stat_id_foreign` (`stat_id`),
  CONSTRAINT `serviceapplication_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `consumer_address` (`ca_id`),
  CONSTRAINT `serviceapplication_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `serviceapplication_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`cust_id`),
  CONSTRAINT `serviceapplication_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ServiceApplication`
--

LOCK TABLES `ServiceApplication` WRITE;
/*!40000 ALTER TABLE `ServiceApplication` DISABLE KEYS */;
INSERT INTO `ServiceApplication` VALUES (1,2,2,'APP-2025-00001','2025-11-06 04:33:03',NULL,NULL,NULL,1,NULL,NULL),(2,3,3,'APP-2025-00002','2025-11-06 09:11:12',NULL,NULL,NULL,1,NULL,NULL);
/*!40000 ALTER TABLE `ServiceApplication` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ServiceConnection`
--

DROP TABLE IF EXISTS `ServiceConnection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ServiceConnection` (
  `connection_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `address_id` bigint(20) unsigned NOT NULL,
  `account_type_id` bigint(20) unsigned NOT NULL,
  `rate_id` bigint(20) unsigned NOT NULL,
  `started_at` date NOT NULL,
  `ended_at` date DEFAULT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`connection_id`),
  UNIQUE KEY `serviceconnection_account_no_unique` (`account_no`),
  KEY `serviceconnection_customer_id_foreign` (`customer_id`),
  KEY `serviceconnection_address_id_foreign` (`address_id`),
  KEY `serviceconnection_account_type_id_foreign` (`account_type_id`),
  KEY `serviceconnection_rate_id_foreign` (`rate_id`),
  KEY `serviceconnection_stat_id_foreign` (`stat_id`),
  CONSTRAINT `serviceconnection_account_type_id_foreign` FOREIGN KEY (`account_type_id`) REFERENCES `account_type` (`at_id`),
  CONSTRAINT `serviceconnection_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `consumer_address` (`ca_id`),
  CONSTRAINT `serviceconnection_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`cust_id`),
  CONSTRAINT `serviceconnection_rate_id_foreign` FOREIGN KEY (`rate_id`) REFERENCES `water_rates` (`wr_id`),
  CONSTRAINT `serviceconnection_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ServiceConnection`
--

LOCK TABLES `ServiceConnection` WRITE;
/*!40000 ALTER TABLE `ServiceConnection` DISABLE KEYS */;
/*!40000 ALTER TABLE `ServiceConnection` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `account_type`
--

DROP TABLE IF EXISTS `account_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account_type` (
  `at_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `at_desc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`at_id`),
  KEY `account_type_stat_id_foreign` (`stat_id`),
  CONSTRAINT `account_type_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_type`
--

LOCK TABLES `account_type` WRITE;
/*!40000 ALTER TABLE `account_type` DISABLE KEYS */;
INSERT INTO `account_type` VALUES (1,'Individual',2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(2,'Corporation',2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(3,'Partnership',2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(4,'Government',2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(5,'Non-Profit Organization',2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(6,'Cooperative',2,'2025-11-06 01:46:14','2025-11-06 01:46:14');
/*!40000 ALTER TABLE `account_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `area`
--

DROP TABLE IF EXISTS `area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `area` (
  `a_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `a_desc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`a_id`),
  KEY `area_stat_id_foreign` (`stat_id`),
  CONSTRAINT `area_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `area`
--

LOCK TABLES `area` WRITE;
/*!40000 ALTER TABLE `area` DISABLE KEYS */;
/*!40000 ALTER TABLE `area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `barangay`
--

DROP TABLE IF EXISTS `barangay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `barangay` (
  `b_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `b_desc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`b_id`),
  KEY `barangay_b_desc_index` (`b_desc`),
  KEY `barangay_stat_id_foreign` (`stat_id`),
  CONSTRAINT `barangay_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `barangay`
--

LOCK TABLES `barangay` WRITE;
/*!40000 ALTER TABLE `barangay` DISABLE KEYS */;
INSERT INTO `barangay` VALUES (1,'Aluna',2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(2,'Andales',2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(3,'Apas',2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(4,'Calacapan',2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(5,'Gimangpang',2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(6,'Jampason',2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(7,'Kamelon',2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(8,'Kanitoan',2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(9,'Oguis',2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(10,'Pagahan',2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(11,'Poblacion',2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(12,'Pontacon',2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(13,'San Pedro',2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(14,'Sinalac',2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(15,'Tawantawan',2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(16,'Tubigan',2,'2025-11-06 01:46:13','2025-11-06 01:46:13');
/*!40000 ALTER TABLE `barangay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consumer_address`
--

DROP TABLE IF EXISTS `consumer_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consumer_address` (
  `ca_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` bigint(20) unsigned NOT NULL,
  `b_id` bigint(20) unsigned NOT NULL,
  `t_id` bigint(20) unsigned NOT NULL,
  `prov_id` bigint(20) unsigned NOT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ca_id`),
  KEY `consumer_address_p_id_foreign` (`p_id`),
  KEY `consumer_address_b_id_foreign` (`b_id`),
  KEY `consumer_address_t_id_foreign` (`t_id`),
  KEY `consumer_address_prov_id_foreign` (`prov_id`),
  KEY `consumer_address_stat_id_foreign` (`stat_id`),
  CONSTRAINT `consumer_address_b_id_foreign` FOREIGN KEY (`b_id`) REFERENCES `barangay` (`b_id`),
  CONSTRAINT `consumer_address_p_id_foreign` FOREIGN KEY (`p_id`) REFERENCES `purok` (`p_id`),
  CONSTRAINT `consumer_address_prov_id_foreign` FOREIGN KEY (`prov_id`) REFERENCES `province` (`prov_id`),
  CONSTRAINT `consumer_address_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`),
  CONSTRAINT `consumer_address_t_id_foreign` FOREIGN KEY (`t_id`) REFERENCES `town` (`t_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consumer_address`
--

LOCK TABLES `consumer_address` WRITE;
/*!40000 ALTER TABLE `consumer_address` DISABLE KEYS */;
INSERT INTO `consumer_address` VALUES (2,269,16,1,1,2,NULL,NULL),(3,266,2,1,1,2,NULL,NULL);
/*!40000 ALTER TABLE `consumer_address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consumer_ledger`
--

DROP TABLE IF EXISTS `consumer_ledger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consumer_ledger` (
  `cl_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `c_id` bigint(20) unsigned NOT NULL,
  `cl_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `debit` decimal(10,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(10,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(10,2) NOT NULL,
  `create_date` datetime NOT NULL,
  `or_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`cl_id`),
  KEY `consumer_ledger_consumer_index` (`c_id`),
  KEY `consumer_ledger_number_index` (`cl_no`),
  KEY `consumer_ledger_date_index` (`create_date`),
  KEY `consumer_ledger_stat_id_foreign` (`stat_id`),
  KEY `consumer_ledger_user_id_foreign` (`user_id`),
  CONSTRAINT `consumer_ledger_c_id_foreign` FOREIGN KEY (`c_id`) REFERENCES `Consumer` (`c_id`),
  CONSTRAINT `consumer_ledger_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`),
  CONSTRAINT `consumer_ledger_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consumer_ledger`
--

LOCK TABLES `consumer_ledger` WRITE;
/*!40000 ALTER TABLE `consumer_ledger` DISABLE KEYS */;
/*!40000 ALTER TABLE `consumer_ledger` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consumer_meters`
--

DROP TABLE IF EXISTS `consumer_meters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consumer_meters` (
  `cm_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mr_id` bigint(20) unsigned NOT NULL,
  `create_date` datetime NOT NULL,
  `install_date` datetime NOT NULL,
  `initial_readout` decimal(10,2) NOT NULL,
  `last_reading` decimal(10,2) DEFAULT NULL,
  `pulled_out_at` datetime DEFAULT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`cm_id`),
  KEY `consumer_meter_create_date_index` (`create_date`),
  KEY `consumer_meter_install_date_index` (`install_date`),
  KEY `consumer_meters_mr_id_foreign` (`mr_id`),
  KEY `consumer_meters_stat_id_foreign` (`stat_id`),
  KEY `consumer_meters_user_id_foreign` (`user_id`),
  CONSTRAINT `consumer_meters_mr_id_foreign` FOREIGN KEY (`mr_id`) REFERENCES `meter_readers` (`mr_id`),
  CONSTRAINT `consumer_meters_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`),
  CONSTRAINT `consumer_meters_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consumer_meters`
--

LOCK TABLES `consumer_meters` WRITE;
/*!40000 ALTER TABLE `consumer_meters` DISABLE KEYS */;
/*!40000 ALTER TABLE `consumer_meters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer` (
  `cust_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `create_date` datetime NOT NULL,
  `cust_last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cust_first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cust_middle_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ca_id` bigint(20) unsigned NOT NULL,
  `land_mark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `c_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resolution_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`cust_id`),
  KEY `customer_name_index` (`cust_last_name`,`cust_first_name`),
  KEY `customer_ca_id_foreign` (`ca_id`),
  KEY `customer_stat_id_foreign` (`stat_id`),
  CONSTRAINT `customer_ca_id_foreign` FOREIGN KEY (`ca_id`) REFERENCES `consumer_address` (`ca_id`),
  CONSTRAINT `customer_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer`
--

LOCK TABLES `customer` WRITE;
/*!40000 ALTER TABLE `customer` DISABLE KEYS */;
INSERT INTO `customer` VALUES (2,'2025-11-06 04:33:03','DELA CRUZ','JUAN','SANTOS',2,'NEAR MIDWAY WHITE BEACH',1,'RESIDENTIAL','INITAO-JD-20251106043303',NULL,NULL),(3,'2025-11-06 09:11:12','AZUCENA','EHNAND','ALBURO',3,'NEAR MIDWAY WHITE BEACH',1,'RESIDENTIAL','INITAO-EA-20251106091112',NULL,NULL);
/*!40000 ALTER TABLE `customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meter`
--

DROP TABLE IF EXISTS `meter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meter` (
  `mtr_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mtr_serial` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mtr_brand` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`mtr_id`),
  UNIQUE KEY `meter_mtr_serial_unique` (`mtr_serial`),
  KEY `meter_stat_id_foreign` (`stat_id`),
  CONSTRAINT `meter_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meter`
--

LOCK TABLES `meter` WRITE;
/*!40000 ALTER TABLE `meter` DISABLE KEYS */;
/*!40000 ALTER TABLE `meter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meter_readers`
--

DROP TABLE IF EXISTS `meter_readers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meter_readers` (
  `mr_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mr_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`mr_id`),
  KEY `meter_reader_name_index` (`mr_name`),
  KEY `meter_readers_stat_id_foreign` (`stat_id`),
  CONSTRAINT `meter_readers_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meter_readers`
--

LOCK TABLES `meter_readers` WRITE;
/*!40000 ALTER TABLE `meter_readers` DISABLE KEYS */;
/*!40000 ALTER TABLE `meter_readers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meter_reading_old`
--

DROP TABLE IF EXISTS `meter_reading_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meter_reading_old` (
  `mro_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection_id` bigint(20) unsigned NOT NULL,
  `meter_id` bigint(20) unsigned NOT NULL,
  `reading` decimal(10,2) NOT NULL,
  `reading_date` date NOT NULL,
  `reader_id` bigint(20) unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `stat_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`mro_id`),
  KEY `meter_reading_old_connection_index` (`connection_id`),
  KEY `meter_reading_old_date_index` (`reading_date`),
  KEY `meter_reading_old_meter_index` (`meter_id`),
  KEY `meter_reading_old_reader_id_foreign` (`reader_id`),
  KEY `meter_reading_old_stat_id_foreign` (`stat_id`),
  KEY `meter_reading_old_user_id_foreign` (`user_id`),
  CONSTRAINT `meter_reading_old_connection_id_foreign` FOREIGN KEY (`connection_id`) REFERENCES `ServiceConnection` (`connection_id`),
  CONSTRAINT `meter_reading_old_meter_id_foreign` FOREIGN KEY (`meter_id`) REFERENCES `meter` (`mtr_id`),
  CONSTRAINT `meter_reading_old_reader_id_foreign` FOREIGN KEY (`reader_id`) REFERENCES `meter_readers` (`mr_id`),
  CONSTRAINT `meter_reading_old_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`),
  CONSTRAINT `meter_reading_old_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meter_reading_old`
--

LOCK TABLES `meter_reading_old` WRITE;
/*!40000 ALTER TABLE `meter_reading_old` DISABLE KEYS */;
/*!40000 ALTER TABLE `meter_reading_old` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'0001_statuses_table',1),(5,'0002_user_types_table',1),(6,'0003_roles_table',1),(7,'0004_user_roles_table',1),(8,'0005_permissions_table',1),(9,'0006_role_permissions_table',1),(10,'0007_account_types_table',1),(11,'0008_areas_table',1),(12,'0009_area_assignments_table',1),(13,'0010_barangays_table',1),(14,'0011_bill_adjustment_types_table',1),(15,'0013_provinces_table',1),(16,'0014_towns_table',1),(17,'0015_periods_table',1),(18,'0015_water_rates_table',1),(19,'0016_puroks_table',1),(20,'0017_consumer_addresses_table',1),(21,'0018_customers_table',1),(22,'0019_meter_readers_table',1),(23,'0019_service_connections_table',1),(24,'0020_meters_table',1),(25,'0021_consumer_meters_table',1),(26,'0021_meter_assignments_table',1),(27,'0022_consumer_ledgers_table',1),(28,'0023_meter_readings_table',1),(29,'0024_water_bills_table',1),(30,'0025_payments_table',1),(31,'0026_consumers_table',1),(32,'0027_customer_ledger_table',1),(33,'0028_water_bill_history_table',1),(34,'0029_bill_adjustments_table',1),(35,'0030_payment_allocations_table',1),(36,'0032_water_bill_adjustments_table',1),(37,'0034_create_charge_items_table',1),(38,'0035_service_applications_table',1),(39,'0036_misc_references_table',1),(40,'0037_customer_charges_table',1),(41,'0037_meter_reading_olds_table',1),(42,'0038_misc_bills_table',1),(43,'0041_payment_transactions_table',1),(44,'0043_reading_schedules_table',1),(45,'2025_10_26_075218_update_users_table',1),(46,'2025_10_26_160541_update_water_bill_table',1),(47,'2025_10_26_160741_add_water_bill_columns',1),(48,'2025_10_27_000000_add_foreign_keys_to_consumer_ledger_table',1),(49,'2025_10_27_000001_add_foreign_keys_to_consumer_table',1),(50,'2025_10_27_000002_add_foreign_keys_to_consumer_meters_table',1),(51,'2025_10_27_000003_add_foreign_keys_to_customer_table',1),(52,'2025_10_27_000004_add_foreign_keys_to_consumer_address_table',1),(53,'2025_10_27_000005_add_foreign_keys_to_purok_table',1),(54,'2025_10_27_000006_add_foreign_keys_to_town_table',1),(55,'2025_10_27_000007_add_foreign_keys_to_province_table',1),(56,'2025_10_27_000008_add_foreign_keys_to_area_table',1),(57,'2025_10_27_000009_add_foreign_keys_to_meter_readers_table',1),(58,'2025_10_27_000010_add_foreign_keys_to_water_bill_history_table',1),(59,'2025_10_27_000011_add_foreign_keys_to_customer_ledger_table',1),(60,'2025_10_27_000012_add_foreign_keys_to_water_bill_table',1),(61,'2025_10_27_000013_add_foreign_keys_to_meter_reading_table',1),(62,'2025_10_27_000014_add_foreign_keys_to_meter_assignment_table',1),(63,'2025_10_27_000015_add_foreign_keys_to_meter_table',1),(64,'2025_10_27_000016_add_foreign_keys_to_service_connection_table',1),(65,'2025_10_27_000017_add_foreign_keys_to_account_type_table',1),(66,'2025_10_27_000018_add_foreign_keys_to_water_rates_table',1),(67,'2025_10_27_000019_add_foreign_keys_to_area_assignment_table',1),(68,'2025_10_27_000020_add_foreign_keys_to_barangay_table',1),(69,'2025_10_27_000021_add_foreign_keys_to_bill_adjustment_table',1),(70,'2025_10_27_000022_add_foreign_keys_to_bill_adjustment_type_table',1),(71,'2025_10_27_000023_add_foreign_keys_to_payment_table',1),(72,'2025_10_27_000024_add_foreign_keys_to_payment_allocation_table',1),(73,'2025_10_27_000025_add_foreign_keys_to_water_bill_adjustment_table',1),(74,'2025_10_27_000026_add_foreign_keys_to_charge_item_table',1),(75,'2025_10_27_000027_add_foreign_keys_to_service_application_table',1),(76,'2025_10_27_000028_add_foreign_keys_to_misc_reference_table',1),(77,'2025_10_27_000029_add_foreign_keys_to_customer_charge_table',1),(78,'2025_10_27_000030_add_foreign_keys_to_meter_reading_old_table',1),(79,'2025_10_27_000031_add_foreign_keys_to_misc_bill_table',1),(80,'2025_10_27_000032_add_foreign_keys_to_payment_transactions_table',1),(81,'2025_10_27_000033_add_foreign_keys_to_reading_schedule_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `misc_bill`
--

DROP TABLE IF EXISTS `misc_bill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `misc_bill` (
  `mb_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection_id` bigint(20) unsigned NOT NULL,
  `misc_reference_id` bigint(20) unsigned NOT NULL,
  `bill_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `billing_date` date NOT NULL,
  `due_date` date NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `paid_date` date DEFAULT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`mb_id`),
  UNIQUE KEY `misc_bill_bill_number_unique` (`bill_number`),
  KEY `misc_bill_connection_index` (`connection_id`),
  KEY `misc_bill_number_index` (`bill_number`),
  KEY `misc_bill_billing_date_index` (`billing_date`),
  KEY `misc_bill_paid_index` (`is_paid`),
  KEY `misc_bill_misc_reference_id_foreign` (`misc_reference_id`),
  KEY `misc_bill_stat_id_foreign` (`stat_id`),
  KEY `misc_bill_created_by_foreign` (`created_by`),
  CONSTRAINT `misc_bill_connection_id_foreign` FOREIGN KEY (`connection_id`) REFERENCES `ServiceConnection` (`connection_id`),
  CONSTRAINT `misc_bill_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `misc_bill_misc_reference_id_foreign` FOREIGN KEY (`misc_reference_id`) REFERENCES `misc_reference` (`misc_reference_id`),
  CONSTRAINT `misc_bill_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `misc_bill`
--

LOCK TABLES `misc_bill` WRITE;
/*!40000 ALTER TABLE `misc_bill` DISABLE KEYS */;
/*!40000 ALTER TABLE `misc_bill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `misc_reference`
--

DROP TABLE IF EXISTS `misc_reference`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `misc_reference` (
  `misc_reference_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_amount` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`misc_reference_id`),
  UNIQUE KEY `misc_reference_reference_code_unique` (`reference_code`),
  KEY `misc_reference_type_index` (`reference_type`),
  KEY `misc_reference_code_index` (`reference_code`),
  KEY `misc_reference_active_index` (`is_active`),
  KEY `misc_reference_stat_id_foreign` (`stat_id`),
  KEY `misc_reference_created_by_foreign` (`created_by`),
  CONSTRAINT `misc_reference_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `misc_reference_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `misc_reference`
--

LOCK TABLES `misc_reference` WRITE;
/*!40000 ALTER TABLE `misc_reference` DISABLE KEYS */;
INSERT INTO `misc_reference` VALUES (1,'penalty','LATE_PAYMENT','Late payment penalty',100.00,1,2,1,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(2,'discount','SENIOR_CITIZEN','Senior citizen discount',100.00,1,2,1,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(3,'surcharge','DAMAGED_METER','Damaged meter surcharge',500.00,1,2,1,'2025-11-06 01:46:14','2025-11-06 01:46:14');
/*!40000 ALTER TABLE `misc_reference` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_transactions`
--

DROP TABLE IF EXISTS `payment_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_transactions` (
  `transaction_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint(20) unsigned NOT NULL,
  `bill_id` bigint(20) unsigned DEFAULT NULL,
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `applied_to_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `applied_to_id` bigint(20) unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `processed_by` bigint(20) unsigned NOT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`transaction_id`),
  KEY `payment_transaction_payment_index` (`payment_id`),
  KEY `payment_transaction_bill_index` (`bill_id`),
  KEY `payment_transaction_reference_index` (`reference_number`),
  KEY `payment_transaction_applied_to_index` (`applied_to_type`,`applied_to_id`),
  KEY `payment_transactions_processed_by_foreign` (`processed_by`),
  KEY `payment_transactions_stat_id_foreign` (`stat_id`),
  CONSTRAINT `payment_transactions_bill_id_foreign` FOREIGN KEY (`bill_id`) REFERENCES `water_bill` (`wb_id`) ON DELETE SET NULL,
  CONSTRAINT `payment_transactions_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `Payment` (`payment_id`) ON DELETE CASCADE,
  CONSTRAINT `payment_transactions_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `payment_transactions_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_transactions`
--

LOCK TABLES `payment_transactions` WRITE;
/*!40000 ALTER TABLE `payment_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `period`
--

DROP TABLE IF EXISTS `period`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `period` (
  `per_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `per_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `per_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT '0',
  `closed_at` datetime DEFAULT NULL,
  `closed_by` bigint(20) unsigned DEFAULT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`per_id`),
  UNIQUE KEY `period_date_range_unique` (`start_date`,`end_date`),
  UNIQUE KEY `period_per_code_unique` (`per_code`),
  KEY `period_closed_by_foreign` (`closed_by`),
  KEY `period_stat_id_foreign` (`stat_id`),
  KEY `period_code_index` (`per_code`),
  KEY `period_start_date_index` (`start_date`),
  KEY `period_closed_index` (`is_closed`),
  CONSTRAINT `period_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `period_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `period`
--

LOCK TABLES `period` WRITE;
/*!40000 ALTER TABLE `period` DISABLE KEYS */;
INSERT INTO `period` VALUES (1,'November 2025','202511','2025-11-01','2025-11-30',0,NULL,NULL,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(2,'December 2025','202512','2025-12-01','2025-12-31',0,NULL,NULL,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(3,'January 2026','202601','2026-01-01','2026-01-31',0,NULL,NULL,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(4,'February 2026','202602','2026-02-01','2026-02-28',0,NULL,NULL,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(5,'March 2026','202603','2026-03-01','2026-03-31',0,NULL,NULL,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(6,'April 2026','202604','2026-04-01','2026-04-30',0,NULL,NULL,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(7,'May 2026','202605','2026-05-01','2026-05-31',0,NULL,NULL,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(8,'June 2026','202606','2026-06-01','2026-06-30',0,NULL,NULL,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(9,'July 2026','202607','2026-07-01','2026-07-31',0,NULL,NULL,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(10,'August 2026','202608','2026-08-01','2026-08-31',0,NULL,NULL,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(11,'September 2026','202609','2026-09-01','2026-09-30',0,NULL,NULL,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(12,'October 2026','202610','2026-10-01','2026-10-31',0,NULL,NULL,2,'2025-11-06 01:46:14','2025-11-06 01:46:14');
/*!40000 ALTER TABLE `period` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `permission_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `province`
--

DROP TABLE IF EXISTS `province`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `province` (
  `prov_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `prov_desc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`prov_id`),
  KEY `province_desc_index` (`prov_desc`),
  KEY `province_stat_id_foreign` (`stat_id`),
  CONSTRAINT `province_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `province`
--

LOCK TABLES `province` WRITE;
/*!40000 ALTER TABLE `province` DISABLE KEYS */;
INSERT INTO `province` VALUES (1,'Misamis Oriental',2,'2025-11-06 01:46:13','2025-11-06 01:46:13');
/*!40000 ALTER TABLE `province` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purok`
--

DROP TABLE IF EXISTS `purok`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purok` (
  `p_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `p_desc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `b_id` bigint(20) unsigned NOT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`p_id`),
  KEY `purok_desc_index` (`p_desc`),
  KEY `purok_b_id_foreign` (`b_id`),
  KEY `purok_stat_id_foreign` (`stat_id`),
  CONSTRAINT `purok_b_id_foreign` FOREIGN KEY (`b_id`) REFERENCES `barangay` (`b_id`),
  CONSTRAINT `purok_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=385 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purok`
--

LOCK TABLES `purok` WRITE;
/*!40000 ALTER TABLE `purok` DISABLE KEYS */;
INSERT INTO `purok` VALUES (1,'Purok 1-A',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(2,'Purok 1-B',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(3,'Purok 2-A',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(4,'Purok 2-B',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(5,'Purok 3-A',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(6,'Purok 3-B',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(7,'Purok 4-A',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(8,'Purok 4-B',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(9,'Purok 5-A',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(10,'Purok 5-B',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(11,'Purok 6-A',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(12,'Purok 6-B',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(13,'Purok 7-A',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(14,'Purok 7-B',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(15,'Purok 8-A',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(16,'Purok 8-B',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(17,'Purok 9-A',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(18,'Purok 9-B',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(19,'Purok 10-A',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(20,'Purok 10-B',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(21,'Purok 11-A',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(22,'Purok 11-B',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(23,'Purok 12-A',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(24,'Purok 12-B',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(25,'Purok 1-A',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(26,'Purok 1-B',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(27,'Purok 2-A',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(28,'Purok 2-B',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(29,'Purok 3-A',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(30,'Purok 3-B',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(31,'Purok 4-A',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(32,'Purok 4-B',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(33,'Purok 5-A',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(34,'Purok 5-B',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(35,'Purok 6-A',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(36,'Purok 6-B',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(37,'Purok 7-A',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(38,'Purok 7-B',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(39,'Purok 8-A',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(40,'Purok 8-B',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(41,'Purok 9-A',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(42,'Purok 9-B',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(43,'Purok 10-A',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(44,'Purok 10-B',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(45,'Purok 11-A',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(46,'Purok 11-B',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(47,'Purok 12-A',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(48,'Purok 12-B',2,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(49,'Purok 1-A',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(50,'Purok 1-B',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(51,'Purok 2-A',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(52,'Purok 2-B',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(53,'Purok 3-A',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(54,'Purok 3-B',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(55,'Purok 4-A',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(56,'Purok 4-B',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(57,'Purok 5-A',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(58,'Purok 5-B',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(59,'Purok 6-A',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(60,'Purok 6-B',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(61,'Purok 7-A',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(62,'Purok 7-B',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(63,'Purok 8-A',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(64,'Purok 8-B',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(65,'Purok 9-A',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(66,'Purok 9-B',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(67,'Purok 10-A',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(68,'Purok 10-B',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(69,'Purok 11-A',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(70,'Purok 11-B',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(71,'Purok 12-A',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(72,'Purok 12-B',3,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(73,'Purok 1-A',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(74,'Purok 1-B',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(75,'Purok 2-A',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(76,'Purok 2-B',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(77,'Purok 3-A',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(78,'Purok 3-B',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(79,'Purok 4-A',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(80,'Purok 4-B',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(81,'Purok 5-A',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(82,'Purok 5-B',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(83,'Purok 6-A',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(84,'Purok 6-B',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(85,'Purok 7-A',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(86,'Purok 7-B',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(87,'Purok 8-A',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(88,'Purok 8-B',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(89,'Purok 9-A',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(90,'Purok 9-B',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(91,'Purok 10-A',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(92,'Purok 10-B',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(93,'Purok 11-A',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(94,'Purok 11-B',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(95,'Purok 12-A',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(96,'Purok 12-B',4,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(97,'Purok 1-A',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(98,'Purok 1-B',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(99,'Purok 2-A',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(100,'Purok 2-B',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(101,'Purok 3-A',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(102,'Purok 3-B',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(103,'Purok 4-A',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(104,'Purok 4-B',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(105,'Purok 5-A',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(106,'Purok 5-B',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(107,'Purok 6-A',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(108,'Purok 6-B',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(109,'Purok 7-A',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(110,'Purok 7-B',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(111,'Purok 8-A',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(112,'Purok 8-B',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(113,'Purok 9-A',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(114,'Purok 9-B',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(115,'Purok 10-A',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(116,'Purok 10-B',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(117,'Purok 11-A',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(118,'Purok 11-B',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(119,'Purok 12-A',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(120,'Purok 12-B',5,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(121,'Purok 1-A',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(122,'Purok 1-B',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(123,'Purok 2-A',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(124,'Purok 2-B',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(125,'Purok 3-A',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(126,'Purok 3-B',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(127,'Purok 4-A',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(128,'Purok 4-B',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(129,'Purok 5-A',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(130,'Purok 5-B',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(131,'Purok 6-A',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(132,'Purok 6-B',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(133,'Purok 7-A',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(134,'Purok 7-B',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(135,'Purok 8-A',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(136,'Purok 8-B',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(137,'Purok 9-A',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(138,'Purok 9-B',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(139,'Purok 10-A',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(140,'Purok 10-B',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(141,'Purok 11-A',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(142,'Purok 11-B',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(143,'Purok 12-A',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(144,'Purok 12-B',6,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(145,'Purok 1-A',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(146,'Purok 1-B',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(147,'Purok 2-A',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(148,'Purok 2-B',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(149,'Purok 3-A',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(150,'Purok 3-B',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(151,'Purok 4-A',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(152,'Purok 4-B',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(153,'Purok 5-A',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(154,'Purok 5-B',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(155,'Purok 6-A',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(156,'Purok 6-B',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(157,'Purok 7-A',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(158,'Purok 7-B',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(159,'Purok 8-A',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(160,'Purok 8-B',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(161,'Purok 9-A',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(162,'Purok 9-B',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(163,'Purok 10-A',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(164,'Purok 10-B',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(165,'Purok 11-A',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(166,'Purok 11-B',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(167,'Purok 12-A',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(168,'Purok 12-B',7,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(169,'Purok 1-A',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(170,'Purok 1-B',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(171,'Purok 2-A',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(172,'Purok 2-B',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(173,'Purok 3-A',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(174,'Purok 3-B',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(175,'Purok 4-A',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(176,'Purok 4-B',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(177,'Purok 5-A',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(178,'Purok 5-B',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(179,'Purok 6-A',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(180,'Purok 6-B',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(181,'Purok 7-A',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(182,'Purok 7-B',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(183,'Purok 8-A',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(184,'Purok 8-B',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(185,'Purok 9-A',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(186,'Purok 9-B',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(187,'Purok 10-A',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(188,'Purok 10-B',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(189,'Purok 11-A',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(190,'Purok 11-B',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(191,'Purok 12-A',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(192,'Purok 12-B',8,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(193,'Purok 1-A',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(194,'Purok 1-B',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(195,'Purok 2-A',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(196,'Purok 2-B',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(197,'Purok 3-A',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(198,'Purok 3-B',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(199,'Purok 4-A',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(200,'Purok 4-B',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(201,'Purok 5-A',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(202,'Purok 5-B',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(203,'Purok 6-A',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(204,'Purok 6-B',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(205,'Purok 7-A',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(206,'Purok 7-B',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(207,'Purok 8-A',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(208,'Purok 8-B',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(209,'Purok 9-A',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(210,'Purok 9-B',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(211,'Purok 10-A',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(212,'Purok 10-B',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(213,'Purok 11-A',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(214,'Purok 11-B',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(215,'Purok 12-A',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(216,'Purok 12-B',9,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(217,'Purok 1-A',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(218,'Purok 1-B',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(219,'Purok 2-A',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(220,'Purok 2-B',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(221,'Purok 3-A',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(222,'Purok 3-B',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(223,'Purok 4-A',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(224,'Purok 4-B',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(225,'Purok 5-A',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(226,'Purok 5-B',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(227,'Purok 6-A',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(228,'Purok 6-B',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(229,'Purok 7-A',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(230,'Purok 7-B',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(231,'Purok 8-A',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(232,'Purok 8-B',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(233,'Purok 9-A',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(234,'Purok 9-B',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(235,'Purok 10-A',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(236,'Purok 10-B',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(237,'Purok 11-A',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(238,'Purok 11-B',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(239,'Purok 12-A',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(240,'Purok 12-B',10,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(241,'Purok 1-A',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(242,'Purok 1-B',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(243,'Purok 2-A',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(244,'Purok 2-B',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(245,'Purok 3-A',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(246,'Purok 3-B',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(247,'Purok 4-A',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(248,'Purok 4-B',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(249,'Purok 5-A',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(250,'Purok 5-B',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(251,'Purok 6-A',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(252,'Purok 6-B',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(253,'Purok 7-A',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(254,'Purok 7-B',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(255,'Purok 8-A',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(256,'Purok 8-B',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(257,'Purok 9-A',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(258,'Purok 9-B',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(259,'Purok 10-A',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(260,'Purok 10-B',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(261,'Purok 11-A',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(262,'Purok 11-B',11,2,'2025-11-06 01:46:13','2025-11-06 01:46:13'),(263,'Purok 12-A',11,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(264,'Purok 12-B',11,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(265,'Purok 1-A',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(266,'Purok 1-B',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(267,'Purok 2-A',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(268,'Purok 2-B',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(269,'Purok 3-A',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(270,'Purok 3-B',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(271,'Purok 4-A',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(272,'Purok 4-B',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(273,'Purok 5-A',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(274,'Purok 5-B',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(275,'Purok 6-A',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(276,'Purok 6-B',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(277,'Purok 7-A',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(278,'Purok 7-B',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(279,'Purok 8-A',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(280,'Purok 8-B',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(281,'Purok 9-A',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(282,'Purok 9-B',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(283,'Purok 10-A',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(284,'Purok 10-B',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(285,'Purok 11-A',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(286,'Purok 11-B',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(287,'Purok 12-A',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(288,'Purok 12-B',12,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(289,'Purok 1-A',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(290,'Purok 1-B',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(291,'Purok 2-A',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(292,'Purok 2-B',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(293,'Purok 3-A',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(294,'Purok 3-B',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(295,'Purok 4-A',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(296,'Purok 4-B',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(297,'Purok 5-A',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(298,'Purok 5-B',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(299,'Purok 6-A',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(300,'Purok 6-B',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(301,'Purok 7-A',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(302,'Purok 7-B',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(303,'Purok 8-A',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(304,'Purok 8-B',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(305,'Purok 9-A',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(306,'Purok 9-B',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(307,'Purok 10-A',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(308,'Purok 10-B',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(309,'Purok 11-A',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(310,'Purok 11-B',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(311,'Purok 12-A',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(312,'Purok 12-B',13,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(313,'Purok 1-A',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(314,'Purok 1-B',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(315,'Purok 2-A',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(316,'Purok 2-B',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(317,'Purok 3-A',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(318,'Purok 3-B',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(319,'Purok 4-A',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(320,'Purok 4-B',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(321,'Purok 5-A',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(322,'Purok 5-B',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(323,'Purok 6-A',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(324,'Purok 6-B',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(325,'Purok 7-A',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(326,'Purok 7-B',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(327,'Purok 8-A',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(328,'Purok 8-B',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(329,'Purok 9-A',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(330,'Purok 9-B',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(331,'Purok 10-A',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(332,'Purok 10-B',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(333,'Purok 11-A',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(334,'Purok 11-B',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(335,'Purok 12-A',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(336,'Purok 12-B',14,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(337,'Purok 1-A',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(338,'Purok 1-B',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(339,'Purok 2-A',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(340,'Purok 2-B',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(341,'Purok 3-A',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(342,'Purok 3-B',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(343,'Purok 4-A',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(344,'Purok 4-B',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(345,'Purok 5-A',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(346,'Purok 5-B',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(347,'Purok 6-A',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(348,'Purok 6-B',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(349,'Purok 7-A',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(350,'Purok 7-B',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(351,'Purok 8-A',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(352,'Purok 8-B',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(353,'Purok 9-A',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(354,'Purok 9-B',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(355,'Purok 10-A',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(356,'Purok 10-B',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(357,'Purok 11-A',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(358,'Purok 11-B',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(359,'Purok 12-A',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(360,'Purok 12-B',15,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(361,'Purok 1-A',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(362,'Purok 1-B',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(363,'Purok 2-A',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(364,'Purok 2-B',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(365,'Purok 3-A',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(366,'Purok 3-B',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(367,'Purok 4-A',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(368,'Purok 4-B',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(369,'Purok 5-A',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(370,'Purok 5-B',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(371,'Purok 6-A',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(372,'Purok 6-B',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(373,'Purok 7-A',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(374,'Purok 7-B',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(375,'Purok 8-A',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(376,'Purok 8-B',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(377,'Purok 9-A',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(378,'Purok 9-B',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(379,'Purok 10-A',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(380,'Purok 10-B',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(381,'Purok 11-A',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(382,'Purok 11-B',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(383,'Purok 12-A',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(384,'Purok 12-B',16,2,'2025-11-06 01:46:14','2025-11-06 01:46:14');
/*!40000 ALTER TABLE `purok` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reading_schedule`
--

DROP TABLE IF EXISTS `reading_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reading_schedule` (
  `schedule_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `period_id` bigint(20) unsigned NOT NULL,
  `area_id` bigint(20) unsigned NOT NULL,
  `reader_id` bigint(20) unsigned NOT NULL,
  `scheduled_start_date` date NOT NULL,
  `scheduled_end_date` date NOT NULL,
  `actual_start_date` date DEFAULT NULL,
  `actual_end_date` date DEFAULT NULL,
  `status` enum('pending','in_progress','completed','delayed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `total_meters` int(11) NOT NULL DEFAULT '0',
  `meters_read` int(11) NOT NULL DEFAULT '0',
  `meters_missed` int(11) NOT NULL DEFAULT '0',
  `created_by` bigint(20) unsigned NOT NULL,
  `completed_by` bigint(20) unsigned DEFAULT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`schedule_id`),
  KEY `reading_schedule_period_index` (`period_id`),
  KEY `reading_schedule_area_index` (`area_id`),
  KEY `reading_schedule_reader_index` (`reader_id`),
  KEY `reading_schedule_status_index` (`status`),
  KEY `reading_schedule_start_date_index` (`scheduled_start_date`),
  KEY `reading_schedule_created_by_foreign` (`created_by`),
  KEY `reading_schedule_completed_by_foreign` (`completed_by`),
  KEY `reading_schedule_stat_id_foreign` (`stat_id`),
  CONSTRAINT `reading_schedule_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `area` (`a_id`),
  CONSTRAINT `reading_schedule_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reading_schedule_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `reading_schedule_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `period` (`per_id`),
  CONSTRAINT `reading_schedule_reader_id_foreign` FOREIGN KEY (`reader_id`) REFERENCES `meter_readers` (`mr_id`),
  CONSTRAINT `reading_schedule_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reading_schedule`
--

LOCK TABLES `reading_schedule` WRITE;
/*!40000 ALTER TABLE `reading_schedule` DISABLE KEYS */;
/*!40000 ALTER TABLE `reading_schedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permissions` (
  `role_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `role_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statuses`
--

DROP TABLE IF EXISTS `statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statuses` (
  `stat_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stat_desc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`stat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statuses`
--

LOCK TABLES `statuses` WRITE;
/*!40000 ALTER TABLE `statuses` DISABLE KEYS */;
INSERT INTO `statuses` VALUES (1,'PENDING'),(2,'ACTIVE'),(3,'INACTIVE');
/*!40000 ALTER TABLE `statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `town`
--

DROP TABLE IF EXISTS `town`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `town` (
  `t_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `t_desc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prov_id` bigint(20) unsigned NOT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`t_id`),
  KEY `town_desc_index` (`t_desc`),
  KEY `town_prov_id_foreign` (`prov_id`),
  KEY `town_stat_id_foreign` (`stat_id`),
  CONSTRAINT `town_prov_id_foreign` FOREIGN KEY (`prov_id`) REFERENCES `province` (`prov_id`),
  CONSTRAINT `town_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `town`
--

LOCK TABLES `town` WRITE;
/*!40000 ALTER TABLE `town` DISABLE KEYS */;
INSERT INTO `town` VALUES (1,'Initao',1,2,'2025-11-06 01:46:13','2025-11-06 01:46:13');
/*!40000 ALTER TABLE `town` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_roles` (
  `user_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `user_roles_role_id_foreign` (`role_id`),
  CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_types`
--

DROP TABLE IF EXISTS `user_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_types` (
  `ut_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ut_desc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ut_id`),
  KEY `user_types_stat_id_foreign` (`stat_id`),
  CONSTRAINT `user_types_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_types`
--

LOCK TABLES `user_types` WRITE;
/*!40000 ALTER TABLE `user_types` DISABLE KEYS */;
INSERT INTO `user_types` VALUES (3,'ADMIN',2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(4,'BILLING',2,'2025-11-06 01:46:14','2025-11-06 01:46:14');
/*!40000 ALTER TABLE `user_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `u_type` bigint(20) unsigned NOT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_u_type_foreign` (`u_type`),
  KEY `users_stat_id_foreign` (`stat_id`),
  CONSTRAINT `users_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`),
  CONSTRAINT `users_u_type_foreign` FOREIGN KEY (`u_type`) REFERENCES `user_types` (`ut_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','Admin User','admin@initao-water.gov.ph','2025-11-06 01:46:14','$2y$12$XTMPclpzmFbkCrPGSQdf1OYBc0HWjMN49GK50I0aXlDdLN6ghq4ga',3,1,'y73qCCTlG2','2025-11-06 01:46:14','2025-11-06 01:46:14');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `water_bill`
--

DROP TABLE IF EXISTS `water_bill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `water_bill` (
  `wb_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bill_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `connection_id` bigint(20) unsigned DEFAULT NULL,
  `cl_id` bigint(20) unsigned NOT NULL,
  `per_id` bigint(20) unsigned NOT NULL,
  `reading_id` bigint(20) unsigned DEFAULT NULL,
  `previous_reading` decimal(10,2) NOT NULL DEFAULT '0.00',
  `current_reading` decimal(10,2) NOT NULL DEFAULT '0.00',
  `consumption` decimal(10,2) NOT NULL DEFAULT '0.00',
  `rate_id` bigint(20) unsigned DEFAULT NULL,
  `rate_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `basic_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `surcharge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `penalty` decimal(10,2) NOT NULL DEFAULT '0.00',
  `misc_charges` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `billing_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `payment_status` enum('unpaid','partially_paid','paid','overdue','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `paid_date` date DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `create_date` datetime NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `cancelled_by` bigint(20) unsigned DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`wb_id`),
  UNIQUE KEY `water_bill_bill_number_unique` (`bill_number`),
  KEY `water_bill_consumer_period_index` (`cl_id`,`per_id`),
  KEY `water_bill_reading_id_foreign` (`reading_id`),
  KEY `water_bill_cancelled_by_foreign` (`cancelled_by`),
  KEY `water_bill_bill_number_index` (`bill_number`),
  KEY `water_bill_connection_index` (`connection_id`),
  KEY `water_bill_period_index` (`per_id`),
  KEY `water_bill_payment_status_index` (`payment_status`),
  KEY `water_bill_stat_id_foreign` (`stat_id`),
  CONSTRAINT `water_bill_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `water_bill_cl_id_foreign` FOREIGN KEY (`cl_id`) REFERENCES `consumer_ledger` (`cl_id`),
  CONSTRAINT `water_bill_per_id_foreign` FOREIGN KEY (`per_id`) REFERENCES `period` (`per_id`),
  CONSTRAINT `water_bill_reading_id_foreign` FOREIGN KEY (`reading_id`) REFERENCES `MeterReading` (`reading_id`),
  CONSTRAINT `water_bill_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `water_bill`
--

LOCK TABLES `water_bill` WRITE;
/*!40000 ALTER TABLE `water_bill` DISABLE KEYS */;
/*!40000 ALTER TABLE `water_bill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `water_bill_adjustment`
--

DROP TABLE IF EXISTS `water_bill_adjustment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `water_bill_adjustment` (
  `wba_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `wb_id` bigint(20) unsigned NOT NULL,
  `adj_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`wba_id`),
  KEY `water_bill_adjustment_bill_index` (`wb_id`),
  KEY `water_bill_adjustment_type_index` (`adj_type`),
  KEY `water_bill_adjustment_approved_by_foreign` (`approved_by`),
  CONSTRAINT `water_bill_adjustment_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `water_bill_adjustment_wb_id_foreign` FOREIGN KEY (`wb_id`) REFERENCES `water_bill` (`wb_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `water_bill_adjustment`
--

LOCK TABLES `water_bill_adjustment` WRITE;
/*!40000 ALTER TABLE `water_bill_adjustment` DISABLE KEYS */;
/*!40000 ALTER TABLE `water_bill_adjustment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `water_bill_history`
--

DROP TABLE IF EXISTS `water_bill_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `water_bill_history` (
  `bill_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection_id` bigint(20) unsigned NOT NULL,
  `period_id` bigint(20) unsigned NOT NULL,
  `prev_reading_id` bigint(20) unsigned NOT NULL,
  `curr_reading_id` bigint(20) unsigned NOT NULL,
  `consumption` decimal(12,3) NOT NULL,
  `water_amount` decimal(12,2) NOT NULL,
  `due_date` date DEFAULT NULL,
  `adjustment_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) GENERATED ALWAYS AS ((`water_amount` + `adjustment_total`)) STORED,
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `stat_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`bill_id`),
  UNIQUE KEY `unique_connection_period` (`connection_id`,`period_id`),
  KEY `water_bill_history_connection_id_index` (`connection_id`),
  KEY `water_bill_history_period_id_index` (`period_id`),
  KEY `water_bill_history_stat_id_index` (`stat_id`),
  KEY `water_bill_history_prev_reading_id_foreign` (`prev_reading_id`),
  KEY `water_bill_history_curr_reading_id_foreign` (`curr_reading_id`),
  CONSTRAINT `water_bill_history_connection_id_foreign` FOREIGN KEY (`connection_id`) REFERENCES `ServiceConnection` (`connection_id`) ON DELETE CASCADE,
  CONSTRAINT `water_bill_history_curr_reading_id_foreign` FOREIGN KEY (`curr_reading_id`) REFERENCES `MeterReading` (`reading_id`),
  CONSTRAINT `water_bill_history_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `period` (`per_id`) ON DELETE CASCADE,
  CONSTRAINT `water_bill_history_prev_reading_id_foreign` FOREIGN KEY (`prev_reading_id`) REFERENCES `MeterReading` (`reading_id`),
  CONSTRAINT `water_bill_history_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `water_bill_history`
--

LOCK TABLES `water_bill_history` WRITE;
/*!40000 ALTER TABLE `water_bill_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `water_bill_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `water_rates`
--

DROP TABLE IF EXISTS `water_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `water_rates` (
  `wr_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rate_desc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate` decimal(10,5) NOT NULL,
  `stat_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`wr_id`),
  KEY `water_rate_desc_index` (`rate_desc`),
  KEY `water_rates_stat_id_foreign` (`stat_id`),
  CONSTRAINT `water_rates_stat_id_foreign` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `water_rates`
--

LOCK TABLES `water_rates` WRITE;
/*!40000 ALTER TABLE `water_rates` DISABLE KEYS */;
INSERT INTO `water_rates` VALUES (1,'Residential - Minimum (0-10 cu.m)',150.00000,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(2,'Residential - 11-20 cu.m',18.00000,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(3,'Residential - 21-30 cu.m',20.00000,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(4,'Residential - 31+ cu.m',25.00000,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(5,'Commercial - Minimum (0-10 cu.m)',250.00000,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(6,'Commercial - 11-20 cu.m',28.00000,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(7,'Commercial - 21-30 cu.m',30.00000,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(8,'Commercial - 31+ cu.m',35.00000,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(9,'Industrial - Minimum (0-10 cu.m)',350.00000,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(10,'Industrial - 11-20 cu.m',35.00000,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(11,'Industrial - 21+ cu.m',40.00000,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(12,'Government - Minimum (0-10 cu.m)',200.00000,2,'2025-11-06 01:46:14','2025-11-06 01:46:14'),(13,'Government - 11+ cu.m',22.00000,2,'2025-11-06 01:46:14','2025-11-06 01:46:14');
/*!40000 ALTER TABLE `water_rates` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-02  9:34:43
