-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 01, 2018 at 01:55 PM
-- Server version: 5.7.19
-- PHP Version: 7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jyothi1`
--

-- --------------------------------------------------------

--
-- Table structure for table `assign_shifts`
--

DROP TABLE IF EXISTS `assign_shifts`;
CREATE TABLE IF NOT EXISTS `assign_shifts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` int(10) UNSIGNED NOT NULL,
  `batch_id` int(10) UNSIGNED DEFAULT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `shift_id` int(10) UNSIGNED NOT NULL,
  `work_type_id` int(10) UNSIGNED NOT NULL,
  `status_id` int(10) UNSIGNED DEFAULT NULL,
  `leave_id` int(10) UNSIGNED DEFAULT NULL,
  `otHours` double(8,2) DEFAULT NULL,
  `nowdate` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `changed_department_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `foreign_user_department` (`department_id`),
  KEY `foreign_user_batch` (`batch_id`),
  KEY `foreign_user_employee` (`employee_id`),
  KEY `foreign_user_shift` (`shift_id`),
  KEY `foreign_user_status` (`status_id`),
  KEY `foreign_user_leave` (`leave_id`),
  KEY `foreign_user_work_type` (`work_type_id`),
  KEY `foreign_user_change_department_id` (`changed_department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=148 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assign_shifts`
--

INSERT INTO `assign_shifts` (`id`, `department_id`, `batch_id`, `employee_id`, `shift_id`, `work_type_id`, `status_id`, `leave_id`, `otHours`, `nowdate`, `created_at`, `updated_at`, `changed_department_id`) VALUES
(115, 4, 25, 5, 1, 1, 2, 5, NULL, '2018-03-26', NULL, '2018-03-27 03:06:49', 1),
(114, 1, 25, 5, 1, 1, 1, NULL, NULL, '2018-03-24', NULL, NULL, 0),
(113, 4, 24, 4, 1, 2, 2, 5, NULL, '2018-03-28', NULL, '2018-03-30 02:09:34', 1),
(112, 1, 24, 4, 1, 1, 1, NULL, NULL, '2018-03-27', NULL, NULL, 0),
(111, 4, 24, 4, 1, 1, 1, NULL, NULL, '2018-03-26', NULL, '2018-03-27 02:10:03', 1),
(110, 1, 24, 4, 1, 1, 1, NULL, NULL, '2018-03-24', NULL, NULL, 0),
(109, 1, 23, 3, 1, 1, 1, NULL, NULL, '2018-03-28', NULL, NULL, 0),
(108, 1, 23, 3, 1, 1, 1, NULL, NULL, '2018-03-27', NULL, NULL, 0),
(119, 1, 15, 1, 2, 2, 2, NULL, NULL, '2018-03-27', NULL, NULL, 0),
(118, 1, 15, 1, 2, 2, 2, NULL, NULL, '2018-03-26', NULL, NULL, 0),
(107, 1, 23, 3, 1, 1, 1, NULL, NULL, '2018-03-26', NULL, NULL, 0),
(106, 1, 23, 3, 1, 1, 1, NULL, NULL, '2018-03-24', NULL, NULL, 0),
(105, 1, 22, 2, 1, 2, 4, NULL, 2.00, '2018-03-28', NULL, '2018-03-30 02:52:36', 0),
(104, 1, 22, 2, 1, 1, 1, NULL, NULL, '2018-03-27', NULL, NULL, 0),
(103, 1, 22, 2, 1, 1, 1, NULL, NULL, '2018-03-26', NULL, NULL, 0),
(102, 1, 22, 2, 1, 1, 1, NULL, NULL, '2018-03-24', NULL, NULL, 0),
(101, 1, 21, 1, 1, 1, 1, NULL, NULL, '2018-03-30', NULL, NULL, 0),
(100, 1, 21, 1, 1, 1, 1, NULL, NULL, '2018-03-29', NULL, NULL, 0),
(116, 1, 25, 5, 1, 1, 1, NULL, NULL, '2018-03-27', NULL, NULL, 0),
(123, 1, 27, 5, 1, 1, 4, NULL, 4.00, '2018-03-28', NULL, '2018-03-30 02:52:19', 1),
(120, 1, 15, 1, 2, 2, 2, NULL, NULL, '2018-03-28', NULL, NULL, 0),
(124, 1, 28, 1, 1, 1, 1, NULL, NULL, '2018-04-17', NULL, NULL, 0),
(125, 1, 28, 1, 1, 1, 1, NULL, NULL, '2018-04-18', NULL, NULL, 0),
(126, 1, 28, 1, 1, 1, 1, NULL, NULL, '2018-04-19', NULL, NULL, 0),
(127, 1, 28, 1, 1, 1, 1, NULL, NULL, '2018-04-20', NULL, NULL, 0),
(128, 1, 28, 1, 1, 1, 1, NULL, NULL, '2018-04-21', NULL, NULL, 0),
(129, 1, 28, 1, 1, 1, 1, NULL, NULL, '2018-04-23', NULL, NULL, 0),
(130, 1, 28, 1, 1, 1, 1, NULL, NULL, '2018-04-24', NULL, NULL, 0),
(131, 1, 28, 1, 1, 1, 1, NULL, NULL, '2018-04-25', NULL, NULL, 0),
(132, 1, 29, 2, 1, 1, 1, NULL, NULL, '2018-04-17', NULL, NULL, 0),
(133, 1, 29, 2, 1, 1, 1, NULL, NULL, '2018-04-18', NULL, NULL, 0),
(134, 1, 29, 2, 1, 1, 1, NULL, NULL, '2018-04-19', NULL, NULL, 0),
(135, 1, 29, 2, 1, 1, 1, NULL, NULL, '2018-04-20', NULL, NULL, 0),
(136, 1, 29, 2, 1, 1, 1, NULL, NULL, '2018-04-21', NULL, NULL, 0),
(137, 1, 29, 2, 1, 1, 1, NULL, NULL, '2018-04-23', NULL, NULL, 0),
(138, 1, 29, 2, 1, 1, 1, NULL, NULL, '2018-04-24', NULL, NULL, 0),
(139, 1, 29, 2, 1, 1, 1, NULL, NULL, '2018-04-25', NULL, NULL, 0),
(140, 1, 30, 3, 1, 1, 1, NULL, NULL, '2018-04-17', NULL, NULL, 0),
(141, 1, 30, 3, 1, 1, 1, NULL, NULL, '2018-04-18', NULL, NULL, 0),
(142, 1, 30, 3, 1, 1, 1, NULL, NULL, '2018-04-19', NULL, NULL, 0),
(143, 1, 30, 3, 1, 1, 1, NULL, NULL, '2018-04-20', NULL, NULL, 0),
(144, 1, 30, 3, 1, 1, 1, NULL, NULL, '2018-04-21', NULL, NULL, 0),
(145, 1, 30, 3, 1, 1, 1, NULL, NULL, '2018-04-23', NULL, NULL, 0),
(146, 1, 30, 3, 1, 1, 1, NULL, NULL, '2018-04-24', NULL, NULL, 0),
(147, 1, 30, 3, 1, 1, 1, NULL, NULL, '2018-04-25', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

DROP TABLE IF EXISTS `batches`;
CREATE TABLE IF NOT EXISTS `batches` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fromDate` date NOT NULL,
  `toDate` date NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `foreign_user` (`department_id`),
  KEY `foreign_user_emp_id` (`employee_id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `batches`
--

INSERT INTO `batches` (`id`, `fromDate`, `toDate`, `department_id`, `status`, `created_at`, `updated_at`, `employee_id`) VALUES
(25, '2018-03-24', '2018-03-28', 4, 'pending', '2018-03-23 22:55:57', '2018-03-23 22:55:57', 5),
(15, '2018-03-25', '2018-03-28', 1, 'confirmed', '2018-03-23 22:16:34', '2018-03-24 03:06:06', 1),
(24, '2018-03-24', '2018-03-28', 4, 'pending', '2018-03-23 22:55:57', '2018-03-23 22:55:57', 4),
(23, '2018-03-24', '2018-03-28', 1, 'pending', '2018-03-23 22:55:57', '2018-03-23 22:55:57', 3),
(22, '2018-03-24', '2018-03-28', 1, 'pending', '2018-03-23 22:55:57', '2018-03-23 22:55:57', 2),
(21, '2018-03-29', '2018-03-30', 1, 'pending', '2018-03-23 22:55:57', '2018-03-23 22:55:57', 1),
(27, '2018-03-28', '2018-03-28', 1, 'confirmed', '2018-03-30 02:51:16', '2018-03-30 02:51:16', 5),
(28, '2018-04-17', '2018-04-25', 1, 'pending', '2018-04-01 03:33:11', '2018-04-01 03:33:11', 1),
(29, '2018-04-17', '2018-04-25', 1, 'pending', '2018-04-01 03:33:12', '2018-04-01 03:33:12', 2),
(30, '2018-04-17', '2018-04-25', 1, 'pending', '2018-04-01 03:33:12', '2018-04-01 03:33:12', 3);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(2, 'Staff', '2018-02-27 23:02:21', '2018-02-27 23:02:21');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Marketing', '2018-02-28 00:12:03', '2018-02-28 00:12:03'),
(4, 'Finance', '2018-02-28 00:12:03', '2018-02-28 00:12:03');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
CREATE TABLE IF NOT EXISTS `employees` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED NOT NULL,
  `cost_centre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost_centre_desc` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gl_accounts` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gl_description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `foreign_user` (`department_id`),
  KEY `foreign_user_location` (`location_id`),
  KEY `foreign_user_category` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `gender`, `department_id`, `category_id`, `location_id`, `cost_centre`, `cost_centre_desc`, `gl_accounts`, `gl_description`, `created_at`, `updated_at`) VALUES
(1, 'BALACHANDRAN', 'male', 1, 2, 2, 'Cost Centre', 'Cost Centre Description', 'GL Accounts', 'GL Description', '2018-03-10 17:50:09', '2018-03-10 19:03:06'),
(2, 'Rajaraman', 'male', 1, 2, 2, 'Cost Centre 2', 'Cost Centre Description 2', 'GL Accounts 2', 'GL Description 2', '2018-03-11 04:21:49', '2018-03-11 04:21:49'),
(3, 'Siva', 'male', 1, 2, 2, 'Cost Centre 3', 'Cost Centre Description 3', 'GL Accounts 3', 'GL Description 3', '2018-03-11 04:22:26', '2018-03-11 04:22:26'),
(4, 'Bala', 'male', 4, 2, 2, 'Cost Centre 4', 'Cost Centre Description 4', 'GL Accounts 4', 'GL Description 4', '2018-03-11 04:22:56', '2018-03-11 04:22:56'),
(5, 'Baba', 'male', 4, 2, 2, 'Cost Centre 5', 'Cost Centre Description 5', 'GL Accounts 5', 'GL Description 5', '2018-03-11 04:23:30', '2018-03-11 04:23:45');

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
--

DROP TABLE IF EXISTS `leaves`;
CREATE TABLE IF NOT EXISTS `leaves` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leaves`
--

INSERT INTO `leaves` (`id`, `name`, `created_at`, `updated_at`) VALUES
(2, 'Absent', '2018-02-27 23:18:47', '2018-02-27 23:18:47'),
(3, 'Comp-off', '2018-02-27 23:18:47', '2018-02-27 23:18:47'),
(4, 'Leave', '2018-02-27 23:18:47', '2018-02-27 23:18:47'),
(5, 'LOP', '2018-02-27 23:18:47', '2018-02-27 23:18:47');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
CREATE TABLE IF NOT EXISTS `locations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`, `created_at`, `updated_at`) VALUES
(2, 'Pondicherry', '2018-03-10 13:06:11', '2018-03-10 13:06:11');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2016_02_24_121007_create_departments_table', 1),
(2, '2017_04_10_000000_create_users_table', 1),
(3, '2017_04_10_000001_create_password_resets_table', 1),
(4, '2017_04_10_000003_create_roles_table', 1),
(5, '2017_04_10_000004_create_users_roles_table', 1),
(6, '2018_02_24_121239_create_statuses_table', 1),
(7, '2018_02_24_121322_create_leaves_table', 1),
(14, '2018_03_10_121429_create_shifts_table', 3),
(9, '2018_02_24_125107_create_categories_table', 1),
(17, '2018_03_10_196529_create_employees_table', 5),
(12, '2018_02_28_054735_create_work_types_table', 2),
(15, '2018_03_10_183340_create_locations_table', 4),
(18, '2018_03_11_235755_create_batches_table', 6),
(19, '2018_03_11_235836_create_assign_shifts_table', 7),
(20, '2018_03_24_033332_add_emp_id_to_batches', 8),
(21, '2018_03_25_231938_rename_shift_table_column', 9),
(22, '2018_03_25_232741_add_column_assign_shift_table', 10);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `weight` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `weight`) VALUES
(1, 'administrator', 0),
(2, 'hr', 0),
(3, 'dept', 0);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

DROP TABLE IF EXISTS `shifts`;
CREATE TABLE IF NOT EXISTS `shifts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `allias` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `intime` time NOT NULL,
  `outtime` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `foreign_user` (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `department_id`, `name`, `allias`, `intime`, `outtime`, `created_at`, `updated_at`) VALUES
(1, 1, 'Sample Shift', 'SS1', '08:01:00', '16:00:00', '2018-03-10 10:28:37', '2018-03-10 10:41:01'),
(2, 1, 'Sample Shift2', 'SS2', '15:00:00', '23:00:00', '2018-03-10 10:28:37', '2018-03-10 10:41:01'),
(3, 1, 'Sample Shift3', 'SS3', '00:00:00', '08:00:00', '2018-03-10 10:28:37', '2018-03-10 10:41:01');

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--

DROP TABLE IF EXISTS `statuses`;
CREATE TABLE IF NOT EXISTS `statuses` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `foreign_user` (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `statuses`
--

INSERT INTO `statuses` (`id`, `department_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 1, 'Present', '2018-02-27 23:48:45', '2018-02-28 00:16:22'),
(2, 1, 'Leave', '2018-02-27 23:48:45', '2018-02-28 00:16:22'),
(3, 1, 'On-Duty', '2018-02-27 23:48:45', '2018-02-28 00:16:22'),
(4, 1, 'OT', '2018-02-27 23:48:45', '2018-02-28 00:16:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `confirmation_code` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confirmed` tinyint(1) NOT NULL DEFAULT '1',
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `foreign_user` (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `active`, `confirmation_code`, `confirmed`, `department_id`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin', 'admin@jyothi.com', '$2y$10$0KAERxprdcVK.qHnep.rjO0y3EWomKUv/Z7WqnnUGf5s8O3AJMVYy', 1, NULL, 1, NULL, 'aFEPuFPlKLq9KmUmjRlkyQpo8o0pM8xeuIAp0VvFoGzWbiKs3uKnnbdC1L2c', '2018-02-26 23:06:47', '2018-02-26 23:06:47', NULL),
(3, 'BALACHANDRAN', 'hr@jyothi.com', '$2y$10$UmQ3ML5OjGBX4SRM5Off8.F3vSwZMZIMgoxqJd4pjvS0PPnrCghbi', 1, NULL, 1, NULL, '3dhTrVd4dRLOu8QvELPuK3Hfhss2U6N3TERokCQCMLw2u43CXdHb7xEJyo5Z', '2018-02-26 23:28:29', '2018-02-26 23:28:29', NULL),
(5, 'Rajaraman', 'prod@dept.com', '$2y$10$vwPydT2FKLt3yMZwhJawOOqn/3FN0RJAShZ96Ycr7PQuKKIa.HD/m', 1, NULL, 1, 1, 'URQhyhCX2LZB8XEF3VLPtFoi9KCAUjZz7SaRCSgUp3CEespEcRMV3h7B1vL9', '2018-02-27 00:06:50', '2018-02-27 00:06:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_roles`
--

DROP TABLE IF EXISTS `users_roles`;
CREATE TABLE IF NOT EXISTS `users_roles` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  UNIQUE KEY `users_roles_user_id_role_id_unique` (`user_id`,`role_id`),
  KEY `foreign_role` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_roles`
--

INSERT INTO `users_roles` (`user_id`, `role_id`) VALUES
(1, 1),
(3, 2),
(4, 3),
(5, 3);

-- --------------------------------------------------------

--
-- Table structure for table `work_types`
--

DROP TABLE IF EXISTS `work_types`;
CREATE TABLE IF NOT EXISTS `work_types` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `foreign_user` (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `work_types`
--

INSERT INTO `work_types` (`id`, `department_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 1, 'Sample Work Type1', '2018-03-10 11:31:52', '2018-03-10 11:37:44'),
(2, 1, 'Sample Work Type2', '2018-03-10 11:31:52', '2018-03-10 11:37:44');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
