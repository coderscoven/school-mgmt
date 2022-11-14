-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 11, 2022 at 11:14 AM
-- Server version: 5.7.31
-- PHP Version: 8.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school_mgmt_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `class_details`
--

DROP TABLE IF EXISTS `class_details`;
CREATE TABLE IF NOT EXISTS `class_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `fees_id` int(11) NOT NULL,
  `class_details` text,
  `num_of_students` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='class details, teacher, fees';

--
-- Dumping data for table `class_details`
--

INSERT INTO `class_details` (`id`, `class_id`, `teacher_id`, `fees_id`, `class_details`, `num_of_students`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 1, 'Fusce nunc nisi, facilisis sit amet tempus quis, consequat blandit lacus', NULL, '2022-11-04 09:55:42', '2022-11-04 12:15:33'),
(2, 2, 4, 1, NULL, NULL, '2022-11-04 09:56:32', '2022-11-04 09:56:32'),
(3, 3, 2, 1, NULL, NULL, '2022-11-04 09:56:43', '2022-11-04 09:56:43'),
(4, 4, 1, 2, NULL, NULL, '2022-11-04 09:56:54', '2022-11-04 09:56:54'),
(5, 5, 6, 2, 'Phasellus nec euismod ante. Integer suscipit et est et pellentesqu', NULL, '2022-11-04 12:19:16', '2022-11-04 12:20:09'),
(6, 6, 5, 2, 'Morbi placerat ex sed orci dignissim placerat. Sed eu justo ut velit luctus auctor. Nulla quam odio, tempor vel magna', NULL, '2022-11-04 12:20:00', '2022-11-04 12:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `class_streams`
--

DROP TABLE IF EXISTS `class_streams`;
CREATE TABLE IF NOT EXISTS `class_streams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='classes';

--
-- Dumping data for table `class_streams`
--

INSERT INTO `class_streams` (`id`, `class_name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'S.1', '2022-11-04 09:55:42', '2022-11-04 12:15:33', NULL),
(2, 'S.2', '2022-11-04 09:56:32', '2022-11-04 09:56:32', NULL),
(3, 'S.3', '2022-11-04 09:56:43', '2022-11-04 09:56:43', NULL),
(4, 'S.4', '2022-11-04 09:56:54', '2022-11-04 09:56:54', NULL),
(5, 'S.5', '2022-11-04 12:19:16', '2022-11-04 12:20:09', NULL),
(6, 'S.6', '2022-11-04 12:20:00', '2022-11-04 12:20:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `course` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `level` varchar(150) NOT NULL,
  `total_amount` float NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `error_logs`
--

DROP TABLE IF EXISTS `error_logs`;
CREATE TABLE IF NOT EXISTS `error_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `error_details` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='stores error logs';

--
-- Dumping data for table `error_logs`
--

INSERT INTO `error_logs` (`id`, `error_details`, `created_at`, `updated_at`) VALUES
(1, 'SQLSTATE[42S02]: Base table or view not found: 1146 Table \'school_mgmt_db.track_fees_payments\' doesn\'t exist', '2022-11-08 08:23:34', '2022-11-08 08:23:34'),
(2, 'SQLSTATE[42S22]: Column not found: 1054 Unknown column \'term_paid_for\' in \'where clause\'', '2022-11-08 08:24:05', '2022-11-08 08:24:05'),
(3, 'SQLSTATE[42S22]: Column not found: 1054 Unknown column \'term_paid_for\' in \'where clause\'', '2022-11-08 08:24:34', '2022-11-08 08:24:34'),
(4, 'SQLSTATE[HY000]: General error: 1366 Incorrect integer value: \'\' for column \'stud_id\' at row 1', '2022-11-08 09:32:45', '2022-11-08 09:32:45'),
(5, 'SQLSTATE[HY000]: General error: 1366 Incorrect integer value: \'\' for column \'stud_id\' at row 1', '2022-11-08 09:33:32', '2022-11-08 09:33:32'),
(6, 'SQLSTATE[HY000]: General error: 1366 Incorrect integer value: \'Term 2 | 2022\' for column \'school_term\' at row 1', '2022-11-08 09:34:01', '2022-11-08 09:34:01'),
(7, 'SQLSTATE[23000]: Integrity constraint violation: 1048 Column \'school_term\' cannot be null', '2022-11-08 12:42:11', '2022-11-08 12:42:11'),
(8, 'SQLSTATE[23000]: Integrity constraint violation: 1048 Column \'school_term\' cannot be null', '2022-11-08 12:42:19', '2022-11-08 12:42:19'),
(9, 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near \'= \'6\', ef_id = \'1\', slip_serial = \'123\', amount = \'50,000\', class_id = \'3\', paym\' at line 1', '2022-11-08 12:56:05', '2022-11-08 12:56:05'),
(10, 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near \'= \'6\', ef_id = \'1\', slip_serial = \'123\', amount = \'50,000\', class_id = \'3\', paym\' at line 1', '2022-11-08 12:56:13', '2022-11-08 12:56:13'),
(11, 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near \'= \'6\', ef_id = \'1\', slip_serial = \'123\', amount = \'50,000\', class_id = \'3\', paym\' at line 1', '2022-11-08 12:56:28', '2022-11-08 12:56:28'),
(12, 'SQLSTATE[01000]: Warning: 1265 Data truncated for column \'amount\' at row 1', '2022-11-08 12:57:34', '2022-11-08 12:57:34'),
(13, 'SQLSTATE[HY000]: General error: 1366 Incorrect integer value: \'\' for column \'stud_id\' at row 1', '2022-11-08 12:58:18', '2022-11-08 12:58:18');

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

DROP TABLE IF EXISTS `fees`;
CREATE TABLE IF NOT EXISTS `fees` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `school_section` varchar(100) NOT NULL,
  `amount` double NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`id`, `school_section`, `amount`, `created_at`, `updated_at`) VALUES
(1, 'Day', 370000, '2022-11-04 12:06:49', '2022-11-04 01:38:02'),
(2, 'Boarding', 700000, '2022-11-04 12:06:49', '2022-11-04 12:06:49');

-- --------------------------------------------------------

--
-- Table structure for table `houses`
--

DROP TABLE IF EXISTS `houses`;
CREATE TABLE IF NOT EXISTS `houses` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `house_name` varchar(70) NOT NULL,
  `house_slug` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `houses`
--

INSERT INTO `houses` (`id`, `house_name`, `house_slug`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Africa', 'africa', '2022-10-15 07:20:16', '2022-10-15 07:20:16', NULL),
(2, 'Europe', 'europe', '2022-10-15 07:20:42', '2022-10-15 07:20:42', NULL),
(3, 'Asia', 'asia', '2022-10-15 07:21:07', '2022-10-15 07:21:07', NULL),
(4, 'America', 'america', '2022-10-15 07:23:28', '2022-10-15 07:23:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

DROP TABLE IF EXISTS `parents`;
CREATE TABLE IF NOT EXISTS `parents` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` varchar(255) NOT NULL,
  `parent_names` varchar(90) NOT NULL,
  `contacts` varchar(255) NOT NULL,
  `email` varchar(72) DEFAULT NULL,
  `residence` text NOT NULL,
  `gender` varchar(8) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`id`, `student_id`, `parent_names`, `contacts`, `email`, `residence`, `gender`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '3', 'JK Kenedy', '0701202569', 'jk@gmail.com', 'Donec id arcu id mauris viverra volutpat eget et ex', 'Male', '2022-11-03 07:56:02', '2022-11-03 08:13:29', NULL),
(2, '1, 2', 'Charles Opio', '0765100200', 'charles@gmail.com', 'Integer suscipit et est et pellentesque', 'Male', '2022-11-03 07:58:26', '2022-11-03 07:58:26', NULL),
(3, '6', 'Cathy ', '0725411236', 'cathy@gmail.com', 'Donec a magna vitae lacus auctor vulputate', 'Female', '2022-11-03 07:59:41', '2022-11-03 08:10:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(30) NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `stud_id` int(11) NOT NULL COMMENT 'student id',
  `ef_id` int(11) NOT NULL COMMENT 'track payment id',
  `slip_serial` varchar(100) NOT NULL COMMENT 'payment slip serial',
  `amount` double NOT NULL COMMENT 'amount paid',
  `school_term` int(11) DEFAULT NULL,
  `class_id` int(11) NOT NULL,
  `remarks` text COMMENT 'remarks; optional',
  `payment_date` date NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `stud_id`, `ef_id`, `slip_serial`, `amount`, `school_term`, `class_id`, `remarks`, `payment_date`, `created_at`, `updated_at`) VALUES
(3, 7, 3, '1687', 150000, NULL, 5, NULL, '2022-11-07', '2022-11-08 02:29:07', '2022-11-08 02:29:07'),
(4, 7, 3, '9815', 200000, NULL, 5, NULL, '2022-11-08', '2022-11-08 02:30:29', '2022-11-08 02:30:29'),
(5, 6, 4, '123', 65000, NULL, 3, NULL, '2022-11-10', '2022-11-10 06:45:49', '2022-11-10 06:45:49'),
(6, 2, 2, '919198', 250000, NULL, 1, NULL, '2022-11-09', '2022-11-10 06:46:25', '2022-11-10 06:46:25'),
(7, 5, 5, '984089', 650000, NULL, 4, NULL, '2022-11-11', '2022-11-11 08:33:39', '2022-11-11 08:33:39');

-- --------------------------------------------------------

--
-- Table structure for table `requirements`
--

DROP TABLE IF EXISTS `requirements`;
CREATE TABLE IF NOT EXISTS `requirements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(180) NOT NULL,
  `item_description` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='stores school requirements';

--
-- Dumping data for table `requirements`
--

INSERT INTO `requirements` (`id`, `item_name`, `item_description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'Mops', '2 of them', '2022-11-03 01:49:07', '2022-11-03 01:49:07', NULL),
(3, 'Scrubbing brush', '1, wooden handle', '2022-11-03 01:49:27', '2022-11-03 01:49:27', NULL),
(4, 'Ream of paper', 'Ruled or plane, 500 sheets', '2022-11-03 01:49:57', '2022-11-03 01:49:57', NULL),
(5, 'Brooms', '2, Locally made', '2022-11-03 02:03:22', '2022-11-03 02:03:22', NULL),
(6, 'Floor polish', '5 litre container, grey color', '2022-11-05 04:44:19', '2022-11-05 04:44:19', NULL),
(7, 'Bucket', '10 litre', '2022-11-05 04:45:31', '2022-11-05 04:45:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roll_call`
--

DROP TABLE IF EXISTS `roll_call`;
CREATE TABLE IF NOT EXISTS `roll_call` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `roll_call_date` date NOT NULL,
  `stud_status` varchar(20) NOT NULL COMMENT 'Absent, Present',
  `stud_reason` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sch_terms`
--

DROP TABLE IF EXISTS `sch_terms`;
CREATE TABLE IF NOT EXISTS `sch_terms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sch_term` int(11) NOT NULL,
  `sch_year` year(4) NOT NULL,
  `sch_sts` varchar(3) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sch_terms`
--

INSERT INTO `sch_terms` (`id`, `sch_term`, `sch_year`, `sch_sts`, `created_at`, `updated_at`) VALUES
(1, 1, 2022, 'no', '2022-11-06 07:36:49', '2022-11-06 07:39:48'),
(2, 2, 2022, 'yes', '2022-11-06 07:39:48', '2022-11-07 08:08:33'),
(3, 1, 2021, 'no', '2022-11-06 07:36:49', '2022-11-06 07:39:48'),
(4, 2, 2021, 'no', '2022-11-06 07:39:48', '2022-11-07 08:08:33');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `photo` text,
  `id_no` varchar(100) NOT NULL,
  `name` text NOT NULL,
  `gender` varchar(7) NOT NULL,
  `email` varchar(200) NOT NULL,
  `dob` date DEFAULT NULL,
  `house_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `class_id` int(11) NOT NULL,
  `requirement_id` int(11) DEFAULT NULL,
  `school_section` varchar(50) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `photo`, `id_no`, `name`, `gender`, `email`, `dob`, `house_id`, `parent_id`, `class_id`, `requirement_id`, `school_section`, `date_created`, `created_at`, `updated_at`) VALUES
(1, 'assets/uploads/students/1/1.jpg', '2022-001', 'James', 'Male', 'james@gmail.com', '1990-01-01', 1, NULL, 1, 1, 'Day', '2022-11-07 14:49:20', '2022-11-07 11:49:20', '2022-11-11 07:30:24'),
(2, 'assets/uploads/students/2/2.jpg', '2022-002', 'Grace', 'Female', 'grace@gmail.com', '1990-02-03', 1, NULL, 1, 2, 'Day', '2022-11-07 14:49:48', '2022-11-07 11:49:48', '2022-11-11 07:30:46'),
(3, 'assets/uploads/students/3/3.jpg', '2022-003', 'Mathew', 'Male', 'matt@ymail.com', '1990-11-03', 1, NULL, 2, NULL, 'Day', '2022-11-07 14:51:16', '2022-11-07 11:51:16', '2022-11-07 11:51:16'),
(4, 'assets/uploads/students/4/4.jpg', '2022-004', 'Jasmine', 'Female', 'jas@yahoo.com', '1990-04-03', 1, NULL, 5, NULL, 'Boarding', '2022-11-07 14:52:06', '2022-11-07 11:52:06', '2022-11-07 11:52:06'),
(5, 'assets/uploads/students/5/5.jpg', '2022-005', 'Elouise', 'Female', 'elouise2@gmail.com', '1990-02-03', 3, NULL, 4, 3, 'Boarding', '2022-11-07 14:52:37', '2022-11-07 11:52:37', '2022-11-11 07:30:53'),
(6, 'assets/uploads/students/6/6.jpg', '2022-006', 'Catherina', 'Female', 'cathy@gmail.com', '1991-02-03', 3, NULL, 3, NULL, 'Day', '2022-11-07 14:53:17', '2022-11-07 11:53:17', '2022-11-07 11:53:17'),
(7, 'assets/uploads/students/7/7.jpg', '2022-007', 'Drake', 'Male', 'drake@gmail.com', '1990-02-03', 2, NULL, 5, NULL, 'Boarding', '2022-11-07 14:53:46', '2022-11-07 11:53:46', '2022-11-07 11:53:46');

-- --------------------------------------------------------

--
-- Table structure for table `student_requirement_id`
--

DROP TABLE IF EXISTS `student_requirement_id`;
CREATE TABLE IF NOT EXISTS `student_requirement_id` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `requirement_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='store all requirements history for student';

--
-- Dumping data for table `student_requirement_id`
--

INSERT INTO `student_requirement_id` (`id`, `student_id`, `requirement_id`, `created_at`) VALUES
(1, 1, 1, '2022-11-06 07:38:51'),
(2, 1, 2, '2022-11-06 07:40:42'),
(3, 1, 1, '2022-11-11 07:30:24'),
(4, 2, 2, '2022-11-11 07:30:46'),
(5, 5, 3, '2022-11-11 07:30:53');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `cover_img` text NOT NULL,
  `about_content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `name`, `email`, `contact`, `cover_img`, `about_content`) VALUES
(1, 'School Fees Payment System', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
CREATE TABLE IF NOT EXISTS `teachers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `photo` varchar(100) NOT NULL,
  `teacher_names` varchar(190) NOT NULL,
  `teacher_dob` date NOT NULL,
  `teacher_sex` varchar(8) NOT NULL,
  `teacher_tel` varchar(20) NOT NULL,
  `teacher_email` varchar(72) NOT NULL,
  `teacher_education` text NOT NULL,
  `teacher_location_address` text NOT NULL,
  `teacher_salary` varchar(50) NOT NULL DEFAULT '0.0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='teacher details';

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `photo`, `teacher_names`, `teacher_dob`, `teacher_sex`, `teacher_tel`, `teacher_email`, `teacher_education`, `teacher_location_address`, `teacher_salary`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'assets/uploads/teachers/1/1.jpg', 'Okello Julius', '1994-08-04', 'Male', '0777123456', 'ok.jus@gmail.com', 'Bachelors in Education', 'Kireka Kamuli road', '700,000', '2022-11-03 09:51:02', '2022-11-03 10:01:26', NULL),
(2, 'assets/uploads/teachers/2/2.jpg', 'Nakato Patricia', '1989-01-05', 'Female', '0775200300', 'nak@ymail.com', 'Phd', 'Donec molestie sit amet tortor eu aliquam', '1,200,000', '2022-11-03 10:03:21', '2022-11-03 10:03:21', NULL),
(3, 'assets/uploads/teachers/3/3.jpg', 'Lorrain Mie', '1982-08-16', 'Female', '0705125698', 'lor@yahoo.com', 'Bachelors', 'Integer suscipit et est et pellentesque. In pharetra', '850,000', '2022-11-03 10:04:51', '2022-11-03 10:04:51', NULL),
(4, 'assets/uploads/teachers/4/4.jpg', 'Mugabe Jonathan', '1982-09-29', 'Male', '0321200300', 'muga@gmail.com', 'Diploma', 'Nullam feugiat urna sit amet tellus congue', '954,000', '2022-11-03 10:06:23', '2022-11-03 10:06:23', NULL),
(5, 'assets/uploads/teachers/5/5.jpg', 'Pamela Julie', '1989-07-27', 'Female', '0321200355', 'pam.jul@gmail.com', 'Bachelors', 'Nullam feugiat urna sit amet tellus congue, in porttitor quam dapibus', '995,400', '2022-11-04 12:17:36', '2022-11-04 12:17:36', NULL),
(6, 'assets/uploads/teachers/6/6.jpg', 'Ambrose Melon', '1992-12-12', 'Male', '0707120256', 'ambrose@yahoo.co.uk', 'Diploma', 'In pharetra libero sit amet orci aliquam', '750,000', '2022-11-04 12:18:50', '2022-11-04 12:18:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `track_fee_payments`
--

DROP TABLE IF EXISTS `track_fee_payments`;
CREATE TABLE IF NOT EXISTS `track_fee_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `stud_id` int(11) NOT NULL COMMENT 'student pkey',
  `stud_no` varchar(100) NOT NULL COMMENT 'student number',
  `ef_no` varchar(200) NOT NULL COMMENT 'enrollment reference number',
  `school_term` int(11) NOT NULL COMMENT 'school term',
  `total_fee` double NOT NULL DEFAULT '0' COMMENT 'fee to pay',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completion_sts` varchar(3) NOT NULL DEFAULT 'no',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `track_fee_payments`
--

INSERT INTO `track_fee_payments` (`id`, `stud_id`, `stud_no`, `ef_no`, `school_term`, `total_fee`, `date_created`, `completion_sts`, `created_at`, `updated_at`) VALUES
(2, 2, '2022-002', '2-2022-11-FXJ7AM', 2, 370000, '2022-11-08 15:55:06', 'no', '2022-11-08 12:55:06', '2022-11-08 12:55:06'),
(3, 7, '2022-007', '3-2022-11-Z44MLQ', 2, 700000, '2022-11-08 16:13:18', 'no', '2022-11-08 01:13:18', '2022-11-08 01:13:18'),
(4, 6, '2022-006', '4-2022-11-WNLWNG', 2, 370000, '2022-11-10 21:45:25', 'no', '2022-11-10 06:45:25', '2022-11-10 06:45:25'),
(5, 5, '2022-005', '5-2022-11-GY1QXU', 1, 700000, '2022-11-11 11:33:05', 'no', '2022-11-11 08:33:05', '2022-11-11 08:33:05');

-- --------------------------------------------------------

--
-- Table structure for table `track_requirements`
--

DROP TABLE IF EXISTS `track_requirements`;
CREATE TABLE IF NOT EXISTS `track_requirements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `requirement_id` varchar(50) NOT NULL,
  `req_sts` varchar(3) DEFAULT NULL,
  `school_term` int(3) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='tracks student requirements';

--
-- Dumping data for table `track_requirements`
--

INSERT INTO `track_requirements` (`id`, `student_id`, `class_id`, `requirement_id`, `req_sts`, `school_term`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '5, 7, 6, 2', 'no', 2, '2022-11-11 07:30:24', '2022-11-11 07:30:24'),
(2, 2, 1, '5, 7, 6, 2, 4, 3', 'yes', 2, '2022-11-11 07:30:46', '2022-11-11 07:30:46'),
(3, 5, 4, '3', 'no', 2, '2022-11-11 07:30:53', '2022-11-11 07:30:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `access_level` varchar(10) NOT NULL,
  `password` text NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '3' COMMENT '1=Admin,2=Bursar,3=Teacher',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `access_level`, `password`, `type`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin', 'admin', 'Admin', '$2y$10$TPquiaalBNmvI5fB9DUucu4XWFCufj.U5hk0dHtqu1r5mktahTW1i', 1, '2022-10-23 21:18:46', '2022-10-31 05:10:41', NULL),
(2, 'Peter', 'peter', 'Bursar', '$2y$10$Rsh7b4vwoxZrw1zFnMLW7Ox4c99hFfjMYqdlnHb5JBNV3lNs1z.1y', 2, '2022-10-23 21:18:46', '2022-10-23 21:18:46', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
