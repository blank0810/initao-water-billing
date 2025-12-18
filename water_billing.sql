-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2025 at 01:35 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `water_billing`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_type`
--

CREATE TABLE `account_type` (
  `at_id` bigint(20) NOT NULL,
  `at_desc` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `account_type`
--

INSERT INTO `account_type` (`at_id`, `at_desc`, `stat_id`) VALUES
(1, 'Residential', 1),
(2, 'Public Tap', 1),
(3, 'Government', 1),
(4, 'Commercial', 1);

-- --------------------------------------------------------

--
-- Table structure for table `area`
--

CREATE TABLE `area` (
  `a_id` bigint(11) NOT NULL,
  `a_name` varchar(10) NOT NULL,
  `a_desc` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `area`
--

INSERT INTO `area` (`a_id`, `a_name`, `a_desc`, `stat_id`) VALUES
(1, '1', 'AreaDesc Near the sea along the river', 1),
(2, 'A', 'Aroung the market', 1);

-- --------------------------------------------------------

--
-- Table structure for table `areaassignment`
--

CREATE TABLE `areaassignment` (
  `area_assignment_id` bigint(20) NOT NULL,
  `area_id` bigint(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `areaassignment`
--

INSERT INTO `areaassignment` (`area_assignment_id`, `area_id`, `emp_id`, `effective_from`, `effective_to`) VALUES
(1, 1, 1, '2025-11-20', '2025-12-31');

-- --------------------------------------------------------

--
-- Table structure for table `barangay`
--

CREATE TABLE `barangay` (
  `b_id` int(11) NOT NULL,
  `b_desc` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `barangay`
--

INSERT INTO `barangay` (`b_id`, `b_desc`, `stat_id`) VALUES
(1, 'Aluna', 1),
(2, 'Andales', 1),
(3, 'Apas', 1),
(4, 'Calacapan', 1),
(5, 'Gimangpang', 1),
(6, 'Jampason', 1),
(7, 'Kamelon', 1),
(8, 'Kanitoan', 1),
(9, 'Oguis', 1),
(10, 'Pagahan', 1),
(11, 'Poblacion', 1),
(12, 'Pontacon', 1),
(13, 'San Pedro', 1),
(14, 'Sinalac', 1),
(15, 'Tawantawan', 1),
(16, 'Tubigan', 1);

-- --------------------------------------------------------

--
-- Table structure for table `billadjustment`
--

CREATE TABLE `billadjustment` (
  `bill_adjustment_id` bigint(20) NOT NULL,
  `bill_id` bigint(20) NOT NULL,
  `bill_adjustment_type_id` bigint(20) NOT NULL,
  `old_rdng` float NOT NULL,
  `new_rdng` float NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `remarks` varchar(200) DEFAULT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `billadjustmenttype`
--

CREATE TABLE `billadjustmenttype` (
  `bill_adjustment_type_id` bigint(20) NOT NULL,
  `name` varchar(80) NOT NULL,
  `direction` enum('+','-') NOT NULL,
  `stat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billadjustmenttype`
--

INSERT INTO `billadjustmenttype` (`bill_adjustment_type_id`, `name`, `direction`, `stat_id`) VALUES
(1, 'Reading Adjustment', '+', 1),
(2, 'Change Meter', '+', 1),
(3, 'Reading Adjustment', '-', 1),
(4, 'Change Meter', '-', 1);

-- --------------------------------------------------------

--
-- Table structure for table `chargeitem`
--

CREATE TABLE `chargeitem` (
  `charge_item_id` bigint(20) NOT NULL,
  `name` varchar(80) NOT NULL,
  `default_amount` decimal(12,2) NOT NULL,
  `stat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chargeitem`
--

INSERT INTO `chargeitem` (`charge_item_id`, `name`, `default_amount`, `stat_id`) VALUES
(1, 'Registration fee', 50.00, 1),
(2, 'Reconnection ', 500.00, 1),
(3, 'Transfer Fee', 250.00, 1),
(4, 'Meter Fee', 900.00, 1),
(5, 'Lock Wing', 200.00, 1),
(6, 'Installation fee', 200.00, 1),
(7, 'Tapping fee', 50.00, 1),
(8, 'Excavation fee', 50.00, 1),
(9, 'Rec. fee of Temp. disc. line', 100.00, 1),
(10, 'Others', 300.00, 1),
(11, 'Old accounts', 0.00, 1),
(12, 'Penalty', 10.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `class_desc` varchar(50) NOT NULL,
  `stat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_desc`, `stat_id`) VALUES
(1, 'RESIDENTIAL', 1),
(2, 'COMMERCIAL', 1);

-- --------------------------------------------------------

--
-- Table structure for table `connection_history`
--

CREATE TABLE `connection_history` (
  `ch_id` bigint(20) NOT NULL,
  `connection_id` bigint(20) NOT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `stat_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consumer_address`
--

CREATE TABLE `consumer_address` (
  `ca_id` bigint(20) NOT NULL,
  `p_id` int(11) DEFAULT NULL,
  `b_id` int(11) DEFAULT NULL,
  `t_id` int(11) DEFAULT NULL,
  `prov_id` int(11) DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `consumer_address`
--

INSERT INTO `consumer_address` (`ca_id`, `p_id`, `b_id`, `t_id`, `prov_id`, `stat_id`) VALUES
(1, 1, 1, 1, 1, 1),
(2, 2, 1, 1, 1, 1),
(3, 3, 1, 1, 1, 1),
(4, 4, 1, 1, 1, 1),
(5, 5, 1, 1, 1, 1),
(6, 6, 1, 1, 1, 1),
(7, 7, 1, 1, 1, 1),
(8, 8, 1, 1, 1, 1),
(9, 9, 1, 1, 1, 1),
(10, 10, 1, 1, 1, 1),
(11, 11, 1, 1, 1, 1),
(12, 12, 1, 1, 1, 1),
(13, 13, 1, 1, 1, 1),
(14, 14, 1, 1, 1, 1),
(15, 15, 1, 1, 1, 1),
(16, 16, 1, 1, 1, 1),
(17, 17, 1, 1, 1, 1),
(18, 18, 1, 1, 1, 1),
(19, 19, 1, 1, 1, 1),
(20, 20, 1, 1, 1, 1),
(21, 1, 2, 1, 1, 1),
(22, 2, 2, 1, 1, 1),
(23, 3, 2, 1, 1, 1),
(24, 4, 2, 1, 1, 1),
(25, 5, 2, 1, 1, 1),
(26, 6, 2, 1, 1, 1),
(27, 7, 2, 1, 1, 1),
(28, 8, 2, 1, 1, 1),
(29, 9, 2, 1, 1, 1),
(30, 10, 2, 1, 1, 1),
(31, 11, 2, 1, 1, 1),
(32, 12, 2, 1, 1, 1),
(33, 13, 2, 1, 1, 1),
(34, 14, 2, 1, 1, 1),
(35, 15, 2, 1, 1, 1),
(36, 16, 2, 1, 1, 1),
(37, 17, 2, 1, 1, 1),
(38, 18, 2, 1, 1, 1),
(39, 19, 2, 1, 1, 1),
(40, 20, 2, 1, 1, 1),
(41, 1, 3, 1, 1, 1),
(42, 2, 3, 1, 1, 1),
(43, 3, 3, 1, 1, 1),
(44, 4, 3, 1, 1, 1),
(45, 5, 3, 1, 1, 1),
(46, 6, 3, 1, 1, 1),
(47, 7, 3, 1, 1, 1),
(48, 8, 3, 1, 1, 1),
(49, 9, 3, 1, 1, 1),
(50, 10, 3, 1, 1, 1),
(51, 11, 3, 1, 1, 1),
(52, 12, 3, 1, 1, 1),
(53, 13, 3, 1, 1, 1),
(54, 14, 3, 1, 1, 1),
(55, 15, 3, 1, 1, 1),
(56, 16, 3, 1, 1, 1),
(57, 17, 3, 1, 1, 1),
(58, 18, 3, 1, 1, 1),
(59, 19, 3, 1, 1, 1),
(60, 20, 3, 1, 1, 1),
(61, 1, 4, 1, 1, 1),
(62, 2, 4, 1, 1, 1),
(63, 3, 4, 1, 1, 1),
(64, 4, 4, 1, 1, 1),
(65, 5, 4, 1, 1, 1),
(66, 6, 4, 1, 1, 1),
(67, 7, 4, 1, 1, 1),
(68, 8, 4, 1, 1, 1),
(69, 9, 4, 1, 1, 1),
(70, 10, 4, 1, 1, 1),
(71, 11, 4, 1, 1, 1),
(72, 12, 4, 1, 1, 1),
(73, 13, 4, 1, 1, 1),
(74, 14, 4, 1, 1, 1),
(75, 15, 4, 1, 1, 1),
(76, 16, 4, 1, 1, 1),
(77, 17, 4, 1, 1, 1),
(78, 18, 4, 1, 1, 1),
(79, 19, 4, 1, 1, 1),
(80, 20, 4, 1, 1, 1),
(81, 1, 5, 1, 1, 1),
(82, 2, 5, 1, 1, 1),
(83, 3, 5, 1, 1, 1),
(84, 4, 5, 1, 1, 1),
(85, 5, 5, 1, 1, 1),
(86, 6, 5, 1, 1, 1),
(87, 7, 5, 1, 1, 1),
(88, 8, 5, 1, 1, 1),
(89, 9, 5, 1, 1, 1),
(90, 10, 5, 1, 1, 1),
(91, 11, 5, 1, 1, 1),
(92, 12, 5, 1, 1, 1),
(93, 13, 5, 1, 1, 1),
(94, 14, 5, 1, 1, 1),
(95, 15, 5, 1, 1, 1),
(96, 16, 5, 1, 1, 1),
(97, 17, 5, 1, 1, 1),
(98, 18, 5, 1, 1, 1),
(99, 19, 5, 1, 1, 1),
(100, 20, 5, 1, 1, 1),
(101, 1, 6, 1, 1, 1),
(102, 2, 6, 1, 1, 1),
(103, 3, 6, 1, 1, 1),
(104, 4, 6, 1, 1, 1),
(105, 5, 6, 1, 1, 1),
(106, 6, 6, 1, 1, 1),
(107, 7, 6, 1, 1, 1),
(108, 8, 6, 1, 1, 1),
(109, 9, 6, 1, 1, 1),
(110, 10, 6, 1, 1, 1),
(111, 11, 6, 1, 1, 1),
(112, 12, 6, 1, 1, 1),
(113, 13, 6, 1, 1, 1),
(114, 14, 6, 1, 1, 1),
(115, 15, 6, 1, 1, 1),
(116, 16, 6, 1, 1, 1),
(117, 17, 6, 1, 1, 1),
(118, 18, 6, 1, 1, 1),
(119, 19, 6, 1, 1, 1),
(120, 20, 6, 1, 1, 1),
(121, 1, 7, 1, 1, 1),
(122, 2, 7, 1, 1, 1),
(123, 3, 7, 1, 1, 1),
(124, 4, 7, 1, 1, 1),
(125, 5, 7, 1, 1, 1),
(126, 6, 7, 1, 1, 1),
(127, 7, 7, 1, 1, 1),
(128, 8, 7, 1, 1, 1),
(129, 9, 7, 1, 1, 1),
(130, 10, 7, 1, 1, 1),
(131, 11, 7, 1, 1, 1),
(132, 12, 7, 1, 1, 1),
(133, 13, 7, 1, 1, 1),
(134, 14, 7, 1, 1, 1),
(135, 15, 7, 1, 1, 1),
(136, 16, 7, 1, 1, 1),
(137, 17, 7, 1, 1, 1),
(138, 18, 7, 1, 1, 1),
(139, 19, 7, 1, 1, 1),
(140, 20, 7, 1, 1, 1),
(141, 1, 8, 1, 1, 1),
(142, 2, 8, 1, 1, 1),
(143, 3, 8, 1, 1, 1),
(144, 4, 8, 1, 1, 1),
(145, 5, 8, 1, 1, 1),
(146, 6, 8, 1, 1, 1),
(147, 7, 8, 1, 1, 1),
(148, 8, 8, 1, 1, 1),
(149, 9, 8, 1, 1, 1),
(150, 10, 8, 1, 1, 1),
(151, 11, 8, 1, 1, 1),
(152, 12, 8, 1, 1, 1),
(153, 13, 8, 1, 1, 1),
(154, 14, 8, 1, 1, 1),
(155, 15, 8, 1, 1, 1),
(156, 16, 8, 1, 1, 1),
(157, 17, 8, 1, 1, 1),
(158, 18, 8, 1, 1, 1),
(159, 19, 8, 1, 1, 1),
(160, 20, 8, 1, 1, 1),
(161, 1, 9, 1, 1, 1),
(162, 2, 9, 1, 1, 1),
(163, 3, 9, 1, 1, 1),
(164, 4, 9, 1, 1, 1),
(165, 5, 9, 1, 1, 1),
(166, 6, 9, 1, 1, 1),
(167, 7, 9, 1, 1, 1),
(168, 8, 9, 1, 1, 1),
(169, 9, 9, 1, 1, 1),
(170, 10, 9, 1, 1, 1),
(171, 11, 9, 1, 1, 1),
(172, 12, 9, 1, 1, 1),
(173, 13, 9, 1, 1, 1),
(174, 14, 9, 1, 1, 1),
(175, 15, 9, 1, 1, 1),
(176, 16, 9, 1, 1, 1),
(177, 17, 9, 1, 1, 1),
(178, 18, 9, 1, 1, 1),
(179, 19, 9, 1, 1, 1),
(180, 20, 9, 1, 1, 1),
(181, 1, 10, 1, 1, 1),
(182, 2, 10, 1, 1, 1),
(183, 3, 10, 1, 1, 1),
(184, 4, 10, 1, 1, 1),
(185, 5, 10, 1, 1, 1),
(186, 6, 10, 1, 1, 1),
(187, 7, 10, 1, 1, 1),
(188, 8, 10, 1, 1, 1),
(189, 9, 10, 1, 1, 1),
(190, 10, 10, 1, 1, 1),
(191, 11, 10, 1, 1, 1),
(192, 12, 10, 1, 1, 1),
(193, 13, 10, 1, 1, 1),
(194, 14, 10, 1, 1, 1),
(195, 15, 10, 1, 1, 1),
(196, 16, 10, 1, 1, 1),
(197, 17, 10, 1, 1, 1),
(198, 18, 10, 1, 1, 1),
(199, 19, 10, 1, 1, 1),
(200, 20, 10, 1, 1, 1),
(201, 1, 11, 1, 1, 1),
(202, 2, 11, 1, 1, 1),
(203, 3, 11, 1, 1, 1),
(204, 4, 11, 1, 1, 1),
(205, 5, 11, 1, 1, 1),
(206, 6, 11, 1, 1, 1),
(207, 7, 11, 1, 1, 1),
(208, 8, 11, 1, 1, 1),
(209, 9, 11, 1, 1, 1),
(210, 10, 11, 1, 1, 1),
(211, 11, 11, 1, 1, 1),
(212, 12, 11, 1, 1, 1),
(213, 13, 11, 1, 1, 1),
(214, 14, 11, 1, 1, 1),
(215, 15, 11, 1, 1, 1),
(216, 16, 11, 1, 1, 1),
(217, 17, 11, 1, 1, 1),
(218, 18, 11, 1, 1, 1),
(219, 19, 11, 1, 1, 1),
(220, 20, 11, 1, 1, 1),
(221, 1, 12, 1, 1, 1),
(222, 2, 12, 1, 1, 1),
(223, 3, 12, 1, 1, 1),
(224, 4, 12, 1, 1, 1),
(225, 5, 12, 1, 1, 1),
(226, 6, 12, 1, 1, 1),
(227, 7, 12, 1, 1, 1),
(228, 8, 12, 1, 1, 1),
(229, 9, 12, 1, 1, 1),
(230, 10, 12, 1, 1, 1),
(231, 11, 12, 1, 1, 1),
(232, 12, 12, 1, 1, 1),
(233, 13, 12, 1, 1, 1),
(234, 14, 12, 1, 1, 1),
(235, 15, 12, 1, 1, 1),
(236, 16, 12, 1, 1, 1),
(237, 17, 12, 1, 1, 1),
(238, 18, 12, 1, 1, 1),
(239, 19, 12, 1, 1, 1),
(240, 20, 12, 1, 1, 1),
(241, 1, 13, 1, 1, 1),
(242, 2, 13, 1, 1, 1),
(243, 3, 13, 1, 1, 1),
(244, 4, 13, 1, 1, 1),
(245, 5, 13, 1, 1, 1),
(246, 6, 13, 1, 1, 1),
(247, 7, 13, 1, 1, 1),
(248, 8, 13, 1, 1, 1),
(249, 9, 13, 1, 1, 1),
(250, 10, 13, 1, 1, 1),
(251, 11, 13, 1, 1, 1),
(252, 12, 13, 1, 1, 1),
(253, 13, 13, 1, 1, 1),
(254, 14, 13, 1, 1, 1),
(255, 15, 13, 1, 1, 1),
(256, 16, 13, 1, 1, 1),
(257, 17, 13, 1, 1, 1),
(258, 18, 13, 1, 1, 1),
(259, 19, 13, 1, 1, 1),
(260, 20, 13, 1, 1, 1),
(261, 1, 14, 1, 1, 1),
(262, 2, 14, 1, 1, 1),
(263, 3, 14, 1, 1, 1),
(264, 4, 14, 1, 1, 1),
(265, 5, 14, 1, 1, 1),
(266, 6, 14, 1, 1, 1),
(267, 7, 14, 1, 1, 1),
(268, 8, 14, 1, 1, 1),
(269, 9, 14, 1, 1, 1),
(270, 10, 14, 1, 1, 1),
(271, 11, 14, 1, 1, 1),
(272, 12, 14, 1, 1, 1),
(273, 13, 14, 1, 1, 1),
(274, 14, 14, 1, 1, 1),
(275, 15, 14, 1, 1, 1),
(276, 16, 14, 1, 1, 1),
(277, 17, 14, 1, 1, 1),
(278, 18, 14, 1, 1, 1),
(279, 19, 14, 1, 1, 1),
(280, 20, 14, 1, 1, 1),
(281, 1, 15, 1, 1, 1),
(282, 2, 15, 1, 1, 1),
(283, 3, 15, 1, 1, 1),
(284, 4, 15, 1, 1, 1),
(285, 5, 15, 1, 1, 1),
(286, 6, 15, 1, 1, 1),
(287, 7, 15, 1, 1, 1),
(288, 8, 15, 1, 1, 1),
(289, 9, 15, 1, 1, 1),
(290, 10, 15, 1, 1, 1),
(291, 11, 15, 1, 1, 1),
(292, 12, 15, 1, 1, 1),
(293, 13, 15, 1, 1, 1),
(294, 14, 15, 1, 1, 1),
(295, 15, 15, 1, 1, 1),
(296, 16, 15, 1, 1, 1),
(297, 17, 15, 1, 1, 1),
(298, 18, 15, 1, 1, 1),
(299, 19, 15, 1, 1, 1),
(300, 20, 15, 1, 1, 1),
(301, 1, 16, 1, 1, 1),
(302, 2, 16, 1, 1, 1),
(303, 3, 16, 1, 1, 1),
(304, 4, 16, 1, 1, 1),
(305, 5, 16, 1, 1, 1),
(306, 6, 16, 1, 1, 1),
(307, 7, 16, 1, 1, 1),
(308, 8, 16, 1, 1, 1),
(309, 9, 16, 1, 1, 1),
(310, 10, 16, 1, 1, 1),
(311, 11, 16, 1, 1, 1),
(312, 12, 16, 1, 1, 1),
(313, 13, 16, 1, 1, 1),
(314, 14, 16, 1, 1, 1),
(315, 15, 16, 1, 1, 1),
(316, 16, 16, 1, 1, 1),
(317, 17, 16, 1, 1, 1),
(318, 18, 16, 1, 1, 1),
(319, 19, 16, 1, 1, 1),
(320, 20, 16, 1, 1, 1),
(322, 13, 8, 1, 1, NULL),
(323, 13, 8, 1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `cust_id` bigint(20) NOT NULL,
  `create_date` datetime DEFAULT NULL,
  `cust_last_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `cust_first_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `cust_middle_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `ca_id` bigint(20) DEFAULT NULL,
  `land_mark` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`cust_id`, `create_date`, `cust_last_name`, `cust_first_name`, `cust_middle_name`, `ca_id`, `land_mark`, `stat_id`, `user_id`) VALUES
(5, '2025-08-26 13:14:56', 'Tan', 'Vanissa', 'Parido', 323, 'Duol ila Ehnand', 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `customercharge`
--

CREATE TABLE `customercharge` (
  `charge_id` bigint(20) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `application_id` bigint(20) DEFAULT NULL,
  `connection_id` bigint(20) DEFAULT NULL,
  `charge_item_id` bigint(20) NOT NULL,
  `quantity` decimal(12,3) NOT NULL DEFAULT 1.000,
  `unit_amount` decimal(12,2) NOT NULL,
  `total_amount` decimal(14,3) GENERATED ALWAYS AS (`quantity` * `unit_amount`) STORED,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `due_date` date DEFAULT NULL,
  `stat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customercharge`
--

INSERT INTO `customercharge` (`charge_id`, `customer_id`, `application_id`, `connection_id`, `charge_item_id`, `quantity`, `unit_amount`, `created_at`, `due_date`, `stat_id`) VALUES
(1, 5, 3, NULL, 1, 1.000, 50.00, '2025-11-22 12:37:16.000000', '2025-11-22', 1);

-- --------------------------------------------------------

--
-- Table structure for table `customerledger`
--

CREATE TABLE `customerledger` (
  `ledger_entry_id` bigint(20) NOT NULL,
  `txn_date` date NOT NULL,
  `source_type_id` bigint(20) NOT NULL,
  `source_id` bigint(20) NOT NULL,
  `source_line_no` bigint(20) DEFAULT NULL,
  `source_line_no_nz` int(11) GENERATED ALWAYS AS (ifnull(`source_line_no`,0)) STORED,
  `debit` decimal(12,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(12,2) NOT NULL DEFAULT 0.00,
  `user_id` int(11) DEFAULT NULL,
  `stat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customerledger`
--

INSERT INTO `customerledger` (`ledger_entry_id`, `txn_date`, `source_type_id`, `source_id`, `source_line_no`, `debit`, `credit`, `user_id`, `stat_id`) VALUES
(1, '2025-11-22', 2, 1, NULL, 50.00, 0.00, 1, 1),
(2, '2025-11-22', 4, 1, NULL, 0.00, 50.00, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `emp_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) NOT NULL,
  `option_name` varchar(50) NOT NULL,
  `pos_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `landmark`
--

CREATE TABLE `landmark` (
  `lm_id` int(11) NOT NULL,
  `location_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `landmark`
--

INSERT INTO `landmark` (`lm_id`, `location_name`) VALUES
(1, 'Near the river'),
(2, 'Before Echavez Store'),
(3, 'Purok 2 Poblacion '),
(4, 'P-13 Ruiz Comp'),
(5, 'National Highway'),
(6, 'Prk 3A Poblacion near Concepcion Sacay'),
(7, 'Back of INCHS'),
(8, 'INCHS'),
(9, 'P3A Poblacion'),
(10, 'P-3A National Highway'),
(11, 'Slaughterhouse'),
(12, 'P-1 Jampason'),
(13, 'P-3 National Highway'),
(14, 'P-3'),
(15, 'P-3 Back of Leonila Alza'),
(16, 'P-3 Osmeña Extension'),
(17, 'Prk 4 Angelito Balmes'),
(18, 'P-4 Bugwak'),
(19, 'P-3 Bugwak'),
(20, 'Bugwak Nxt to Andrea Culaniban'),
(21, 'Bugwak Nxt to Jessie Galendez'),
(22, 'Bugwak Nxt to Roque bagares'),
(23, 'Bugwak National Highway'),
(24, 'P-4 National Highway'),
(25, 'Quezon St'),
(26, 'Bonifacio St'),
(27, 'P-9 Rizal St'),
(28, 'P-9 Madriaga St'),
(29, 'P-9 Madraiga St'),
(30, 'P-9 Poblacion old Rogelio Acut'),
(31, 'P-9 Burgos Srt'),
(32, 'Tangke Apas'),
(33, 'P-9 Burgos St'),
(34, 'Prk 9 Poblacion near Evelyn Cua'),
(35, 'P-9 Osmeña St'),
(36, 'P-10 National Highway'),
(37, 'P-8 Rizal St'),
(38, 'P-8 National Highway near Intoy Dadang Shop'),
(39, 'P-8'),
(40, 'P-10 '),
(41, 'P-10 Osmeña St'),
(42, 'P-10 Osmeña St'),
(43, 'P-9 Near Acain Neil'),
(44, 'P-2 old Ponciano Sandoval'),
(45, 'P-2 Codilla St'),
(46, 'P-2 '),
(47, 'P-2 Bagares St'),
(48, 'P-2 Taborra St'),
(49, 'P-2 National Highway'),
(50, 'P-8 Codilla St'),
(51, 'P-8 Bonifacio St'),
(52, 'P-8 T Claudio St'),
(53, 'P-8 Rizla St'),
(54, 'P-2 nxt to Carlit Nelson old Dino Padrigori'),
(55, 'P-7 Codilla St (balay)'),
(56, 'P-7 Rizal St'),
(57, 'P-7'),
(58, 'P-7 T Claudio St'),
(59, 'P-7 '),
(60, 'P-2'),
(61, 'Back of GYM'),
(62, 'T Claudio St'),
(63, 'Codilla St'),
(64, 'Codilla cor T Claudio Sts'),
(65, 'Burgos Cor Codilla Sts'),
(66, 'nxt to Fajardo Nieves'),
(67, 'P-7 Baybay'),
(68, 'P11 Codilla Street'),
(69, 'P-11 Codilla St'),
(70, 'P-11 Baybay'),
(71, 'P-11 nxt to Feliciano Dumaog'),
(72, 'Codilla cor Burgos Sts'),
(73, 'Burgos St nxt to Basilisa Jamaca'),
(74, 'P-10 Burgos St'),
(75, 'Burgos St nxt to Asterio Lagas # 1'),
(76, 'P-10 Burgos cor National Highway'),
(77, 'P-10 old Lugille Quirino'),
(78, 'P-10 Codilla St'),
(79, 'P-11 Cesar Magno St'),
(80, 'P-11 del Pilar cor Codilla Sts'),
(81, 'P-11 del Pilar St'),
(82, 'P-11 National Highway - Errol Frank'),
(83, 'P-11 nxt to Enerito Acain Jr'),
(84, 'P-11 Oblina St'),
(85, 'P-13 Ruiz Compd'),
(86, 'P13'),
(87, 'P13 Codilla St'),
(88, 'P-13 Baybay'),
(89, 'P-13 Oblina St'),
(90, 'P-13 National Highway'),
(91, 'P-13 Codilla St'),
(92, 'P-13 Veterans St'),
(93, 'P-13 Ratunil St'),
(94, 'P-13National Highway'),
(95, 'P-13 National Highay'),
(96, 'P13 old Alfeche Levy'),
(97, 'P14 Veterans St'),
(98, 'P-14 National Highway'),
(99, 'P-14 Ratunil St           temp cut-off'),
(100, 'P-14 Magsaysay St'),
(101, ' Poblacion old Rolando Bagares #1'),
(102, 'P14 Acut St Extn'),
(103, 'P-14 Acut St'),
(104, 'P-14 Oblina St'),
(105, 'P14 Galarpe St Pob'),
(106, '2498 Galarpe Street Poblacion'),
(107, 'P14 Galarpe St'),
(108, 'P-14 Veteran St'),
(109, 'P-14 Galarpe Street'),
(110, 'P-14 Back of RBI'),
(111, 'P-14 nxt to Roxas Othelia'),
(112, 'P-14 Back of Roy Aristoteles'),
(113, 'P-10'),
(114, 'Osmeña St'),
(115, 'Burgos St cor Osmeña St'),
(116, 'Burgos St cor Osmeña Sts'),
(117, 'Nxt to Alavanza Hanz'),
(118, 'old Waslo Constancio Oblina St'),
(119, 'Burgos St P10'),
(120, 'P-12 Galarpe St'),
(121, 'P-12 Oblina St old Allan Roxas'),
(122, 'P-12 '),
(123, 'P-12'),
(124, 'Ruiz Compound transfer from Area P'),
(125, 'P12 Burgos Street old Alfonso Yabo'),
(126, 'P-12 Byrgos St'),
(127, 'Tangk Apas'),
(128, 'Nxt to Ellen Perater'),
(129, 'Purok 5 apas'),
(130, 'San Pedro '),
(131, 'P-1 INC Compd'),
(132, 'Beside INC'),
(133, 'San Pedro '),
(134, 'P1 San Pedro'),
(135, 'Kanipaan'),
(136, 'Bless'),
(137, 'P-2 San Pedro Next to Iñigo Gil'),
(138, 'P14 Old Patron Omilao'),
(139, 'P-14 Galarpe'),
(140, 'Tangke near Guzman Penton'),
(141, 'P-14 Oblina Street'),
(142, 'P-10 Burgos St cor National Highway'),
(143, 'old Esther Tupag'),
(144, 'SP nxt to Diomedes Tacbobo'),
(145, 'P-13 nxt to Rochelle Galarroza'),
(146, 'San Pedro National Highway'),
(147, 'P-13 Ratunils Compound'),
(148, 'Ratunils Compound'),
(149, 'P-13 near Elbert Ratunil'),
(150, 'near Elbert Ratunil'),
(151, 'Lawis nxt to Maria Librada'),
(152, 'P-13 nxt to Jesusa Paulin'),
(153, 'P-12 Oblina Street'),
(154, 'Bugwak nxt to Ester Peroso'),
(155, 'P1 San Pedro old Shammah'),
(156, 'Roxas Carmelino Jr'),
(157, 'Tangke near A Acut'),
(158, 'Amarga St Initao Mis Or old Belen Magas'),
(159, 'P-14 Galarpe Street Poblacion'),
(160, 'P-12 old Ruperto Amen'),
(161, 'P-10 nxt to Corazon Melendez'),
(162, 'Gimangpang Initao'),
(163, 'P14 Near Mabini Kent'),
(164, 'P-14 Near Sun Tower'),
(165, 'P-1 Lawis  near Nanette Bagares'),
(166, 'P2 nxt to Placida Jagonal'),
(167, 'P12 nxt to Borling'),
(168, 'Initao Public Market'),
(169, 'P2 Near Placida Jagonal'),
(170, 'P9 Burgos St'),
(171, 'P12 Galarpe St nxt to Randy Bagares'),
(172, 'Lawis Nxt to Jose Apora'),
(173, 'P-10 nxt to Eusebio Jalagat'),
(174, 'Prk 8 Poblacion near Linog'),
(175, 'Lawis Next to  Tacbobo Gilbert'),
(176, 'P-9 Next to Ellys Learning Center'),
(177, 'P-10; Back of Galarpe Hermenia'),
(178, 'P12 Galarpe St near Buhian Aurora'),
(179, 'P-11 Back of Initao District Jail'),
(180, 'BLISS near Dajis Reynaldo'),
(181, 'Cemetery c/o Edna Monceda near Tacbobo Diomedes'),
(182, 'P-14 near Tacbobo Cesar'),
(183, 'P-11 near Carmen Lacsi'),
(184, 'P14 near Jecebel Dagsaan'),
(185, 'Cemetery c/o Edna Monceda'),
(186, 'P-3 Near Taer Septempia'),
(187, 'P-1 San Pedro; Near Fermina Fedelis'),
(188, 'Ruiz compound'),
(189, 'Bugwak ( near Emma Balaba)'),
(190, 'Lawis nxt to Arnel Lagrada old Godelia Pacuño'),
(191, 'P-12 Galarpe Street'),
(192, 'P-13 Ratunil Comp'),
(193, 'P-14  Pob Initao ( Next to Apao Eduardo)'),
(194, 'P-7 (near St Roque Church)'),
(195, 'San Pedro nxt to Victoria Galarroza'),
(196, 'BLISS nxt to David Roxas'),
(197, 'Pob Initao'),
(198, 'Pob Initao Mis Or'),
(199, 'Purok 3 Poblacion back of GALEON'),
(200, 'Lawis nxt to Esther Villahermosa'),
(201, 'P 1 San Pedro old Anecito Magas'),
(202, 'P14 Galarpe St'),
(203, 'P14 nxt to Lolito Sacay'),
(204, 'P13 Pob (next to Cheryl Ogdol)'),
(205, 'P 14 National Highway'),
(206, 'P1 San Pedro nxt to J Pineda'),
(207, 'P1 nxt to Jospeh Pineda'),
(208, 'P3A Next to  Leny Jacalan'),
(209, 'Bugwak nxt to Madula Severo'),
(210, 'P 13 near lapuerta'),
(211, 'Lawis nxt to Janolino Juvie'),
(212, 'Lawis nxt to Cuerpo Jesusa'),
(213, 'P3A back of Galarpe Lilia'),
(214, 'National Highway P14 Veterans Strret'),
(215, 'P13 Jog Pawnshop'),
(216, 'P-12 Oblina St (next to Gamo Jaime)'),
(217, 'P 14 Pob (next to Illuminado Sy)'),
(218, 'P-14 next to Mabini kent'),
(219, 'P 13 near Echavez ritchie Ray'),
(220, 'P-12 Pob'),
(221, 'BLISS San Pedro'),
(222, 'Ratunil Comp (next to Ebesa Nelsie)'),
(223, 'P14 National Highway'),
(224, 'Bugwak nxt to Septemia Taer'),
(225, 'P-2 San Pedro Back of Alliance Church'),
(226, 'P 13 back of Aleja Go'),
(227, 'P14 nxt to Catiloc Evelyn'),
(228, 'P3A nxt to Pacanut Medalyn'),
(229, 'P3a nxt to Pacanut Medalyn'),
(230, 'Lawis next to Cruza Oledan'),
(231, 'P-7 Old Arlene Manal '),
(232, 'P11 Baybay'),
(233, 'P13 Ruiz Cmpound nxt to Daylinda Wabe'),
(234, 'P-12 Near Cesar Bulaybulay'),
(235, 'P-1 San Pedro near  Zernick Galarpe'),
(236, 'P-1 Lawis next to Camaro Josephine'),
(237, 'P14 nxt to Roger Tupag'),
(238, 'P2 near Mojica Pablo'),
(239, 'P2 nxt to Mojica Pablo'),
(240, 'P-3 next to Jarales Pacifico'),
(241, 'P-11 Natl Highway'),
(242, 'P11 Cesar T Magno St'),
(243, 'P9 near Acain Felicidad'),
(244, 'P-7 nxt to Consorcia Abilo'),
(245, 'P12 nxt to R Magdadaro'),
(246, 'Bugwak nxt to Quitoriano Manuel'),
(247, 'near Vilma Cantiga P-2'),
(248, 'P13 nxt to Abragan Vivien Luz'),
(249, 'P14 Ratunil St'),
(250, 'P14 Ratunil St cor Acut St'),
(251, 'P- 14 (next to Caballero Josefa)'),
(252, 'Bugwak nxt to Ocampo Saturnino'),
(253, 'Bugwak'),
(254, 'Osmeña St near MadulaSevero'),
(255, 'P1 San Pedro Magsaysay St'),
(256, 'P-11 Oblina St'),
(257, 'P3A National Highway near Epifanio Echavez'),
(258, 'P14 nxt to Fely Bagares'),
(259, 'P1 San Pedro near Galarpe Leonito'),
(260, 'P12 Galarpe St old Melvin Amen'),
(261, 'P3A Dike Poblacion'),
(262, 'Ratunils Compound (near Agustina Plaza)'),
(263, 'Osmena St P10'),
(264, 'P14 Acut St'),
(265, 'P13 Ruiz Compd'),
(266, 'P13 Veterans St'),
(267, 'P13 Codilla St'),
(268, 'P13 ALFONS'),
(269, 'P13 Magsaysay St'),
(270, 'P14 Acut St'),
(271, 'INCHS Canteen'),
(272, 'Purok 12 next to Orestes Dadang'),
(273, 'P11 Codilla St near Acain Beatriz'),
(274, 'P12 nxt to Maricel Borbon'),
(275, 'P14 Roxas St'),
(276, 'P10 National Highway'),
(277, 'San Pedro'),
(278, 'P10 back of Balido Alice'),
(279, 'Lawis near Tacio Sacay'),
(280, 'Ruiz Compd'),
(281, 'INCHS-Ms Jagonal'),
(282, 'P-1 San Pedro ( next to Waga Mira)'),
(283, 'Ratunil Compd'),
(284, 'P-9 nxt to Irving Jalagat'),
(285, 'P13 Codilla St near Echavez Renato'),
(286, 'Bugwak near Nimfa Mutia'),
(287, 'Codilla St nxt to Fajardo Alenie'),
(288, 'P-11 near Lobendina Edwin'),
(289, 'Back of Gym nxt to Entria Alfredo'),
(290, 'Magsaysay St'),
(291, 'Acut St nxt to Paulino Autentico'),
(292, 'Lawis nxt to Ragmac Inecita'),
(293, 'P2 old Quitoriano Uldarico'),
(294, 'Purok 2'),
(295, 'nxt to Odon Echavez'),
(296, 'P14 Galarpe St nxt to L Bote'),
(297, 'P1 San Pedro near Herculano Galarpe'),
(298, 'P-11 nxt to Osias Janolino'),
(299, 'P14 near Rosemarie Dandasan'),
(300, 'P-11'),
(301, 'BLISS nxt to Aleja Amarga'),
(302, 'P-11 National Highway'),
(303, 'P9'),
(304, 'P4 Bugwak'),
(305, 'P9 nxt to Leonardo Dandasan'),
(306, 'Prk 2B Jampason'),
(307, 'Prk 3 Jampason'),
(308, 'Prk 4A Jampason'),
(309, 'Jampason'),
(310, 'Prk 3 Jampason old Abundio Tacbobo'),
(311, 'Prk 3 Jampason old Edgardo Tacbobo'),
(312, 'Ruiz Compd'),
(313, 'P14 Oblina St nxt to Wabe Evelyn'),
(314, 'nxt to Galarrita Andrew'),
(315, 'Bugwka nxt to Bundaug'),
(316, 'P 14 Galarpe St'),
(317, 'Dulag'),
(318, 'Zone 10 Corner Codilla-Magno Sts'),
(319, 'P-10 Corner Codill- Magno Sts'),
(320, 'P4-A Jampason'),
(321, 'San Pedro nxt to Mira Waga'),
(322, 'Prk 1A Jampson'),
(323, 'Prk 1A Jampason old Clarissa Ubanan'),
(324, 'Prk 1B Jampason'),
(325, 'Prk 2A Jampason'),
(326, 'Prk 2A Jampason near Mars Jabien'),
(327, 'Jamaca Compound Jampason'),
(328, 'Prk 4B Jampason'),
(329, 'P12'),
(330, 'Burgos St'),
(331, 'Bagares St nxt to Asian Bites'),
(332, 'P9 old Balbon Jennifer'),
(333, 'Prk 3 BLISS SP'),
(334, 'Jampason near Jampason Elem School'),
(335, 'Prk 2A Jampason near IC'),
(336, 'P1 San Pedro nxt to Mira Waga'),
(337, 'Ruiz Compound'),
(338, 'P2 nxt to Jerry Entia'),
(339, 'P2 Kanipaan'),
(340, 'P9 Burgos-Osmeña Sts'),
(341, 'P1 National Highway cor Magsaysay St'),
(342, 'Tangke'),
(343, 'P12 nxt to Josefa Tacbobo'),
(344, 'P14 nxt to Jessie Asidre'),
(345, 'P14 Veteran St'),
(346, 'SP BLISS back of Carmona Res'),
(347, 'P7 near Palapos Lorna'),
(348, 'P9 National Highway'),
(349, 'P-9 '),
(350, 'P14'),
(351, 'P2 nxt to Nelson Carlit'),
(352, 'Prk 2 Jampason'),
(353, 'P13 nxt to Jimmy Redondo'),
(354, 'P1A Kamarin'),
(355, 'P1A-Jampason'),
(356, 'National Highway P13'),
(357, 'Prk2B-Jampason'),
(358, 'Prk 2B'),
(359, 'Prk3 near BrgyHall'),
(360, 'P14 near Madelicious'),
(361, 'P14  Near Bagares Pedrita'),
(362, 'Prk 1A Jampason'),
(363, 'Ruiz Comp near Candelanza'),
(364, 'Lawis'),
(365, 'P3A'),
(366, 'P3A nxt to U-Fe Berjame'),
(367, 'P11 nxt to Alma Payusan'),
(368, 'P3A near Lilia Galarpe'),
(369, 'Jampason P-1A back of New Municipal Hall'),
(370, 'P1A Jampason'),
(371, 'Prk 1 San Pedro'),
(372, 'P4 near Amper res'),
(373, 'Prk 3A near Berjame'),
(374, 'P3 nxt to Salvador Patlunag old Oliva Galgao'),
(375, 'P2A near Tacbobo Joana'),
(376, 'Bugwak near Galarpe Domison'),
(377, 'P14 near Addie Quijano'),
(378, 'P6 Jampason'),
(379, 'Purok 3B near tumapon florita'),
(380, 'Prk 6 Jampason'),
(381, 'P3A near Uy Marcelo'),
(382, 'P-11 Codilla St'),
(383, 'Jampason near Tacbobo Banjamin'),
(384, 'P6 '),
(385, 'BLISS'),
(386, 'Enok Fastfoosd'),
(387, 'P4B near Pabilona Deby'),
(388, 'Prk1A'),
(389, 'P14 National High way'),
(390, 'P13 near East Coast'),
(391, 'Lawis near Dumaran Geralda'),
(392, 'Lawis near Oedan Cruza old Joy Caagbay'),
(393, 'P6 near Justina Tacbobo'),
(394, 'P9 near Acain Rofe'),
(395, 'near Myrna Quilab'),
(396, 'Purok 4A Jampason'),
(397, 'Prk 2A near Carmelita Jabien'),
(398, 'Prk 2B near Gaid Elsie'),
(399, 'P12 Oblina St'),
(400, 'P9 Burgos St near Dahlia Telen'),
(401, 'Prk 1A'),
(402, 'SP National Highway'),
(403, 'near dela pena renelyn'),
(404, 'P14 Pob'),
(405, 'P3A near Pacut Nermalyn'),
(406, 'nxt to Alavanza'),
(407, 'Prk 2A'),
(408, 'P2b Near Jamaca Nestor'),
(409, 'P14 nxt to Caballero Josefa'),
(410, 'Bugwak near Villanueva Excel'),
(411, 'Prk 2B near Bulalahos'),
(412, 'P12 National Highway'),
(413, 'P12 Oblina Street'),
(414, 'P14 near Princess Acut'),
(415, 'Prk 2A near Marivic Galarpe'),
(416, 'Prk 2A near Quimada Vicenta'),
(417, 'Prk 4A'),
(418, 'Virginia Jabien'),
(419, 'Pk 2 Jampason'),
(420, 'P2A'),
(421, 'P-9 Poblacion'),
(422, 'P2B'),
(423, 'Jampason (purok-3)'),
(424, 'P1A Jampason National Highway'),
(425, 'P7 nxt to dofeliz florence'),
(426, 'P-11 old Janolino Osias I-36'),
(427, 'P 9 Burgos cor ICS'),
(428, 'P1 San Pedro near Roxas Diophantus'),
(429, 'P2 San Pedro Kanipaan'),
(430, 'P3A Jampason'),
(431, 'P1B Jampason'),
(432, 'P9 near Milquiades Mingoy'),
(433, 'P1A Jampason near Mila Apao'),
(434, 'Lawis near Yting Guadalupe'),
(435, 'P1A'),
(436, 'National Hi - way P8'),
(437, 'P9 Burgos St near Ariel Glaroza'),
(438, 'P9-Amarga St near Macalutas Rogelio'),
(439, 'Bugwak near Galendez'),
(440, 'P8 nxt to Caburatan Purita'),
(441, 'Purok 12 National highway'),
(442, 'Purok 3 near Baquero Marina'),
(443, 'P1b near Ybañez Richel'),
(444, 'P1A near Poblete Noel'),
(445, 'P11 Codilla St'),
(446, 'Prk 3A Bugwak'),
(447, 'P7'),
(448, 'P 10 National Highway'),
(449, 'P13 back of Petron'),
(450, 'P1 Lawis '),
(451, 'Lawis old Angelina Entia'),
(452, 'Prk 2A near Patlunag Doyjun'),
(453, 'P8 National Highway'),
(454, 'Lawis near Meñales Jocelyn'),
(455, 'Jampason near Teresita Tacbobo'),
(456, 'Tubigan'),
(457, 'ICS'),
(458, 'P1B near Bañado Ana Fe'),
(459, 'Prk 2A near Cesar Jabien'),
(460, 'P3A near Catholic Church'),
(461, 'P3A near Alavanza Jose'),
(462, 'Prk 2B near James Nabangue'),
(463, 'P1A near Obut'),
(464, 'Prk 1A near Obut'),
(465, 'P13 near Ruth Pacana'),
(466, 'Prk 1B'),
(467, 'P13 National Highway '),
(468, 'Prk 2B Jampason near Nabangue James'),
(469, 'Jampason near Rudy Aguilar'),
(470, 'P2AJampason near Melito Madula '),
(471, 'P14 near Mabini Kent'),
(472, 'P7 near Amancio Monalisa'),
(473, 'Prk2A nxt to Virginia Jabien'),
(474, 'Prk2B next to Gaid Elsie'),
(475, 'Prk 3 nearb Jaime Jermelyn'),
(476, 'P14 Back of Mormon'),
(477, 'P3B near Martha Paderanga'),
(478, 'P3A near Catiil Terence Joy'),
(479, 'P11 near Ratunil Nida'),
(480, 'P2a near Jeralyn Paclibar'),
(481, 'near Pacut Felda'),
(482, 'P3A Jampason near Cleta Diaz'),
(483, 'Prk 3A'),
(484, 'Prk 3A Jampason'),
(485, 'Prk 3B near Brgy Hall'),
(486, 'Prk 13 Poblacion near Renato Echavez'),
(487, 'Prk 3B Jampason'),
(488, 'Prk 3B Jampason near Paderanga Martha'),
(489, 'Prk 3B'),
(490, 'Prk 3A Jampason near Allan Patlunag'),
(491, 'Prk 3B Jampason near Martha Paderanga'),
(492, 'Prk6Jampason near Tacbobo Segundina'),
(493, 'Prk2 Poblacion  near Gabriel Patlunag'),
(494, 'Prk1A Sn Pedro near Cabil Soliven'),
(495, 'P12 Poblacion near Ernita Camaro'),
(496, 'Prk 3A Poblacion'),
(497, 'Prk 2 Poblacion Initao'),
(498, 'Prk 7 Poblacion near Saavedra'),
(499, 'Lawis near Rosita Briones'),
(500, 'Prk1 Lawis near the Bridge'),
(501, 'INITAO CENTRAL SCHOOL'),
(502, 'Jampason near Martha Paderanga'),
(503, 'Prk 14 near Joel Calledo'),
(504, 'Prk1 Lawis near Lacapag Reynaldo'),
(505, 'Prk4 Bugwak near Ronnie Vedra'),
(506, 'Prk2B Jampason near Jorlyn Velasco'),
(507, 'Prk 4B Jampason near Vernie Pongo'),
(508, 'Prk 1A Jampason near Cristita Ratunil'),
(509, 'Prk 3A Poblacion '),
(510, 'Jampason near oliva galgao'),
(511, 'Gimangpamg'),
(512, 'Food Court N-2'),
(513, 'Food Court N-3'),
(514, 'Food Court N-5'),
(515, 'Food Court N-6'),
(516, 'P-4B Jampason -near Jobo Limbaco'),
(517, 'Food Court N-7'),
(518, 'Food Court N-8'),
(519, 'Food Court N-9'),
(520, 'Food Court N-11'),
(521, 'Food Court N-13'),
(522, 'Food Court N-14'),
(523, 'Food Court N-15'),
(524, 'Food Court N-16'),
(525, 'Food Court N-18'),
(526, 'Food Court N-19'),
(527, 'Food Court N-20'),
(528, 'Food Court N-21'),
(529, 'Food Court'),
(530, 'Food Court N-24'),
(531, 'Food Court N-17'),
(532, 'Food Court N-10 - old Tessie Cabillan'),
(533, 'Food Court N-12'),
(534, 'Food Court N-1'),
(535, 'Prk 13 Ruiz Cmpd near Ramona Pacanut'),
(536, 'Purok 14 near Rito Vedra'),
(537, 'Lawis Pob Initao'),
(538, 'Prk 3 National Highway'),
(539, 'Purok 3A Jampason Initao'),
(540, 'Prk 9 Poblacion near Gerry Sancho'),
(541, 'Poblacion Initao near Maning Echavez'),
(542, 'Brgy Jampason'),
(543, 'BLISS-San Pedro Initao Mis Or'),
(544, 'Prk 14 near Eduardo Apao '),
(545, 'Prk 9 Poblacion near Jason Jalagat'),
(546, 'Prk 4 Jampason'),
(547, 'Prk14 Poblacion near Muriel Balasabas'),
(548, 'Prk 1A Jampason near Philip Obut'),
(549, 'Prk 3A Jampason near Olive Galgao'),
(550, 'Prk 4 Poblacion near Cancleto Ratunil'),
(551, 'Prk 7 Poblacion'),
(552, 'Prk 1B Jampason near Loreto Bañado'),
(553, 'Prk 13 Codilla St Poblacion'),
(554, 'Prk 6 Jampason near Justina Tacbobo'),
(555, 'Prk 9 Poblacion near SDA'),
(556, 'Prk 3 BLISS San Pedro'),
(557, 'Prk 1 Lawis Pobalcion near Juvy Janolino'),
(558, 'Prk 14 Poblacion near Rosemarie Dandasan'),
(559, 'Prk 1 San Pedro back INC'),
(560, 'Prk 1 San Pedro old Shammah'),
(561, 'Prk2A Jampason near Carmelita Jabien'),
(562, 'Jampason boundary Tubigan'),
(563, 'Prk 4A Jampason near Jeffrey Agbalog'),
(564, 'Prk 1 Jampason near Adilie Poblete'),
(565, 'Prk 1 Jampason near Gasmebel Villa'),
(566, 'Prk 4A Jampason '),
(567, 'Prk 1 Lawis'),
(568, 'Prk 13 Poblacion Ruiz Comp'),
(569, 'Prk3A Poblacion'),
(570, 'N-4  Food Court'),
(571, 'Prk 9 Poblacion'),
(572, 'Prk 13 Poblacion Ruiz Cmpd'),
(573, 'Prk 14 back of Wilson Quince'),
(574, 'P11 Magno St'),
(575, 'Prk 11 beside IDH'),
(576, 'Prk 1 San Pedro near Fermina Fedelis'),
(577, 'Prk 3B Jampason near reservoir'),
(578, 'Prk 2B Jampason near Miguel Bulalahos'),
(579, 'P4 near Maturan'),
(580, 'Prk 2 Kanipaan San pedro near Loreta Sabellano'),
(581, 'Prk 13 Ruiz Cmpd Poblacion'),
(582, 'Prk 3B Jampason near Ybañez Leoncio'),
(583, 'Prk 12 Poblacion'),
(584, 'Prk 2 Poblacion near Dino Padrigori'),
(585, 'Prk 6 Jampason near Allan Bulaybulay'),
(586, 'Jampason near Diaz Jerry'),
(587, 'Prk 3A Jampason near Emily Odiada'),
(588, 'Prk 2A Jampason near Denino Paclibar'),
(589, 'Prk 3A Jampason near Charry Tacbobo'),
(590, 'Prk 4B Jampason near Lacno Leosita'),
(591, 'Prk 3 Poblacion near Cenon Dulalas'),
(592, 'Vegetable SecPublic Market'),
(593, 'Prk 12 Poblacion near Diego Janog'),
(594, 'Prk 1 San Pedro menteryo near SOMO'),
(595, 'Prk 1 Lawis Initao Mis Or'),
(596, 'Prk 4B Jampason near Susana Egania'),
(597, 'P3b near Villahermosa Maribeth'),
(598, 'Prk 13 Poblacion back of MORESCO'),
(599, 'P4 near Sidano'),
(600, 'Prk 14 Poblacion near Mateo Caco'),
(601, 'Prk 4B Jampason'),
(602, 'Prk 14 Poblacion Initao near Susan Galarpe'),
(603, 'Food Court '),
(604, 'Prk 2A Jampason near Georgia Baruc'),
(605, 'Prk 4A Jampason near Mila Amper'),
(606, 'Tangke Initao near Belen Pontemayor'),
(607, 'Prk 1 San Pedro near Mira Waga'),
(608, 'Prk 10 Poblacion near Condor Malou'),
(609, 'Lawis Initao near Iresh Janolino'),
(610, 'Prk 1 Jampason near Kagayhaan Holdings'),
(611, 'Prk 14 Poblacion near Marino Lacapag'),
(612, 'Prk 2 Poblacion near Halibas Leonora'),
(613, 'Prk 14 Poblacion near Hermingola Asidre'),
(614, 'N-26 Food Court'),
(615, 'Prk 12 Poblacion near Sharon Tacbobo'),
(616, 'Prk 11 Poblacion near Masi'),
(617, 'Prk 3B Jampason near Mely Basar'),
(618, 'Prk 11 Poblacion '),
(619, 'Prk 11 Poblacion near Helen Patlunag'),
(620, 'Prk 4A Jampason near Marife Nabangue'),
(621, 'Prk 4 Bugwak Poblacion near Larry Lim'),
(622, 'Prk 4B Jampason Bondoy Cmpd'),
(623, 'Prk 3 Bugwak Poblacion old Erlinda Janog'),
(624, 'Prk 3A Poblacion near Logem Castillon'),
(625, 'Prk 3 Poblacion near Leonila Alza'),
(626, 'Prk  14 Poblacion near Senior Citizen Bldng'),
(627, 'Initao Central School 2 Storey Bldng'),
(628, 'Prk 3A Jampason near Mercy Grace Acain'),
(629, 'Prk 11 Poblacion near Erico Capinpuyan'),
(630, 'Prk 1 Jampason near Bagares Clunest'),
(631, 'Prk 12 Poblacion near Melvin Amen'),
(632, 'Prk 4A Jampason near Arce Pasco'),
(633, 'Prk 1 San Pedro beside Iglesia ni Cristo'),
(634, 'Jampason Micmic'),
(635, 'Prk 12 Poblacion near Virgilio Mertalla'),
(636, 'Prk 1 San Pedro in front of Joel Sulita'),
(637, 'Prk 1 San Pedro'),
(638, 'Prk 1 San Pedro Initao'),
(639, 'Bugwak near Emilia Tacbobo'),
(640, 'Prk 1 Lawis near Mabesa Cuerpo '),
(641, 'Prk 1 San Pedro near Dennis Magsayo'),
(642, 'Prk 6 Jampason near Leosita Lacno'),
(643, 'Prk 4B Jampason near Antonio Tacbobo'),
(644, 'Bugwak in front of INCHS'),
(645, 'Bugwak near Santiago Jarales'),
(646, 'Bugwak Poblacion near Taer'),
(647, 'Prk 3 Bugwak Poblacion near TRuth Chavit'),
(648, 'Prk 12 Poblacion near Rosalinda Buhian'),
(649, 'Prk3A Jampason near Kag Gagay Acain'),
(650, 'Prk 2A Jampason near Initao College'),
(651, 'Prk 2A Jampason near IC old Lucita Sombilon'),
(652, 'Prk 14 Poblacion near OGIS'),
(653, 'Prk 14 Poblacion near Walter Galarroza'),
(654, 'Prk 2A Jampason near Genie Jabien'),
(655, 'Prk14  Poblacion'),
(656, 'Prk 2A Jampason near Salvador Patlunag'),
(657, 'PETRON Initao Mis Or'),
(658, 'Prk 3A Poblacion near Eufemia Berjame'),
(659, 'Prk 11 Poblacion near Esther Pasague'),
(660, 'Prk 2A Jampason '),
(661, 'Prk 3B Jampason near Resily Sacay'),
(662, 'Prk 2A Jampason near Resily Sacay'),
(663, 'Prk 2B Jampason near Jam Elem School'),
(664, 'Prk 1 Jampason near Carolyn Jalagat'),
(665, 'Lawis near Elena Gera'),
(666, 'Prk 3A Jampason in front of Emily Odiada'),
(667, 'N-5 Food Court'),
(668, 'Prk 1 San Pedro near Cesinia Galarroza'),
(669, 'Prk 6 Jampason near Luisita Lacno'),
(670, 'Lawis near Lydia Fajardo'),
(671, 'Prk 14 Poblacion near Eddie Mutya'),
(672, 'Prk 13 Ruiz Compound'),
(673, 'Prk 4 Poblacion near Alter Ratunil'),
(674, 'Poblacion near Ernesto Taleon'),
(675, 'Lawis near Aida Entia'),
(676, 'Prk 1B Jampason near Carolyn Jalagat'),
(677, 'Lawis near Lorena Ladera'),
(678, 'Prk 2 Poblacion beside Manuel Alavanza'),
(679, 'Prk2 Poblacion near Nora Aliñabon'),
(680, 'Prk 14 Pobacion near Eddie Mutya '),
(681, 'Prk 7 Poblacion near Merry Grace Amancio'),
(682, 'Prk 13 Poblacion near Vicente Ferrer Acain'),
(683, 'Prk 11 Poblacion near Auring'),
(684, 'Prk 13 Poblacion Ruiz Compound '),
(685, 'Prk 2A Jampason near Modesa Jabien'),
(686, 'Prk 1A Jampason near Emma Bogo'),
(687, 'Prk 13 Poblacion near MORESCO paying stn'),
(688, 'Prk 13 Ratunils cmpd near Elbert Ratunil'),
(689, 'Prk 11 Poblacion near Analyn Otom'),
(690, 'Prk 1 Lawis near Rogelio Sandoval'),
(691, 'Prk 9 Poblacion Initao'),
(692, 'Prk 9 Poblacion near Alma Laput old Thelma Jarlata'),
(693, 'Prk 12 Poblacion near Pretex Dwight Palasan'),
(694, 'Prk 12 Poblacion near Randy Bagares'),
(695, 'Prk 14 Poblacion near Concepcion Banogon'),
(696, 'Poblacion near Esther Pasague'),
(697, 'P14 Galarpe Street Poblacion'),
(698, 'Prk 2B near Susan Belandres'),
(699, 'Prk 2 back of Evangeline Sanchez'),
(700, 'Prk 1B Jampason near Mary Ann Ladua'),
(701, 'Prk 12 Poblacion near Berma Catapang'),
(702, 'Prk 3A Poblacion near Linda Vedra'),
(703, 'Prk 14 Poblacion near Pacultad Crisanto'),
(704, 'Prk 3A near Alavanza Ebella'),
(705, 'Prk 2 Poblacion near Betsy Rose Culi'),
(706, 'Prk 3A Poblacio near Takindingan Isidro'),
(707, 'Prk 1 Lawis near Jocelyn Meñales'),
(708, 'Prk 1 Lawis near Juvy Janolino'),
(709, 'Prk 1 Lawis near Sarah Sayson'),
(710, 'Prk 8 near Fausto Ubanan'),
(711, 'Prk 4B Jampason near Dexter Ubagan'),
(712, 'Prk 2 Poblacion near Alfredo Entia'),
(713, 'Prk 1 San Pedro near Dioscoro Valiente'),
(714, 'Prk 3A Jampason near Gagay Acain'),
(715, 'Prk 8 Poblacion near Fausto Ubanan'),
(716, 'Prk 1A San Pedro near Almira Dalumbar'),
(717, 'Prk 2 Poblacion near Martina Tiu'),
(718, 'Prk 2A Jampason near Marivic Galarpe'),
(719, 'Prk 14 Poblacion near Barrymore Roa'),
(720, 'Prk 11 Poblacion near Sabina Villegas'),
(721, 'Prk 13 Ruiz Compound near Ritchie Roa'),
(722, 'Prk 13Ruiz Compound near Ritchie Roa'),
(723, 'Prk 1 San Pedro near Carmen Febria'),
(724, 'Prk 7 back at GYM'),
(725, 'Prk 13 Poblacion old Rodulfo Gaid'),
(726, 'Prk 1 San Pedro near Sarah Veronilla #2'),
(727, 'Prk-13 Poblacion beside Fire Station'),
(728, 'Prk 3 Jampason near Emily Odiada'),
(729, 'Prk 1A San Pedro near Amy Dalumbar'),
(730, 'Prk 2A Jampason near Edwin Patlunag'),
(731, 'Prk 9 Poblacion near Mamerto Abueva'),
(732, 'N-4  Food Court'),
(733, 'Prk 1B Jampason in front of Richel Ybañez'),
(734, 'Prk 13 Poblacion near Edna Edubos'),
(735, 'Prk 4A Jampason'),
(736, 'Prk 14 Poblacion near M Lhuillier'),
(737, 'Jampason Initao College'),
(738, 'Prk 1B Jampason near Ritchel Ybañez'),
(739, 'Prk 9 Poblacion near Doreen Tinai'),
(740, 'Prk 1 Lawis near Nida Behagan'),
(741, 'Prk 9 Poblacion old Rogelio Macalutas '),
(742, 'Lawis near Amor Moreno'),
(743, 'Prk 11 Poblacion beside Hospital'),
(744, 'San Pedro near Soliven Cabil old Elaine Sarmiento'),
(745, 'P12 Poblacion Tangke'),
(746, 'Prk 11 Poblacion near Bernardo'),
(747, 'Prk 14 Poblacion near Balasabas'),
(748, 'Prk 2C Jampason near Alan Galarroza'),
(749, 'Prk 4A Jampason near Nena Nabangue'),
(750, 'Prk 13 Poblacion near Ritchie Echavez'),
(751, 'Prk 3A Poblacion old Jerry Ogdol'),
(752, 'Prk 14 Poblacion old Binbenido Abellanosa'),
(753, 'Prk 3B Jampason near Allan Roxas'),
(754, 'Prk 9 Poblacion near Danilo Amarga'),
(755, 'Prk 3 Jampason near Gagay Acain'),
(756, 'Prk 2B Jampason near Brgy Health Center'),
(757, 'Prk 1A Jampason near Analyn Ucab'),
(758, 'Prk 2C Jampason near Alan Galaroza'),
(759, 'Prk 3B Jampason near covered court'),
(760, 'Prk 3B Jampason near Wendell Janog'),
(761, 'Prk 2 Poblacion near Lourdes Sandoval'),
(762, 'Prk 6 Jampason near Tomasito Pongo'),
(763, 'Prk 9 Poblacion old Leo Acain #1'),
(764, 'beside Alicia Echavez'),
(765, 'Prk 12 Poblacion near Minerva Balaba'),
(766, 'prk 2 Poblacion near Melita Monceda'),
(767, 'Prk 13 Poblacion near Big Js Breadhauz'),
(768, 'Prk 1 San Pedro back of INC'),
(769, 'Prk 14 near Day Care Center'),
(770, 'Prk 6 Jampason back of Dryer near JoBo'),
(771, 'Prk 14 Poblacion near Jecebel Dagsaan'),
(772, 'Prk 14 near Cebuana'),
(773, 'Prk 4A Jampason near Helen Veloz'),
(774, 'Ruiz Cmpd Poblacion'),
(775, 'Prk 1 San Pedro near Alistair Pacana'),
(776, 'Prk 2B Jampason near Darling Nabangue'),
(777, 'Prk 1A Jampason near Martina Ratunil'),
(778, 'Prk 3A Jampason near Ernesto Jamaca'),
(779, 'Prk 14 Poblacion near Evelyn Catiloc'),
(780, 'Prk 13 Poblacion near EAST COAST'),
(781, 'Bugwak near Gorgonia Alburo'),
(782, 'Prk 2A Jampason near Riza Fe Maagad'),
(783, 'Food Court N-!7'),
(784, 'Food Court N-13'),
(785, 'Prk 4 Bugwak old Cristina Legaspi'),
(786, 'Prk 2 near Nora Aliñabon'),
(787, 'Public Mkt near Motorboat Terminal'),
(788, 'Prk 14 Ace Gas Station'),
(789, 'Prk 6 Jampason near Bobby Bañado'),
(790, 'Lawis near Cresilda Meñales'),
(791, 'Prk 9 Poblacion near Belen Pontemayor'),
(792, 'Prk 6 Jampason near Cristita Ratunil'),
(793, 'Prk 3A Poblacion beside Felda Pacut'),
(794, 'Prk 12 Poblacion back of ICS'),
(795, 'Prk 2 Poblacion near Lorna Belida'),
(796, 'Prk 3A Poblacion near Loida Alavanza'),
(797, 'Prk 9 old Jovenciano Saluna'),
(798, 'Prk 3A Jampason near Rosie Quijano'),
(799, 'P2A Jampason near Georgia Baruc'),
(800, 'Lawis Poblacion near Emie Behagan'),
(801, 'Prk 10 Poblacion near Tony Pupos'),
(802, 'P-9 Madriaga Street'),
(803, 'P-13 Oblina Street Poblacion'),
(804, 'Lawis near Marivic Sandoval'),
(805, 'Prk 8 Poblacion beside PICC'),
(806, 'Lawis near Frisco Ubanan'),
(807, 'Purok 4 near UCCP'),
(808, 'Ruiz Comp Purok 13 Pob'),
(809, 'P-1 Lawis (near Juvy Janolino)'),
(810, 'Duknayon near Hilarion Bongcalon'),
(811, 'Prk 2A Jampason near Balaba Sita'),
(812, 'Prk 2A Jampason near Tracy Ubanan'),
(813, 'Prk 13 near Market old Emy Echavez'),
(814, 'Prk 9 Poblacion near Nena Sancho'),
(815, 'Prk 1A Jampason near Genelie Quitoriano'),
(816, 'Prk 1 Lawis near Dina Sacay'),
(817, 'Prk 11 Poblacion near Julie Borinaga'),
(818, 'Prk 13 Poblacion near Antonio Ogdol'),
(819, 'Bugwak near Marvin Bernaldez'),
(820, 'Prk 13 near Rochelle Balabat (Globe Cell Site)'),
(821, 'P-4B Jampason'),
(822, 'Prk 1 San Pedro near Veronilla Sarah'),
(823, 'Lawis near Alberta Balansag'),
(824, 'P14 Galarpe St near Montebon'),
(825, 'Prk 6 Jampason near Andro Bulaybulay'),
(826, 'Prk 4 Bugwak near Cancleto Ratunil'),
(827, 'Prk 1 San Pedro near Leonisa Balabat'),
(828, 'Prk 13 Poblacion near Rogelia Paradero'),
(829, 'Prk 4A Jampason near Jahzeel Nabangue'),
(830, 'Prk 11 Poblacion near Osias Janolino'),
(831, 'Prk 2 Poblacion near Yolanda Dumo'),
(832, 'Osmeña St Purok 3 Poblacion'),
(833, 'Prk 12 Poblacion near Adoralyn Clavite'),
(834, 'Prk 1 Lawis near Pedro Poblete'),
(835, 'Prk 1 San Pedro old Marlon Brian'),
(836, 'Prk 2B Jampason near Miguel Bulalahos '),
(837, 'N-5 Food Court'),
(838, 'Prk 1A Jampason near Clarissa Ubanan'),
(839, 'Meat Section'),
(840, 'Fish Section'),
(841, 'Fish Section old Elizabeth Benavente'),
(842, 'Prk 3 Bugwak near Teresita Bagas'),
(843, 'Prk 1B Jampason near Patricia  Buntag'),
(844, 'Prk 1B Jampason near Patricia Buntag'),
(845, 'Prk 14 Poblacion near Randy Bagares'),
(846, 'Prk 6 Jampason near Seundina Tacbobo'),
(847, 'Prk 14 Poblacion near Janeth Marfori'),
(848, 'Prk 11 Poblacion near Jonafe Masi'),
(849, 'Prk 6 Jampason near Gerlie Ladlad'),
(850, 'Jampason near Grace Lapinig'),
(851, 'Prk 1A San Pedro near Christine Galarpe'),
(852, 'Prk 3B Jampason near Gaspar Jimenez'),
(853, 'Doknayon near Mario Bagares'),
(854, 'Doknayon back of Vergie Jagonal'),
(855, 'Duknayon near Pilow Magas'),
(856, 'Prk 1A Jampason near Grace Marinduque'),
(857, 'Prk 12 Poblacion near Mendy Balaba'),
(858, 'Duknayon near Rena Galarpe'),
(859, 'Prk 1 San Pedro near Christine Galarpe'),
(860, 'Prk 3A Jampason old Charmlyn Odiada'),
(861, 'Prk 3B Jampason near Leoncio Ybañez'),
(862, 'Prk 3 Jampason near Leoncio Ybañez'),
(863, 'Prk 9 near Nena Sancho'),
(864, 'Prk 1 San Pedro near Samsona Perez'),
(865, 'Prk 9 Poblacion near Dahlia Telen'),
(866, 'Prk 7 Poblacion near Gina Sacay'),
(867, 'Fish Section Wet Market'),
(868, 'Meat Section Wet Market'),
(869, 'Fish Section Wet Section'),
(870, 'Prk 2 Poblacion in front of Nora Aliñabon'),
(871, 'Prk 2 Poblacion near Nila Gesalan'),
(872, 'Duknayon near Vergie Jagonal'),
(873, 'Duknayon near Mario Bagares'),
(874, 'Prk 13 Ruiz comp near Ritchie Roa'),
(875, 'Prk 3A Jampason near Niña Pagmanoja'),
(876, 'Prk 14 Pobalcion near Chooks to go'),
(877, 'Prk 9 Poblacion near Paterno Limbaco'),
(878, 'Prk 9 Poblacion near Ariel Galaroza'),
(879, 'Prk 13 Poblacion near John Angel Chio'),
(880, 'Lawis Poblacion'),
(881, 'Prk 14 near Walter Galarroza'),
(882, 'Lawis near Bridge'),
(883, 'Tangke near Refilling Stn'),
(884, 'Prk 11 Pobalcion near Belinda Bagares'),
(885, 'Jampason near Evacuation'),
(886, 'Prk 12 Poblacion near Ramon Absin'),
(887, 'Tangke near Duthai pool'),
(888, 'Lawis near Guadalupe Yting'),
(889, 'Prk 3A Poblacion near Felda Pacut'),
(890, 'Prk 1 San Pedro near Mina Rabuyo'),
(891, 'N-16  Food Court'),
(892, 'Prk 9 Poblacion near George Quitoriano'),
(893, 'Prk 2B Jampason back of Sammy Buntag (bay2)'),
(894, 'Prk 1 Lawis near Amor Moreno'),
(895, 'Prk 3A Jampason near Allan Roxas'),
(896, 'Prk 14 Poblacion back Concepcion Banogon'),
(897, 'N-19 Food Court'),
(898, 'Sebukawon near pump house'),
(899, 'Prk 14 Poblacion near Bernadeth Jimenez'),
(900, 'Food Court   '),
(901, 'Prk 3A Jampason near Exequel Manait'),
(902, 'Prk 3A Poblacion near Concepcion Suerte'),
(903, 'Sebukawon'),
(904, 'Prk 12 Poblacion near Ernita Camaro'),
(905, 'Prk 12 Poblacion near Jaime Gamo'),
(906, 'P-1 Apas'),
(907, 'Purok 3 Bugwak near Nermalyn Pacut'),
(908, 'Doknayon near Leo Bagares'),
(909, 'Prk 2A Jampason near Avelina Decenilla'),
(910, '2501 Galarpe St Poblacion'),
(911, 'Prk 14 in front of Calledo Joel'),
(912, 'Prk 2A Jampason near Carmelita Jabien #2'),
(913, 'Prk 1 Lawis near Yting Guadalupe'),
(914, 'Lawis old Rosita Glaban'),
(915, 'Prk 14 Poblacion near Pedablin Marfori'),
(916, 'Lawis near Iresh Janolino'),
(917, 'Prk 3A Poblacion near Jujie Galarpe'),
(918, 'Prk 2 Poblacion near Gabino Poblete'),
(919, 'Prk 2 Poblacion near Francisca Miñao'),
(920, 'Prk 1 Jampason near Darimbang'),
(921, 'Prk 14 Poblacion near Emlita Alagao'),
(922, 'Prk 11 Poblacion near Beatriz Acain'),
(923, 'Prk 13 Poblacion near MORESCO'),
(924, 'Prk 3A Poblacion near Efipanio Echavez'),
(925, 'Prk 6 Jampason near Lacno Leosita'),
(926, 'Prk 3A near Maam Tabares'),
(927, 'Tangke near Belen Pontemayor'),
(928, 'Prk 2 Poblacion'),
(929, 'Prk 3A Jampason near  Mamerto Quince'),
(930, 'N-19 Food Court old Wilboy Acut'),
(931, 'Prk 3A Jampason near Michael Cagalawan'),
(932, 'Prk 2 Poblacion near Roselito Sandoval'),
(933, 'Prk 1A Jampason near Mila Apao'),
(934, 'c/o Devie Jimenez Wahing'),
(935, 'Prk 1 San Pedro near Almira Dalumbar'),
(936, 'Lawis near Flordeliza Meñales'),
(937, 'Public Plaza'),
(938, 'Bugwak near Medalyn Pacanut'),
(939, 'Prk 2A Jampason near Regina Linggian'),
(940, 'Prk 2 Poblacion near Therasa sandoval'),
(941, 'Prk 13 Poblacion Luisa Echavez'),
(942, 'Prk 4B Jampason near Dave Obias'),
(943, 'Prk 3 Apas near Rolly Tacbobo'),
(944, 'Prk 1 SAn Pedro near Conchita Legaspi'),
(945, 'Prk 3A Apas near Rolly Tacbobo'),
(946, 'Prk 3A Poblacion near Elma Madula'),
(947, 'Prk 3A Poblacion near Isidro Takindingan'),
(948, '2500 Galarpe Street Poblacion'),
(949, 'Prk 1 San Pedro near Sitoy Lechon'),
(950, 'Prk 12 Poblacion back of Ransom Roa'),
(951, 'Prk 14 Poblacion near Grace Acman'),
(952, 'Prk 13 Poblacion Old Samson Bldg'),
(953, 'Prk 3A Jampason'),
(954, 'Prk 2B Jampason near Jun Bularon'),
(955, 'Prk 2B Jampason near Felimon Bularon'),
(956, 'Prk 12 Poblacion near Hanz Alavanza'),
(957, 'Doknayon back of Bagares Mansion'),
(958, 'Prk 2A Jampason near Franklin Masamayor'),
(959, 'Prk 2A Jampason near Marivic Paclibar'),
(960, 'Poblacion in front of Magno Residence'),
(961, 'Prk 2 Poblacion near Paylita Sandoval'),
(962, 'Prk 2A Jampason near Arong Ignacia'),
(963, 'Prk 3B Jampason near Rosie Adante'),
(964, 'Prk 2A Jampason near Frank Estaño'),
(965, 'Prk 2A Jampason near Grace Lapinig'),
(966, 'Prk 3A Poblacion near Ailyn Talaroc'),
(967, 'Prk 3A Jampason near Annalisa Alisin'),
(968, 'Doknayon beside Eduardo Orjalesa'),
(969, 'Prk 4A Jampason near Jahzeel Rey  Nabangue'),
(970, 'Prk 9 Poblacion near Milquiades Mingoy'),
(971, 'Prk 7 Codilla Street Poblacion '),
(972, 'Prk 4 Bugwak near Jun Mar Bagares'),
(973, 'Prk 3A Poblacion near Nermalyn Pacut'),
(974, 'Doknayon near Eduardo Orjalesa'),
(975, 'Prk 14 Poblacion in front of DAY CARE '),
(976, 'Prk 13 near Leo Cabillan'),
(977, 'Prk 6 Jampason near Jovena Tacbobo'),
(978, 'Prk 3 Jampason near Daryl Roble'),
(979, 'Lawis near Erlinda Aron'),
(980, 'Prk 4 Apas near Belen Galario'),
(981, 'Lawis near Guadaluppe Yting'),
(982, 'Doknayon Apas near Maria Clark (dolores)'),
(983, 'Prk 9 Poblacion beside Galula Bagares'),
(984, 'Doknayon near Jena Galario'),
(985, 'Prk 14 Poblacion near Emilita Alagao'),
(986, 'Dokanayon near Eric Rebalde'),
(987, 'Prk 2A Jampason near DENR'),
(988, 'Prk 11 Poblacion near Merry Grace Amancio'),
(989, 'Prk 1A Jampason near Ellen Patlunag'),
(990, 'Prk 1 San Pedro near Carmen Bongo'),
(991, 'Prk 2A Jampason near Evacuation'),
(992, 'Prk 12 Poblacion near Jonathan Bagares'),
(993, 'Prk 2A Jampason near Vicenta Quimada'),
(994, 'Prk 12 Poblacion near Willy Tan'),
(995, 'P 14 Galarpe Street Poblacion'),
(996, 'LTO Office - Initao Market'),
(997, 'Prk 14 Poblacion near Evelyn Wabe'),
(998, 'Bugwak near Rosalinda Tero'),
(999, 'Bugwak near Elvira Velasco'),
(1000, 'Prk 1 San Pedro near Cemetery'),
(1001, 'Jampason near Jenny Jamaca'),
(1002, 'Prk-4 Bugwak near at Eniceto Balaba'),
(1003, 'Prk 3A Poblacion nearTiting Sabellano'),
(1004, 'Doknayon near SEHRDEF'),
(1005, 'Prk 12 Poblacion near Roel Cabillan'),
(1006, 'Prk 3-A Poblacion near Merly Khant'),
(1007, 'Prk 7 Poblacion near Angelo Saavedra'),
(1008, 'Jampason Elem School - Contractor'),
(1009, 'Doknayon near Jerramy Bagares'),
(1010, 'Prk 2 Jampason near EVACUATION'),
(1011, 'Prk 7 Poblacion near Dativa Galarroza'),
(1012, 'Prk-14 Poblacion near at Santosidad Rae Anne'),
(1013, 'Prk-11 Poblacion near at Beatriz Acain'),
(1014, 'Prk-10 Poblacion near at Dizon Anthony'),
(1015, 'Ratunils Comp near Elbert Ratunil'),
(1016, 'Prk-1 Lawis near at Yting Gwadalupe'),
(1017, 'Prk-13 Poblacion near at Elbert Ratunil'),
(1018, 'Prk-3A Jampason near at Jamaca Jeovanie'),
(1019, 'Prk-13 Poblacion near at Richie Echavez'),
(1020, 'Prk-12 Poblacion near at Yabo Delia'),
(1021, 'Prk 3A Jampason near Emery Lusterio'),
(1022, 'Prk-4 Apas near at sabayle marilou'),
(1023, 'Prk- 14 Poblacion near at Campion Serilo'),
(1024, 'Prk 1B Jampason old Mayors Beach'),
(1025, 'Fish Section Market'),
(1026, 'Doknayon near Lorna Rebalde'),
(1027, 'Jampason Initao Mis Or'),
(1028, 'Bugwak near Editho Jalagat'),
(1029, 'Prk 3B Jampason near Ernesto Jamaca'),
(1030, 'Prk 2A Jampason beside Initao College'),
(1031, 'Prl 11 Poblacion old Osiais Janolino'),
(1032, 'Prk 12 Poblacion old Lita Aguilar '),
(1033, 'Prk 12 Poblacion near Rogelio Cabillan'),
(1034, 'Prk 12 Poblacion near Wilma Larongco'),
(1035, 'Prk 14 Poblacion near Riza Paulin'),
(1036, 'Prk 4B Jampason near Mayors Beach'),
(1037, 'Prk 14 Poblacion near Muriel Balasabas'),
(1038, 'Lawis near Ariel Sumile'),
(1039, 'Poblacion near Roberth Salem'),
(1040, 'Lawis near Arnel Lagrada'),
(1041, 'Prk 1A Jampason near Ronald Aron'),
(1042, 'Prk 4A Jampason near Liliosa Jamaca'),
(1043, 'Doknayon near Duthai Bagares'),
(1044, 'Poblacion near Market old Edward Sabellina'),
(1045, 'Prk 13 at Emy Echavez location'),
(1046, 'Prk 9 Poblacion near Roselyn Acut'),
(1047, 'Prk 3 Jampason near Ernesto Jamaca KALAHHI Road'),
(1048, 'Prk 8 Poblacion near Fausto Ubanan'),
(1049, 'Prk 3A Poblacin near Elma Madula'),
(1050, 'Prk 9 Poblacion in front of Rey Cabillan'),
(1051, 'Prk 6 Jampason near Allan Bulaybulay'),
(1052, 'Doknayon near Wilma Galarpe'),
(1053, 'Doknayon near SHERDEF'),
(1054, 'Prk 14 Poblacion near Sun Cell Site'),
(1055, 'Seukawon'),
(1056, 'Prk 2 Poblacion near Virginia Monceda'),
(1057, 'Prk 13 Poblacion near Elpidio Requiz'),
(1058, 'Prk 11 Poblacion old Jean Go'),
(1059, 'Prk 11 Poblacion'),
(1060, 'Prk 6 Jampason near Ernie Bagares'),
(1061, 'Tangke near Delfina Gozales'),
(1062, 'Tangke near Delfina Gonzales'),
(1063, 'Prk 3A Poblacion near Lourdes Madula'),
(1064, 'Prk 2C Jampason near Rodrigo Pingol'),
(1065, 'Tangke old Marivic Bagares'),
(1066, 'Prk 2A Jampason duol Mangga IC'),
(1067, 'Prk 1 San Pedro near Eddie Maglangit'),
(1068, 'Prk 1 San Pedro in front of Cesinia Galarroza'),
(1069, 'Sebukawon near Rolly Tacbobo'),
(1070, 'Prk 1 San Pedro in front of Lilibeth Dala'),
(1071, 'Prk 3B Jampason near Al Entera'),
(1072, 'Prk 13 Poblacion near PETRON'),
(1073, 'Prk 2A Jampason at INITAO COLLEGE'),
(1074, 'Prk 3A Bugwak near Elvira Velasco'),
(1075, 'Prk 9 Poblacion near Agustin Tamsi'),
(1076, 'Prk 2A Jampason near old Basketball Court'),
(1077, 'Doknayon near Haydee Hubaran'),
(1078, 'Prk 10 Poblacion near Francis Dadole'),
(1079, 'Public Market near Asian Bites'),
(1080, 'Prk 6 Jampason near Pablo Tacbobo'),
(1081, 'Prk 6 Jampason near Merlyn Tacbobo'),
(1082, 'Prk 9 Poblacion near Lorie Sabellano'),
(1083, 'Prk 3 Bugwak near Concepcion Suerte'),
(1084, 'Prk 1B Jampason near Nenette Gayamo'),
(1085, 'Prk 12 Poblacion near Virgilia Pasuit'),
(1086, 'Prk 14 Poblacion near Emma Galarroza #2'),
(1087, 'Prk 3A Jampason near Mila Gandarosa'),
(1088, 'Tangke crossing Dulag'),
(1089, 'Public Market beside Asian Bite'),
(1090, 'Tangke Apas in front of Crisanto Acain'),
(1091, 'Doknayon near Kag Cris Rebalde'),
(1092, 'Prk 6 Jampason near Emma Maki'),
(1093, 'Prk 6 Jampason near Cherry Ratunil'),
(1094, 'Prk 14 Poblacion near Delfin Opamin'),
(1095, 'Prk 1 San Pedro near Philita Acut'),
(1096, 'Tangke near Bagares Pool'),
(1097, 'Tangke near Louie Yangyang'),
(1098, 'Tangke near Elesa Aguilar'),
(1099, 'Lawis near Alden Villahermosa'),
(1100, 'Prk 4B Jampason near Noly Joy Ubagan'),
(1101, 'Prk 3B Jampason near Dryer'),
(1102, 'Prk 14 Poblacion in front of Alistair Pacana'),
(1103, 'Prk 1A Jampason near Darimbang'),
(1104, 'Lawis near Jerry Meñales'),
(1105, 'Doknayon near Eduardo Orjaleza'),
(1106, 'Prk 4 Bugwak near Lita Bondaug'),
(1107, 'Prk 9 Poblacion in front of Engr Bangalao'),
(1108, 'Jampason near Pagmanoja Niña'),
(1109, 'Prk 9 Poblacion in Front Engr Bangalao'),
(1110, 'Prk 3A Poblacion near Madula Lourdes'),
(1111, 'Prk 3A Jampason near Juan Macana'),
(1112, 'Prk 10 Poblacion near Abecula Magno'),
(1113, 'Jampason near Catherine Quince'),
(1114, 'Prk4Apas Doknayon near Renato Oblimar'),
(1115, 'Prk4 DoknayonApas near Renato Oblimar'),
(1116, 'Prk4DoknayonApas near Renato Oblimar'),
(1117, 'Prk3 Jampason near Ernesto Jamaca'),
(1118, 'Prk 4Poblacion near Bob Dapanas'),
(1119, 'Prk 6 Jampason near Ransom Roa'),
(1120, 'Prk 3A Jampason near  Emily Odiada'),
(1121, 'Prk3A Poblacion near Lourdes Madula'),
(1122, 'Prk3A Poblacion near Echavez Epifanio'),
(1123, 'Prk6 Jampason in Front of Lacno'),
(1124, 'Prk13 Poblacion near Maygay Jhizell Ammor'),
(1125, 'Prk1A San Pedro near Annielle Sabala'),
(1126, 'Prk1AJampason near Emma Bogo'),
(1127, 'Prk-1A San Pedro near Emmerson Fedelis'),
(1128, 'Prk-11 Poblacion near Leopicita Jalagat'),
(1129, 'Prk4 Apas near Erik Rebalde'),
(1130, 'Prk 9 Poblacion near Marvelous Galono'),
(1131, 'Prk2 Poblacion near Evangeline Sanchez'),
(1132, 'Prk 10 Poblacion near Irish Jalagat'),
(1133, 'Prk 12 Poblacion near Ronnie Bagares'),
(1134, 'Prk 4 Bugwak tabuk Tulay'),
(1135, 'Doknayon near Eric Rebalde'),
(1136, 'Doknayon near Renato Oblimar'),
(1137, 'Prk 7 Poblacion near Mary Dawn Payusan'),
(1138, 'Prk 7 Poblacion near Edgardo Wabe'),
(1139, 'San Pedro near Melfe Cabanesas'),
(1140, 'Doknayon near Nestor Rebalde'),
(1141, 'Back of INCHS near Ariel Sumile'),
(1142, 'Prk 2 Poblacion near Evelyn Millan '),
(1143, 'Prk 12 Poblacion near Lita Aguilar'),
(1144, 'Prk 3A Poblacion near Merly Khant'),
(1145, 'Prk 14 Poblacion near Kent Mabini'),
(1146, 'Prk 2B Jampason in front of DENR'),
(1147, 'Lawis Poblacion near Amor Moreno'),
(1148, 'Prk 3B Jampason back of Wendell Janog'),
(1149, 'Bugwak near Samuel Manuel'),
(1150, 'Prk 13 near Marcel Pacana old Francisco Ratunil '),
(1151, 'Prk 13 near Marcel Pacana old Francisco Ratunil'),
(1152, 'Prk 3A Poblacion near Christopher Pacut'),
(1153, 'Prk 2B Jampason near Richel Relox'),
(1154, 'Prk 14 Poblacion near Merlita Tacbobo'),
(1155, 'Prk 13 Ruiz cmpd near Teresita Buhian'),
(1156, 'Prk 12 Poblacion near Myrna Babatido'),
(1157, 'Prk 1 San Pedro near Melinda Nabangue'),
(1158, 'Lawis old William Masuangat'),
(1159, 'Prk 7 Poblacion near Aida Alaba'),
(1160, 'Prk 11 Poblacion near Rechelle Israel '),
(1161, 'Prk 9 Poblacion near Marvelous Galono'),
(1162, 'Prk 11 Poblacion near Charick Israel'),
(1163, 'Prk 2A Jampason near Teresita Balaba'),
(1164, 'Prk 1 San Pedro near Irish Jalagat'),
(1165, 'Prk 13 Poblacion back of Luisa Echavez'),
(1166, 'Doknayon near Milagros Bagares'),
(1167, 'Prk 3A Jampason near Cell Site'),
(1168, 'Prk 3A Poblacion near Julius Apolinario'),
(1169, 'Jampason in front of Vicente Galarroza'),
(1170, 'Bugwak Poblacion old Myrna Lim'),
(1171, 'Prk 3 Jampason near John Romuald Rañin'),
(1172, 'Tangke in front of Cayhao Lyn'),
(1173, 'Doknayon'),
(1174, 'Lawis Poblacion near Guadalupe Yting'),
(1175, 'Purok 2C Jampason near Maay Go'),
(1176, 'Doknayon back of SEHRDEF'),
(1177, 'Prl 11 Poblacion (ZARJ)'),
(1178, 'Prk 1 San Pedro near Flora Oporto'),
(1179, 'Sebukawon near BJMP'),
(1180, 'Prk 4 Jampason near Nena Nabangue'),
(1181, 'Prk 2B Jampason near Jessie Acut'),
(1182, 'Prk 9 Poblacion near Elma Recolito'),
(1183, 'Prk 7 Poblacion near Enriqueta Payusan'),
(1184, 'Prk 10 Poblacion back of Candelaria Tiu'),
(1185, 'Prk 3A near Rohaina Hitutuane'),
(1186, 'Prk 7 Poblacion near Antonia Sombilon'),
(1187, 'Prk 6 Jampason near Roselyn Roble'),
(1188, 'Prk 3A Poblacion  near Rohaina Hitutuane'),
(1189, 'Prk 3A Poblacion near Rohaina Hitutuane'),
(1190, 'Prk 3A Poblacio near Estrella Sabellano'),
(1191, 'Prk 1 San Pedro near Kag Alistair Pacana'),
(1192, 'Prk 4A Jampason near Arlyn Fineza'),
(1193, 'Prk 4B Jampason near Judy Enriquez'),
(1194, 'Prk 3A Jampason near Globe Cell Site'),
(1195, 'Tangke near Franklin Baguio'),
(1196, 'Sebukawon near Kenneth Joy Saladaga'),
(1197, 'Bugwak near Garry Jalagat'),
(1198, 'Prk 14 Poblacion near Kent Mabini'),
(1199, 'Prk 6 Jampason near Rita Tacbobo'),
(1200, 'Prk 7 Poblacion near Dino Padrigori'),
(1201, 'oLd Emily Odiada/KARANCHO'),
(1202, 'Prk 3A Jampason '),
(1203, 'Prk 14 Poblacion near Jerry Amor'),
(1204, 'Prk 1 San Pedro near Juanito Febia'),
(1205, 'Prk 1 San Pedro near Juanito Febria'),
(1206, 'Prk 1 San Pedro back of Engracio Galarroza'),
(1207, 'Prk 1 Jampason near Joseto Poblete'),
(1208, 'Prk 13 Poblacion near James Sanchez'),
(1209, 'Prk 1 Lawis near Gerlie Ladlad'),
(1210, 'Prk 2C Jampason near Nenita Cosio '),
(1211, 'Tangke near Lwas Zvika'),
(1212, 'Lawis near Reynaldo Lacapag'),
(1213, 'Tangke near Julito Cobias'),
(1214, 'Tangke near Toto Bagares'),
(1215, 'Prk 2 Poblacion Fish Port '),
(1216, 'Prk 1A San Pedro near Almira Dalumbar'),
(1217, 'Tangke near Crisanto Acain'),
(1218, 'Tangke near Eva Flora '),
(1219, 'Prk 3A Jampason near Teotimo Quitoriano'),
(1220, 'Prk 5 Tangke near Guzman Penton'),
(1221, 'Ptk 5 Tangke near Guzman Penton'),
(1222, 'Prk 2B Jampason near Erlmer Magno'),
(1223, 'Prk 14 Poblacion near Ma Bonita Balabat'),
(1224, 'Prk 4A Jampason near Maribeth Villahermosa'),
(1225, 'Prk 3A Jampason near Romana Tacbobo'),
(1226, 'Sebukawon near Provincial Jail'),
(1227, 'Codilla St near Celieto Magsayo'),
(1228, 'Tangke near Emma Cabulay'),
(1229, 'Sebukawon near Adelia Asok'),
(1230, 'Prk 3A Poblacion near Leonila Ong'),
(1231, 'Tangke Apas near Jonisa Sabal'),
(1232, 'Doknayon near Kriza Jagus'),
(1233, 'Prk 4 bugwak near Gorgonia Alburo'),
(1234, 'Prk 3A Poblacion near King Mike Bañado'),
(1235, 'Prk 2B Jampason near Willa Dizon'),
(1236, 'Prk 3A Poblacion near Efipanio Echavez'),
(1237, 'purok 9'),
(1238, 'Prk 3A Jampason near Rosie Adante'),
(1239, 'Prk 9 near Wilfredo Tan'),
(1240, 'Prk 1 San Pedro near Diomedes Tacbobo'),
(1241, 'Prk 3C Jampasson near Oliva Galgao'),
(1242, 'Tangke near Elsie Bagares'),
(1243, 'Apas near Richlane Lobendina'),
(1244, 'Prk 2 Poblacion beside EBA gym'),
(1245, 'Tangke near Praxedes Balacuit'),
(1246, 'Prk 11 Poblacion near Farmacia ni Dok'),
(1247, 'Lawis near Antonio Entia'),
(1248, 'Tangke near Penton Guzman'),
(1249, 'Prk 1A San Pedro  old Annie Oraya'),
(1250, 'Lawis back of Jesusa Cuerpo'),
(1251, 'Prk 1 San Pedro near Librada Lacapag'),
(1252, 'Prk 13 Poblacion near Clarita Espinosa'),
(1253, 'Prk 2a Jampason near Gina Waybight'),
(1254, 'Prk 2 Poblacion ner Nila Gesalan'),
(1255, 'Bugwak near Mila Roxas'),
(1256, 'Tangke Apas near Elsie Bagares'),
(1257, 'Tangke near Evelyn Dela Cerna'),
(1258, 'Prk 10 Poblacion back of Le Bistro'),
(1259, 'Prk 13 near LOTTO outlet'),
(1260, 'Tangke near Lita Aguilar'),
(1261, 'Prk 1 San Pedro near Ally Larongco'),
(1262, 'Prk 1A Jampason near Edwin Besin'),
(1263, 'Prk 9 Poblacion near Pedra Escarda'),
(1264, 'Prk 1 San Pedro near Ritchie Dagapioso'),
(1265, 'Prk 14  Poblacion near Cheryl Villahermosa'),
(1266, 'Prk 1 San Pedro near George Abragan'),
(1267, 'Tangke crossing to Dulag'),
(1268, 'Prk 2A Jampason near IC'),
(1269, 'Docnayon near Nestor Buhian'),
(1270, 'Prk 3A Poblacion near Berjame'),
(1271, 'Tangke near Amy Magpulong'),
(1272, 'Prk 13 Poblacion old Simmonette Sy #1'),
(1273, 'Prk 13 Poblacion old Ribonettes Door #3'),
(1274, 'Prk 13 Poblacion Old Ribonettes Door #1'),
(1275, 'Prk 2A Jampason near Lysel Jabien'),
(1276, 'Prk 13 Pblacion Near Romeo Kho'),
(1277, 'Prk 4-A Jampson near Jeffrey Agbalog'),
(1278, 'Prk 4 Poblacion in front of Gregor Chio'),
(1279, 'Prk 10 Poblacion back of Jescelyn Lagas'),
(1280, 'Prk 2A Jampason in front of IC'),
(1281, 'Prk 13 Poblacion old Ribonettes Door A'),
(1282, 'Prk 6 Jampason Initao'),
(1283, 'Prk 1A Jampapson near Genelie Quitoriano'),
(1284, 'Prk 6 Jampason near Maki Emma'),
(1285, 'Prk 3B Jampason Initao near Flora Wabe'),
(1286, 'Prk 2 Poblacion near Virginia Monceda'),
(1287, 'P2 Poblacion Initao near Paylita Sandoval'),
(1288, 'Prk 13 Poblacion near Kusina by Hernest'),
(1289, 'P4 Apas Initao Mis Or near Obsiioma MaTheresa'),
(1290, 'P14 Poblacion Initao near Gilbert Mercado'),
(1291, 'P1A San Pedro Initao Near Arlene DePaz'),
(1292, 'Jampson Initao near Mayor Gogoy'),
(1293, 'Prk 2 Poblacion Near Jesus Carlit'),
(1294, 'Prk 14 Poblacion Seven Eleven'),
(1295, 'Bugwak near Fe Ratunil'),
(1296, 'Prk 13 Poblacion near Mary Joy Ubanan'),
(1297, 'Prk 3A Poblacion near Linda Vedra'),
(1298, 'Prk 4A Jampason Fineza Water Refilling'),
(1299, 'Prk 11 Poblacion near Prose Lee Mae Magas'),
(1300, 'Prk 10 Poblacion near Winston Dadang'),
(1301, 'Prk 4B Jampason near Wenceslao Paraguele'),
(1302, 'Prk 14 Poblacion near Joevanne Joie Opamin'),
(1303, 'Prk 2B Jampason near Vicente Nabangue'),
(1304, 'Prk 3A Poblacion near Arnold Dandasan'),
(1305, 'Prk 1 San Pedro near Melfe Cabañesas'),
(1306, 'Prk 3A Jampason near Exequel Manait'),
(1307, 'Prk 1A Jampason (church)'),
(1308, 'Prk 1B Jamapason near Signature Resort'),
(1309, 'Prk 11 Poblacion near Zenith Fajardo'),
(1310, 'Prk 14 Poblacion near Zenaida Galarpe'),
(1311, 'Prk 14 Poblacion near Sarah Balaba'),
(1312, 'Prk 12 Poblacion old Kitanglay'),
(1313, 'Prk 1A Jampason near JM Horizon'),
(1314, 'Prk 1 Jampason near Emma Bogo'),
(1315, 'Prk 3B Jampason back Allan Roxas'),
(1316, 'Prk 11 Poblacion near Dominador Acain Jr'),
(1317, 'Prk 1A Jampason near Phillip Obut'),
(1318, 'Prk 1 Jamason near KAGAYHAAN HOLDINGS'),
(1319, 'Pik7 old HONORATA ACAIN'),
(1320, 'Jampason near DENR'),
(1321, 'P-2A Jampason near SALVADOR PATLUNAG'),
(1322, 'Prk 9 Poblacion near Reynaldo Acut'),
(1323, 'Prk6 Jampason near Roselyn Roble'),
(1324, 'Prk 1 Lawis'),
(1325, 'P-2 Jampason near Carmelita Jabien'),
(1326, 'P-13 Poblacion near Gentiles Dovie'),
(1327, 'P- 1 A Jampason near Clarrisa Ubanan'),
(1328, 'P-3C near Jampason near Geovannie Jamaca'),
(1329, 'Prk 3A Poblacion near Nely Magallanes #2'),
(1330, 'Prk 2A Jampason near Evacuation Center'),
(1331, 'prk3 poblacion initao misor near emilda lobindina'),
(1332, 'Prk 2BJampasonInitao MisOr'),
(1333, 'Prk2BJampason Initao MisOr'),
(1334, 'Prk 14 Poblacion front Lina Manubag'),
(1335, 'Prk2A Jampason near IC'),
(1336, 'Prk 8 Poblacion near Delia Palmares'),
(1337, 'Prk 1A San Pedro near Alistair Pacana'),
(1338, 'KALLAHI Road Jampason'),
(1339, 'KALAHHI Road Jampason'),
(1340, 'Prk 14 Poblacion near Muriel Luntayao'),
(1341, 'Prk 13 Poblacion near Maura Ratunil'),
(1342, 'Prk 3 Bugwak near Ruth Chavit'),
(1343, 'Prk 1A San Pedro near Librada Lacapag'),
(1344, 'Prk 12 Poblacion near Ramon Absin'),
(1345, 'Prk 1 San Pedro near Librada Lacapag'),
(1346, 'Prk 1A San Pedro near Wilfred Cabiles'),
(1347, 'Prk 2C Jampason near Allan Galarroza'),
(1348, 'Bugwak near Belen Ratunil'),
(1349, 'Prk 2A Jampason old Vivien Quimada'),
(1350, 'Prk9 near Engr Edgar Buhian'),
(1351, 'Prk 2A Jampason near Mars Jabien'),
(1352, 'Prk 11 Poblacion near Alita Manal'),
(1353, 'Prk 14 Poblacion near Oscar Acera'),
(1354, 'Prk 2B Jampason near Jessie Acut'),
(1355, 'Prk 4 Jampason near Rice Mill'),
(1356, 'Tangke in front of Evelyn Dela Cerna '),
(1357, 'Food Court Public Market'),
(1358, 'Prk 3C Jampason in front of Emily Odiada'),
(1359, 'Prk 9 Poblacion near Leonardo Dandasan'),
(1360, 'Prk3A Jampason back of Allan Patlunag'),
(1361, 'Prk 4BJampason near Leosita Lacno'),
(1362, 'Prk 14 Poblacion near Virgie Jalagat'),
(1363, 'P2A Jampason near Salvador Patlunag'),
(1364, 'Prk 3 Apas'),
(1365, 'San pedro old Danny Acut');
INSERT INTO `landmark` (`lm_id`, `location_name`) VALUES
(1366, 'Zone-24 Jampason Initao Misamis Oriental'),
(1367, 'Prk 6 Jampason near Andro Bulaybulay '),
(1368, 'Food Court N-13 old Marivic Sabellano'),
(1369, 'Prk 3B Jampason Near Paderanga martha'),
(1370, 'Prk3B Jampason Near Paderanga Martha'),
(1371, 'Purok 4 Doknayon near Jean Rebalde'),
(1372, 'Purok 14  in front of Rae Anne Santosidad'),
(1373, 'Prk 11 near Nida Ratunil'),
(1374, 'Fish Section old Elaine Perater'),
(1375, 'Prk 13 old Symonette Sy #2'),
(1376, 'Prk 2 Poblacion near Susan Belandres'),
(1377, 'Prk 2 Poblacion near Randy Cantiga'),
(1378, 'Prk 3C Jampason KALAHHI Road near Ernesto Jamaca'),
(1379, 'Prk 6 Jampason near Pascuasio Tacbobo'),
(1380, 'Prk 3B Jampason near Leonel Balaba'),
(1381, 'Prk 6 Jampason near Ernie Bagares'),
(1382, 'Food Court Stall #21'),
(1383, 'Food Court N-22'),
(1384, 'Prk 14 Hall of Justice (Fiscal)'),
(1385, 'Doknayon Apas in front of Nestor Buhian'),
(1386, 'Purok 14 near Joy Ariola'),
(1387, 'Prk 1 Jampason near LTO'),
(1388, 'Prk 2C Jampason near Allan Galarroza'),
(1389, 'Prk 3C Jampason near Emily Odiada'),
(1390, 'Prk 2 Poblacion in front of Alicia Echavez'),
(1391, 'Purok 1 San Pedro - Janog Compund'),
(1392, 'P2A Jampason near LTO'),
(1393, 'P14 Poblacion Initao ( PAO )'),
(1394, 'P14 Galarpe Street '),
(1395, 'Purok 3A Jampasaon near Tacbobo Cherry Mae'),
(1396, 'Purok 1 Jampason near LTO'),
(1397, 'Purok 3 Apas near Alice Digang'),
(1398, 'Purok 14 Poblacion near Kent Mabini'),
(1399, 'Purok 2C Jampason near Nabangue James'),
(1400, 'Bugwak back of Mark John Balaba'),
(1401, 'Docnayon beside Maria Clark'),
(1402, 'P-2B Jampason in front IC Dorm'),
(1403, 'P-2B Jampason in front of IC Dorm'),
(1404, 'P1 Lawis near Donata Balansag'),
(1405, 'P-3A JampasonBack of Jessie Acut'),
(1406, 'Purok 8 in front of AIMCOOP'),
(1407, 'P-3C Jampason near Charry Tacbobo'),
(1408, 'P-3C Jampason front of Emily Odiada'),
(1409, 'P-5 Tangke Apas near Lea Lawas'),
(1410, 'P-14 Poblacion side Pamisa Krisylyn'),
(1411, 'Prk 2B Jampason near KAPATERAN'),
(1412, 'Prk 1A San Pedro near Amy Dalumbar'),
(1413, 'Prk 2A Jampason near Jessie Acut'),
(1414, 'P2A Jampason near IC Casas complex'),
(1415, 'P4 Apas near Clarabel Galarpe'),
(1416, 'P2C Jampason near Galarroza Allan'),
(1417, 'P2C Jampason near Galrroza Allan'),
(1418, 'P6 Jampason near Uyan Majella Mae'),
(1419, 'P2C  Jampason near Galarroza Allan'),
(1420, 'P1 Lawis near Phobe Sandoval'),
(1421, 'P4 Apas near Nene Bagares'),
(1422, 'Purok 2B Jampason near Mary Jane Nabangue'),
(1423, 'Prk 14 Poblacion near Elson Quince'),
(1424, 'Doknayon back of Teofilo Magas'),
(1425, 'Prk 2C Jampason back of Alan Galarroza'),
(1426, 'Purok 2 near INCHS'),
(1427, 'P-8  Poblacion beside Trickmags Carwash'),
(1428, 'Purok 14 hall of Justice'),
(1429, 'Purok 9 near  Tan Wilfredo'),
(1430, 'P3A Pob near Cabillan April Joy'),
(1431, 'Prk 6 Jampason old Justina Tacbobo'),
(1432, 'Prk 3A Poblacion near Jahara Pacut'),
(1433, 'Prk 1A San Pedro near Ricardo Zalsos'),
(1434, 'Old Hall of Justice'),
(1435, 'Purok 4 Poblacion near Labis Maricar/Elmer'),
(1436, 'P9 Poblacion beside Leonardo Escarda'),
(1437, 'Purok 1 San Pedro near Lilibeth Dala'),
(1438, 'P1A  Jampason ( Insurance building )'),
(1439, 'Purok 3A Poblacion near Pederica Bolo'),
(1440, 'P1A Jampason near Philip Obut'),
(1441, 'Purok 3A  Poblacion Front of INCHS'),
(1442, 'Prk 2A Jampason beside Basketball Court'),
(1443, 'Purok 3A near Pacut Felda'),
(1444, 'Purok 1 San Pedro near Philita Chio'),
(1445, 'Purok 3A Poblacion near Apolinario Julius'),
(1446, 'Purok 3B Jampason near Ernesto Jamaca'),
(1447, 'Prk 6 Jampason near Lilibeth Pialan'),
(1448, 'Prk 3C Jampason back of Allan Patlunag'),
(1449, 'Sebukawon near Divine Grace Ambong'),
(1450, 'P4 Jampason near Jobo Limbaco'),
(1451, 'P1 Lawis Pob near Catherine Rabuyo'),
(1452, 'P2 C Jampason near Allan Galarroza'),
(1453, 'Foodcourt stall #20'),
(1454, 'Prk 6 Jampason near Felix Jariolne'),
(1455, 'Prk 2A Jampason near Cristelen Maagad'),
(1456, 'Prk 7 Poblacion near Pagayaman'),
(1457, 'Sebukawon near Water Source'),
(1458, 'P4 Doknayon Apas in front of Clark Maria'),
(1459, 'P1 Jampason near Lim Ophelia'),
(1460, 'Prk 3C Jampason KALAHI near Ernesto Jamaca'),
(1461, 'Sebukawon back of Water Source'),
(1462, 'P3A Jampason near Ernesto Jamaca'),
(1463, 'P3 Apas  Initao near Acain Aida'),
(1464, 'Prk 1A Jampason near Yellow House'),
(1465, 'P-3 Cebokawon Apas near Aida M Acain'),
(1466, 'P-5 Jampason near Allan Patlunag'),
(1467, 'Prk 2A Jampason near Quirina Madula'),
(1468, 'Prk 2 Poblacion (Fish Port)'),
(1469, 'Purok 4 Apas near STL'),
(1470, 'Purok4Doknayon  Apas near STL'),
(1471, 'Purok 4 Doknayon Apas near STL'),
(1472, 'Purok 1 Lawis near Dina Sacay'),
(1473, 'Purok 5 Apas near Franklin Baguio'),
(1474, 'Purok 4 Apas near Obsioma Ma Theresa'),
(1475, 'Purok 11 near Dino Magno'),
(1476, 'P4B Jampason nera Mayors Residence'),
(1477, 'P-4 Poblacion near Labis Elmer'),
(1478, 'Lawis  near Ernie Sabellano'),
(1479, 'Purok 3C Jampason in Front of Cell site'),
(1480, 'P-9 Poblacion near Leonardo Dandasan'),
(1481, 'P-3B Kalahi Road Jampason'),
(1482, 'P-3B Jampason near Jaime Jamaca'),
(1483, 'Purok3B Jampason near Sabellina Glendon'),
(1484, 'Purok3C Jampason old  Cosio Ma Nenita #2'),
(1485, 'Purok 14 Pob Back of Ariola Joy'),
(1486, 'Purok 3A Pob near Espinosa Nanette'),
(1487, 'Purok 4B Jampason near Tacbobo Antonio'),
(1488, 'Purok 1A Jampason near Joseto Poblete'),
(1489, 'PRK-4 Jampason'),
(1490, 'P-4 Jampason'),
(1491, 'Prk 2A Jampason near JM Horizon'),
(1492, 'Prk 4 Jampason in front of Kathlyn Ople'),
(1493, 'TangkeApas Initao near Dashielle Caco'),
(1494, 'P-3APoblacion near Khant Merly'),
(1495, 'P1 San Pedro near Tacbobo Diomedes'),
(1496, 'Purok 14 Poblacion old Addie Quijano'),
(1497, 'Prk 2A Jampason near Carmelita Jabien'),
(1498, 'P9 Poblacion beside Leonides Baluran'),
(1499, 'P4B Jampason near Tacbobo Marilou'),
(1500, 'Prk 11 Poblacion near Eric Sabaduquia'),
(1501, 'P7 Poblacion beside Amancio Merry Grace'),
(1502, 'Sebukawn  Apas in front of Jail BankHouse'),
(1503, 'P2A Jampason beside Nena Jabien'),
(1504, 'Purok 4 Bugwak Poblacion near Labis Maricar'),
(1505, 'P2 poblacion near Sandoval Donald'),
(1506, 'P2A Jampason beside Salvador Patlunag'),
(1507, 'Prk-14 Poblacion'),
(1508, 'Prk- 3B Jampason'),
(1509, 'P-2 Jampason'),
(1510, 'Prk 8 Poblacion near Card'),
(1511, 'Prk 2 Jampason '),
(1512, 'Prk 13 Poblacion Ribbonettes old Via Navarro'),
(1513, 'Purok 2A Jamapson near Henonsalao Amado'),
(1514, 'Prk 2A Jampason near Quirina Madula'),
(1515, 'Purok-4B Jampason beside Bulaybulay Allan'),
(1516, 'Purok 3 Sebukawn near Raponsil Bejasa'),
(1517, 'Purok 11 Poblacion - Old  Zalsos Wilfredo'),
(1518, 'WOMENs Bldg Jampason'),
(1519, 'New Municipal Hall'),
(1520, 'Tourist rest area Jampason'),
(1521, 'P-2B Jampason'),
(1522, 'Prk 3A Poblacion  SHELL STATION'),
(1523, 'Prk 2 Jampason nnear Lysel Jabien'),
(1524, 'Prk 2A Jampason near Melito Madula'),
(1525, 'Prk 2B Jampason'),
(1526, 'Prk 2A Jampason near CASAS'),
(1527, 'Prk 2C Jampason near James Nabangue'),
(1528, 'Prk 2A Jampason near Joana Tacbobo'),
(1529, 'Prk 2A Jamapson near Edwin patlunag'),
(1530, 'P-6 Jampason near Mary jane Bagares'),
(1531, 'P-13 Poblacion Ratunil Compound'),
(1532, 'Jamapson near Evacuation'),
(1533, 'Prk 3C Jampason back of Church'),
(1534, 'Prk 2A Jampason old Vivien Quimada'),
(1535, 'Prk 2A Jampason near Evacuation'),
(1536, 'Jampason in front of IC'),
(1537, 'Prk 2A Jampason beside IC'),
(1538, 'Prk 3B Jampason near Resily Sacay'),
(1539, 'Prk 3A Jampason near Romana Tacbobo'),
(1540, 'Prk-2B Jampason'),
(1541, 'Prk 2A Jampason beside IC'),
(1542, 'Prk 2C Jampason near Rayjin'),
(1543, 'P-6 Jampason '),
(1544, 'P-1 Lawis Poblacion Initao'),
(1545, 'Prk 2B Jampason near Laundry'),
(1546, 'Prk 2A Jampason near Ignacia Arong'),
(1547, 'Prk 2A Jampason near Carmelita Jabien'),
(1548, 'Jampason near Elem School'),
(1549, 'Jampason near Leonard Estopo'),
(1550, 'Purok 4 Doknayon Apas near Rey Jarales'),
(1551, 'P14 poblacion near alistair pacana'),
(1552, 'Prk 13 Poblacion  Bernardo Bldg'),
(1553, 'P4A Jampason in Front of #1'),
(1554, 'Prk 2A Jampason near Mona Montesa'),
(1555, 'Prk 6 Jampason near Jinky Joy Clavecilla'),
(1556, 'Purok 3 Jampason'),
(1557, 'Purok 1 San Pedro Janog Compound'),
(1558, 'Purok 5 Tangke near Pontemayor'),
(1559, 'Purok 4 Apas near ObsiomaMa Theresa'),
(1560, 'Purok 4 Apas near Orjaleza Eduardo'),
(1561, 'Purok 14 Poblacion near Caco Romualdo'),
(1562, 'Purok 1A san pedro beside De paz  Arlyn'),
(1563, 'Purok 12 Poblacion near First Valley Bank'),
(1564, 'Purok 1A Jampason beside New Municipal Hall'),
(1565, 'Purok 3B Jampason near Juvy Ubanan'),
(1566, 'Purok 5 Tangke  Apas near Penton Guzman'),
(1567, 'Purok 2A Jampason ( Initao College)'),
(1568, 'P-7 Poboblacion'),
(1569, 'Purok 1 Lawis Initao near Mahipos eddie'),
(1570, 'Purok 3B Jampason near Tacbobo Abundio'),
(1571, 'Purok 2B jampason'),
(1572, 'Purok 2B Jampason'),
(1573, 'Purok 2B  Jampason'),
(1574, 'Purok 13 Poblacion near Echavez Richie'),
(1575, 'Purok 3A Jampason beside Nenita Villanueva'),
(1576, 'Purok 1 Lawis'),
(1577, 'P-3A near Nathaniel Galarpe'),
(1578, 'Purok 2A Jampason near Initao College'),
(1579, 'Purok 2A Jampason beside Womens Building'),
(1580, 'Purok 2B jampason in front of Ernesto Divino'),
(1581, 'Purok 3C Jampason near Ernesto Jamaca'),
(1582, 'Purok 2A Jampason near Salvador Patlunag'),
(1583, 'Prk 3B Jampason near Ernesto Jamaca'),
(1584, 'Prk 4A Jampason near Chito Amarga'),
(1585, 'Prk 7 Poblacion near Elizabeth Benavente'),
(1586, 'Tangke near Alma Ratunil'),
(1587, 'Prk 4A Jampason near Liliosa Jamaca');

-- --------------------------------------------------------

--
-- Table structure for table `ledgersource`
--

CREATE TABLE `ledgersource` (
  `ls_id` bigint(20) NOT NULL,
  `source_type` varchar(50) NOT NULL,
  `source_tb_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ledgersource`
--

INSERT INTO `ledgersource` (`ls_id`, `source_type`, `source_tb_name`) VALUES
(1, 'BILL', 'water_bill'),
(2, 'CHARGE', 'customercharge'),
(3, 'ADJUST', 'billadjustment'),
(4, 'PAYMENT', 'paymentallocation');

-- --------------------------------------------------------

--
-- Table structure for table `meter`
--

CREATE TABLE `meter` (
  `mtr_id` bigint(11) NOT NULL,
  `mtr_serial` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `mtr_brand` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meterassignment`
--

CREATE TABLE `meterassignment` (
  `assignment_id` bigint(20) NOT NULL,
  `connection_id` bigint(20) NOT NULL,
  `meter_id` bigint(20) NOT NULL,
  `installed_at` date NOT NULL,
  `removed_at` date DEFAULT NULL,
  `install_read` decimal(12,3) NOT NULL DEFAULT 0.000,
  `removal_read` decimal(12,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meterreading`
--

CREATE TABLE `meterreading` (
  `reading_id` bigint(20) NOT NULL,
  `assignment_id` bigint(20) NOT NULL,
  `period_id` bigint(20) DEFAULT NULL,
  `reading_date` date NOT NULL,
  `reading_value` decimal(12,3) NOT NULL,
  `is_estimated` tinyint(1) NOT NULL DEFAULT 0,
  `emp_id` int(11) DEFAULT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` bigint(20) NOT NULL,
  `receipt_no` varchar(40) NOT NULL,
  `payer_id` bigint(20) NOT NULL,
  `payment_date` date NOT NULL,
  `amount_received` decimal(12,2) NOT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `user_id` int(11) DEFAULT NULL,
  `stat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `receipt_no`, `payer_id`, `payment_date`, `amount_received`, `created_at`, `user_id`, `stat_id`) VALUES
(1, '123', 5, '2025-11-22', 50.00, '2025-11-22 12:42:32.000000', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `paymentallocation`
--

CREATE TABLE `paymentallocation` (
  `payment_allocation_id` bigint(20) NOT NULL,
  `payment_id` bigint(20) NOT NULL,
  `amount_applied` decimal(12,2) NOT NULL,
  `stat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paymentallocation`
--

INSERT INTO `paymentallocation` (`payment_allocation_id`, `payment_id`, `amount_applied`, `stat_id`) VALUES
(1, 1, 50.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `period`
--

CREATE TABLE `period` (
  `per_id` bigint(11) NOT NULL,
  `period_desc` varchar(10) NOT NULL,
  `create_date` datetime DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `period`
--

INSERT INTO `period` (`per_id`, `period_desc`, `create_date`, `stat_id`) VALUES
(1, '2025-11', '2025-11-19 21:08:51', 2),
(2, '2025-11', '2025-11-19 21:08:51', 1);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `position`
--

CREATE TABLE `position` (
  `pos_id` int(11) NOT NULL,
  `pos_name` varchar(50) NOT NULL,
  `stat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `powerconsumption`
--

CREATE TABLE `powerconsumption` (
  `pow_id` bigint(20) NOT NULL,
  `prod_id` int(11) NOT NULL,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  `kwhr` float NOT NULL,
  `amount` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `production`
--

CREATE TABLE `production` (
  `prod_id` bigint(20) NOT NULL,
  `per_id` bigint(20) NOT NULL,
  `p_source_id` int(11) NOT NULL,
  `read_date` datetime NOT NULL,
  `reading` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `province`
--

CREATE TABLE `province` (
  `prov_id` int(11) NOT NULL,
  `prov_desc` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `province`
--

INSERT INTO `province` (`prov_id`, `prov_desc`, `stat_id`) VALUES
(1, 'Misamis Oriental', 1);

-- --------------------------------------------------------

--
-- Table structure for table `purok`
--

CREATE TABLE `purok` (
  `p_id` int(11) NOT NULL,
  `p_desc` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `purok`
--

INSERT INTO `purok` (`p_id`, `p_desc`, `stat_id`) VALUES
(1, 'Purok 1A', 1),
(2, 'Purok 1B', 1),
(3, 'Purok 2A', 1),
(4, 'Purok 2B', 1),
(5, 'Purok 3A', 1),
(6, 'Purok 3B', 1),
(7, 'Purok 4A', 1),
(8, 'Purok 4B', 1),
(9, 'Purok 5A', 1),
(10, 'Purok 5B', 1),
(11, 'Purok 6A', 1),
(12, 'Purok 6B', 1),
(13, 'Purok 7A', 1),
(14, 'Purok 7B', 1),
(15, 'Purok 8A', 1),
(16, 'Purok 8B', 1),
(17, 'Purok 9A', 1),
(18, 'Purok 9B', 1),
(19, 'Purok 10A', 1),
(20, 'Purok 10B', 1);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'System Administrator', 'Full control over the system (users, roles, configurations, database).\r\n\r\nManages integrations, security, and backups.'),
(2, 'Billing Manager', 'Oversees billing cycles, invoice generation, tariff setup, and adjustments.\r\n\r\nApproves rebates, discounts, or corrections.'),
(3, 'Payment Officer', 'Records payments (cash, bank, mobile money).\n\nIssues receipts and reconciles transactions.'),
(4, 'Meter Reader', 'Inputs or uploads meter readings.\r\n\r\nFlags anomalies (e.g., broken meters, suspected tampering).'),
(5, 'Customer Service Officer', 'Manages customer accounts, queries, and complaints.\r\n\r\nCan update customer details (e.g., address, contact info).'),
(6, 'Customer', 'Views bills, makes payments, and checks usage history.\r\n\r\nCan submit service requests (e.g., new connection, complaints).');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `serviceapplication`
--

CREATE TABLE `serviceapplication` (
  `application_id` bigint(20) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `address_id` bigint(20) DEFAULT NULL,
  `class_id` int(11) NOT NULL,
  `account_type_id` bigint(20) NOT NULL,
  `area_id` bigint(20) NOT NULL,
  `submitted_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `is_printed` tinyint(1) NOT NULL DEFAULT 0,
  `stat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `serviceapplication`
--

INSERT INTO `serviceapplication` (`application_id`, `customer_id`, `address_id`, `class_id`, `account_type_id`, `area_id`, `submitted_at`, `is_printed`, `stat_id`) VALUES
(3, 5, 323, 1, 1, 1, '2025-11-22 12:36:43.000000', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `serviceconnection`
--

CREATE TABLE `serviceconnection` (
  `connection_id` bigint(20) NOT NULL,
  `account_no` varchar(30) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `address_id` bigint(20) NOT NULL,
  `account_type_id` bigint(20) NOT NULL,
  `class_id` int(11) NOT NULL,
  `started_at` date NOT NULL DEFAULT curdate(),
  `ended_at` date DEFAULT NULL,
  `stat_id` int(11) NOT NULL,
  `area_id` bigint(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `serviceconnection`
--

INSERT INTO `serviceconnection` (`connection_id`, `account_no`, `customer_id`, `address_id`, `account_type_id`, `class_id`, `started_at`, `ended_at`, `stat_id`, `area_id`) VALUES
(1, '10001', 5, 323, 1, 1, '2025-11-22', NULL, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--

CREATE TABLE `statuses` (
  `stat_id` int(11) NOT NULL,
  `stat_desc` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `statuses`
--

INSERT INTO `statuses` (`stat_id`, `stat_desc`) VALUES
(1, 'ACTIVE'),
(2, 'INACTIVE'),
(3, 'PENDING'),
(4, 'APPROVED'),
(5, 'REJECTED'),
(6, 'ARCHIVED'),
(7, 'DISCONNECTED');

-- --------------------------------------------------------

--
-- Table structure for table `town`
--

CREATE TABLE `town` (
  `t_id` int(11) NOT NULL,
  `t_desc` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `town`
--

INSERT INTO `town` (`t_id`, `t_desc`, `stat_id`) VALUES
(1, 'Initao', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `u_type` int(11) NOT NULL,
  `status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `fullname`, `created_at`, `u_type`, `status_id`) VALUES
(1, 'jtan', 'password', 'sample@mail.com', 'John Erzon Tan', '2025-06-10 05:56:25', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_types`
--

CREATE TABLE `user_types` (
  `ut_id` int(11) NOT NULL,
  `ut_desc` varchar(50) NOT NULL,
  `status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_types`
--

INSERT INTO `user_types` (`ut_id`, `ut_desc`, `status_id`) VALUES
(3, 'ADMIN', 1),
(4, 'BILLING', 1);

-- --------------------------------------------------------

--
-- Table structure for table `water_bill_history`
--

CREATE TABLE `water_bill_history` (
  `bill_id` bigint(20) NOT NULL,
  `connection_id` bigint(20) NOT NULL,
  `period_id` bigint(20) NOT NULL,
  `prev_reading_id` bigint(20) NOT NULL,
  `curr_reading_id` bigint(20) NOT NULL,
  `consumption` decimal(12,3) NOT NULL,
  `water_amount` decimal(12,2) NOT NULL,
  `due_date` date DEFAULT NULL,
  `adjustment_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) GENERATED ALWAYS AS (`water_amount` + `adjustment_total`) STORED,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `stat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `water_rates`
--

CREATE TABLE `water_rates` (
  `wr_id` bigint(20) NOT NULL,
  `class_id` int(11) NOT NULL,
  `period_id` bigint(20) DEFAULT NULL,
  `min_range` int(11) NOT NULL,
  `max_range` int(11) NOT NULL,
  `rate_value` decimal(12,2) DEFAULT NULL,
  `rate_increment` decimal(12,2) NOT NULL,
  `stat_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zone`
--

CREATE TABLE `zone` (
  `z_id` int(11) NOT NULL,
  `z_desc` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_type`
--
ALTER TABLE `account_type`
  ADD PRIMARY KEY (`at_id`);

--
-- Indexes for table `area`
--
ALTER TABLE `area`
  ADD PRIMARY KEY (`a_id`);

--
-- Indexes for table `areaassignment`
--
ALTER TABLE `areaassignment`
  ADD PRIMARY KEY (`area_assignment_id`),
  ADD KEY `fk_route_a_id` (`area_id`);

--
-- Indexes for table `barangay`
--
ALTER TABLE `barangay`
  ADD PRIMARY KEY (`b_id`);

--
-- Indexes for table `billadjustment`
--
ALTER TABLE `billadjustment`
  ADD PRIMARY KEY (`bill_adjustment_id`),
  ADD KEY `fk_ba_bill` (`bill_id`),
  ADD KEY `fk_ba_user` (`user_id`);

--
-- Indexes for table `billadjustmenttype`
--
ALTER TABLE `billadjustmenttype`
  ADD PRIMARY KEY (`bill_adjustment_type_id`),
  ADD KEY `fk_batype_status` (`stat_id`),
  ADD KEY `UQ_BillAdjustmentType_name` (`name`) USING BTREE;

--
-- Indexes for table `chargeitem`
--
ALTER TABLE `chargeitem`
  ADD PRIMARY KEY (`charge_item_id`),
  ADD UNIQUE KEY `UQ_ChargeItem_name` (`name`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `connection_history`
--
ALTER TABLE `connection_history`
  ADD PRIMARY KEY (`ch_id`),
  ADD KEY `fx_user_id` (`user_id`) USING BTREE;

--
-- Indexes for table `consumer_address`
--
ALTER TABLE `consumer_address`
  ADD PRIMARY KEY (`ca_id`),
  ADD KEY `fk_brgy_address` (`b_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`cust_id`),
  ADD KEY `fx_caddress_id` (`ca_id`) USING BTREE;

--
-- Indexes for table `customercharge`
--
ALTER TABLE `customercharge`
  ADD PRIMARY KEY (`charge_id`),
  ADD KEY `fk_custcharge_customer` (`customer_id`),
  ADD KEY `fk_custcharge_application` (`application_id`),
  ADD KEY `fk_custcharge_connection` (`connection_id`),
  ADD KEY `fk_custcharge_item` (`charge_item_id`),
  ADD KEY `fk_custcharge_status` (`stat_id`);

--
-- Indexes for table `customerledger`
--
ALTER TABLE `customerledger`
  ADD PRIMARY KEY (`ledger_entry_id`),
  ADD UNIQUE KEY `UQ_Ledger_Source` (`source_type_id`,`source_id`,`source_line_no_nz`),
  ADD KEY `fk_ledger_user` (`user_id`),
  ADD KEY `fk_ledger_status` (`stat_id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`emp_id`);

--
-- Indexes for table `landmark`
--
ALTER TABLE `landmark`
  ADD PRIMARY KEY (`lm_id`);

--
-- Indexes for table `ledgersource`
--
ALTER TABLE `ledgersource`
  ADD PRIMARY KEY (`ls_id`);

--
-- Indexes for table `meter`
--
ALTER TABLE `meter`
  ADD PRIMARY KEY (`mtr_id`);

--
-- Indexes for table `meterassignment`
--
ALTER TABLE `meterassignment`
  ADD PRIMARY KEY (`assignment_id`),
  ADD UNIQUE KEY `UQ_MeterAssignment` (`connection_id`,`meter_id`,`installed_at`),
  ADD KEY `fk_ma_meter` (`meter_id`);

--
-- Indexes for table `meterreading`
--
ALTER TABLE `meterreading`
  ADD PRIMARY KEY (`reading_id`),
  ADD UNIQUE KEY `UQ_AssignmentDate` (`assignment_id`,`reading_date`),
  ADD KEY `fk_reading_period` (`period_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `UQ_Payment_receipt` (`receipt_no`),
  ADD KEY `fk_payment_payer` (`payer_id`),
  ADD KEY `fk_payment_user` (`user_id`),
  ADD KEY `fk_payment_status` (`stat_id`);

--
-- Indexes for table `paymentallocation`
--
ALTER TABLE `paymentallocation`
  ADD PRIMARY KEY (`payment_allocation_id`);

--
-- Indexes for table `period`
--
ALTER TABLE `period`
  ADD PRIMARY KEY (`per_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`);

--
-- Indexes for table `powerconsumption`
--
ALTER TABLE `powerconsumption`
  ADD PRIMARY KEY (`pow_id`);

--
-- Indexes for table `production`
--
ALTER TABLE `production`
  ADD PRIMARY KEY (`prod_id`);

--
-- Indexes for table `province`
--
ALTER TABLE `province`
  ADD PRIMARY KEY (`prov_id`);

--
-- Indexes for table `purok`
--
ALTER TABLE `purok`
  ADD PRIMARY KEY (`p_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `serviceapplication`
--
ALTER TABLE `serviceapplication`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `fk_app_customer` (`customer_id`),
  ADD KEY `fk_app_address` (`address_id`),
  ADD KEY `fk_app_status` (`stat_id`);

--
-- Indexes for table `serviceconnection`
--
ALTER TABLE `serviceconnection`
  ADD PRIMARY KEY (`connection_id`),
  ADD UNIQUE KEY `UQ_ServiceConnection_account_no` (`account_no`),
  ADD KEY `fk_sc_customer` (`customer_id`),
  ADD KEY `fk_sc_address` (`address_id`),
  ADD KEY `fk_sc_account_type` (`account_type_id`),
  ADD KEY `fk_sc_status` (`stat_id`),
  ADD KEY `fx_area_id` (`area_id`);

--
-- Indexes for table `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`stat_id`);

--
-- Indexes for table `town`
--
ALTER TABLE `town`
  ADD PRIMARY KEY (`t_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_types`
--
ALTER TABLE `user_types`
  ADD PRIMARY KEY (`ut_id`);

--
-- Indexes for table `water_bill_history`
--
ALTER TABLE `water_bill_history`
  ADD PRIMARY KEY (`bill_id`),
  ADD UNIQUE KEY `UQ_Bill_conn_period` (`connection_id`,`period_id`),
  ADD KEY `fk_bill_period` (`period_id`),
  ADD KEY `fk_bill_prev_read` (`prev_reading_id`),
  ADD KEY `fk_bill_curr_read` (`curr_reading_id`),
  ADD KEY `fk_bill_status` (`stat_id`);

--
-- Indexes for table `water_rates`
--
ALTER TABLE `water_rates`
  ADD PRIMARY KEY (`wr_id`),
  ADD KEY `fx_class_id` (`class_id`) USING BTREE,
  ADD KEY `fx_period_id` (`period_id`) USING BTREE,
  ADD KEY `fx_user_id` (`user_id`);

--
-- Indexes for table `zone`
--
ALTER TABLE `zone`
  ADD PRIMARY KEY (`z_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_type`
--
ALTER TABLE `account_type`
  MODIFY `at_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `area`
--
ALTER TABLE `area`
  MODIFY `a_id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `areaassignment`
--
ALTER TABLE `areaassignment`
  MODIFY `area_assignment_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `barangay`
--
ALTER TABLE `barangay`
  MODIFY `b_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `billadjustment`
--
ALTER TABLE `billadjustment`
  MODIFY `bill_adjustment_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `billadjustmenttype`
--
ALTER TABLE `billadjustmenttype`
  MODIFY `bill_adjustment_type_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `chargeitem`
--
ALTER TABLE `chargeitem`
  MODIFY `charge_item_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `connection_history`
--
ALTER TABLE `connection_history`
  MODIFY `ch_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consumer_address`
--
ALTER TABLE `consumer_address`
  MODIFY `ca_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=324;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `cust_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customercharge`
--
ALTER TABLE `customercharge`
  MODIFY `charge_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customerledger`
--
ALTER TABLE `customerledger`
  MODIFY `ledger_entry_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `emp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `landmark`
--
ALTER TABLE `landmark`
  MODIFY `lm_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1588;

--
-- AUTO_INCREMENT for table `ledgersource`
--
ALTER TABLE `ledgersource`
  MODIFY `ls_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `meterassignment`
--
ALTER TABLE `meterassignment`
  MODIFY `assignment_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meterreading`
--
ALTER TABLE `meterreading`
  MODIFY `reading_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `paymentallocation`
--
ALTER TABLE `paymentallocation`
  MODIFY `payment_allocation_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `period`
--
ALTER TABLE `period`
  MODIFY `per_id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `powerconsumption`
--
ALTER TABLE `powerconsumption`
  MODIFY `pow_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `production`
--
ALTER TABLE `production`
  MODIFY `prod_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `province`
--
ALTER TABLE `province`
  MODIFY `prov_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purok`
--
ALTER TABLE `purok`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `serviceapplication`
--
ALTER TABLE `serviceapplication`
  MODIFY `application_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `serviceconnection`
--
ALTER TABLE `serviceconnection`
  MODIFY `connection_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `statuses`
--
ALTER TABLE `statuses`
  MODIFY `stat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `town`
--
ALTER TABLE `town`
  MODIFY `t_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_types`
--
ALTER TABLE `user_types`
  MODIFY `ut_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `water_bill_history`
--
ALTER TABLE `water_bill_history`
  MODIFY `bill_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `areaassignment`
--
ALTER TABLE `areaassignment`
  ADD CONSTRAINT `areaassignment_ibfk_1` FOREIGN KEY (`area_id`) REFERENCES `area` (`a_id`) ON UPDATE CASCADE;

--
-- Constraints for table `billadjustment`
--
ALTER TABLE `billadjustment`
  ADD CONSTRAINT `fk_ba_bill` FOREIGN KEY (`bill_id`) REFERENCES `water_bill_history` (`bill_id`),
  ADD CONSTRAINT `fk_ba_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `billadjustmenttype`
--
ALTER TABLE `billadjustmenttype`
  ADD CONSTRAINT `fk_batype_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`);

--
-- Constraints for table `consumer_address`
--
ALTER TABLE `consumer_address`
  ADD CONSTRAINT `fk_brgy_address` FOREIGN KEY (`b_id`) REFERENCES `barangay` (`b_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_2` FOREIGN KEY (`ca_id`) REFERENCES `consumer_address` (`ca_id`) ON UPDATE CASCADE;

--
-- Constraints for table `customercharge`
--
ALTER TABLE `customercharge`
  ADD CONSTRAINT `fk_custcharge_application` FOREIGN KEY (`application_id`) REFERENCES `serviceapplication` (`application_id`),
  ADD CONSTRAINT `fk_custcharge_connection` FOREIGN KEY (`connection_id`) REFERENCES `serviceconnection` (`connection_id`),
  ADD CONSTRAINT `fk_custcharge_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`cust_id`),
  ADD CONSTRAINT `fk_custcharge_item` FOREIGN KEY (`charge_item_id`) REFERENCES `chargeitem` (`charge_item_id`),
  ADD CONSTRAINT `fk_custcharge_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`);

--
-- Constraints for table `customerledger`
--
ALTER TABLE `customerledger`
  ADD CONSTRAINT `fk_ledger_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`),
  ADD CONSTRAINT `fk_ledger_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `meterassignment`
--
ALTER TABLE `meterassignment`
  ADD CONSTRAINT `fk_ma_connection` FOREIGN KEY (`connection_id`) REFERENCES `serviceconnection` (`connection_id`),
  ADD CONSTRAINT `fk_ma_meter` FOREIGN KEY (`meter_id`) REFERENCES `meter` (`mtr_id`);

--
-- Constraints for table `meterreading`
--
ALTER TABLE `meterreading`
  ADD CONSTRAINT `fk_reading_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `meterassignment` (`assignment_id`),
  ADD CONSTRAINT `fk_reading_period` FOREIGN KEY (`period_id`) REFERENCES `period` (`per_id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk_payment_payer` FOREIGN KEY (`payer_id`) REFERENCES `customer` (`cust_id`),
  ADD CONSTRAINT `fk_payment_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`),
  ADD CONSTRAINT `fk_payment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `paymentallocation`
--
ALTER TABLE `paymentallocation`
  ADD CONSTRAINT `fk_pa_payment` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE;

--
-- Constraints for table `serviceapplication`
--
ALTER TABLE `serviceapplication`
  ADD CONSTRAINT `fk_app_address` FOREIGN KEY (`address_id`) REFERENCES `consumer_address` (`ca_id`),
  ADD CONSTRAINT `fk_app_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`cust_id`),
  ADD CONSTRAINT `fk_app_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`);

--
-- Constraints for table `serviceconnection`
--
ALTER TABLE `serviceconnection`
  ADD CONSTRAINT `fk_sc_account_type` FOREIGN KEY (`account_type_id`) REFERENCES `account_type` (`at_id`),
  ADD CONSTRAINT `fk_sc_address` FOREIGN KEY (`address_id`) REFERENCES `consumer_address` (`ca_id`),
  ADD CONSTRAINT `fk_sc_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`cust_id`),
  ADD CONSTRAINT `fk_sc_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`),
  ADD CONSTRAINT `serviceconnection_ibfk_1` FOREIGN KEY (`area_id`) REFERENCES `area` (`a_id`) ON UPDATE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;

--
-- Constraints for table `water_bill_history`
--
ALTER TABLE `water_bill_history`
  ADD CONSTRAINT `fk_bill_connection` FOREIGN KEY (`connection_id`) REFERENCES `serviceconnection` (`connection_id`),
  ADD CONSTRAINT `fk_bill_curr_read` FOREIGN KEY (`curr_reading_id`) REFERENCES `meterreading` (`reading_id`),
  ADD CONSTRAINT `fk_bill_period` FOREIGN KEY (`period_id`) REFERENCES `period` (`per_id`),
  ADD CONSTRAINT `fk_bill_prev_read` FOREIGN KEY (`prev_reading_id`) REFERENCES `meterreading` (`reading_id`),
  ADD CONSTRAINT `fk_bill_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`);

--
-- Constraints for table `water_rates`
--
ALTER TABLE `water_rates`
  ADD CONSTRAINT `water_rates_ibfk_1` FOREIGN KEY (`period_id`) REFERENCES `period` (`per_id`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
