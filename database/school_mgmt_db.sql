-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 23, 2022 at 07:27 AM
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='class details, teacher, fees';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='classes';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='stores error logs';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='stores school requirements';

-- --------------------------------------------------------

--
-- Table structure for table `roll_call`
--

DROP TABLE IF EXISTS `roll_call`;
CREATE TABLE IF NOT EXISTS `roll_call` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_names` varchar(100) DEFAULT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `roll_call_date` date NOT NULL,
  `stud_status` varchar(20) NOT NULL COMMENT 'Absent, Present',
  `stud_reason` text,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='store all requirements history for student';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='teacher details';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='tracks student requirements';

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email_address` varchar(72) DEFAULT NULL,
  `access_level` varchar(10) NOT NULL,
  `password` text NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '3' COMMENT '1=Admin,2=Bursar,3=Teacher',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
