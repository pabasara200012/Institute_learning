-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 15, 2024 at 09:15 AM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `diploma_institute`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE IF NOT EXISTS `attendance` (
  `attendance_id` int NOT NULL AUTO_INCREMENT,
  `student_id` int DEFAULT NULL,
  `module_id` int DEFAULT NULL,
  `week` int DEFAULT NULL,
  `attendance_status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`attendance_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `student_id`, `module_id`, `week`, `attendance_status`) VALUES
(1, 123, 6, 1, 1),
(2, 123, 6, 5, 1),
(3, 123, 6, 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `coordinators`
--

DROP TABLE IF EXISTS `coordinators`;
CREATE TABLE IF NOT EXISTS `coordinators` (
  `coordinator_id` int NOT NULL AUTO_INCREMENT,
  `lecturer_id` int DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `department_id` int DEFAULT NULL,
  `account_password` varchar(50) NOT NULL,
  PRIMARY KEY (`coordinator_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `coordinators`
--

INSERT INTO `coordinators` (`coordinator_id`, `lecturer_id`, `name`, `department_id`, `account_password`) VALUES
(5, 123, 'Dhanushka Sisil Bandara Wijekoon', 1, '123'),
(2, 451, 'test test', 1, '$2y$10$iIXo.hURYjHk4lzC0qsYj.TnKNXhbBwReI7kBsljR.R'),
(3, 16078, 'dhfzdshfvzhd', 3, '$2y$10$6aIsJX5QiEQ0BuiHquE/2ezKLTS6NvodHXarhTeMsFh'),
(6, 789, 'Test Cor', 1, '123');

-- --------------------------------------------------------

--
-- Table structure for table `coordinator_modules`
--

DROP TABLE IF EXISTS `coordinator_modules`;
CREATE TABLE IF NOT EXISTS `coordinator_modules` (
  `coordinator_id` int NOT NULL,
  `module_id` int NOT NULL,
  PRIMARY KEY (`coordinator_id`,`module_id`),
  KEY `module_id` (`module_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `coordinator_modules`
--

INSERT INTO `coordinator_modules` (`coordinator_id`, `module_id`) VALUES
(2, 3),
(2, 6),
(3, 2),
(3, 6),
(5, 2),
(5, 3),
(5, 6),
(6, 2);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `department_id` int NOT NULL,
  `department_name` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`) VALUES
(1, 'Statistics Department'),
(2, 'Computer Science Department'),
(3, 'Chemistry Department'),
(4, 'Mathematics Department');

-- --------------------------------------------------------

--
-- Table structure for table `diplomas`
--

DROP TABLE IF EXISTS `diplomas`;
CREATE TABLE IF NOT EXISTS `diplomas` (
  `diploma_id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`diploma_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `diplomas`
--

INSERT INTO `diplomas` (`diploma_id`, `code`, `title`, `description`) VALUES
(1, '#123', 'Diploma', 'Test Diploma 123'),
(2, '#12345', 'Tes', 'wjweffdhfvhsdvfhsfvh'),
(3, 'ndnf', 'bjdbfjsdbfjbf', 'nasdhshfsdvfasdfjd'),
(4, 'jjbjjvjvjvvjv', 'bjdbfjsdbfjbf', 'nasdhshfsdvfasdfjd');

-- --------------------------------------------------------

--
-- Table structure for table `diploma_modules`
--

DROP TABLE IF EXISTS `diploma_modules`;
CREATE TABLE IF NOT EXISTS `diploma_modules` (
  `diploma_module_id` int NOT NULL AUTO_INCREMENT,
  `diploma_id` int NOT NULL,
  `module_id` int NOT NULL,
  PRIMARY KEY (`diploma_module_id`),
  KEY `diploma_id` (`diploma_id`),
  KEY `module_id` (`module_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `diploma_modules`
--

INSERT INTO `diploma_modules` (`diploma_module_id`, `diploma_id`, `module_id`) VALUES
(1, 4, 6),
(2, 4, 2);

-- --------------------------------------------------------

--
-- Table structure for table `enrollment`
--

DROP TABLE IF EXISTS `enrollment`;
CREATE TABLE IF NOT EXISTS `enrollment` (
  `enrollment_id` int NOT NULL,
  `student_id` int DEFAULT NULL,
  `module_id` int DEFAULT NULL,
  `semester_id` int DEFAULT NULL,
  PRIMARY KEY (`enrollment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_modules`
--

DROP TABLE IF EXISTS `lecturer_modules`;
CREATE TABLE IF NOT EXISTS `lecturer_modules` (
  `lecturer_id` int NOT NULL,
  `module_id` int NOT NULL,
  PRIMARY KEY (`lecturer_id`,`module_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lecturer_modules`
--

INSERT INTO `lecturer_modules` (`lecturer_id`, `module_id`) VALUES
(4578, 1),
(7895, 2);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
CREATE TABLE IF NOT EXISTS `modules` (
  `module_id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `credit_value` int DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`module_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`module_id`, `code`, `title`, `credit_value`, `department`) VALUES
(6, 'sjBEJFfbD', 'jzbfbfzbfsdkfb', 3, '1'),
(2, '146464', 'dfjjfbsbgbsg', 2, '3'),
(3, 'sjBEJFfbD', 'jzbfbfzbfsdkfb', 2, '1');

-- --------------------------------------------------------

--
-- Table structure for table `module_content`
--

DROP TABLE IF EXISTS `module_content`;
CREATE TABLE IF NOT EXISTS `module_content` (
  `id` int NOT NULL AUTO_INCREMENT,
  `module_id` int NOT NULL,
  `week` int NOT NULL,
  `content_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `module_content`
--

INSERT INTO `module_content` (`id`, `module_id`, `week`, `content_path`) VALUES
(6, 6, 4, 'uploads/Screenshot 2024-09-08 080020.png');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

DROP TABLE IF EXISTS `results`;
CREATE TABLE IF NOT EXISTS `results` (
  `result_id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `module_id` int NOT NULL,
  `result` varchar(50) NOT NULL,
  PRIMARY KEY (`result_id`),
  UNIQUE KEY `unique_result` (`student_id`,`module_id`),
  KEY `fk_module` (`module_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`result_id`, `student_id`, `module_id`, `result`) VALUES
(1, 123, 6, 'B');

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

DROP TABLE IF EXISTS `semesters`;
CREATE TABLE IF NOT EXISTS `semesters` (
  `semester_id` int NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`semester_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `student_number` int NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `password` varchar(50) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `diploma_id` int DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`student_number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_number`, `name`, `password`, `address`, `diploma_id`, `status`) VALUES
(123, 'dhanushka sisil', '123', 'Colombo, Sri Lanka', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`) VALUES
(1, 'admin', 'admin');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
