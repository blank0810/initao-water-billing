-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 27, 2025 at 04:10 AM
-- Server version: 10.6.22-MariaDB-cll-lve
-- PHP Version: 8.3.22

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
  `at_desc` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `area`
--

CREATE TABLE `area` (
  `a_id` bigint(11) NOT NULL,
  `a_desc` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `AreaAssignment`
--

CREATE TABLE `AreaAssignment` (
  `area_assignment_id` bigint(20) NOT NULL,
  `area_id` bigint(20) NOT NULL,
  `meter_reader_id` bigint(20) NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barangay`
--

CREATE TABLE `barangay` (
  `b_id` int(11) NOT NULL,
  `b_desc` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
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
-- Table structure for table `BillAdjustment`
--

CREATE TABLE `BillAdjustment` (
  `bill_adjustment_id` bigint(20) NOT NULL,
  `bill_id` bigint(20) NOT NULL,
  `bill_adjustment_type_id` bigint(20) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `remarks` varchar(200) DEFAULT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `BillAdjustmentType`
--

CREATE TABLE `BillAdjustmentType` (
  `bill_adjustment_type_id` bigint(20) NOT NULL,
  `name` varchar(80) NOT NULL,
  `direction` enum('+','-') NOT NULL,
  `stat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ChargeItem`
--

CREATE TABLE `ChargeItem` (
  `charge_item_id` bigint(20) NOT NULL,
  `name` varchar(80) NOT NULL,
  `default_amount` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Consumer`
--

CREATE TABLE `Consumer` (
  `c_id` bigint(20) NOT NULL,
  `cust_id` bigint(20) DEFAULT NULL,
  `cm_id` bigint(20) DEFAULT NULL,
  `a_id` int(11) DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL,
  `chng_mtr_stat` bit(1) DEFAULT NULL
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
-- Table structure for table `consumer_ledger`
--

CREATE TABLE `consumer_ledger` (
  `cl_id` bigint(20) NOT NULL,
  `c_id` bigint(20) DEFAULT NULL,
  `cl_no` bigint(20) DEFAULT NULL,
  `debit` decimal(15,2) DEFAULT NULL,
  `credit` decimal(15,2) DEFAULT NULL,
  `balance` decimal(15,2) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `or_no` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consumer_meters`
--

CREATE TABLE `consumer_meters` (
  `cm_id` bigint(20) NOT NULL,
  `mr_id` int(11) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `install_date` datetime DEFAULT NULL,
  `initial_readout` decimal(15,2) DEFAULT NULL,
  `last_reading` decimal(15,2) DEFAULT NULL,
  `pulled_out_at` datetime DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `cust_id` bigint(20) NOT NULL,
  `create_date` datetime DEFAULT NULL,
  `cust_last_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `cust_first_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `cust_middle_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `ca_id` bigint(20) DEFAULT NULL,
  `land_mark` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL,
  `c_type` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `resolution_no` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`cust_id`, `create_date`, `cust_last_name`, `cust_first_name`, `cust_middle_name`, `ca_id`, `land_mark`, `stat_id`, `c_type`, `resolution_no`) VALUES
(3, '2025-07-03 12:08:26', 'Doe', 'Joshua', 'A.', NULL, 'Near the water tower', 1, 'Residential', '22213132'),
(4, '2025-08-26 13:14:32', 'Tan', 'Vanissa', 'Parido', 322, 'Duol ila Ehnand', 3, 'Residential', 'INITAO-VT-20250826131430'),
(5, '2025-08-26 13:14:56', 'Tan', 'Vanissa', 'Parido', 323, 'Duol ila Ehnand', 3, 'Residential', 'INITAO-VT-20250826131455');

-- --------------------------------------------------------

--
-- Table structure for table `CustomerCharge`
--

CREATE TABLE `CustomerCharge` (
  `charge_id` bigint(20) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `application_id` bigint(20) DEFAULT NULL,
  `connection_id` bigint(20) DEFAULT NULL,
  `charge_item_id` bigint(20) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  `quantity` decimal(12,3) NOT NULL DEFAULT 1.000,
  `unit_amount` decimal(12,2) NOT NULL,
  `total_amount` decimal(14,3) GENERATED ALWAYS AS (`quantity` * `unit_amount`) STORED,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `due_date` date DEFAULT NULL,
  `stat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `CustomerLedger`
--

CREATE TABLE `CustomerLedger` (
  `ledger_entry_id` bigint(20) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `connection_id` bigint(20) DEFAULT NULL,
  `period_id` bigint(20) DEFAULT NULL,
  `txn_date` date NOT NULL,
  `post_ts` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `source_type` enum('BILL','CHARGE','ADJUST','PAYMENT','REFUND','WRITE_OFF','TRANSFER','REVERSAL') NOT NULL,
  `source_id` bigint(20) NOT NULL,
  `source_line_no` int(11) DEFAULT NULL,
  `source_line_no_nz` int(11) GENERATED ALWAYS AS (ifnull(`source_line_no`,0)) STORED,
  `description` varchar(200) DEFAULT NULL,
  `debit` decimal(12,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(12,2) NOT NULL DEFAULT 0.00,
  `user_id` int(11) DEFAULT NULL,
  `stat_id` int(11) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `meter`
--

CREATE TABLE `meter` (
  `mtr_id` bigint(11) NOT NULL,
  `mtr_serial` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `mtr_brand` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `MeterAssignment`
--

CREATE TABLE `MeterAssignment` (
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
-- Table structure for table `MeterReading`
--

CREATE TABLE `MeterReading` (
  `reading_id` bigint(20) NOT NULL,
  `assignment_id` bigint(20) NOT NULL,
  `period_id` bigint(20) DEFAULT NULL,
  `reading_date` date NOT NULL,
  `reading_value` decimal(12,3) NOT NULL,
  `is_estimated` tinyint(1) NOT NULL DEFAULT 0,
  `meter_reader_id` bigint(20) DEFAULT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meter_readers`
--

CREATE TABLE `meter_readers` (
  `mr_id` bigint(11) NOT NULL,
  `mr_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meter_reading`
--

CREATE TABLE `meter_reading` (
  `mrr_id` bigint(20) NOT NULL,
  `cm_id` int(11) DEFAULT NULL,
  `wb_id` bigint(20) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `read_date` datetime DEFAULT NULL,
  `current_reading` decimal(6,5) DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `misc_bill`
--

CREATE TABLE `misc_bill` (
  `mb_id` bigint(20) NOT NULL,
  `mref_id` bigint(20) DEFAULT NULL,
  `cl_id` bigint(20) DEFAULT NULL,
  `per_id` int(11) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `misc_reference`
--

CREATE TABLE `misc_reference` (
  `mref_id` int(11) NOT NULL,
  `ref_type` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Payment`
--

CREATE TABLE `Payment` (
  `payment_id` bigint(20) NOT NULL,
  `receipt_no` varchar(40) NOT NULL,
  `payer_id` bigint(20) NOT NULL,
  `payment_date` date NOT NULL,
  `amount_received` decimal(12,2) NOT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `user_id` int(11) DEFAULT NULL,
  `stat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `PaymentAllocation`
--

CREATE TABLE `PaymentAllocation` (
  `payment_allocation_id` bigint(20) NOT NULL,
  `payment_id` bigint(20) NOT NULL,
  `target_type` enum('BILL','CHARGE') NOT NULL,
  `target_id` bigint(20) NOT NULL,
  `amount_applied` decimal(12,2) NOT NULL,
  `period_id` bigint(20) DEFAULT NULL,
  `connection_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `pt_id` bigint(20) NOT NULL,
  `or_no` bigint(20) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `amount_tendered` decimal(15,2) DEFAULT NULL,
  `paid_amount` decimal(15,2) DEFAULT NULL,
  `amount_diff` decimal(15,2) DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `period`
--

CREATE TABLE `period` (
  `per_id` bigint(11) NOT NULL,
  `per_start_date` datetime DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
-- Table structure for table `province`
--

CREATE TABLE `province` (
  `prov_id` int(11) NOT NULL,
  `prov_desc` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
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
  `p_desc` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
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
-- Table structure for table `reading_schedule`
--

CREATE TABLE `reading_schedule` (
  `rs_id` int(11) NOT NULL,
  `a_id` int(11) DEFAULT NULL,
  `per_id` int(11) DEFAULT NULL,
  `rs_start_date` datetime DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
-- Table structure for table `ServiceApplication`
--

CREATE TABLE `ServiceApplication` (
  `application_id` bigint(20) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `address_id` bigint(20) DEFAULT NULL,
  `submitted_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `stat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ServiceConnection`
--

CREATE TABLE `ServiceConnection` (
  `connection_id` bigint(20) NOT NULL,
  `account_no` varchar(30) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `address_id` bigint(20) NOT NULL,
  `account_type_id` bigint(20) NOT NULL,
  `rate_id` bigint(20) DEFAULT NULL,
  `started_at` date NOT NULL DEFAULT curdate(),
  `ended_at` date DEFAULT NULL,
  `stat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(6, 'ARCHIVED');

-- --------------------------------------------------------

--
-- Table structure for table `town`
--

CREATE TABLE `town` (
  `t_id` int(11) NOT NULL,
  `t_desc` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
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
-- Table structure for table `water_bill`
--

CREATE TABLE `water_bill` (
  `wb_id` bigint(20) NOT NULL,
  `cl_id` bigint(20) DEFAULT NULL,
  `per_id` int(11) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `water_bill_adjustments`
--

CREATE TABLE `water_bill_adjustments` (
  `wba_id` bigint(20) NOT NULL,
  `c_id` bigint(20) DEFAULT NULL,
  `adjust_type` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `old_amount` decimal(15,2) DEFAULT NULL,
  `new_amount` decimal(15,2) DEFAULT NULL,
  `remarks` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
  `rate_desc` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `rate` decimal(6,5) DEFAULT NULL,
  `stat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zone`
--

CREATE TABLE `zone` (
  `z_id` int(11) NOT NULL,
  `z_desc` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
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
-- Indexes for table `AreaAssignment`
--
ALTER TABLE `AreaAssignment`
  ADD PRIMARY KEY (`area_assignment_id`),
  ADD KEY `fk_ra_route` (`area_id`),
  ADD KEY `fk_ra_meterreader` (`meter_reader_id`);

--
-- Indexes for table `barangay`
--
ALTER TABLE `barangay`
  ADD PRIMARY KEY (`b_id`);

--
-- Indexes for table `BillAdjustment`
--
ALTER TABLE `BillAdjustment`
  ADD PRIMARY KEY (`bill_adjustment_id`),
  ADD KEY `fk_ba_bill` (`bill_id`),
  ADD KEY `fk_ba_type` (`bill_adjustment_type_id`),
  ADD KEY `fk_ba_user` (`user_id`);

--
-- Indexes for table `BillAdjustmentType`
--
ALTER TABLE `BillAdjustmentType`
  ADD PRIMARY KEY (`bill_adjustment_type_id`),
  ADD UNIQUE KEY `UQ_BillAdjustmentType_name` (`name`),
  ADD KEY `fk_batype_status` (`stat_id`);

--
-- Indexes for table `ChargeItem`
--
ALTER TABLE `ChargeItem`
  ADD PRIMARY KEY (`charge_item_id`),
  ADD UNIQUE KEY `UQ_ChargeItem_name` (`name`);

--
-- Indexes for table `Consumer`
--
ALTER TABLE `Consumer`
  ADD PRIMARY KEY (`c_id`);

--
-- Indexes for table `consumer_address`
--
ALTER TABLE `consumer_address`
  ADD PRIMARY KEY (`ca_id`),
  ADD KEY `fk_brgy_address` (`b_id`);

--
-- Indexes for table `consumer_ledger`
--
ALTER TABLE `consumer_ledger`
  ADD PRIMARY KEY (`cl_id`);

--
-- Indexes for table `consumer_meters`
--
ALTER TABLE `consumer_meters`
  ADD PRIMARY KEY (`cm_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`cust_id`);

--
-- Indexes for table `CustomerCharge`
--
ALTER TABLE `CustomerCharge`
  ADD PRIMARY KEY (`charge_id`),
  ADD KEY `fk_custcharge_customer` (`customer_id`),
  ADD KEY `fk_custcharge_application` (`application_id`),
  ADD KEY `fk_custcharge_connection` (`connection_id`),
  ADD KEY `fk_custcharge_item` (`charge_item_id`),
  ADD KEY `fk_custcharge_status` (`stat_id`);

--
-- Indexes for table `CustomerLedger`
--
ALTER TABLE `CustomerLedger`
  ADD PRIMARY KEY (`ledger_entry_id`),
  ADD UNIQUE KEY `UQ_Ledger_Source` (`source_type`,`source_id`,`source_line_no_nz`),
  ADD KEY `IX_Ledger_CustConnPeriodDate` (`customer_id`,`connection_id`,`period_id`,`txn_date`,`ledger_entry_id`),
  ADD KEY `fk_ledger_connection` (`connection_id`),
  ADD KEY `fk_ledger_period` (`period_id`),
  ADD KEY `fk_ledger_user` (`user_id`),
  ADD KEY `fk_ledger_status` (`stat_id`);

--
-- Indexes for table `meter`
--
ALTER TABLE `meter`
  ADD PRIMARY KEY (`mtr_id`);

--
-- Indexes for table `MeterAssignment`
--
ALTER TABLE `MeterAssignment`
  ADD PRIMARY KEY (`assignment_id`),
  ADD UNIQUE KEY `UQ_MeterAssignment` (`connection_id`,`meter_id`,`installed_at`),
  ADD KEY `fk_ma_meter` (`meter_id`);

--
-- Indexes for table `MeterReading`
--
ALTER TABLE `MeterReading`
  ADD PRIMARY KEY (`reading_id`),
  ADD UNIQUE KEY `UQ_AssignmentDate` (`assignment_id`,`reading_date`),
  ADD KEY `fk_reading_period` (`period_id`),
  ADD KEY `fk_reading_meterreader` (`meter_reader_id`);

--
-- Indexes for table `meter_readers`
--
ALTER TABLE `meter_readers`
  ADD PRIMARY KEY (`mr_id`);

--
-- Indexes for table `meter_reading`
--
ALTER TABLE `meter_reading`
  ADD PRIMARY KEY (`mrr_id`);

--
-- Indexes for table `misc_bill`
--
ALTER TABLE `misc_bill`
  ADD PRIMARY KEY (`mb_id`);

--
-- Indexes for table `misc_reference`
--
ALTER TABLE `misc_reference`
  ADD PRIMARY KEY (`mref_id`);

--
-- Indexes for table `Payment`
--
ALTER TABLE `Payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `UQ_Payment_receipt` (`receipt_no`),
  ADD KEY `fk_payment_payer` (`payer_id`),
  ADD KEY `fk_payment_user` (`user_id`),
  ADD KEY `fk_payment_status` (`stat_id`);

--
-- Indexes for table `PaymentAllocation`
--
ALTER TABLE `PaymentAllocation`
  ADD PRIMARY KEY (`payment_allocation_id`),
  ADD UNIQUE KEY `UQ_PaymentTarget` (`payment_id`,`target_type`,`target_id`),
  ADD KEY `fk_pa_period` (`period_id`),
  ADD KEY `fk_pa_connection` (`connection_id`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`pt_id`);

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
-- Indexes for table `reading_schedule`
--
ALTER TABLE `reading_schedule`
  ADD PRIMARY KEY (`rs_id`);

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
-- Indexes for table `ServiceApplication`
--
ALTER TABLE `ServiceApplication`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `fk_app_customer` (`customer_id`),
  ADD KEY `fk_app_address` (`address_id`),
  ADD KEY `fk_app_status` (`stat_id`);

--
-- Indexes for table `ServiceConnection`
--
ALTER TABLE `ServiceConnection`
  ADD PRIMARY KEY (`connection_id`),
  ADD UNIQUE KEY `UQ_ServiceConnection_account_no` (`account_no`),
  ADD KEY `fk_sc_customer` (`customer_id`),
  ADD KEY `fk_sc_address` (`address_id`),
  ADD KEY `fk_sc_account_type` (`account_type_id`),
  ADD KEY `fk_sc_status` (`stat_id`);

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
-- Indexes for table `water_bill`
--
ALTER TABLE `water_bill`
  ADD PRIMARY KEY (`wb_id`);

--
-- Indexes for table `water_bill_adjustments`
--
ALTER TABLE `water_bill_adjustments`
  ADD PRIMARY KEY (`wba_id`);

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
  ADD PRIMARY KEY (`wr_id`);

--
-- Indexes for table `zone`
--
ALTER TABLE `zone`
  ADD PRIMARY KEY (`z_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `AreaAssignment`
--
ALTER TABLE `AreaAssignment`
  MODIFY `area_assignment_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barangay`
--
ALTER TABLE `barangay`
  MODIFY `b_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `BillAdjustment`
--
ALTER TABLE `BillAdjustment`
  MODIFY `bill_adjustment_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `BillAdjustmentType`
--
ALTER TABLE `BillAdjustmentType`
  MODIFY `bill_adjustment_type_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ChargeItem`
--
ALTER TABLE `ChargeItem`
  MODIFY `charge_item_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consumer_address`
--
ALTER TABLE `consumer_address`
  MODIFY `ca_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=324;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `cust_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `CustomerCharge`
--
ALTER TABLE `CustomerCharge`
  MODIFY `charge_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `CustomerLedger`
--
ALTER TABLE `CustomerLedger`
  MODIFY `ledger_entry_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `MeterAssignment`
--
ALTER TABLE `MeterAssignment`
  MODIFY `assignment_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `MeterReading`
--
ALTER TABLE `MeterReading`
  MODIFY `reading_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Payment`
--
ALTER TABLE `Payment`
  MODIFY `payment_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `PaymentAllocation`
--
ALTER TABLE `PaymentAllocation`
  MODIFY `payment_allocation_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `ServiceApplication`
--
ALTER TABLE `ServiceApplication`
  MODIFY `application_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ServiceConnection`
--
ALTER TABLE `ServiceConnection`
  MODIFY `connection_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `statuses`
--
ALTER TABLE `statuses`
  MODIFY `stat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- Constraints for table `AreaAssignment`
--
ALTER TABLE `AreaAssignment`
  ADD CONSTRAINT `fk_ra_meterreader` FOREIGN KEY (`meter_reader_id`) REFERENCES `meter_readers` (`mr_id`),
  ADD CONSTRAINT `fk_ra_route` FOREIGN KEY (`area_id`) REFERENCES `area` (`a_id`);

--
-- Constraints for table `BillAdjustment`
--
ALTER TABLE `BillAdjustment`
  ADD CONSTRAINT `fk_ba_bill` FOREIGN KEY (`bill_id`) REFERENCES `water_bill_history` (`bill_id`),
  ADD CONSTRAINT `fk_ba_type` FOREIGN KEY (`bill_adjustment_type_id`) REFERENCES `BillAdjustmentType` (`bill_adjustment_type_id`),
  ADD CONSTRAINT `fk_ba_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `BillAdjustmentType`
--
ALTER TABLE `BillAdjustmentType`
  ADD CONSTRAINT `fk_batype_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`);

--
-- Constraints for table `consumer_address`
--
ALTER TABLE `consumer_address`
  ADD CONSTRAINT `fk_brgy_address` FOREIGN KEY (`b_id`) REFERENCES `barangay` (`b_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `CustomerCharge`
--
ALTER TABLE `CustomerCharge`
  ADD CONSTRAINT `fk_custcharge_application` FOREIGN KEY (`application_id`) REFERENCES `ServiceApplication` (`application_id`),
  ADD CONSTRAINT `fk_custcharge_connection` FOREIGN KEY (`connection_id`) REFERENCES `ServiceConnection` (`connection_id`),
  ADD CONSTRAINT `fk_custcharge_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`cust_id`),
  ADD CONSTRAINT `fk_custcharge_item` FOREIGN KEY (`charge_item_id`) REFERENCES `ChargeItem` (`charge_item_id`),
  ADD CONSTRAINT `fk_custcharge_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`);

--
-- Constraints for table `CustomerLedger`
--
ALTER TABLE `CustomerLedger`
  ADD CONSTRAINT `fk_ledger_connection` FOREIGN KEY (`connection_id`) REFERENCES `ServiceConnection` (`connection_id`),
  ADD CONSTRAINT `fk_ledger_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`cust_id`),
  ADD CONSTRAINT `fk_ledger_period` FOREIGN KEY (`period_id`) REFERENCES `period` (`per_id`),
  ADD CONSTRAINT `fk_ledger_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`),
  ADD CONSTRAINT `fk_ledger_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `MeterAssignment`
--
ALTER TABLE `MeterAssignment`
  ADD CONSTRAINT `fk_ma_connection` FOREIGN KEY (`connection_id`) REFERENCES `ServiceConnection` (`connection_id`),
  ADD CONSTRAINT `fk_ma_meter` FOREIGN KEY (`meter_id`) REFERENCES `meter` (`mtr_id`);

--
-- Constraints for table `MeterReading`
--
ALTER TABLE `MeterReading`
  ADD CONSTRAINT `fk_reading_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `MeterAssignment` (`assignment_id`),
  ADD CONSTRAINT `fk_reading_meterreader` FOREIGN KEY (`meter_reader_id`) REFERENCES `meter_readers` (`mr_id`),
  ADD CONSTRAINT `fk_reading_period` FOREIGN KEY (`period_id`) REFERENCES `period` (`per_id`);

--
-- Constraints for table `Payment`
--
ALTER TABLE `Payment`
  ADD CONSTRAINT `fk_payment_payer` FOREIGN KEY (`payer_id`) REFERENCES `customer` (`cust_id`),
  ADD CONSTRAINT `fk_payment_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`),
  ADD CONSTRAINT `fk_payment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `PaymentAllocation`
--
ALTER TABLE `PaymentAllocation`
  ADD CONSTRAINT `fk_pa_connection` FOREIGN KEY (`connection_id`) REFERENCES `ServiceConnection` (`connection_id`),
  ADD CONSTRAINT `fk_pa_payment` FOREIGN KEY (`payment_id`) REFERENCES `Payment` (`payment_id`),
  ADD CONSTRAINT `fk_pa_period` FOREIGN KEY (`period_id`) REFERENCES `period` (`per_id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE;

--
-- Constraints for table `ServiceApplication`
--
ALTER TABLE `ServiceApplication`
  ADD CONSTRAINT `fk_app_address` FOREIGN KEY (`address_id`) REFERENCES `consumer_address` (`ca_id`),
  ADD CONSTRAINT `fk_app_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`cust_id`),
  ADD CONSTRAINT `fk_app_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`);

--
-- Constraints for table `ServiceConnection`
--
ALTER TABLE `ServiceConnection`
  ADD CONSTRAINT `fk_sc_account_type` FOREIGN KEY (`account_type_id`) REFERENCES `account_type` (`at_id`),
  ADD CONSTRAINT `fk_sc_address` FOREIGN KEY (`address_id`) REFERENCES `consumer_address` (`ca_id`),
  ADD CONSTRAINT `fk_sc_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`cust_id`),
  ADD CONSTRAINT `fk_sc_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`);

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
  ADD CONSTRAINT `fk_bill_connection` FOREIGN KEY (`connection_id`) REFERENCES `ServiceConnection` (`connection_id`),
  ADD CONSTRAINT `fk_bill_curr_read` FOREIGN KEY (`curr_reading_id`) REFERENCES `MeterReading` (`reading_id`),
  ADD CONSTRAINT `fk_bill_period` FOREIGN KEY (`period_id`) REFERENCES `period` (`per_id`),
  ADD CONSTRAINT `fk_bill_prev_read` FOREIGN KEY (`prev_reading_id`) REFERENCES `MeterReading` (`reading_id`),
  ADD CONSTRAINT `fk_bill_status` FOREIGN KEY (`stat_id`) REFERENCES `statuses` (`stat_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
