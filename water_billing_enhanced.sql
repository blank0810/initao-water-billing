-- Enhanced Water Billing Database Schema
-- Optimized for single-municipality deployment
-- Based on analysis: removed over-engineering, fixed structural issues

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ========================================
-- CONFIGURATION & SYSTEM TABLES
-- ========================================

-- System configuration for deployment-specific settings
CREATE TABLE `system_config` (
  `config_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(100) NOT NULL,
  `config_value` text,
  `config_type` enum('string','number','boolean','date','json') NOT NULL DEFAULT 'string',
  `description` text,
  `is_editable` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`config_id`),
  UNIQUE KEY `UQ_config_key` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Status lookup table (shared across entities)
CREATE TABLE `statuses` (
  `stat_id` int(11) NOT NULL,
  `stat_desc` varchar(50) NOT NULL,
  PRIMARY KEY (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `statuses` (`stat_id`, `stat_desc`) VALUES
(1, 'ACTIVE'),
(2, 'INACTIVE'),
(3, 'PENDING'),
(4, 'APPROVED'),
(5, 'REJECTED'),
(6, 'ARCHIVED'),
(7, 'DISCONNECTED');

-- ========================================
-- GEOGRAPHIC & LOCATION TABLES
-- ========================================

-- Barangay table (varies within municipality)
CREATE TABLE `barangay` (
  `barangay_id` int(11) NOT NULL AUTO_INCREMENT,
  `barangay_code` varchar(20),
  `barangay_name` varchar(100) NOT NULL,
  `stat_id` int(11) DEFAULT 1,
  PRIMARY KEY (`barangay_id`),
  UNIQUE KEY `UQ_barangay_code` (`barangay_code`),
  KEY `fk_barangay_status` (`stat_id`),
  CONSTRAINT `fk_barangay_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Purok/Zone table (subdivisions within barangay)
CREATE TABLE `purok` (
  `purok_id` int(11) NOT NULL AUTO_INCREMENT,
  `purok_code` varchar(20),
  `purok_name` varchar(100) NOT NULL,
  `barangay_id` int(11) DEFAULT NULL,
  `stat_id` int(11) DEFAULT 1,
  PRIMARY KEY (`purok_id`),
  KEY `fk_purok_barangay` (`barangay_id`),
  KEY `fk_purok_status` (`stat_id`),
  CONSTRAINT `fk_purok_barangay` FOREIGN KEY (`barangay_id`) REFERENCES `barangay` (`barangay_id`),
  CONSTRAINT `fk_purok_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Consumer address with complete address fields
CREATE TABLE `consumer_address` (
  `address_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `house_number` varchar(20),
  `street_name` varchar(100),
  `subdivision_name` varchar(100),
  `barangay_id` int(11) NOT NULL,
  `purok_id` int(11),
  `landmark_description` text,
  `zip_code` varchar(10),
  `gps_latitude` decimal(10,8),
  `gps_longitude` decimal(11,8),
  `stat_id` int(11) DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`address_id`),
  KEY `fk_address_barangay` (`barangay_id`),
  KEY `fk_address_purok` (`purok_id`),
  KEY `fk_address_status` (`stat_id`),
  CONSTRAINT `fk_address_barangay` FOREIGN KEY (`barangay_id`) REFERENCES `barangay` (`barangay_id`),
  CONSTRAINT `fk_address_purok` FOREIGN KEY (`purok_id`) REFERENCES `purok` (`purok_id`),
  CONSTRAINT `fk_address_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- CUSTOMER MANAGEMENT
-- ========================================

-- Customer table
-- billing_address_id = where the customer lives (where bills are sent)
-- service addresses are in serviceconnection table (where meters/connections are)
-- This allows a customer to live at Location A but have connections at B, C, D
CREATE TABLE `customer` (
  `customer_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50),
  `last_name` varchar(50) NOT NULL,
  `contact_phone` varchar(20),
  `contact_email` varchar(100),
  `billing_address_id` bigint(20),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `stat_id` int(11) DEFAULT 1,
  `user_id` int(11),
  PRIMARY KEY (`customer_id`),
  KEY `idx_customer_name` (`last_name`, `first_name`),
  KEY `fk_customer_billing_address` (`billing_address_id`),
  KEY `fk_customer_status` (`stat_id`),
  KEY `fk_customer_user` (`user_id`),
  CONSTRAINT `fk_customer_billing_address` FOREIGN KEY (`billing_address_id`) REFERENCES `consumer_address` (`address_id`),
  CONSTRAINT `fk_customer_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- ACCOUNT CLASSIFICATION
-- ========================================

-- Consolidated account type table (removed separate 'classes' table)
-- rate_category is VARCHAR for flexibility - municipalities can define custom categories
CREATE TABLE `account_type` (
  `account_type_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type_code` varchar(20) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `rate_category` varchar(50) NOT NULL,
  `description` text,
  `stat_id` int(11) DEFAULT 1,
  PRIMARY KEY (`account_type_id`),
  UNIQUE KEY `UQ_account_type_code` (`type_code`),
  KEY `fk_account_type_status` (`stat_id`),
  CONSTRAINT `fk_account_type_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `account_type` (`account_type_id`, `type_code`, `type_name`, `rate_category`, `description`) VALUES
(1, 'RES', 'Residential', 'Residential', 'Individual household connections'),
(2, 'COM', 'Commercial', 'Commercial', 'Business establishments'),
(3, 'GOVT', 'Government', 'Government', 'Government offices and facilities'),
(4, 'PUB_TAP', 'Public Tap', 'Institutional', 'Community water taps');

-- ========================================
-- READING ZONES & AREAS
-- ========================================

-- Reading zone for meter reader assignments (renamed from 'area')
CREATE TABLE `reading_zone` (
  `zone_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `zone_code` varchar(10) NOT NULL,
  `zone_name` varchar(50),
  `description` text,
  `stat_id` int(11) DEFAULT 1,
  PRIMARY KEY (`zone_id`),
  UNIQUE KEY `UQ_zone_code` (`zone_code`),
  KEY `fk_zone_status` (`stat_id`),
  CONSTRAINT `fk_zone_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Employee/Personnel table
CREATE TABLE `employee` (
  `emp_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `middle_name` varchar(50),
  `position_id` int(11),
  `stat_id` int(11) DEFAULT 1,
  PRIMARY KEY (`emp_id`),
  KEY `fk_employee_position` (`position_id`),
  KEY `fk_employee_status` (`stat_id`),
  CONSTRAINT `fk_employee_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Position/Job title table
CREATE TABLE `position` (
  `pos_id` int(11) NOT NULL AUTO_INCREMENT,
  `pos_name` varchar(50) NOT NULL,
  `description` text,
  `stat_id` int(11) DEFAULT 1,
  PRIMARY KEY (`pos_id`),
  KEY `fk_position_status` (`stat_id`),
  CONSTRAINT `fk_position_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `employee`
  ADD CONSTRAINT `fk_employee_position` FOREIGN KEY (`position_id`) REFERENCES `position` (`pos_id`);

-- Zone assignment to meter readers
CREATE TABLE `zone_assignment` (
  `assignment_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `zone_id` bigint(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date,
  PRIMARY KEY (`assignment_id`),
  KEY `fk_assignment_zone` (`zone_id`),
  KEY `fk_assignment_employee` (`emp_id`),
  CONSTRAINT `fk_assignment_zone` FOREIGN KEY (`zone_id`) REFERENCES `reading_zone` (`zone_id`),
  CONSTRAINT `fk_assignment_employee` FOREIGN KEY (`emp_id`) REFERENCES `employee` (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- SERVICE APPLICATION & CONNECTION
-- ========================================

-- Service application table
CREATE TABLE `serviceapplication` (
  `application_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) NOT NULL,
  `service_address_id` bigint(20) NOT NULL,
  `account_type_id` bigint(20) NOT NULL,
  `zone_id` bigint(20) NOT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_printed` tinyint(1) NOT NULL DEFAULT 0,
  `stat_id` int(11) DEFAULT 3,
  PRIMARY KEY (`application_id`),
  KEY `fk_app_customer` (`customer_id`),
  KEY `fk_app_address` (`service_address_id`),
  KEY `fk_app_account_type` (`account_type_id`),
  KEY `fk_app_zone` (`zone_id`),
  KEY `fk_app_status` (`stat_id`),
  CONSTRAINT `fk_app_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  CONSTRAINT `fk_app_address` FOREIGN KEY (`service_address_id`) REFERENCES `consumer_address` (`address_id`),
  CONSTRAINT `fk_app_account_type` FOREIGN KEY (`account_type_id`) REFERENCES `account_type` (`account_type_id`),
  CONSTRAINT `fk_app_zone` FOREIGN KEY (`zone_id`) REFERENCES `reading_zone` (`zone_id`),
  CONSTRAINT `fk_app_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Service connection table (references application for lineage)
CREATE TABLE `serviceconnection` (
  `connection_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_no` varchar(30) NOT NULL,
  `application_id` bigint(20),
  `customer_id` bigint(20) NOT NULL,
  `service_address_id` bigint(20) NOT NULL,
  `account_type_id` bigint(20) NOT NULL,
  `zone_id` bigint(11) NOT NULL,
  `started_at` date NOT NULL,
  `ended_at` date,
  `stat_id` int(11) DEFAULT 1,
  PRIMARY KEY (`connection_id`),
  UNIQUE KEY `UQ_account_no` (`account_no`),
  KEY `fk_sc_application` (`application_id`),
  KEY `fk_sc_customer` (`customer_id`),
  KEY `fk_sc_address` (`service_address_id`),
  KEY `fk_sc_account_type` (`account_type_id`),
  KEY `fk_sc_zone` (`zone_id`),
  KEY `fk_sc_status` (`stat_id`),
  CONSTRAINT `fk_sc_application` FOREIGN KEY (`application_id`) REFERENCES `serviceapplication` (`application_id`),
  CONSTRAINT `fk_sc_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  CONSTRAINT `fk_sc_address` FOREIGN KEY (`service_address_id`) REFERENCES `consumer_address` (`address_id`),
  CONSTRAINT `fk_sc_account_type` FOREIGN KEY (`account_type_id`) REFERENCES `account_type` (`account_type_id`),
  CONSTRAINT `fk_sc_zone` FOREIGN KEY (`zone_id`) REFERENCES `reading_zone` (`zone_id`),
  CONSTRAINT `fk_sc_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- METER MANAGEMENT
-- ========================================

-- Meter inventory
CREATE TABLE `meter` (
  `meter_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `meter_serial` varchar(50) NOT NULL,
  `meter_brand` varchar(50),
  `meter_size` varchar(20),
  `stat_id` int(11) DEFAULT 1,
  PRIMARY KEY (`meter_id`),
  UNIQUE KEY `UQ_meter_serial` (`meter_serial`),
  KEY `fk_meter_status` (`stat_id`),
  CONSTRAINT `fk_meter_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Meter assignment to connections
CREATE TABLE `meterassignment` (
  `assignment_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `connection_id` bigint(20) NOT NULL,
  `meter_id` bigint(20) NOT NULL,
  `installed_at` date NOT NULL,
  `removed_at` date,
  `install_reading` decimal(12,3) NOT NULL DEFAULT 0.000,
  `removal_reading` decimal(12,3),
  PRIMARY KEY (`assignment_id`),
  UNIQUE KEY `UQ_meter_assignment` (`connection_id`,`meter_id`,`installed_at`),
  KEY `fk_ma_connection` (`connection_id`),
  KEY `fk_ma_meter` (`meter_id`),
  CONSTRAINT `fk_ma_connection` FOREIGN KEY (`connection_id`) REFERENCES `serviceconnection` (`connection_id`),
  CONSTRAINT `fk_ma_meter` FOREIGN KEY (`meter_id`) REFERENCES `meter` (`meter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- BILLING PERIOD & METER READING
-- ========================================

-- Billing period
CREATE TABLE `billing_period` (
  `period_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `period_year` int(11) NOT NULL,
  `period_month` int(11) NOT NULL,
  `period_code` varchar(10) NOT NULL,
  `period_start_date` date NOT NULL,
  `period_end_date` date NOT NULL,
  `reading_deadline` date,
  `billing_generation_date` date,
  `payment_due_date` date,
  `created_at` datetime DEFAULT current_timestamp(),
  `stat_id` int(11) DEFAULT 1,
  PRIMARY KEY (`period_id`),
  UNIQUE KEY `UQ_period_code` (`period_code`),
  UNIQUE KEY `UQ_period_year_month` (`period_year`, `period_month`),
  KEY `fk_period_status` (`stat_id`),
  CONSTRAINT `fk_period_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Meter reading with consumption calculation
CREATE TABLE `meterreading` (
  `reading_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `assignment_id` bigint(20) NOT NULL,
  `period_id` bigint(20),
  `reading_date` date NOT NULL,
  `reading_value` decimal(12,3) NOT NULL,
  `previous_reading_value` decimal(12,3) DEFAULT 0.000,
  `consumption` decimal(12,3) GENERATED ALWAYS AS (`reading_value` - `previous_reading_value`) STORED,
  `is_estimated` tinyint(1) NOT NULL DEFAULT 0,
  `estimated_reason` text,
  `reader_emp_id` int(11),
  `photo_url` varchar(255),
  `remarks` text,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`reading_id`),
  UNIQUE KEY `UQ_reading_assignment_date` (`assignment_id`,`reading_date`),
  KEY `fk_reading_assignment` (`assignment_id`),
  KEY `fk_reading_period` (`period_id`),
  KEY `fk_reading_reader` (`reader_emp_id`),
  CONSTRAINT `fk_reading_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `meterassignment` (`assignment_id`),
  CONSTRAINT `fk_reading_period` FOREIGN KEY (`period_id`) REFERENCES `billing_period` (`period_id`),
  CONSTRAINT `fk_reading_reader` FOREIGN KEY (`reader_emp_id`) REFERENCES `employee` (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- WATER RATES
-- ========================================

-- Water rates with tiered pricing
CREATE TABLE `water_rates` (
  `rate_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_type_id` bigint(20) NOT NULL,
  `period_id` bigint(20),
  `min_range` int(11) NOT NULL,
  `max_range` int(11),
  `rate_value` decimal(12,2),
  `rate_increment` decimal(12,2) NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date,
  `stat_id` int(11) DEFAULT 1,
  `user_id` int(11),
  PRIMARY KEY (`rate_id`),
  KEY `fk_rate_account_type` (`account_type_id`),
  KEY `fk_rate_period` (`period_id`),
  KEY `fk_rate_status` (`stat_id`),
  KEY `fk_rate_user` (`user_id`),
  CONSTRAINT `fk_rate_account_type` FOREIGN KEY (`account_type_id`) REFERENCES `account_type` (`account_type_id`),
  CONSTRAINT `fk_rate_period` FOREIGN KEY (`period_id`) REFERENCES `billing_period` (`period_id`),
  CONSTRAINT `fk_rate_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- BILLING & CHARGES
-- ========================================

-- Water bill
CREATE TABLE `water_bill` (
  `bill_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `connection_id` bigint(20) NOT NULL,
  `period_id` bigint(20) NOT NULL,
  `prev_reading_id` bigint(20) NOT NULL,
  `curr_reading_id` bigint(20) NOT NULL,
  `consumption` decimal(12,3) NOT NULL,
  `water_amount` decimal(12,2) NOT NULL,
  `due_date` date,
  `adjustment_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) GENERATED ALWAYS AS (`water_amount` + `adjustment_total`) STORED,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `stat_id` int(11) DEFAULT 1,
  PRIMARY KEY (`bill_id`),
  UNIQUE KEY `UQ_bill_conn_period` (`connection_id`,`period_id`),
  KEY `idx_bill_connection_period` (`connection_id`, `period_id`),
  KEY `fk_bill_period` (`period_id`),
  KEY `fk_bill_prev_read` (`prev_reading_id`),
  KEY `fk_bill_curr_read` (`curr_reading_id`),
  KEY `fk_bill_status` (`stat_id`),
  CONSTRAINT `fk_bill_connection` FOREIGN KEY (`connection_id`) REFERENCES `serviceconnection` (`connection_id`),
  CONSTRAINT `fk_bill_period` FOREIGN KEY (`period_id`) REFERENCES `billing_period` (`period_id`),
  CONSTRAINT `fk_bill_prev_read` FOREIGN KEY (`prev_reading_id`) REFERENCES `meterreading` (`reading_id`),
  CONSTRAINT `fk_bill_curr_read` FOREIGN KEY (`curr_reading_id`) REFERENCES `meterreading` (`reading_id`),
  CONSTRAINT `fk_bill_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Bill adjustment types
CREATE TABLE `billadjustmenttype` (
  `adjustment_type_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(80) NOT NULL,
  `direction` enum('+','-') NOT NULL,
  `description` text,
  `stat_id` int(11) DEFAULT 1,
  PRIMARY KEY (`adjustment_type_id`),
  UNIQUE KEY `UQ_adjustment_type_name` (`type_name`),
  KEY `fk_adjustment_type_status` (`stat_id`),
  CONSTRAINT `fk_adjustment_type_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `billadjustmenttype` (`adjustment_type_id`, `type_name`, `direction`, `description`) VALUES
(1, 'Reading Adjustment (Increase)', '+', 'Correction increasing the reading'),
(2, 'Meter Change (Increase)', '+', 'Adjustment due to meter replacement'),
(3, 'Reading Adjustment (Decrease)', '-', 'Correction decreasing the reading'),
(4, 'Meter Change (Decrease)', '-', 'Adjustment due to meter replacement');

-- Bill adjustments
CREATE TABLE `billadjustment` (
  `adjustment_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `bill_id` bigint(20) NOT NULL,
  `adjustment_type_id` bigint(20) NOT NULL,
  `old_reading` float NOT NULL,
  `new_reading` float NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `remarks` varchar(200),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11),
  PRIMARY KEY (`adjustment_id`),
  KEY `fk_adjustment_bill` (`bill_id`),
  KEY `fk_adjustment_type` (`adjustment_type_id`),
  KEY `fk_adjustment_user` (`user_id`),
  CONSTRAINT `fk_adjustment_bill` FOREIGN KEY (`bill_id`) REFERENCES `water_bill` (`bill_id`),
  CONSTRAINT `fk_adjustment_type` FOREIGN KEY (`adjustment_type_id`) REFERENCES `billadjustmenttype` (`adjustment_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Charge items (fees other than water consumption)
CREATE TABLE `chargeitem` (
  `charge_item_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `item_code` varchar(20),
  `item_name` varchar(80) NOT NULL,
  `default_amount` decimal(12,2) NOT NULL,
  `description` text,
  `stat_id` int(11) DEFAULT 1,
  PRIMARY KEY (`charge_item_id`),
  UNIQUE KEY `UQ_charge_item_name` (`item_name`),
  UNIQUE KEY `UQ_charge_item_code` (`item_code`),
  KEY `fk_charge_item_status` (`stat_id`),
  CONSTRAINT `fk_charge_item_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `chargeitem` (`charge_item_id`, `item_code`, `item_name`, `default_amount`, `description`) VALUES
(1, 'REG_FEE', 'Registration Fee', 50.00, 'New connection registration'),
(2, 'RECON_FEE', 'Reconnection Fee', 500.00, 'Reconnection after disconnection'),
(3, 'TRANSFER_FEE', 'Transfer Fee', 250.00, 'Transfer of ownership'),
(4, 'METER_FEE', 'Meter Fee', 900.00, 'Water meter cost'),
(5, 'LOCK_WING', 'Lock Wing', 200.00, 'Lock wing installation'),
(6, 'INSTALL_FEE', 'Installation Fee', 200.00, 'Service installation'),
(7, 'TAP_FEE', 'Tapping Fee', 50.00, 'Water line tapping'),
(8, 'EXCAVATION', 'Excavation Fee', 50.00, 'Excavation work'),
(9, 'TEMP_DISC_RECON', 'Temp. Disconnection Reconnection', 100.00, 'Temporary disconnection reconnection'),
(10, 'OTHER', 'Other Charges', 0.00, 'Miscellaneous charges'),
(11, 'OLD_ACCT', 'Old Account Charge', 0.00, 'Previous balance transfer'),
(12, 'PENALTY', 'Late Payment Penalty', 10.00, 'Penalty for late payment');

-- Customer charges (other fees)
CREATE TABLE `customercharge` (
  `charge_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) NOT NULL,
  `application_id` bigint(20),
  `connection_id` bigint(20),
  `charge_item_id` bigint(20) NOT NULL,
  `quantity` decimal(12,3) NOT NULL DEFAULT 1.000,
  `unit_amount` decimal(12,2) NOT NULL,
  `total_amount` decimal(14,3) GENERATED ALWAYS AS (`quantity` * `unit_amount`) STORED,
  `due_date` date,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `stat_id` int(11) DEFAULT 1,
  PRIMARY KEY (`charge_id`),
  KEY `fk_charge_customer` (`customer_id`),
  KEY `fk_charge_application` (`application_id`),
  KEY `fk_charge_connection` (`connection_id`),
  KEY `fk_charge_item` (`charge_item_id`),
  KEY `fk_charge_status` (`stat_id`),
  CONSTRAINT `fk_charge_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  CONSTRAINT `fk_charge_application` FOREIGN KEY (`application_id`) REFERENCES `serviceapplication` (`application_id`),
  CONSTRAINT `fk_charge_connection` FOREIGN KEY (`connection_id`) REFERENCES `serviceconnection` (`connection_id`),
  CONSTRAINT `fk_charge_item` FOREIGN KEY (`charge_item_id`) REFERENCES `chargeitem` (`charge_item_id`),
  CONSTRAINT `fk_charge_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- PAYMENT & ALLOCATION
-- ========================================

-- Payment records
CREATE TABLE `payment` (
  `payment_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `receipt_no` varchar(40) NOT NULL,
  `payer_id` bigint(20) NOT NULL,
  `payment_date` date NOT NULL,
  `amount_received` decimal(12,2) NOT NULL,
  `payment_method` enum('cash','check','bank_transfer','mobile_payment','online') DEFAULT 'cash',
  `reference_no` varchar(100),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11),
  `stat_id` int(11) DEFAULT 1,
  PRIMARY KEY (`payment_id`),
  UNIQUE KEY `UQ_receipt_no` (`receipt_no`),
  KEY `idx_payment_date` (`payment_date`),
  KEY `fk_payment_payer` (`payer_id`),
  KEY `fk_payment_user` (`user_id`),
  KEY `fk_payment_status` (`stat_id`),
  CONSTRAINT `fk_payment_payer` FOREIGN KEY (`payer_id`) REFERENCES `customer` (`customer_id`),
  CONSTRAINT `fk_payment_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Payment allocation (FIXED: added bill and charge references)
CREATE TABLE `paymentallocation` (
  `allocation_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `payment_id` bigint(20) NOT NULL,
  `bill_id` bigint(20),
  `charge_id` bigint(20),
  `amount_applied` decimal(12,2) NOT NULL,
  `allocation_type` enum('bill','charge','advance','adjustment') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `stat_id` int(11) DEFAULT 1,
  PRIMARY KEY (`allocation_id`),
  KEY `fk_allocation_payment` (`payment_id`),
  KEY `fk_allocation_bill` (`bill_id`),
  KEY `fk_allocation_charge` (`charge_id`),
  KEY `fk_allocation_status` (`stat_id`),
  CONSTRAINT `fk_allocation_payment` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`),
  CONSTRAINT `fk_allocation_bill` FOREIGN KEY (`bill_id`) REFERENCES `water_bill` (`bill_id`),
  CONSTRAINT `fk_allocation_charge` FOREIGN KEY (`charge_id`) REFERENCES `customercharge` (`charge_id`),
  CONSTRAINT `fk_allocation_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`),
  CONSTRAINT `chk_allocation_target` CHECK (
    `bill_id` IS NOT NULL OR `charge_id` IS NOT NULL
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- LEDGER & ACCOUNTING
-- ========================================

-- Ledger source types
CREATE TABLE `ledgersource` (
  `source_type_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `source_type` varchar(50) NOT NULL,
  `source_table` varchar(50) NOT NULL,
  PRIMARY KEY (`source_type_id`),
  UNIQUE KEY `UQ_ledger_source_type` (`source_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ledgersource` (`source_type_id`, `source_type`, `source_table`) VALUES
(1, 'BILL', 'water_bill'),
(2, 'CHARGE', 'customercharge'),
(3, 'ADJUSTMENT', 'billadjustment'),
(4, 'PAYMENT', 'paymentallocation');

-- Customer ledger (double-entry bookkeeping)
CREATE TABLE `customerledger` (
  `ledger_entry_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) NOT NULL,
  `txn_date` date NOT NULL,
  `source_type_id` bigint(20) NOT NULL,
  `source_id` bigint(20) NOT NULL,
  `source_line_no` bigint(20),
  `source_line_no_nz` int(11) GENERATED ALWAYS AS (ifnull(`source_line_no`,0)) STORED,
  `debit` decimal(12,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(12,2) NOT NULL DEFAULT 0.00,
  `description` text,
  `user_id` int(11),
  `stat_id` int(11) DEFAULT 1,
  PRIMARY KEY (`ledger_entry_id`),
  UNIQUE KEY `UQ_ledger_source` (`source_type_id`,`source_id`,`source_line_no_nz`),
  KEY `idx_ledger_customer_date` (`customer_id`, `txn_date`),
  KEY `idx_ledger_date` (`txn_date`),
  KEY `fk_ledger_customer` (`customer_id`),
  KEY `fk_ledger_source_type` (`source_type_id`),
  KEY `fk_ledger_user` (`user_id`),
  KEY `fk_ledger_status` (`stat_id`),
  CONSTRAINT `fk_ledger_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  CONSTRAINT `fk_ledger_source_type` FOREIGN KEY (`source_type_id`) REFERENCES `ledgersource` (`source_type_id`),
  CONSTRAINT `fk_ledger_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- USER MANAGEMENT & RBAC
-- ========================================

-- User types
CREATE TABLE `user_types` (
  `user_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_code` varchar(20) NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `stat_id` int(11) DEFAULT 1,
  PRIMARY KEY (`user_type_id`),
  UNIQUE KEY `UQ_user_type_code` (`type_code`),
  KEY `fk_user_type_status` (`stat_id`),
  CONSTRAINT `fk_user_type_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_types` (`user_type_id`, `type_code`, `type_name`) VALUES
(1, 'ADMIN', 'System Administrator'),
(2, 'BILLING', 'Billing Staff');

-- Users
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `user_type_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime,
  `stat_id` int(11) DEFAULT 1,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `UQ_username` (`username`),
  UNIQUE KEY `UQ_email` (`email`),
  KEY `fk_user_type` (`user_type_id`),
  KEY `fk_user_status` (`stat_id`),
  CONSTRAINT `fk_user_type` FOREIGN KEY (`user_type_id`) REFERENCES `user_types` (`user_type_id`),
  CONSTRAINT `fk_user_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Roles
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_code` varchar(20) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `UQ_role_code` (`role_code`),
  UNIQUE KEY `UQ_role_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `roles` (`role_id`, `role_code`, `role_name`, `description`) VALUES
(1, 'SYS_ADMIN', 'System Administrator', 'Full system control and configuration'),
(2, 'BILL_MGR', 'Billing Manager', 'Billing cycle and invoice management'),
(3, 'PAY_OFFICER', 'Payment Officer', 'Payment processing and receipts'),
(4, 'METER_READER', 'Meter Reader', 'Meter reading data entry'),
(5, 'CUST_SVC', 'Customer Service', 'Customer account management'),
(6, 'CUSTOMER', 'Customer Portal', 'View bills and make payments');

-- Permissions
CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_code` varchar(50) NOT NULL,
  `permission_name` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `UQ_permission_code` (`permission_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Role-Permission mapping
CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `fk_rp_permission` (`permission_id`),
  CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- User-Role mapping
CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `assigned_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `fk_ur_role` (`role_id`),
  CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- AUDIT & LOGGING
-- ========================================

-- Audit log for critical operations
CREATE TABLE `audit_log` (
  `audit_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(100) NOT NULL,
  `record_id` bigint(20) NOT NULL,
  `action` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `old_values` json,
  `new_values` json,
  `user_id` int(11),
  `ip_address` varchar(45),
  `user_agent` varchar(255),
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`audit_id`),
  KEY `idx_audit_table_record` (`table_name`, `record_id`),
  KEY `idx_audit_user` (`user_id`),
  KEY `idx_audit_timestamp` (`timestamp`),
  KEY `fk_audit_user` (`user_id`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
