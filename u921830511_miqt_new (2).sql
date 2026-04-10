-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 06, 2026 at 10:32 AM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u921830511_miqt_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_events`
--

CREATE TABLE `academic_events` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_type` enum('holiday','exam','event','meeting','other') NOT NULL,
  `is_recurring` enum('yes','no') DEFAULT 'no',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `module`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'Login', 'Authentication', 'User logged in', '::1', '2025-10-27 07:59:38'),
(2, 1, 'Add Class', 'Classes', 'Added class: fds', '::1', '2025-10-27 08:05:07'),
(3, 1, 'Mark Attendance', 'Attendance', 'Marked attendance for 2 students', '::1', '2025-10-27 08:15:36'),
(4, 1, 'Login', 'Authentication', 'User logged in', '127.0.0.1', '2025-10-29 17:16:39'),
(5, 1, 'Login', 'Authentication', 'User logged in', '::1', '2025-10-29 17:17:40'),
(6, 1, 'Logout', 'Authentication', 'User logged out', '::1', '2025-10-29 17:18:01'),
(7, 1, 'Login', 'Authentication', 'User logged in', '::1', '2025-10-29 17:18:08'),
(8, 1, 'Add Teacher', 'HR', 'Added teacher: Ghulam Mujtaba', '::1', '2025-10-29 17:28:30'),
(9, 1, 'Mark Attendance', 'Attendance', 'Marked teacher attendance for 2025-10-29', '::1', '2025-10-29 17:40:39'),
(10, 1, 'Update Settings', 'Settings', 'Updated system settings', '::1', '2025-10-29 17:59:56'),
(11, 1, 'Update Settings', 'Settings', 'Updated system settings', '::1', '2025-10-29 18:00:07'),
(12, 1, 'Update Class Teacher', 'Classes', 'Updated teacher for class ID: 1', '::1', '2025-10-29 19:06:13'),
(13, 1, 'Login', 'Authentication', 'User logged in', '127.0.0.1', '2025-10-29 21:37:06'),
(14, 1, 'Logout', 'Authentication', 'User logged out', '127.0.0.1', '2025-10-29 21:56:20'),
(15, 2, 'Login', 'Authentication', 'User logged in', '127.0.0.1', '2025-10-29 21:56:36'),
(16, 2, 'Save Daily Progress', 'Progress', 'Saved daily progress for date: 2025-10-30', '127.0.0.1', '2025-10-29 22:03:55'),
(17, 2, 'Edit Class', 'Classes', 'Updated class: Hifz Class B (ID: 2)', '127.0.0.1', '2025-10-29 22:28:43'),
(18, 2, 'Save Daily Progress', 'Progress', 'Saved daily progress for date: 2025-10-30', '127.0.0.1', '2025-10-29 23:27:59'),
(19, 2, 'Logout', 'Authentication', 'User logged out', '127.0.0.1', '2025-10-30 09:54:59'),
(20, 1, 'Login', 'Authentication', 'User logged in', '127.0.0.1', '2025-10-30 09:55:11'),
(21, 1, 'Edit Student', 'Students', 'Updated student: Ali Hassan', '127.0.0.1', '2025-10-30 11:50:58'),
(22, 1, 'Edit Student', 'Students', 'Updated student: Ali Hassan', '127.0.0.1', '2025-10-30 11:53:05'),
(23, 1, 'Edit Student', 'Students', 'Updated student: Ali Hassan', '127.0.0.1', '2025-10-30 12:06:08'),
(24, 1, 'Login', 'Authentication', 'User logged in', '127.0.0.1', '2025-10-31 07:01:54'),
(25, 1, 'Logout', 'Authentication', 'User logged out', '127.0.0.1', '2025-10-31 07:42:34'),
(26, 2, 'Login', 'Authentication', 'User logged in', '127.0.0.1', '2025-10-31 07:42:41'),
(27, 2, 'Create Exam', 'Exams', 'Created exam: Para 1 Test', '127.0.0.1', '2025-10-31 08:12:41'),
(28, 2, 'Add Results', 'Exams', 'Added results for exam: Para 1 Test', '127.0.0.1', '2025-10-31 08:19:05'),
(29, 1, 'Login', 'Authentication', 'User logged in', '127.0.0.1', '2025-11-01 10:40:51'),
(30, 1, 'Edit Student', 'Students', 'Updated student: Aisha Fatima', '127.0.0.1', '2025-11-01 13:46:26'),
(31, 1, 'Import Students', 'Students', 'Imported 2 students (skipped 0)', '127.0.0.1', '2025-11-10 18:29:57'),
(32, 1, 'Save Daily Progress', 'Progress', 'Saved daily progress for date: 2025-11-11', '127.0.0.1', '2025-11-10 20:22:53'),
(33, 1, 'Login', 'Authentication', 'User logged in', '127.0.0.1', '2025-11-11 16:48:49'),
(34, 1, 'Login', 'Authentication', 'User logged in', '127.0.0.1', '2025-11-11 18:34:08'),
(35, 1, 'Add Results', 'Exams', 'Added results for exam: Para 1 Test', '127.0.0.1', '2025-11-11 19:14:46'),
(36, 1, 'Mark Attendance', 'Attendance', 'Marked attendance for 2 students', '127.0.0.1', '2025-11-11 19:15:48'),
(37, 1, 'Login', 'Authentication', 'User logged in', '127.0.0.1', '2025-11-19 07:22:53'),
(38, 1, 'Login', 'Authentication', 'User logged in', '127.0.0.1', '2025-11-20 18:27:47'),
(39, 1, 'Login', 'Authentication', 'User logged in', '127.0.0.1', '2025-11-21 17:30:09'),
(40, 1, 'Save Daily Progress', 'Progress', 'Saved daily progress for date: 2025-11-21', '127.0.0.1', '2025-11-21 17:32:39'),
(41, 2, 'Login', 'Authentication', 'User logged in', '127.0.0.1', '2025-11-21 19:47:21'),
(42, 1, 'Login', 'Authentication', 'User logged in', '23.106.249.35', '2025-11-26 13:55:13'),
(43, 1, 'Edit Teacher', 'HR', 'Updated teacher: Ghulam Mujtaba', '23.106.249.35', '2025-11-26 13:58:58'),
(44, 1, 'Edit Student', 'Students', 'Updated student: Ahmed ali Khan', '23.106.249.35', '2025-11-26 14:00:32'),
(45, 1, 'Logout', 'Authentication', 'User logged out', '23.106.249.35', '2025-11-26 14:01:54'),
(46, 1, 'Login', 'Authentication', 'User logged in', '103.179.241.59', '2025-11-26 15:37:57'),
(47, 1, 'Login', 'Authentication', 'User logged in', '116.58.42.132', '2025-11-27 11:43:46'),
(48, 1, 'Logout', 'Authentication', 'User logged out', '116.58.42.132', '2025-11-27 11:43:52'),
(49, 1, 'Login', 'Authentication', 'User logged in', '116.58.42.132', '2025-11-27 11:44:59'),
(50, 1, 'Logout', 'Authentication', 'User logged out', '116.58.42.132', '2025-11-27 11:45:10'),
(51, 1, 'Login', 'Authentication', 'User logged in', '116.58.42.132', '2025-11-27 11:46:56'),
(52, 1, 'Login', 'Authentication', 'User logged in', '116.58.42.132', '2025-11-29 08:30:56'),
(53, 1, 'Login', 'Authentication', 'User logged in', '116.58.42.132', '2025-12-01 11:38:28'),
(54, 2, 'Login', 'Authentication', 'User logged in', '202.142.168.70', '2025-12-02 05:00:38'),
(55, 2, 'Update Class Teacher', 'Classes', 'Updated teacher for class ID: 2', '202.142.168.70', '2025-12-02 05:10:17'),
(56, 2, 'Add Results', 'Exams', 'Added results for exam: Para 1 Test', '202.142.168.70', '2025-12-02 05:11:29'),
(57, 2, 'Logout', 'Authentication', 'User logged out', '202.142.168.70', '2025-12-02 05:11:49'),
(58, 2, 'Login', 'Authentication', 'User logged in', '202.142.168.70', '2025-12-02 05:12:03'),
(59, 1, 'Login', 'Authentication', 'User logged in', '202.142.168.70', '2025-12-02 05:26:21'),
(60, 1, 'Logout', 'Authentication', 'User logged out', '202.142.168.70', '2025-12-02 05:32:45'),
(61, 1, 'Login', 'Authentication', 'User logged in', '202.142.168.70', '2025-12-02 05:34:43'),
(62, 1, 'Edit Student', 'Students', 'Updated student: Ahmed ali Khan', '202.142.168.70', '2025-12-02 06:05:21'),
(63, 1, 'Mark Attendance', 'Attendance', 'Marked attendance for 1 students', '202.142.168.70', '2025-12-02 06:17:42'),
(64, 1, 'Login', 'Authentication', 'User logged in', '144.48.130.155', '2025-12-10 04:26:07'),
(65, 1, 'Add Student', 'Students', 'Added student: Muhammad Tayyab Ameen', '144.48.130.155', '2025-12-10 04:29:51'),
(66, 1, 'Login', 'Authentication', 'User logged in', '223.123.6.233', '2025-12-10 13:56:17'),
(67, 1, 'Login', 'Authentication', 'User logged in', '116.58.42.132', '2025-12-16 16:09:30'),
(68, 3, 'Login', 'Authentication', 'User logged in', '119.73.112.169', '2025-12-20 06:39:01'),
(69, 3, 'Add Student', 'Students', 'Added student: Hasnain ali Shahzad', '119.73.112.169', '2025-12-20 06:55:50'),
(70, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Muhammad Abu bakar', '119.73.112.169', '2025-12-20 07:01:27'),
(71, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Muh Masood', '119.73.112.169', '2025-12-20 07:04:36'),
(72, 3, 'Edit Teacher', 'HR', 'Updated teacher: Qari Muhammad Masood', '119.73.112.169', '2025-12-20 07:05:02'),
(73, 3, 'Edit Teacher', 'HR', 'Updated teacher: Qari Muhammad Masood', '119.73.112.169', '2025-12-20 07:06:09'),
(74, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Muhammad Irfan', '119.73.112.169', '2025-12-20 07:32:33'),
(75, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Muhammad Imran', '119.73.112.169', '2025-12-20 07:39:24'),
(76, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Muhammad Imran', '119.73.112.169', '2025-12-20 07:39:29'),
(77, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Abdullah Basheer', '119.73.112.169', '2025-12-20 07:42:50'),
(78, 3, 'Delete Teacher', 'HR', 'Deleted teacher: Qari Muhammad Imran', '119.73.112.169', '2025-12-20 07:48:00'),
(79, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Yasir Ahmad', '119.73.112.169', '2025-12-20 07:52:17'),
(80, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Anees ur Rehman', '119.73.112.169', '2025-12-20 07:56:03'),
(81, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Muhammad Sufian', '119.73.112.169', '2025-12-20 08:00:13'),
(82, 3, 'Edit Teacher', 'HR', 'Updated teacher: Qari Muhammad Sufian', '119.73.112.169', '2025-12-20 08:01:42'),
(83, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Abdul Latif', '119.73.112.169', '2025-12-20 08:06:56'),
(84, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Muhammade Ilyas', '119.73.112.169', '2025-12-20 08:09:32'),
(85, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Muhammad Fiaz', '119.73.112.169', '2025-12-20 08:12:52'),
(86, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Muhammad Arif', '119.73.112.169', '2025-12-20 08:18:41'),
(87, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Muhammad Waseem', '119.73.112.169', '2025-12-20 08:24:15'),
(88, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Ikram Ullah', '119.73.112.169', '2025-12-20 08:27:34'),
(89, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Muhammad Rafaqat', '119.73.112.169', '2025-12-20 08:31:01'),
(90, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Muhammad Amir', '119.73.112.169', '2025-12-20 08:34:29'),
(91, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Muhammad Muzammil', '119.73.112.169', '2025-12-20 08:37:37'),
(92, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Tanveer Ahmad', '119.73.112.169', '2025-12-20 08:41:25'),
(93, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Muhammad Abrar', '119.73.112.169', '2025-12-20 08:43:54'),
(94, 3, 'Add Class', 'Classes', 'Added class: S. Abdullah bin Abbas', '119.73.112.169', '2025-12-20 08:54:56'),
(95, 3, 'Edit Class', 'Classes', 'Updated class: Syedna Abdullah bin Abbas (ID: 6)', '119.73.112.169', '2025-12-20 08:55:56'),
(96, 3, 'Add Class', 'Classes', 'Added class: Syedna Abdullah bin Masood', '119.73.112.169', '2025-12-20 08:56:36'),
(97, 3, 'Add Class', 'Classes', 'Added class: Syedna Ali bin Abi Talib', '119.73.112.169', '2025-12-20 08:57:14'),
(98, 3, 'Add Class', 'Classes', 'Added class: Syedna Moaz bin Jabal', '223.123.22.147', '2025-12-20 09:01:55'),
(99, 3, 'Add Class', 'Classes', 'Added class: Syedna Zaid Bin Sabit', '223.123.22.147', '2025-12-20 09:02:26'),
(100, 3, 'Add Class', 'Classes', 'Added class: Syedna Ameer Hamza', '223.123.22.147', '2025-12-20 09:03:28'),
(101, 3, 'Add Class', 'Classes', 'Added class: Syedna Hassan Bin Ali', '223.123.22.147', '2025-12-20 09:03:46'),
(102, 3, 'Add Class', 'Classes', 'Added class: Syedna Ammar Bin Yasir', '223.123.22.147', '2025-12-20 09:05:14'),
(103, 3, 'Add Class', 'Classes', 'Added class: Syedna Abd Al Rehman Bin Awf', '223.123.22.147', '2025-12-20 09:05:49'),
(104, 3, 'Add Class', 'Classes', 'Added class: Syedna Ubai Bin Kaab', '223.123.22.147', '2025-12-20 09:06:19'),
(105, 3, 'Add Class', 'Classes', 'Added class: Syedna Zain Ul Abideen', '223.123.22.147', '2025-12-20 09:06:50'),
(106, 3, 'Add Class', 'Classes', 'Added class: Syedna Abu Musa Ashaari', '223.123.22.147', '2025-12-20 09:07:13'),
(107, 3, 'Add Class', 'Classes', 'Added class: Syedna Hussain Bin Ali', '223.123.22.147', '2025-12-20 09:07:44'),
(108, 3, 'Add Class', 'Classes', 'Added class: Syedna Bilal Bin Rubah', '223.123.22.147', '2025-12-20 09:08:11'),
(109, 3, 'Add Class', 'Classes', 'Added class: Syedna Usman Bin Affan', '223.123.22.147', '2025-12-20 09:08:38'),
(110, 3, 'Add Class', 'Classes', 'Added class: Syedna Abdullah Bin AdbulMutlib', '223.123.22.147', '2025-12-20 09:09:44'),
(111, 3, 'Add Class', 'Classes', 'Added class: Syedna Anas Bin Malik', '223.123.22.147', '2025-12-20 09:10:09'),
(112, 3, 'Add Class', 'Classes', 'Added class: Syedna Abubakr Siddiq', '223.123.22.147', '2025-12-20 09:10:43'),
(113, 3, 'Add Class', 'Classes', 'Added class: Syedna Abdullah Bin Umer', '223.123.22.147', '2025-12-20 09:11:10'),
(114, 3, 'Add Class', 'Classes', 'Added class: Syedna Mosab Bin Umair', '223.123.22.147', '2025-12-20 09:11:50'),
(115, 3, 'Add Class', 'Classes', 'Added class: Syedna Salman Farsi', '223.123.22.147', '2025-12-20 09:12:11'),
(116, 3, 'Add Class', 'Classes', 'Added class: Syedna Abu Ayoub Ansari', '223.123.22.147', '2025-12-20 09:12:40'),
(117, 3, 'Add Class', 'Classes', 'Added class: Syedna Umer Bin Khattab', '223.123.22.147', '2025-12-20 09:13:11'),
(118, 3, 'Add Class', 'Classes', 'Added class: Syedna Abdul Qadir Jillani', '223.123.22.147', '2025-12-20 09:13:55'),
(119, 3, 'Add Class', 'Classes', 'Added class: Syedna Tahir Allauddin', '223.123.22.147', '2025-12-20 09:14:29'),
(120, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Sardar Inaam Ullah Khan', '14.1.106.155', '2025-12-20 09:42:24'),
(121, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Abdul Khaliq Qammer', '14.1.106.155', '2025-12-20 09:50:19'),
(122, 3, 'Add Teacher', 'HR', 'Added teacher: Qari Muhammad Tayyab', '14.1.106.155', '2025-12-20 09:53:40'),
(123, 3, 'Edit Class', 'Classes', 'Updated class: Syedna Zain Ul Abideen (ID: 16)', '14.1.106.155', '2025-12-20 09:55:17'),
(124, 3, 'Edit Class', 'Classes', 'Updated class: Syedna Moaz bin Jabal (ID: 9)', '14.1.106.155', '2025-12-20 09:56:01'),
(125, 3, 'Edit Class', 'Classes', 'Updated class: Syedna Abdullah bin Abbas (ID: 6)', '14.1.106.155', '2025-12-20 09:56:26'),
(126, 3, 'Login', 'Authentication', 'User logged in', '2402:ad80:138:e910:61aa:cb62:c1d2:f40c', '2025-12-22 07:26:49'),
(127, 3, 'Add Class', 'Classes', 'Added class: Syedna Abdullah bin Abbas', '2402:ad80:138:e910:61aa:cb62:c1d2:f40c', '2025-12-22 07:28:29'),
(128, 3, 'Add Class', 'Classes', 'Added class: Syedna Moaz Bin Jabbal', '2402:ad80:138:e910:61aa:cb62:c1d2:f40c', '2025-12-22 07:29:08'),
(129, 3, 'Add Class', 'Classes', 'Added class: Syedna Ali bin Abi talib', '2402:ad80:138:e910:61aa:cb62:c1d2:f40c', '2025-12-22 07:29:33'),
(130, 3, 'Login', 'Authentication', 'User logged in', '154.57.223.93', '2025-12-24 04:33:56'),
(131, 3, 'Add Student', 'Students', 'Added student: Muhammad Umar Hassan', '154.57.223.93', '2025-12-24 04:52:49'),
(132, 1, 'Login', 'Authentication', 'User logged in', '223.123.23.200', '2025-12-29 06:52:16'),
(133, 3, 'Login', 'Authentication', 'User logged in', '14.1.106.155', '2025-12-29 07:09:11'),
(134, 1, 'Login', 'Authentication', 'User logged in', '116.58.42.132', '2025-12-29 08:45:55'),
(135, 1, 'Login', 'Authentication', 'User logged in', '223.123.22.239', '2026-01-01 14:01:26'),
(136, 1, 'Login', 'Authentication', 'User logged in', '127.0.0.1', '2026-04-03 14:49:46'),
(137, 1, 'Login', 'Authentication', 'User logged in', '::1', '2026-04-03 15:03:47'),
(138, 1, 'Edit Student', 'Students', 'Updated student: Muhammad Umar Hassan', '::1', '2026-04-03 17:02:49'),
(139, 1, 'Edit Student', 'Students', 'Updated student: Muhammad Umar Hassan', '::1', '2026-04-03 17:03:03'),
(140, 1, 'Login', 'Authentication', 'User logged in', '139.135.60.63', '2026-04-06 03:14:03');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `class_teacher_id` int(11) DEFAULT NULL,
  `capacity` int(11) DEFAULT 30,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `class_name`, `class_teacher_id`, `capacity`, `status`, `created_at`) VALUES
(1, 'Hifz Class A', 3, 25, 'active', '2025-10-27 07:58:16'),
(2, 'Hifz Class B', 1, 30, 'inactive', '2025-10-27 07:58:16'),
(3, 'Tajweed Beginner', NULL, 20, 'active', '2025-10-27 07:58:16'),
(5, 'fds', NULL, 30, 'active', '2025-10-27 08:05:07'),
(6, 'Syedna Abdullah bin Abbas', 26, 30, 'active', '2025-12-20 08:54:56'),
(7, 'Syedna Abdullah bin Masood', 6, 30, 'active', '2025-12-20 08:56:36'),
(8, 'Syedna Ali bin Abi Talib', 10, 30, 'active', '2025-12-20 08:57:14'),
(9, 'Syedna Moaz bin Jabal', 25, 30, 'active', '2025-12-20 09:01:55'),
(10, 'Syedna Zaid Bin Sabit', NULL, 30, 'active', '2025-12-20 09:02:26'),
(11, 'Syedna Ameer Hamza', 9, 30, 'active', '2025-12-20 09:03:28'),
(12, 'Syedna Hassan Bin Ali', NULL, 30, 'active', '2025-12-20 09:03:46'),
(13, 'Syedna Ammar Bin Yasir', 11, 30, 'active', '2025-12-20 09:05:14'),
(14, 'Syedna Abd Al Rehman Bin Awf', 4, 30, 'active', '2025-12-20 09:05:49'),
(15, 'Syedna Ubai Bin Kaab', 5, 30, 'active', '2025-12-20 09:06:19'),
(16, 'Syedna Zain Ul Abideen', 24, 30, 'active', '2025-12-20 09:06:50'),
(17, 'Syedna Abu Musa Ashaari', NULL, 30, 'active', '2025-12-20 09:07:13'),
(18, 'Syedna Hussain Bin Ali', 12, 30, 'active', '2025-12-20 09:07:44'),
(19, 'Syedna Bilal Bin Rubah', 15, 30, 'active', '2025-12-20 09:08:11'),
(20, 'Syedna Usman Bin Affan', 19, 30, 'active', '2025-12-20 09:08:38'),
(21, 'Syedna Abdullah Bin AdbulMutlib', 22, 30, 'active', '2025-12-20 09:09:44'),
(22, 'Syedna Anas Bin Malik', 20, 30, 'active', '2025-12-20 09:10:09'),
(23, 'Syedna Abubakr Siddiq', 23, 30, 'active', '2025-12-20 09:10:43'),
(24, 'Syedna Abdullah Bin Umer', 13, 30, 'active', '2025-12-20 09:11:10'),
(25, 'Syedna Mosab Bin Umair', 17, 30, 'active', '2025-12-20 09:11:50'),
(26, 'Syedna Salman Farsi', 18, 30, 'active', '2025-12-20 09:12:11'),
(27, 'Syedna Abu Ayoub Ansari', 21, 30, 'active', '2025-12-20 09:12:40'),
(28, 'Syedna Umer Bin Khattab', 14, 30, 'active', '2025-12-20 09:13:11'),
(29, 'Syedna Abdul Qadir Jillani', NULL, 30, 'active', '2025-12-20 09:13:55'),
(30, 'Syedna Tahir Allauddin', 16, 30, 'active', '2025-12-20 09:14:29'),
(31, 'Syedna Abdullah bin Abbas', 26, 30, 'active', '2025-12-22 07:28:29'),
(32, 'Syedna Moaz Bin Jabbal', 25, 30, 'active', '2025-12-22 07:29:08'),
(33, 'Syedna Ali bin Abi talib', 10, 30, 'active', '2025-12-22 07:29:33');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `exam_type_id` int(11) NOT NULL,
  `exam_title` varchar(200) NOT NULL,
  `exam_date` date NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `total_marks` int(11) DEFAULT 100,
  `passing_marks` int(11) DEFAULT 40,
  `status` enum('scheduled','ongoing','completed','cancelled') DEFAULT 'scheduled',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `exam_type_id`, `exam_title`, `exam_date`, `class_id`, `total_marks`, `passing_marks`, `status`, `created_by`, `created_at`) VALUES
(3, 1, 'Para 1 Test', '2025-10-31', 1, 100, 50, 'scheduled', NULL, '2025-10-31 08:12:41');

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `obtained_marks` decimal(5,2) DEFAULT NULL,
  `grade` varchar(10) DEFAULT NULL,
  `status` enum('pass','fail') DEFAULT NULL COMMENT 'Pass/Fail status for the exam',
  `remarks` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_results`
--

INSERT INTO `exam_results` (`id`, `exam_id`, `student_id`, `subject_id`, `obtained_marks`, `grade`, `status`, `remarks`, `created_by`, `created_at`) VALUES
(4, 3, 2, 1, NULL, NULL, 'pass', 'Needs Improvement', NULL, '2025-10-31 08:19:05'),
(5, 3, 4, 1, NULL, NULL, 'pass', '1-20', NULL, '2025-11-11 19:14:46');

-- --------------------------------------------------------

--
-- Table structure for table `exam_subjects`
--

CREATE TABLE `exam_subjects` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `total_marks` int(11) NOT NULL,
  `passing_marks` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_subjects`
--

INSERT INTO `exam_subjects` (`id`, `exam_id`, `subject_name`, `total_marks`, `passing_marks`) VALUES
(1, 3, 'Overall', 100, 50);

-- --------------------------------------------------------

--
-- Table structure for table `exam_types`
--

CREATE TABLE `exam_types` (
  `id` int(11) NOT NULL,
  `exam_name` varchar(100) NOT NULL,
  `exam_type` enum('monthly','quarterly','half_yearly','annual','special') NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_types`
--

INSERT INTO `exam_types` (`id`, `exam_name`, `exam_type`, `description`, `status`, `created_at`) VALUES
(1, 'Para Completion Test', 'special', NULL, 'active', '2025-10-31 08:12:03');

-- --------------------------------------------------------

--
-- Table structure for table `juz_reference`
--

CREATE TABLE `juz_reference` (
  `id` int(11) NOT NULL,
  `juz_number` int(11) NOT NULL,
  `juz_name_arabic` varchar(100) DEFAULT NULL,
  `juz_name_english` varchar(100) DEFAULT NULL,
  `start_surah` varchar(100) DEFAULT NULL,
  `start_ayah` int(11) DEFAULT NULL,
  `end_surah` varchar(100) DEFAULT NULL,
  `end_ayah` int(11) DEFAULT NULL,
  `number_of_lines` int(11) DEFAULT NULL COMMENT 'Total number of lines in this Juz'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `juz_reference`
--

INSERT INTO `juz_reference` (`id`, `juz_number`, `juz_name_arabic`, `juz_name_english`, `start_surah`, `start_ayah`, `end_surah`, `end_ayah`, `number_of_lines`) VALUES
(1, 1, 'الٓمٓ', 'Alif Lam Meem', 'Al-Fatiha', 1, 'Al-Baqarah', 141, 286),
(2, 2, 'سَيَقُولُ', 'Sa Yaqool', 'Al-Baqarah', 142, 'Al-Baqarah', 252, NULL),
(3, 3, 'تِلْكَ ٱلرُّسُلُ', 'Tilkal Rusul', 'Al-Baqarah', 253, 'Aal-Imran', 92, NULL),
(4, 4, 'لَن تَنَالُوا۟', 'Lan Tana Loo', 'Aal-Imran', 93, 'An-Nisa', 23, NULL),
(5, 5, 'وَٱلْمُحْصَنَٰتُ', 'Wal Mohsanat', 'An-Nisa', 24, 'An-Nisa', 147, NULL),
(6, 6, 'لَّا يُحِبُّ ٱللَّهُ', 'La Yuhibbullah', 'An-Nisa', 148, 'Al-Maidah', 81, NULL),
(7, 7, 'وَإِذَا سَمِعُوا۟', 'Wa Iza Samiu', 'Al-Maidah', 82, 'Al-Anam', 110, NULL),
(8, 8, 'وَلَوْ أَنَّنَا', 'Wa Lau Annana', 'Al-Anam', 111, 'Al-Araf', 87, NULL),
(9, 9, 'قَالَ ٱلْمَلَأُ', 'Qalal Mala', 'Al-Araf', 88, 'Al-Anfal', 40, NULL),
(10, 10, 'وَٱعْلَمُوٓا۟', 'Wa Alamoo', 'Al-Anfal', 41, 'At-Tawbah', 92, NULL),
(11, 11, 'يَعْتَذِرُونَ', 'Yatazeroon', 'At-Tawbah', 93, 'Hud', 5, NULL),
(12, 12, 'وَمَا مِنْ دَآبَّةٍ', 'Wa Ma Min Dabbah', 'Hud', 6, 'Yusuf', 52, NULL),
(13, 13, 'وَمَآ أُبَرِّئُ', 'Wa Ma Ubri', 'Yusuf', 53, 'Ibrahim', 52, NULL),
(14, 14, 'رُبَمَا', 'Rubama', 'Al-Hijr', 1, 'An-Nahl', 128, NULL),
(15, 15, 'سُبْحَٰنَ ٱلَّذِىٓ', 'Subhanallazi', 'Al-Isra', 1, 'Al-Kahf', 74, NULL),
(16, 16, 'قَالَ أَلَمْ', 'Qal Alam', 'Al-Kahf', 75, 'Ta-Ha', 135, NULL),
(17, 17, 'ٱقْتَرَبَ لِلنَّاسِ', 'Iqtaraba', 'Al-Anbiya', 1, 'Al-Hajj', 78, NULL),
(18, 18, 'قَدْ أَفْلَحَ', 'Qad Aflaha', 'Al-Muminoon', 1, 'Al-Furqan', 20, NULL),
(19, 19, 'وَقَالَ ٱلَّذِينَ', 'Wa Qalallazina', 'Al-Furqan', 21, 'An-Naml', 55, NULL),
(20, 20, 'أَمَّنْ خَلَقَ', 'Amman Khalaqa', 'An-Naml', 56, 'Al-Ankabut', 45, NULL),
(21, 21, 'ٱتْلُ مَآ أُوحِىَ', 'Utlu Ma Oohi', 'Al-Ankabut', 46, 'Al-Ahzab', 30, NULL),
(22, 22, 'وَمَن يَقْنُتْ', 'Wa Man Yaqnut', 'Al-Ahzab', 31, 'Ya-Sin', 27, NULL),
(23, 23, 'وَمَآ لِىَ', 'Wa Mali', 'Ya-Sin', 28, 'Az-Zumar', 31, NULL),
(24, 24, 'فَمَنْ أَظْلَمُ', 'Faman Azlam', 'Az-Zumar', 32, 'Fussilat', 46, NULL),
(25, 25, 'إِلَيْهِ يُرَدُّ', 'Ilayhi Yuraddu', 'Fussilat', 47, 'Al-Jathiyah', 37, NULL),
(26, 26, 'حمٓ', 'Ha Meem', 'Al-Ahqaf', 1, 'Az-Zariyat', 30, NULL),
(27, 27, 'قَالَ فَمَا', 'Qala Fama', 'Az-Zariyat', 31, 'Al-Hadid', 29, NULL),
(28, 28, 'قَدْ سَمِعَ', 'Qad Samia', 'Al-Mujadila', 1, 'At-Tahrim', 12, NULL),
(29, 29, 'تَبَارَكَ ٱلَّذِى', 'Tabarakallazi', 'Al-Mulk', 1, 'Al-Mursalat', 50, NULL),
(30, 30, 'عَمَّ', 'Amma', 'An-Naba', 1, 'An-Nas', 6, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `manzil_records`
--

CREATE TABLE `manzil_records` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `record_date` date NOT NULL,
  `juz_from` int(11) NOT NULL,
  `juz_to` int(11) NOT NULL,
  `completion_time` int(11) DEFAULT NULL COMMENT 'Time in minutes',
  `accuracy_percentage` decimal(5,2) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `manzil_records`
--

INSERT INTO `manzil_records` (`id`, `student_id`, `record_date`, `juz_from`, `juz_to`, `completion_time`, `accuracy_percentage`, `teacher_id`, `remarks`, `created_at`) VALUES
(1, 2, '2025-10-30', 17, 13, NULL, NULL, 3, '', '2025-10-29 22:03:55'),
(2, 1, '2025-10-30', 13, 13, NULL, NULL, 3, '', '2025-10-29 22:03:55'),
(3, 2, '2025-10-30', 16, 30, NULL, NULL, 3, 'Ali Hassan', '2025-10-29 23:27:58'),
(4, 1, '2025-10-30', 16, 30, NULL, NULL, 3, 'Aisha Fatima', '2025-10-29 23:27:59'),
(5, 4, '2025-11-11', 15, 15, NULL, NULL, 3, 'Heard by: Ghulam Mujtaba (Teacher)', '2025-11-10 20:22:53'),
(6, 2, '2025-11-11', 1, 1, NULL, NULL, 3, 'Heard by: Ahmed ali Khan', '2025-11-10 20:22:53'),
(7, 4, '2025-11-21', 1, 1, NULL, NULL, 3, 'Heard by: Ghulam Mujtaba (Teacher)', '2025-11-21 17:32:39');

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `parent_id` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `cnic` varchar(20) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `relation` enum('father','mother','guardian','other') DEFAULT 'father',
  `occupation` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parent_student_relation`
--

CREATE TABLE `parent_student_relation` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `relation_type` enum('father','mother','guardian','other') DEFAULT 'father',
  `is_primary` tinyint(1) DEFAULT 0 COMMENT 'Primary contact parent',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quran_ayahs`
--

CREATE TABLE `quran_ayahs` (
  `id` int(11) NOT NULL,
  `surah_id` int(11) NOT NULL,
  `ayah_number` int(11) NOT NULL,
  `text_ar` text DEFAULT NULL,
  `page_number` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quran_juz`
--

CREATE TABLE `quran_juz` (
  `id` int(11) NOT NULL,
  `juz_number` int(11) NOT NULL,
  `juz_name` varchar(100) NOT NULL,
  `start_surah` varchar(100) NOT NULL,
  `end_surah` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quran_juz`
--

INSERT INTO `quran_juz` (`id`, `juz_number`, `juz_name`, `start_surah`, `end_surah`) VALUES
(1, 1, 'الٓمٓ', 'Al-Fatihah', 'Al-Baqarah 141'),
(2, 2, 'سَيَقُولُ', 'Al-Baqarah 142', 'Al-Baqarah 252'),
(3, 3, 'تِلْكَ ٱلرُّسُلُ', 'Al-Baqarah 253', 'Al-Imran 92'),
(4, 4, 'لَن تَنَالُوا۟', 'Al-Imran 93', 'An-Nisa 23'),
(5, 5, 'وَٱلْمُحْصَنَٰتُ', 'An-Nisa 24', 'An-Nisa 147'),
(6, 6, 'لَّا يُحِبُّ ٱللَّهُ', 'An-Nisa 148', 'Al-Ma\'idah 81'),
(7, 7, 'وَإِذَا سَمِعُوا۟', 'Al-Ma\'idah 82', 'Al-An\'am 110'),
(8, 8, 'وَلَوْ أَنَّنَا', 'Al-An\'am 111', 'Al-A\'raf 87'),
(9, 9, 'قَالَ ٱلْمَلَأُ', 'Al-A\'raf 88', 'Al-Anfal 40'),
(10, 10, 'وَٱعْلَمُوٓا۟', 'Al-Anfal 41', 'At-Tawbah 92'),
(11, 11, 'يَعْتَذِرُونَ', 'At-Tawbah 93', 'Hud 5'),
(12, 12, 'وَمَا مِنْ دَآبَّةٍ', 'Hud 6', 'Yusuf 52'),
(13, 13, 'وَمَآ أُبَرِّئُ', 'Yusuf 53', 'Ibrahim 52'),
(14, 14, 'رُبَمَا', 'Al-Hijr 1', 'An-Nahl 128'),
(15, 15, 'سُبْحَٰنَ ٱلَّذِىٓ', 'Al-Isra 1', 'Al-Kahf 74'),
(16, 16, 'قَالَ أَلَمْ', 'Al-Kahf 75', 'Ta-Ha 135'),
(17, 17, 'ٱقْتَرَبَ لِلنَّاسِ', 'Al-Anbiya 1', 'Al-Hajj 78'),
(18, 18, 'قَدْ أَفْلَحَ', 'Al-Mu\'minun 1', 'Al-Furqan 20'),
(19, 19, 'وَقَالَ ٱلَّذِينَ', 'Al-Furqan 21', 'An-Naml 55'),
(20, 20, 'أَمَّنْ خَلَقَ', 'An-Naml 56', 'Al-Ankabut 45'),
(21, 21, 'ٱتْلُ مَآ أُوحِىَ', 'Al-Ankabut 46', 'Al-Ahzab 30'),
(22, 22, 'وَمَن يَقْنُتْ', 'Al-Ahzab 31', 'Ya-Sin 27'),
(23, 23, 'وَمَآ لِىَ', 'Ya-Sin 28', 'Az-Zumar 31'),
(24, 24, 'فَمَنْ أَظْلَمُ', 'Az-Zumar 32', 'Fussilat 46'),
(25, 25, 'إِلَيْهِ يُرَدُّ', 'Fussilat 47', 'Al-Jathiyah 37'),
(26, 26, 'حمٓ', 'Al-Ahqaf 1', 'Adh-Dhariyat 30'),
(27, 27, 'قَالَ فَمَا', 'Adh-Dhariyat 31', 'Al-Hadid 29'),
(28, 28, 'قَدْ سَمِعَ', 'Al-Mujadila 1', 'At-Tahrim 12'),
(29, 29, 'تَبَارَكَ ٱلَّذِى', 'Al-Mulk 1', 'Al-Mursalat 50'),
(30, 30, 'عَمَّ', 'An-Naba 1', 'An-Nas 6');

-- --------------------------------------------------------

--
-- Table structure for table `quran_progress`
--

CREATE TABLE `quran_progress` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `progress_date` date NOT NULL,
  `sabak_type` enum('new_lesson','revision','manzil') NOT NULL,
  `juz_number` int(11) DEFAULT NULL,
  `surah_name` varchar(100) DEFAULT NULL,
  `ayah_from` int(11) DEFAULT NULL,
  `ayah_to` int(11) DEFAULT NULL,
  `page_from` int(11) DEFAULT NULL,
  `page_to` int(11) DEFAULT NULL,
  `performance` enum('excellent','good','average','needs_improvement') DEFAULT NULL,
  `mistakes` int(11) DEFAULT 0,
  `teacher_id` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quran_surahs`
--

CREATE TABLE `quran_surahs` (
  `id` int(11) NOT NULL,
  `surah_number` int(11) NOT NULL,
  `surah_name_ar` varchar(100) DEFAULT NULL,
  `surah_name_en` varchar(100) NOT NULL,
  `ayah_count` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quran_surahs`
--

INSERT INTO `quran_surahs` (`id`, `surah_number`, `surah_name_ar`, `surah_name_en`, `ayah_count`) VALUES
(1, 1, 'الفاتحة', 'Al-Fatihah', 7),
(2, 2, 'البقرة', 'Al-Baqarah', 286),
(3, 3, 'آل عمران', 'Al-Imran', 200),
(4, 4, 'النساء', 'An-Nisa', 176),
(5, 5, 'المائدة', 'Al-Ma\'idah', 120),
(6, 6, 'الأنعام', 'Al-Anam', 165),
(7, 7, 'الأعراف', 'Al-A\'raf', 206),
(8, 8, 'الأنفال', 'Al-Anfal', 75),
(9, 9, 'التوبة', 'At-Tawbah', 129),
(10, 10, 'يونس', 'Yunus', 109),
(11, 11, 'هود', 'Hud', 123),
(12, 12, 'يوسف', 'Yusuf', 111),
(13, 13, 'الرعد', 'Ar-Ra\'d', 43),
(14, 14, 'إبراهيم', 'Ibrahim', 52),
(15, 15, 'الحجر', 'Al-Hijr', 99),
(16, 16, 'النحل', 'An-Nahl', 128),
(17, 17, 'الإسراء', 'Al-Isra', 111),
(18, 18, 'الكهف', 'Al-Kahf', 110),
(19, 19, 'مريم', 'Maryam', 98),
(20, 20, 'طه', 'Ta-Ha', 135),
(21, 21, 'الأنبياء', 'Al-Anbiya', 112),
(22, 22, 'الحج', 'Al-Hajj', 78),
(23, 23, 'المؤمنون', 'Al-Mu\'minun', 118),
(24, 24, 'النور', 'An-Nur', 64),
(25, 25, 'الفرقان', 'Al-Furqan', 77),
(26, 26, 'الشعراء', 'Ash-Shuara', 227),
(27, 27, 'النمل', 'An-Naml', 93),
(28, 28, 'القصص', 'Al-Qasas', 88),
(29, 29, 'العنكبوت', 'Al-Ankabut', 69),
(30, 30, 'الروم', 'Ar-Rum', 60),
(31, 31, 'لقمان', 'Luqman', 34),
(32, 32, 'السجدة', 'As-Sajdah', 30),
(33, 33, 'الأحزاب', 'Al-Ahzab', 73),
(34, 34, 'سبأ', 'Saba', 54),
(35, 35, 'فاطر', 'Fatir', 45),
(36, 36, 'يس', 'Ya-Sin', 83),
(37, 37, 'الصافات', 'As-Saffat', 182),
(38, 38, 'ص', 'Sad', 88),
(39, 39, 'الزمر', 'Az-Zumar', 75),
(40, 40, 'غافر', 'Ghafir', 85),
(41, 41, 'فصلت', 'Fussilat', 54),
(42, 42, 'الشورى', 'Ash-Shura', 53),
(43, 43, 'الزخرف', 'Az-Zukhruf', 89),
(44, 44, 'الدخان', 'Ad-Dukhan', 59),
(45, 45, 'الجاثية', 'Al-Jathiyah', 37),
(46, 46, 'الأحقاف', 'Al-Ahqaf', 35),
(47, 47, 'محمد', 'Muhammad', 38),
(48, 48, 'الفتح', 'Al-Fath', 29),
(49, 49, 'الحجرات', 'Al-Hujurat', 18),
(50, 50, 'ق', 'Qaf', 45),
(51, 51, 'الذاريات', 'Adh-Dhariyat', 60),
(52, 52, 'الطور', 'At-Tur', 49),
(53, 53, 'النجم', 'An-Najm', 62),
(54, 54, 'القمر', 'Al-Qamar', 55),
(55, 55, 'الرحمن', 'Ar-Rahman', 78),
(56, 56, 'الواقعة', 'Al-Waqi\'ah', 96),
(57, 57, 'الحديد', 'Al-Hadid', 29),
(58, 58, 'المجادلة', 'Al-Mujadila', 22),
(59, 59, 'الحشر', 'Al-Hashr', 24),
(60, 60, 'الممتحنة', 'Al-Mumtahanah', 13),
(61, 61, 'الصف', 'As-Saff', 14),
(62, 62, 'الجمعة', 'Al-Jumu\'ah', 11),
(63, 63, 'المنافقون', 'Al-Munafiqun', 11),
(64, 64, 'التغابن', 'At-Taghabun', 18),
(65, 65, 'الطلاق', 'At-Talaq', 12),
(66, 66, 'التحريم', 'At-Tahrim', 12),
(67, 67, 'الملك', 'Al-Mulk', 30),
(68, 68, 'القلم', 'Al-Qalam', 52),
(69, 69, 'الحاقة', 'Al-Haqqah', 52),
(70, 70, 'المعارج', 'Al-Ma\'arij', 44),
(71, 71, 'نوح', 'Nuh', 28),
(72, 72, 'الجن', 'Al-Jinn', 28),
(73, 73, 'المزمل', 'Al-Muzzammil', 20),
(74, 74, 'المدثر', 'Al-Muddaththir', 56),
(75, 75, 'القيامة', 'Al-Qiyamah', 40),
(76, 76, 'الإنسان', 'Al-Insan', 31),
(77, 77, 'المرسلات', 'Al-Mursalat', 50),
(78, 78, 'النبأ', 'An-Naba', 40),
(79, 79, 'النازعات', 'An-Nazi\'at', 46),
(80, 80, 'عبس', 'Abasa', 42),
(81, 81, 'التكوير', 'At-Takwir', 29),
(82, 82, 'الإنفطار', 'Al-Infitar', 19),
(83, 83, 'المطففين', 'Al-Mutaffifin', 36),
(84, 84, 'الإنشقاق', 'Al-Inshiqaq', 25),
(85, 85, 'البروج', 'Al-Buruj', 22),
(86, 86, 'الطارق', 'At-Tariq', 17),
(87, 87, 'الأعلى', 'Al-A\'la', 19),
(88, 88, 'الغاشية', 'Al-Ghashiyah', 26),
(89, 89, 'الفجر', 'Al-Fajr', 30),
(90, 90, 'البلد', 'Al-Balad', 20),
(91, 91, 'الشمس', 'Ash-Shams', 15),
(92, 92, 'الليل', 'Al-Layl', 21),
(93, 93, 'الضحى', 'Ad-Duha', 11),
(94, 94, 'الشرح', 'Ash-Sharh', 8),
(95, 95, 'التين', 'At-Tin', 8),
(96, 96, 'العلق', 'Al-Alaq', 19),
(97, 97, 'القدر', 'Al-Qadr', 5),
(98, 98, 'البيّنة', 'Al-Bayyinah', 8),
(99, 99, 'الزلزلة', 'Az-Zalzalah', 8),
(100, 100, 'العاديات', 'Al-Adiyat', 11),
(101, 101, 'القارعة', 'Al-Qari\'ah', 11),
(102, 102, 'التكاثر', 'At-Takathur', 8),
(103, 103, 'العصر', 'Al-Asr', 3),
(104, 104, 'الهمزة', 'Al-Humazah', 9),
(105, 105, 'الفيل', 'Al-Fil', 5),
(106, 106, 'قريش', 'Quraysh', 4),
(107, 107, 'الماعون', 'Al-Ma\'un', 7),
(108, 108, 'الكوثر', 'Al-Kawthar', 3),
(109, 109, 'الكافرون', 'Al-Kafirun', 6),
(110, 110, 'النصر', 'An-Nasr', 3),
(111, 111, 'المسد', 'Al-Masad', 5),
(112, 112, 'الإخلاص', 'Al-Ikhlas', 4),
(113, 113, 'الفلق', 'Al-Falaq', 5),
(114, 114, 'الناس', 'An-Nas', 6);

-- --------------------------------------------------------

--
-- Table structure for table `sabak_records`
--

CREATE TABLE `sabak_records` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `record_date` date NOT NULL,
  `juz_number` int(11) DEFAULT NULL,
  `surah_name` varchar(100) NOT NULL,
  `page_from` int(11) NOT NULL,
  `page_to` int(11) NOT NULL,
  `lines_memorized` int(11) DEFAULT NULL,
  `performance_rating` decimal(3,2) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sabak_records`
--

INSERT INTO `sabak_records` (`id`, `student_id`, `record_date`, `juz_number`, `surah_name`, `page_from`, `page_to`, `lines_memorized`, `performance_rating`, `teacher_id`, `remarks`, `created_at`) VALUES
(1, 2, '2025-10-30', 10, 'naas', 1, 5, 10, NULL, 3, NULL, '2025-10-29 22:03:55'),
(2, 1, '2025-10-30', 10, '', 1, 5, 10, NULL, 3, NULL, '2025-10-29 22:03:55'),
(3, 2, '2025-10-30', 11, 'muzamil', 1, 5, 4, NULL, 3, NULL, '2025-10-29 23:27:58'),
(4, 1, '2025-10-30', 11, 'muzamil', 1, 5, 4, NULL, 3, NULL, '2025-10-29 23:27:58'),
(5, 4, '2025-11-11', 1, 'Al-Baqarah', 15, 15, 10, NULL, 3, NULL, '2025-11-10 20:22:53'),
(6, 2, '2025-11-11', 1, 'Al-Baqarah', 15, 15, 10, NULL, 3, NULL, '2025-11-10 20:22:53'),
(7, 4, '2025-11-21', 1, 'Al-Baqarah', 15, 30, 55, NULL, 3, NULL, '2025-11-21 17:32:39');

-- --------------------------------------------------------

--
-- Table structure for table `sabqi_records`
--

CREATE TABLE `sabqi_records` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `record_date` date NOT NULL,
  `juz_number` int(11) DEFAULT NULL,
  `surah_name` varchar(100) NOT NULL,
  `page_from` int(11) NOT NULL,
  `page_to` int(11) NOT NULL,
  `accuracy_percentage` decimal(5,2) DEFAULT NULL,
  `mistakes_count` int(11) DEFAULT 0,
  `teacher_id` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sabqi_records`
--

INSERT INTO `sabqi_records` (`id`, `student_id`, `record_date`, `juz_number`, `surah_name`, `page_from`, `page_to`, `accuracy_percentage`, `mistakes_count`, `teacher_id`, `remarks`, `created_at`) VALUES
(1, 2, '2025-10-30', 13, 'naas', 1, 5, NULL, 0, 3, NULL, '2025-10-29 22:03:55'),
(2, 2, '2025-10-30', 11, '', 55, 55, NULL, 0, 3, NULL, '2025-10-29 23:27:58'),
(3, 1, '2025-10-30', 11, '', 25, 25, NULL, 0, 3, NULL, '2025-10-29 23:27:58'),
(4, 4, '2025-11-11', 1, '', 0, 0, NULL, 0, 3, NULL, '2025-11-10 20:22:53'),
(5, 2, '2025-11-11', 1, '', 0, 0, NULL, 0, 3, NULL, '2025-11-10 20:22:53'),
(6, 4, '2025-11-21', 1, '', 0, 0, NULL, 0, 3, NULL, '2025-11-21 17:32:39');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `school_address` text DEFAULT NULL,
  `school_phone` varchar(50) DEFAULT NULL,
  `school_email` varchar(100) DEFAULT NULL,
  `academic_year` int(11) NOT NULL,
  `attendance_required` tinyint(1) DEFAULT 1,
  `progress_required` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `school_name`, `school_address`, `school_phone`, `school_email`, `academic_year`, `attendance_required`, `progress_required`, `created_at`, `updated_at`) VALUES
(1, 'MINHAJ INSTITUTE OF QIRAT &amp;amp; TAJWEED', '', '03314057324', '', 2025, 1, 1, '2025-10-27 08:34:32', '2025-10-29 18:00:07');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `student_id` varchar(50) NOT NULL,
  `admission_no` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `father_name` varchar(100) NOT NULL,
  `cnic_bform` varchar(20) DEFAULT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `student_type` varchar(255) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `admission_date` date NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `guardian_phone` varchar(20) NOT NULL,
  `guardian_name` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `previous_education` text DEFAULT NULL,
  `medical_info` text DEFAULT NULL,
  `status` enum('active','inactive','graduated','left','alumni','expelled') DEFAULT 'active',
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mother_name` varchar(100) DEFAULT NULL,
  `date_of_admission` date DEFAULT NULL,
  `date_of_leaving` date DEFAULT NULL,
  `reason_of_leaving` text DEFAULT NULL,
  `father_profession` varchar(100) DEFAULT NULL,
  `father_cnic` varchar(20) DEFAULT NULL,
  `admission_challan_no` varchar(50) DEFAULT NULL,
  `guardian_phone_2` varchar(20) DEFAULT NULL,
  `whatsapp_no` varchar(20) DEFAULT NULL,
  `previous_result_card` varchar(255) DEFAULT NULL,
  `total_marks` int(11) DEFAULT NULL,
  `obtained_marks` int(11) DEFAULT NULL,
  `class_status` enum('pass','fail') DEFAULT NULL,
  `previous_school_class` varchar(100) DEFAULT NULL,
  `current_school_class` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `student_id`, `admission_no`, `first_name`, `last_name`, `father_name`, `cnic_bform`, `date_of_birth`, `gender`, `student_type`, `class_id`, `admission_date`, `phone`, `guardian_phone`, `guardian_name`, `address`, `city`, `previous_education`, `medical_info`, `status`, `photo`, `created_at`, `updated_at`, `mother_name`, `date_of_admission`, `date_of_leaving`, `reason_of_leaving`, `father_profession`, `father_cnic`, `admission_challan_no`, `guardian_phone_2`, `whatsapp_no`, `previous_result_card`, `total_marks`, `obtained_marks`, `class_status`, `previous_school_class`, `current_school_class`) VALUES
(1, NULL, 'STD-001', 'ADM-2024-001', 'Ali', 'Hassan', 'Hassan Ahmad', '35202', '2010-05-15', 'male', NULL, 1, '2024-01-15', '0300-1111111', '0300-1111111', 'Hassan Ahmad', 'fsdofhosfd', 'Lahore', 'idfskdnfi', 'fdnsindfiosndf', 'graduated', NULL, '2025-10-27 07:58:16', '2025-10-30 12:06:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, NULL, 'STD-002', 'ADM-2024-002', 'Aisha', 'Fatima', 'Muhammad Saeed', '&lt;br /&gt;&lt;b&gt', '2011-03-20', 'female', 'day_scholar', 1, '2024-01-15', '&lt;br /&gt;&lt;b&gt', '0300-2222222', 'Muhammad Saeed', 'House#651, Sector 2, D-1 Township L 365-M-ModelTown, Lahore', 'Lahore', 'Metric', 'no', 'active', NULL, '2025-10-27 07:58:16', '2025-11-10 18:47:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, NULL, 'STD-003', 'ADM-2024-003', 'Omar', 'Abdullah', 'Abdullah Khan', NULL, '2010-08-10', 'male', NULL, 2, '2024-02-01', NULL, '0300-3333333', 'Abdullah Khan', NULL, NULL, NULL, NULL, 'active', NULL, '2025-10-27 07:58:16', '2025-10-27 07:58:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, NULL, 'STD-55CC0FB3E3', 'ADM-2025-004', 'Ahmed ali', 'Khan', 'Mohammad Khan', '12345-6789012-5', '2010-05-12', 'male', 'day_scholar', 1, '2024-08-15', '3007654321', '3001234567', 'Khan Sb', 'Street 1, City', 'Lahore', 'Hifz basics', 'N/A', '', '692708000e5b9_1764165632.png', '2025-11-10 18:29:57', '2025-12-02 06:05:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, NULL, 'STD-369787D425', 'ADM-2025-005', 'Ahmed noor', 'Khan', 'Mohammad Khan', '12345-6789012-7', '2010-05-12', 'male', 'day_scholar', 2, '2024-08-15', '3007654321', '3001234567', 'Khan Sb', 'Street 1, City', 'Lahore', 'Hifz basics', 'N/A', 'active', '', '2025-11-10 18:29:57', '2025-11-10 18:29:57', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 4, 'STD-6B929E1431', '1', 'Muhammad Tayyab', 'Ameen', 'Muhammad Ameen', '3450105721019', '2002-03-13', 'male', 'boarder', 1, '2011-05-02', '03014948154', '03014948154', 'Muhammad Ameen', 'P/O Narowal T/D Narowal', 'principal', '', '', 'active', '', '2025-12-10 04:29:51', '2025-12-10 04:29:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 5, 'STD-76504E1C71', '10001', 'Hasnain ali', 'Shahzad', 'Shahzad Ali', '36601-2852728-5', '2011-12-03', 'male', 'boarder', 1, '2024-12-16', '0333-6298629', '0333-6298629', 'Shahzad ali', '', 'Bure wala', '', '', 'active', '', '2025-12-20 06:55:50', '2025-12-20 06:55:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, NULL, 'STD-214CF32A40', '10002', 'Muhammad Umar', 'Hassan', 'Shoukat Siddique Kiyani', '8230376863967', '2011-04-17', 'male', 'boarder', 30, '2025-01-06', '0346-5172766', '0346-5172766', 'Shoukat Siddique Kiyani', '', '', '', '', 'active', '', '2025-12-24 04:52:49', '2026-04-03 17:03:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, NULL, 'STD-24-0001', 'ADM-2024-0001', 'Syed M.Noor ul Hassan Bukhari', '', 'Syed Usman Ali Shah Bukhari', NULL, '2010-11-22', 'male', 'border', NULL, '2024-01-02', NULL, '0300-6495924', 'Syed Usman Ali Shah Bukhari', NULL, 'gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Buisness', '34101-7618710-1', NULL, '0301-3088605', '0300-6403588', NULL, NULL, NULL, NULL, NULL, '6th'),
(10, NULL, 'STD-24-0002', 'ADM-2024-0002', 'Muhammad Sibtain Raza', '', 'Muhammad Ijaz Raza Baber', NULL, '2012-06-28', 'male', 'day_scholar', NULL, '2024-01-09', NULL, '0300-4820836', 'Muhammad Ijaz Raza Baber', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Private job', '35202-8627648-5', NULL, '0324-4814792', '0300-4820836', NULL, NULL, NULL, NULL, NULL, '6th'),
(11, NULL, 'STD-24-0003', 'ADM-2024-0003', 'Ahmad Shahzad', '', 'Adil Hussain', NULL, '2014-06-15', 'male', 'border', NULL, '2024-01-09', NULL, '0302-7839763', 'Adil Hussain', NULL, 'Toba Tak Singh', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Homeopathic Doctor', '33302-2208077-5', NULL, '0346-52954392', '0303-0555763', NULL, NULL, NULL, NULL, NULL, '6th'),
(12, NULL, 'STD-24-0004', 'ADM-2024-0004', 'Muhammad Siddiq Akbar', '', 'Muhammad Akbar', NULL, '2012-05-16', 'male', 'day_scholar', NULL, '2024-01-10', NULL, '0300-3860786', 'Muhammad Akbar', NULL, 'Multan', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Alim e Deen', '36304-2977944-5', NULL, '0309-8382440', '0300-3860786', NULL, NULL, NULL, NULL, NULL, '6th'),
(13, NULL, 'STD-24-0005', 'ADM-2024-0005', 'Ahmad Raza', '', 'Tahir Shafi', NULL, '2010-09-29', 'male', 'border', NULL, '2024-01-22', NULL, '0316-4467978', 'Tahir Shafi', NULL, 'Lahore', NULL, NULL, 'left', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Treat Corporation', '61101-6759904-0', 'Left', '0325-41255206', '0316-4467978', NULL, NULL, NULL, NULL, NULL, '6th'),
(14, NULL, 'STD-24-0006', 'ADM-2024-0006', 'Anas Iqbal', '', 'Qamar Iqbal', NULL, '2008-04-24', 'male', 'border', NULL, '2024-01-24', NULL, '0309-4527620', 'Qamar Iqbal', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Senior Salesman State Life Insurance', '35202-2110047-1', NULL, '0327-8892515', '0309-4527620', NULL, NULL, NULL, NULL, NULL, '6th'),
(15, NULL, 'STD-24-0007', 'ADM-2024-0007', 'Asim', '', 'Muhammad Sabir ur Rehman', NULL, '2010-07-14', 'male', 'border', NULL, '2024-01-26', NULL, '0324-1481654', 'Muhammad Sabir ur Rehman', NULL, 'Bahawalpur', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Saudia', '31202-7113057-6', NULL, '0325-9546651', '966509732445', NULL, NULL, NULL, NULL, NULL, '6th'),
(16, NULL, 'STD-24-0008', 'ADM-2024-0008', 'Ayan Ahmad', '', 'Maqbool Ahmad', NULL, '2010-11-12', 'male', 'border', NULL, '2024-01-29', NULL, '0301-7323282', 'Maqbool Ahmad', NULL, 'Sialkot', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Medical Store', '34603-5558998-1', NULL, '0321-7963282', '0301-7323282', NULL, NULL, NULL, NULL, NULL, '6th'),
(17, NULL, 'STD-24-0009', 'ADM-2024-0009', 'Muhammad Shayan', '', 'Qaisar Jawed', NULL, '2015-01-21', 'male', 'day_scholar', NULL, '2024-01-31', NULL, '0300-4795894', 'Qaisar Jawed', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Private job', NULL, NULL, '0306-4261388', '0300-4795894', NULL, NULL, NULL, NULL, NULL, '6th'),
(18, NULL, 'STD-24-0010', 'ADM-2024-0010', 'Muhammad Zain Ali', '', 'Qari Abdul Khaliq Qammar', NULL, '2012-04-16', 'male', 'day_scholar', NULL, '2024-02-02', NULL, '0300-4102351', 'Qari Abdul Khaliq Qammar', NULL, 'Khaniwal', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Teacher Tahfizul Quran', '36101-0243549-1', NULL, NULL, '0300-4102351', NULL, NULL, NULL, NULL, NULL, '6th'),
(19, NULL, 'STD-24-0011', 'ADM-2024-0011', 'Faiz Rasool', '', 'Muhammad ilyas', NULL, '2012-09-28', 'male', 'day_scholar', NULL, '2024-02-02', NULL, '0321-5429299', 'Muhammad ilyas', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own buisness', '35202-6161004-1', NULL, '0303-4434842', '0321-5429299', NULL, NULL, NULL, NULL, NULL, '6th'),
(20, NULL, 'STD-24-0012', 'ADM-2024-0012', 'M. Abdullah Ashraf', '', 'Muhammad Ashraf', NULL, '2011-05-17', 'male', 'border', NULL, '2024-02-06', NULL, '0321-3547156', 'Muhammad Ashraf', NULL, 'Fasialabad', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Saudia', '33101-0328315-5', NULL, '0344-0788270', '0344-0788270', NULL, NULL, NULL, NULL, NULL, '6th'),
(21, NULL, 'STD-24-0013', 'ADM-2024-0013', 'Muhammad Ismail', '', 'Muhammad Ismail', NULL, '2010-05-04', 'male', 'border', NULL, '2024-02-12', NULL, '0340-1160003', 'Muhammad Ismail', NULL, 'Baltistan', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Chief', '71301-6001092-1', NULL, '0315-4752670', '0340-1160003', NULL, NULL, NULL, NULL, NULL, '6th'),
(22, NULL, 'STD-24-0014', 'ADM-2024-0014', 'M. Zeeshan', '', 'Muhammad Ahmad', NULL, '2014-08-02', 'male', 'border', NULL, '2024-02-13', NULL, '0306-4242002', 'Muhammad Ahmad', NULL, 'Okara', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Labour', '35301-1953538-9', NULL, '0304-7213028', '0306-4242002', NULL, NULL, NULL, NULL, NULL, '6th'),
(23, NULL, 'STD-24-0015', 'ADM-2024-0015', 'Abdul Hadi', '', 'Usman Talib', NULL, '2012-02-14', 'male', 'border', NULL, '2024-02-15', NULL, '0300-6169601', 'Usman Talib', NULL, 'Sialkot', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Agriculture', '34603-2222838-9', NULL, '0313-4756225', '0300-6169601', NULL, NULL, NULL, NULL, NULL, '6th'),
(24, NULL, 'STD-24-0016', 'ADM-2024-0016', 'Muhammad Qasim', '', 'Shahid Qayyum', NULL, '2010-10-04', 'male', 'border', NULL, '2024-02-17', NULL, '0328-9456510', 'Shahid Qayyum', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Died', '35202-5115784-8', NULL, '0328-9456510', '0311-4057962', NULL, NULL, NULL, NULL, NULL, '6th'),
(25, NULL, 'STD-24-0017', 'ADM-2024-0017', 'Amir Shabeer', '', 'Muhammad Shabbir', NULL, '2011-07-14', 'male', 'day_scholar', NULL, '2024-02-20', NULL, '0300-8892940', 'Muhammad Shabbir', NULL, 'Faisalabad', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Private job', '33105-0301494-9', NULL, '0341-4639575', NULL, NULL, NULL, NULL, NULL, NULL, '6th'),
(26, NULL, 'STD-24-0018', 'ADM-2024-0018', 'Ali Hassan', '', 'M. Jawed Iqbal', NULL, '2007-12-19', 'male', 'day_scholar', NULL, '2024-02-21', NULL, '0321-7869333', 'M. Jawed Iqbal', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'C.I.A Police', '35201-1397152-7', NULL, '0300-9407013', '0300-9407013', NULL, NULL, NULL, NULL, NULL, '6th'),
(27, NULL, 'STD-24-0019', 'ADM-2024-0019', 'Ahmad Ali', '', 'Amir Ali', NULL, '2009-12-19', 'male', 'day_scholar', NULL, '2024-02-21', NULL, '0321-7869333', 'Amir Ali', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own buisness', '35201-1448067-3', NULL, '0321-8883454', '0321-8883454', NULL, NULL, NULL, NULL, NULL, '6th'),
(28, NULL, 'STD-24-0020', 'ADM-2024-0020', 'Mubeen Waheed', '', 'AbdulWaheed Khan', NULL, '2013-12-09', 'male', 'border', NULL, '2024-02-22', NULL, '0322-3600128', 'AbdulWaheed Khan', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, NULL, '35201-6638556-7', NULL, '0321-4099315', '0322-3600128', NULL, NULL, NULL, NULL, NULL, '6th'),
(29, NULL, 'STD-24-0021', 'ADM-2024-0021', 'Syed Saidan Ali Shah Gillani', '', 'Syed Noor Ali Shah Gillani', NULL, '2013-01-12', 'male', 'day_scholar', NULL, '2024-02-24', NULL, '0307-4318211', 'Syed Noor Ali Shah Gillani', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '35202-7983473-9', NULL, '0322-4931220', '0307-4318211', NULL, NULL, NULL, NULL, NULL, '6th'),
(30, NULL, 'STD-24-0022', 'ADM-2024-0022', 'Hassan Mustafa', '', 'Hafiz Hamid Saeed', NULL, '2012-09-27', 'male', 'border', NULL, '2024-02-26', NULL, '0306-6606426', 'Hafiz Hamid Saeed', NULL, 'Gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Private Teacher', '34104-8435355-1', NULL, '0307-0609066', '0306-6606426', NULL, NULL, NULL, NULL, NULL, '6th'),
(31, NULL, 'STD-24-0023', 'ADM-2024-0023', 'Muhammad Usman', '', 'Arshad Ahmad', NULL, '2011-11-03', 'male', 'border', NULL, '2024-02-26', NULL, '0333-8696012', 'Arshad Ahmad', NULL, 'Sialkot', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Hardware Tools Manifacture', '34604-0405355-1', NULL, '0300-6106847', '0300-6106847', NULL, NULL, NULL, NULL, NULL, '6th'),
(32, NULL, 'STD-24-0024', 'ADM-2024-0024', 'CH. Abdulrehman', '', 'Ahsanullah', NULL, '2011-10-11', 'male', 'border', NULL, '2024-03-04', NULL, '', 'Ahsanullah', NULL, 'Mandi Bahauddin', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '34403-1480640-7', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '6th'),
(33, NULL, 'STD-24-0025', 'ADM-2024-0025', 'Ch. AbdulFaizan', '', 'Ahsanullah', NULL, '2013-09-24', 'male', 'border', NULL, '2024-03-04', NULL, '', 'Ahsanullah', NULL, 'Mandi Bahauddin', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '34403-1480640-7', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '6th'),
(34, NULL, 'STD-24-0026', 'ADM-2024-0026', 'Rana Saim Akmal', '', 'M. akmal', NULL, '2013-02-20', 'male', 'border', NULL, '2024-03-04', NULL, '0302-1366648', 'M. akmal', NULL, 'Gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Teacher', '34103-0304259-7', NULL, '0302-1366648', '0318-73600002', NULL, NULL, NULL, NULL, NULL, '6th'),
(35, NULL, 'STD-24-0027', 'ADM-2024-0027', 'Muhammad Hassan', '', 'Asalm Khan', NULL, '2012-02-21', 'male', 'border', NULL, '2024-03-04', NULL, '0332-5775541', 'Asalm Khan', NULL, 'Chakwal', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Private Teacher', '37203-8829844-9', NULL, '0315-5776505', '0332-5775541', NULL, NULL, NULL, NULL, NULL, '6th'),
(36, NULL, 'STD-24-0028', 'ADM-2024-0028', 'M. Ahamd Abdullah', '', 'Muhammad irfan', NULL, '2012-12-12', 'male', 'border', NULL, '2024-03-05', NULL, '0304-5612033', 'Muhammad irfan', NULL, 'Faisalabad', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Privatejob', '33100-1013221-9', NULL, '0335-3311211', '0304-5612033', NULL, NULL, NULL, NULL, NULL, '6th'),
(37, NULL, 'STD-24-0029', 'ADM-2024-0029', 'Syed Fasih ul Hassan Kazmi', '', 'Syed Mashhood Hussain Kazmi', NULL, '2011-06-15', 'male', 'day_scholar', NULL, '2024-03-06', NULL, '0332-5121290', 'Syed Mashhood Hussain Kazmi', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Dubai', '37105-9647122-1', NULL, NULL, '0332-5121290', NULL, NULL, NULL, NULL, NULL, '6th'),
(38, NULL, 'STD-24-0030', 'ADM-2024-0030', 'Syed Fawad Hassan Kazmi', '', 'Syed Shahid Hussain Kazmi', NULL, '2011-05-26', 'male', 'day_scholar', NULL, '2024-03-06', NULL, '0332-5121290', 'Syed Shahid Hussain Kazmi', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Dubai', '37105-9647122-1', NULL, NULL, '0332-5121290', NULL, NULL, NULL, NULL, NULL, '6th'),
(39, NULL, 'STD-24-0031', 'ADM-2024-0031', 'Ibad Mustafa', '', 'Sajjad Hussain', NULL, '2012-10-28', 'male', 'border', NULL, '2024-03-06', NULL, '0321-6144149', 'Sajjad Hussain', NULL, 'Sialkot', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Privatejob', NULL, NULL, '0349-4977456', '0321-6144149', NULL, NULL, NULL, NULL, NULL, '6th'),
(40, NULL, 'STD-24-0032', 'ADM-2024-0032', 'M. Mustafa Khan', '', 'M. umer Khan', NULL, '2012-01-07', 'male', 'day_scholar', NULL, '2024-03-06', NULL, '0321-9435439', 'M. umer Khan', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Auto Spair Parts', '35202-2871840-7', NULL, '0309-9070408', '0321-6435439', NULL, NULL, NULL, NULL, NULL, '6th'),
(41, NULL, 'STD-24-0033', 'ADM-2024-0033', 'Ahmad Shahzad', '', 'Adil Hussain', NULL, '2014-06-15', 'male', 'border', NULL, '2024-03-06', NULL, '0302-7839763', 'Adil Hussain', NULL, 'Toba Tak Singh', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Medical Store', '33302-2208077-5', NULL, '0303-0555763', '0303-0555763', NULL, NULL, NULL, NULL, NULL, '6th'),
(42, NULL, 'STD-24-0034', 'ADM-2024-0034', 'M. Ahmad Zaffer', '', 'Zaffer Ali', NULL, '2013-05-10', 'male', 'day_scholar', NULL, '2024-03-09', NULL, '0321-4663932', 'Zaffer Ali', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'cardiology (Dispensor)', '36601-1555538-1', NULL, '0324-4599831', '0321-4663932', NULL, NULL, NULL, NULL, NULL, '6th'),
(43, NULL, 'STD-24-0035', 'ADM-2024-0035', 'Abdul Hadi', '', 'Muhammad Wasim', NULL, '2014-12-12', 'male', 'day_scholar', NULL, '2024-03-09', NULL, '0301-4941392', 'Muhammad Wasim', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Masala jat', '35202-1104244-5', NULL, '0301-0013911', NULL, NULL, NULL, NULL, NULL, NULL, '6th'),
(44, NULL, 'STD-24-0036', 'ADM-2024-0036', 'Azan Ali', '', 'Ghulam Rasool', NULL, '2014-12-17', 'male', 'border', NULL, '2024-03-09', NULL, '', 'Ghulam Rasool', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Carpenter', '35202-8182511-5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '6th'),
(45, NULL, 'STD-24-0037', 'ADM-2024-0037', 'Zman Ali', '', 'Asif Ali', NULL, '2011-08-16', 'male', 'day_scholar', NULL, '2024-03-09', NULL, '0301-8111772', 'Asif Ali', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '35201-1318649-3', NULL, '0300-4730669', '0301-8111772', NULL, NULL, NULL, NULL, NULL, '6th'),
(46, NULL, 'STD-24-0038', 'ADM-2024-0038', 'Saqib Ali', '', 'AsifAli', NULL, '2012-10-20', 'male', 'day_scholar', NULL, '2024-03-09', NULL, '0301-8111772', 'AsifAli', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '35201-1318649-3', NULL, '0300-4730669', '0301-8111772', NULL, NULL, NULL, NULL, NULL, '6th'),
(47, NULL, 'STD-24-0039', 'ADM-2024-0039', 'M. Hamza Awais', '', 'Awais Safder', NULL, '2012-06-12', 'male', 'day_scholar', NULL, '2024-03-11', NULL, '3354956819', 'Awais Safder', NULL, 'Jehlum', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '37302-4355426-3', NULL, '0325-5737107', '0335-4956819', NULL, NULL, NULL, NULL, NULL, '6th'),
(48, NULL, 'STD-24-0040', 'ADM-2024-0040', 'M. Hussain mustafa', '', 'Sikander Hussain Chattha', NULL, '2013-01-17', 'male', 'border', NULL, '2024-03-11', NULL, '0301-6634214', 'Sikander Hussain Chattha', NULL, 'Gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Petroling Police', '34101-2720514-3', NULL, '0348-6084909', '0301-6634214', NULL, NULL, NULL, NULL, NULL, '6th'),
(49, NULL, 'STD-24-0041', 'ADM-2024-0041', 'Muhammad Ibrahim', '', 'Ahtsham ul haq', NULL, '2012-05-15', 'male', 'day_scholar', NULL, '2024-03-11', NULL, '0303-9653728', 'Ahtsham ul haq', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Labour', '35202-4259856-9', NULL, '0303-9653728', '0340-4720173', NULL, NULL, NULL, NULL, NULL, '6th'),
(50, NULL, 'STD-24-0042', 'ADM-2024-0042', 'Syed Saqlain Haider Shah Gillani', '', 'Syed Ahmad Hassan', NULL, '2012-01-23', 'male', 'border', NULL, '2024-03-11', NULL, '0306-4780544', 'Syed Ahmad Hassan', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '35202-2220416-3', NULL, '0302-4040842', '0306-4780544', NULL, NULL, NULL, NULL, NULL, '6th'),
(51, NULL, 'STD-24-0043', 'ADM-2024-0043', 'M. Abdullah', '', 'M. Majeedullah', NULL, '2012-12-05', 'male', 'border', NULL, '2024-03-11', NULL, '0300-8499606', 'M. Majeedullah', NULL, 'Toba Tak Singh', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Shopkeeper', '33302-9760211-5', NULL, '0300-8499606', '0300-4374884', NULL, NULL, NULL, NULL, NULL, '6th'),
(52, NULL, 'STD-24-0044', 'ADM-2024-0044', 'Muhammad Abu Bakar', '', 'Muhammad Jahangir', NULL, '2012-09-23', 'male', 'border', NULL, '2024-03-11', NULL, '0300-8499606', 'Muhammad Jahangir', NULL, 'Toba Tak Singh', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '33302-3064991-1', NULL, '0300-8499606', '0300-4374884', NULL, NULL, NULL, NULL, NULL, '6th'),
(53, NULL, 'STD-24-0045', 'ADM-2024-0045', 'Muhammad Abdullah', '', 'M. Naseer', NULL, '2011-10-04', 'male', 'border', NULL, '2024-03-13', NULL, '0346-2353918', 'M. Naseer', NULL, 'Jehlum', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Driver (Islamabad)', '37302-1196400-1', NULL, '0346-2353918', '0346-2354918', NULL, NULL, NULL, NULL, NULL, '6th'),
(54, NULL, 'STD-24-0046', 'ADM-2024-0046', 'Dilawer Saddam', '', 'Saddam Hussain', NULL, '2013-01-01', 'male', 'aghosh', NULL, '2024-03-14', NULL, '0313-4402771', 'Saddam Hussain', NULL, 'Wahari', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Orfan', '36601-8704563-2', NULL, NULL, '0313-4402771', NULL, NULL, NULL, NULL, NULL, '6th'),
(55, NULL, 'STD-24-0047', 'ADM-2024-0047', 'Raja Husnain Nasir', '', 'Raja Sajeel Ahmad Nasir', NULL, '2011-07-01', 'male', 'border', NULL, '2024-03-14', NULL, '0309-1092038', 'Raja Sajeel Ahmad Nasir', NULL, 'Gujrat', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Branch manager Diamond Trading Compony', '34202-7604654-1', NULL, '0333-8118003', '0300-624749', NULL, NULL, NULL, NULL, NULL, '6th'),
(56, NULL, 'STD-24-0048', 'ADM-2024-0048', 'Umar Shahzad', '', 'Muhammad Arif', NULL, '2013-11-13', 'male', 'orphan', NULL, '2024-03-14', NULL, '0313-4402771', 'Muhammad Arif', NULL, NULL, NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Deid', '13503-99471417', NULL, '0313-4402771', '0313-4402771', NULL, NULL, NULL, NULL, NULL, '6th'),
(57, NULL, 'STD-24-0049', 'ADM-2024-0049', 'Malik Azan', '', 'Muhammad Atif', NULL, '2014-08-16', 'male', 'border', NULL, '2024-03-15', NULL, '0322-5101011', 'Muhammad Atif', NULL, 'Gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness (packages)', '34101-9176901-1', NULL, '0319-1987903', '0322-05101011', NULL, NULL, NULL, NULL, NULL, '6th'),
(58, NULL, 'STD-24-0050', 'ADM-2024-0050', 'Muhammad Hussain', '', 'kashif Mahmood Shahzad', NULL, '2010-04-25', 'male', 'border', NULL, '2024-04-10', NULL, '0301-88664270', 'kashif Mahmood Shahzad', NULL, 'Sialkot', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '34602-8869689-5', NULL, '0300-6194039', '0301-8664270', NULL, NULL, NULL, NULL, NULL, '7th'),
(59, NULL, 'STD-24-0051', 'ADM-2024-0051', 'Muhammad Saad Naveed', '', 'Ghulam Naveed', NULL, '2009-09-23', 'male', 'border', NULL, '2024-04-10', NULL, '0345-6282707', 'Ghulam Naveed', NULL, 'Sialkot', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '34602-0915500-7', NULL, '0345-6632100', '0345-6282707', NULL, NULL, NULL, NULL, NULL, '7th'),
(60, NULL, 'STD-24-0052', 'ADM-2024-0052', 'M. Zain ul Abideen', '', 'M. Irshad Hussain', NULL, '2012-09-22', 'male', 'day_scholar', NULL, '2024-04-14', NULL, '0300-4219407', 'M. Irshad Hussain', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Government employee', '35201-1500973-3', NULL, '0334-4167928', '0300-4219407', NULL, NULL, NULL, NULL, NULL, '6th'),
(61, NULL, 'STD-24-0053', 'ADM-2024-0053', 'M. Tahir Afaq', '', 'Aftab Ahmad', NULL, '2013-02-09', 'male', 'border', NULL, '2024-04-16', NULL, '0333-8909590', 'Aftab Ahmad', NULL, 'Bhakhar', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Private Dispenser', '38101-0673589-9', NULL, '0344-1780055', NULL, NULL, NULL, NULL, NULL, NULL, '6th'),
(62, NULL, 'STD-24-0054', 'ADM-2024-0054', 'Abdul Hadi', '', 'Tariq Mahmood', NULL, '2013-11-20', 'male', 'border', NULL, '2024-04-16', NULL, '0345-6288076', 'Tariq Mahmood', NULL, 'Sheikupura', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Welding Work', '35404-1485543-3', NULL, '0307-7770425', '0345-6288076', NULL, NULL, NULL, NULL, NULL, '6th'),
(63, NULL, 'STD-24-0055', 'ADM-2024-0055', 'Muhammad Moaz', '', 'abdulhanan', NULL, '2013-11-20', 'male', 'border', NULL, '2024-04-16', NULL, '0301-4429216', 'abdulhanan', NULL, 'Okara', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Imamt', '35301-5146273-7', NULL, '0308-2646933', '0301-4429216', NULL, NULL, NULL, NULL, NULL, '6th'),
(64, NULL, 'STD-24-0056', 'ADM-2024-0056', 'Ifrahim khan', '', 'M. Frhan Khan', NULL, '2012-05-01', 'male', 'day_scholar', NULL, '2024-04-16', NULL, '0301-4473168', 'M. Frhan Khan', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '35202-2803839-7', NULL, '0312-4551922', '0301-4473168', NULL, NULL, NULL, NULL, NULL, '6th'),
(65, NULL, 'STD-24-0057', 'ADM-2024-0057', 'M. Zubair Khan', '', 'Muhammad shoaib', NULL, '2012-06-05', 'male', 'day_scholar', NULL, '2024-04-16', NULL, '0302-5389723', 'Muhammad shoaib', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Electronics Shop', '35201-1698477-1', NULL, '0334-4028545', '0302-5389723', NULL, NULL, NULL, NULL, NULL, '6th'),
(66, NULL, 'STD-24-0058', 'ADM-2024-0058', 'Muneeb Ghafoor', '', 'Muhammad Ghafoor', NULL, '2011-03-15', 'male', 'day_scholar', NULL, '2024-04-16', NULL, '0326-2824535', 'Muhammad Ghafoor', NULL, 'Okara', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Labour', '35301-2008773-5', NULL, '0308-6251709', '0326-2824535', NULL, NULL, NULL, NULL, NULL, '6th'),
(67, NULL, 'STD-24-0059', 'ADM-2024-0059', 'Ebd e Ali Akbar Saqi', '', 'Malik Parvaiz Akbar', NULL, '2012-07-29', 'male', 'border', NULL, '2024-04-16', NULL, '0300-4809583', 'Malik Parvaiz Akbar', NULL, 'Khushab', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Agriculture', '38201-3073498-5', NULL, '0300-7800357', '0300-4809583', NULL, NULL, NULL, NULL, NULL, '6th'),
(68, NULL, 'STD-24-0060', 'ADM-2024-0060', 'Syed Farhan Ali Bahadur', '', 'Syed Sultan Ali', NULL, '2012-06-16', 'male', 'border', NULL, '2024-04-17', NULL, '0300-662529', 'Syed Sultan Ali', NULL, 'Fasialabad', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '33104-2244559-9', NULL, '0313-7278673', '0300-662529', NULL, NULL, NULL, NULL, NULL, '6th'),
(69, NULL, 'STD-24-0061', 'ADM-2024-0061', 'M. Sudais Haider', '', 'Shamsher Haider', NULL, '2013-08-02', 'male', 'border', NULL, '2024-04-17', NULL, '0333-9849539', 'Shamsher Haider', NULL, 'Kasur', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'R. Army Person', '32203-99240791', NULL, '0300-4872584', '0333-9849539', NULL, NULL, NULL, NULL, NULL, '6th'),
(70, NULL, 'STD-24-0062', 'ADM-2024-0062', 'Muhammad Ahmad', '', 'Khizer Hayat', NULL, '2013-01-23', 'male', 'border', NULL, '2024-04-17', NULL, '0348-6740836', 'Khizer Hayat', NULL, 'Sialkot', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'privatejob Saudia', '34601-0744427-9', NULL, '0348-6740836', '960572994151', NULL, NULL, NULL, NULL, NULL, '6th'),
(71, NULL, 'STD-24-0063', 'ADM-2024-0063', 'Ahamd Faiz', '', 'Imran Sardar', NULL, '2013-01-03', 'male', 'day_scholar', NULL, '2024-04-17', NULL, '0331-4409768', 'Imran Sardar', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Old Age Home Employee', '35202-3319180-7', NULL, '0334-4189868', '0331-4409768', NULL, NULL, NULL, NULL, NULL, '6th'),
(72, NULL, 'STD-24-0064', 'ADM-2024-0064', 'M. Yahya Kashif', '', 'M. Kashif Iqbal', NULL, '2014-11-21', 'male', 'border', NULL, '2024-04-17', NULL, '0307-7777880', 'M. Kashif Iqbal', NULL, 'Gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'WaPDA Employee', '34101-7762598-9', NULL, '0333-5164370', '0307-7777880', NULL, NULL, NULL, NULL, NULL, '6th'),
(73, NULL, 'STD-24-0065', 'ADM-2024-0065', 'Shafqat Mahmood', '', 'Talhat Mahmood', NULL, '2011-09-30', 'male', 'day_scholar', NULL, '2024-04-17', NULL, '0307-5402646', 'Talhat Mahmood', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Uber Driver', '35201-3511224-5', NULL, '0344-8509826', '0307-5402646', NULL, NULL, NULL, NULL, NULL, '6th'),
(74, NULL, 'STD-24-0066', 'ADM-2024-0066', 'M. Saad Sati', '', 'Atif Mahmood', NULL, '2012-11-08', 'male', 'border', NULL, '2024-04-17', NULL, '0343-5050465', 'Atif Mahmood', NULL, 'Rawalpindi', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'privatejob Dubai', '37403-0121576-5', NULL, '0346-6588055', '0343-5050465', NULL, NULL, NULL, NULL, NULL, '6th'),
(75, NULL, 'STD-24-0067', 'ADM-2024-0067', 'Inam Mustafa', '', 'Ashiq Hussain', NULL, '2011-12-24', 'male', 'border', NULL, '2024-04-17', NULL, '0347-8659890', 'Ashiq Hussain', NULL, 'Chiniot', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Ice Work', '33201-8648830-9', NULL, '0347-8659890', '0303-6312937', NULL, NULL, NULL, NULL, NULL, '6th'),
(76, NULL, 'STD-24-0068', 'ADM-2024-0068', 'Ahmad Jawed', '', 'M. Jawed Iqbal', NULL, '2012-03-25', 'male', 'border', NULL, '2024-04-18', NULL, '0300-6624585', 'M. Jawed Iqbal', NULL, 'faisalabad', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '33303-5736248-4', NULL, '0346-7760511', '0346-7760511', NULL, NULL, NULL, NULL, NULL, '6th'),
(77, NULL, 'STD-24-0069', 'ADM-2024-0069', 'Muhammad Haroon', '', 'Jawed Iqbal', NULL, '2013-03-20', 'male', 'border', NULL, '2024-04-18', NULL, '0346-5478477', 'Jawed Iqbal', NULL, 'Lowerder kpk', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '15302-8546053-9', NULL, '0327-4526050', '0346-5478477', NULL, NULL, NULL, NULL, NULL, '6th'),
(78, NULL, 'STD-24-0070', 'ADM-2024-0070', 'Shahid Iqbal', '', 'Muhammad Iqbal', NULL, '2012-08-01', 'male', 'border', NULL, '2024-04-18', NULL, '0301-7241892', 'Muhammad Iqbal', NULL, 'Pak Patan', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Electrition', '36402-0372043-3', NULL, '0304-1302231', '0301-7241892', NULL, NULL, NULL, NULL, NULL, '6th'),
(79, NULL, 'STD-24-0071', 'ADM-2024-0071', 'Muhammad Bilal', '', 'Mirza Ijaz Baig', NULL, '2011-04-02', 'male', 'day_scholar', NULL, '2024-04-18', NULL, '0321-4332324', 'Mirza Ijaz Baig', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Labour', '35202-2957341-1', NULL, '0346-7968983', '0321-4332324', NULL, NULL, NULL, NULL, NULL, '6th'),
(80, NULL, 'STD-24-0072', 'ADM-2024-0072', 'Muhammad Hashir', '', 'Wakeel Ahmad', NULL, '2009-08-01', 'male', 'border', NULL, '2024-04-18', NULL, '0301-4060627', 'Wakeel Ahmad', NULL, 'Nankana', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '35402-4667820-5', NULL, '971507407548', '0301-4060627', NULL, NULL, NULL, NULL, NULL, '6th'),
(81, NULL, 'STD-24-0073', 'ADM-2024-0073', 'Muhammad Mubashir', '', 'Wakeel Ahmad', NULL, '2011-09-24', 'male', 'border', NULL, '2024-04-18', NULL, '0301-4064627', 'Wakeel Ahmad', NULL, 'Nankana', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '35402-4667820-5', NULL, '971507407548', '0301-4060627', NULL, NULL, NULL, NULL, NULL, '6th'),
(82, NULL, 'STD-24-0074', 'ADM-2024-0074', 'M. Baqir Hussain', '', 'Bawar Hussain', NULL, '2010-08-12', 'male', 'border', NULL, '2024-04-18', NULL, '0300-6438228', 'Bawar Hussain', NULL, 'Gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '34101-2520247-1', NULL, '0300-6655478', '0300-6438228', NULL, NULL, NULL, NULL, NULL, '6th'),
(83, NULL, 'STD-24-0075', 'ADM-2024-0075', 'Muhammad Wajid', '', 'Muhammad Sajid', NULL, '2012-12-27', 'male', 'border', NULL, '2024-04-18', NULL, '0305-2849649', 'Muhammad Sajid', NULL, 'Rahim Yaar Khan', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Agriculture Pharmacy', '31302-7891847-9', NULL, '0300-8629689', '0305-2849649', NULL, NULL, NULL, NULL, NULL, '6th'),
(84, NULL, 'STD-24-0076', 'ADM-2024-0076', 'Farhan Khalid', '', 'Khalid Mahmood', NULL, '2013-06-27', 'male', 'border', NULL, '2024-04-18', NULL, '0306-6657046', 'Khalid Mahmood', NULL, 'Gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'privatejob South korea', '34101-8385399-1', NULL, '0347-6741433', NULL, NULL, NULL, NULL, NULL, NULL, '6th'),
(85, NULL, 'STD-24-0077', 'ADM-2024-0077', 'Muhammad Dawood Asif', '', 'Muhammad Asif', NULL, '2013-10-26', 'male', 'day_scholar', NULL, '2024-04-18', NULL, '0300-4360732', 'Muhammad Asif', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Almunir High School', '35201-7081118-5', NULL, '0324-4705994', '0300-4360732', NULL, NULL, NULL, NULL, NULL, '6th'),
(86, NULL, 'STD-24-0078', 'ADM-2024-0078', 'Syed Hassan Abdal Gillani', '', 'syed Abdal Hussain', NULL, '2008-04-05', 'male', 'border', NULL, '2024-04-18', NULL, '0300-41673383', 'syed Abdal Hussain', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Medical Store', '35101-2475883-1', NULL, '0300-9491268', '0300-4167383', NULL, NULL, NULL, NULL, NULL, '6th'),
(87, NULL, 'STD-24-0079', 'ADM-2024-0079', 'Zain ul Abideen', '', 'abdulqadir', NULL, '2014-01-22', 'male', 'border', NULL, '2024-04-18', NULL, '0345-9585053', 'abdulqadir', NULL, 'Abotabad', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Teacher', '33101-5623940-1', NULL, '0321-9972292', '0345-9585053', NULL, NULL, NULL, NULL, NULL, '6th'),
(88, NULL, 'STD-24-0080', 'ADM-2024-0080', 'M. Sohail Zulfiqar', '', 'Zulfikar Ali', NULL, '2012-10-29', 'male', 'border', NULL, '2024-04-18', NULL, '0300-7962866', 'Zulfikar Ali', NULL, 'Okara', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Agriculture', '35301-1868082-9', NULL, '0300-46381853', '0300-7962866', NULL, NULL, NULL, NULL, NULL, '6th'),
(89, NULL, 'STD-24-0081', 'ADM-2024-0081', 'Muhammad Umer', '', 'Muhammad Asif', NULL, '2012-12-22', 'male', 'border', NULL, '2024-04-18', NULL, '0300-4381853', 'Muhammad Asif', NULL, 'Okara', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Teacher', '35301-1881177-3', NULL, '0300-4092800', '0300-4381853', NULL, NULL, NULL, NULL, NULL, '6th'),
(90, NULL, 'STD-24-0082', 'ADM-2024-0082', 'Muhammad Hasaan', '', 'Asim Rafiq', NULL, '2011-01-05', 'male', 'border', NULL, '2024-04-18', NULL, '0306-5469871', 'Asim Rafiq', NULL, 'Attack', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Govt Employee Revenue Office', '37102-9115704-5', NULL, '0310-5148996', '0306-5469871', NULL, NULL, NULL, NULL, NULL, '8th'),
(91, NULL, 'STD-24-0083', 'ADM-2024-0083', 'M. Abdul Hamid Mahmood', '', 'Khalid Mahmood', NULL, '2012-01-23', 'male', 'border', NULL, '2024-04-18', NULL, '0310-2129604', 'Khalid Mahmood', NULL, 'Rawalpindi', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Agriculture', '37406-1556114-5', NULL, '0320-5091370', '0310-5793613', NULL, NULL, NULL, NULL, NULL, '6th'),
(92, NULL, 'STD-24-0084', 'ADM-2024-0084', 'Faizan Rasool', '', 'Mubashir Hussain', NULL, '2013-03-20', 'male', 'border', NULL, '2024-04-18', NULL, '0302-4279354', 'Mubashir Hussain', NULL, 'Nankana', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Army Officer (NCB)', '35501-0290830-9', NULL, '0322-2850160', '0322-2850160', NULL, NULL, NULL, NULL, NULL, '6th'),
(93, NULL, 'STD-24-0085', 'ADM-2024-0085', 'M. Tahir Mustafa', '', 'Ghulam Mustafa Siddique', NULL, '2010-10-11', 'male', 'border', NULL, '2024-04-18', NULL, '0301-6266512', 'Ghulam Mustafa Siddique', NULL, 'Bhimber AzadKahsmir', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Khatib', '81101-9298514-9', NULL, '0301-6297154', '0301-6266512', NULL, NULL, NULL, NULL, NULL, '6th'),
(94, NULL, 'STD-24-0086', 'ADM-2024-0086', 'Rana Usaid ur Rasool', '', 'Rana Tanveer Hussain', NULL, '2011-11-02', 'male', 'day_scholar', NULL, '2024-04-18', NULL, '0300-4449096', 'Rana Tanveer Hussain', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Professor Minhaj University', '35401-6069486-3', NULL, '0333-4660882', '0300-4449096', NULL, NULL, NULL, NULL, NULL, '6th'),
(95, NULL, 'STD-24-0087', 'ADM-2024-0087', 'M. Mehrban Asghar', '', 'M. Imran', NULL, '2013-03-17', 'male', 'border', NULL, '2024-04-18', NULL, '0333-5877982', 'M. Imran', NULL, 'Jehlum', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '37301-0563450-7', NULL, '0346-0450463', '0333-5877982', NULL, NULL, NULL, NULL, NULL, '6th'),
(96, NULL, 'STD-24-0088', 'ADM-2024-0088', 'Shazil Aman', '', 'Amanullah', NULL, '2013-03-10', 'male', 'border', NULL, '2024-04-18', NULL, '0306-7420774', 'Amanullah', NULL, 'Jhung', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Labour', '35202-8764037-1', NULL, '0305-5100905', '0342-7927798', NULL, NULL, NULL, NULL, NULL, '6th'),
(97, NULL, 'STD-24-0089', 'ADM-2024-0089', 'M. Subhan Hafeez', '', 'Shahid Hafeez', NULL, '2009-04-17', 'male', 'border', NULL, '2024-04-18', NULL, '0311-4961545', 'Shahid Hafeez', NULL, 'Rawalpindi', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Shopkeeper', '37401-1485077-9', NULL, '0346-5733468', '0311-4961545', NULL, NULL, NULL, NULL, NULL, NULL),
(98, NULL, 'STD-24-0090', 'ADM-2024-0090', 'Rana Junaid Rasool', '', 'Rana Tanveer Hussain', NULL, '2013-12-31', 'male', 'day_scholar', NULL, '2024-04-18', NULL, '0300-4449096', 'Rana Tanveer Hussain', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Professor Minhaj University', '35401-6069486-3', NULL, '0333-4660882', '0300-4449096', NULL, NULL, NULL, NULL, NULL, '6th'),
(99, NULL, 'STD-24-0091', 'ADM-2024-0091', 'Farman Ali', '', 'Tariq Mahmood', NULL, '2013-11-19', 'male', 'day_scholar', NULL, '2024-04-19', NULL, '0321-2503750', 'Tariq Mahmood', NULL, 'Rajanpur', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'InDrive Driver', '32402-6208001-3', NULL, '0333-6452310', '0321-2503750', NULL, NULL, NULL, NULL, NULL, '6th'),
(100, NULL, 'STD-24-0092', 'ADM-2024-0092', 'Muhammad Usman', '', 'Muhammad Shaban', NULL, '2010-05-12', 'male', 'day_scholar', NULL, '2024-04-19', NULL, '0305-2946346', 'Muhammad Shaban', NULL, 'Pak Patan', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Factory Employee', '36402-9966660-3', NULL, '0306-4740304', '0306-4740304', NULL, NULL, NULL, NULL, NULL, '7th'),
(101, NULL, 'STD-24-0093', 'ADM-2024-0093', 'Hussain Ali', '', 'Manzoor Hussain', NULL, '2012-12-24', 'male', 'border', NULL, '2024-04-19', NULL, '0303-3915000', 'Manzoor Hussain', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Farmer', '35202-2736013-1', NULL, '0300-8090819', '0303-3915000', NULL, NULL, NULL, NULL, NULL, '6th'),
(102, NULL, 'STD-24-0094', 'ADM-2024-0094', 'Ahmad Mustafa', '', 'Mumtaz Ahmad Rabbani', NULL, '2009-08-28', 'male', 'border', NULL, '2024-04-19', NULL, '0321-5077879', 'Mumtaz Ahmad Rabbani', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Khatib', '34102-0422635-7', NULL, '0300-95403408', '0321-5077879', NULL, NULL, NULL, NULL, NULL, '6th'),
(103, NULL, 'STD-24-0095', 'ADM-2024-0095', 'Muhammad Abdul Rehman', '', 'Zahir Khan Siddique', NULL, '2014-03-15', 'male', 'border', NULL, '2024-04-19', NULL, '0342-5187602', 'Zahir Khan Siddique', NULL, 'Islamabad', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '82303-6447378-9', NULL, '0310-9329391', '0310-9329391', NULL, NULL, NULL, NULL, NULL, '6th'),
(104, NULL, 'STD-24-0096', 'ADM-2024-0096', 'Muhammad Ahil', '', 'Muhammad Arshad', NULL, '2013-10-20', 'male', 'border', NULL, '2024-04-20', NULL, '0303-6785688', 'Muhammad Arshad', NULL, 'Gujrat', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Sweets distributer', '34202-0760859-1', NULL, '0342-1062688', '0303-6785688', NULL, NULL, NULL, NULL, NULL, '6th'),
(105, NULL, 'STD-24-0097', 'ADM-2024-0097', 'Armgan Naveed Ahmad', '', 'Naveed Ahmad', NULL, '2013-05-10', 'male', 'day_scholar', NULL, '2024-04-20', NULL, '0321-9465812', 'Naveed Ahmad', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '35202-5717040-1', NULL, '0323-4487165', '0323-4487165', NULL, NULL, NULL, NULL, NULL, '6th'),
(106, NULL, 'STD-24-0098', 'ADM-2024-0098', 'Ahmad Mustafa', '', 'M. Amjad Mahmood', NULL, '2015-04-28', 'male', 'border', NULL, '2024-04-20', NULL, '0301-5039105', 'M. Amjad Mahmood', NULL, 'Wahari', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Army Officer (NCB)', '36602-1181537-3', NULL, '0314-5275743', '0314-5275743', NULL, NULL, NULL, NULL, NULL, '6th'),
(107, NULL, 'STD-24-0099', 'ADM-2024-0099', 'Burhan Ali', '', 'Abdulkhaliq', NULL, '2012-10-26', 'male', 'day_scholar', NULL, '2024-04-20', NULL, '0300-8870708', 'Abdulkhaliq', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '35202-2451613-5', NULL, NULL, '0300-8870708', NULL, NULL, NULL, NULL, NULL, '6th'),
(108, NULL, 'STD-24-0100', 'ADM-2024-0100', 'Usman Zaffar', '', 'Zafar Iqbal', NULL, '2012-11-22', 'male', 'border', NULL, '2024-04-20', NULL, '0345-8619415', 'Zafar Iqbal', NULL, 'Gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Army Soilder', '34301-2330008-5', NULL, '0300-6453675', '0345-8619415', NULL, NULL, NULL, NULL, NULL, '6th'),
(109, NULL, 'STD-24-0101', 'ADM-2024-0101', 'Hammad ul Hassan', '', 'Sarfaraz Ali', NULL, '2012-05-07', 'male', 'border', NULL, '2024-04-22', NULL, '0300-3493126', 'Sarfaraz Ali', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '35202-7614260-9', NULL, '0300-9430511', '0300-4393126', NULL, NULL, NULL, NULL, NULL, '6th'),
(110, NULL, 'STD-24-0102', 'ADM-2024-0102', 'M. Asjal Toor', '', 'Rizwan ullah', NULL, '2012-11-04', 'male', 'border', NULL, '2024-04-22', NULL, '0320-3564558', 'Rizwan ullah', NULL, 'Sahiwal', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '35202-5872777-9', NULL, '0321-4682941', '0320-3564558', NULL, NULL, NULL, NULL, NULL, '6th'),
(111, NULL, 'STD-24-0103', 'ADM-2024-0103', 'M. Shams ul Mustafa', '', 'Tahir Aslam Jawed', NULL, '2013-06-25', 'male', 'border', NULL, '2024-04-22', NULL, '0322-2158232', 'Tahir Aslam Jawed', NULL, 'Sadhnooti AzadKashmir', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'PAF', '82401-8404427-3', NULL, '0319-5285214', '0322-2158232', NULL, NULL, NULL, NULL, NULL, '6th'),
(112, NULL, 'STD-24-0104', 'ADM-2024-0104', 'Muhammad Haris', '', 'Muhammad Mumtaz', NULL, '2013-08-01', 'male', 'border', NULL, '2024-04-22', NULL, '0302-6535634', 'Muhammad Mumtaz', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '36401-0874710-1', NULL, '0307-4716465', '0302-6535634', NULL, NULL, NULL, NULL, NULL, '6th'),
(113, NULL, 'STD-24-0105', 'ADM-2024-0105', 'Hamid Raza', '', 'Muhammad Ishfaq Ahmad', NULL, '2010-08-29', 'male', 'border', NULL, '2024-04-22', NULL, '0322-9284482', 'Muhammad Ishfaq Ahmad', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Agriculture', '35202-6356351-7', NULL, '0334-9863680', '0322-9284482', NULL, NULL, NULL, NULL, NULL, '7th'),
(114, NULL, 'STD-24-0106', 'ADM-2024-0106', 'Muhammad Taha', '', 'Umar Hayat', NULL, '2011-01-25', 'male', 'day_scholar', NULL, '2024-04-22', NULL, '0300-8040620', 'Umar Hayat', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Labour', '35201-8661897-5', NULL, '0300-4055682', '0300-8040620', NULL, NULL, NULL, NULL, NULL, '6th'),
(115, NULL, 'STD-24-0107', 'ADM-2024-0107', 'Muhammad Rehan', '', 'Muhammad iqbal', NULL, '2011-07-20', 'male', 'border', NULL, '2024-04-22', NULL, '0308-0482414', 'Muhammad iqbal', NULL, 'Sheikupura', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Labour', '35401-1847966-3', NULL, '0344-4983541', '0308-0482414', NULL, NULL, NULL, NULL, NULL, '6th'),
(116, NULL, 'STD-24-0108', 'ADM-2024-0108', 'M. Taha Hassan', '', 'M. atta ul Mohsin Nadeem', NULL, '2012-08-05', 'male', 'border', NULL, '2024-04-22', NULL, '0301-7227641', 'M. atta ul Mohsin Nadeem', NULL, 'Jhung', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private school', '33202-1463268-1', NULL, '0346-4668693', '0301-7227641', NULL, NULL, NULL, NULL, NULL, '6th'),
(117, NULL, 'STD-24-0109', 'ADM-2024-0109', 'Muhammad Fahad', '', 'Muhammad Hammid', NULL, '2013-06-12', 'male', 'border', NULL, '2024-04-23', NULL, '0300-6878593', 'Muhammad Hammid', NULL, 'Khaniwal', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Farmer', '36104-0458517-1', NULL, '0315-0500091', '0306-1777196', NULL, NULL, NULL, NULL, NULL, '6th'),
(118, NULL, 'STD-24-0110', 'ADM-2024-0110', 'Muhammad Yousuf', '', 'Muhammd Mustafa', NULL, '2013-07-08', 'male', 'border', NULL, '2024-04-24', NULL, '0300-4514703', 'Muhammd Mustafa', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'paintworker', '35201-2646930-7', NULL, '0321-4174240', '0300-4514703', NULL, NULL, NULL, NULL, NULL, '6th'),
(119, NULL, 'STD-24-0111', 'ADM-2024-0111', 'Abdul Hafeez', '', 'Syed Muhammad Sahal Ahmad', NULL, '2011-05-12', 'male', 'day_scholar', NULL, '2024-04-24', NULL, '0316-1964546', 'Syed Muhammad Sahal Ahmad', NULL, 'Noshera', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Imamt', '17201-9400086-5', NULL, '0345-9361280', '0316-1964546', NULL, NULL, NULL, NULL, NULL, '6th'),
(120, NULL, 'STD-24-0112', 'ADM-2024-0112', 'Mehboob Subhani', '', 'Syed M. Sahal Ahmad', NULL, '2012-05-02', 'male', 'day_scholar', NULL, '2024-04-24', NULL, '0316-1964546', 'Syed M. Sahal Ahmad', NULL, 'Noshera', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Imamt', '17201-9400086-5', NULL, '0345-9361280', '0316-1964546', NULL, NULL, NULL, NULL, NULL, '6th'),
(121, NULL, 'STD-24-0113', 'ADM-2024-0113', 'Ghous Muhammad', '', 'Faiz Muhammad', NULL, '2013-03-10', 'male', 'border', NULL, '2024-04-24', NULL, '0317-5345550', 'Faiz Muhammad', NULL, 'Manshera', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Teacher', '13503-4460056-9', NULL, '0316-5976370', '0317-5345550', NULL, NULL, NULL, NULL, NULL, '6th');
INSERT INTO `students` (`id`, `user_id`, `student_id`, `admission_no`, `first_name`, `last_name`, `father_name`, `cnic_bform`, `date_of_birth`, `gender`, `student_type`, `class_id`, `admission_date`, `phone`, `guardian_phone`, `guardian_name`, `address`, `city`, `previous_education`, `medical_info`, `status`, `photo`, `created_at`, `updated_at`, `mother_name`, `date_of_admission`, `date_of_leaving`, `reason_of_leaving`, `father_profession`, `father_cnic`, `admission_challan_no`, `guardian_phone_2`, `whatsapp_no`, `previous_result_card`, `total_marks`, `obtained_marks`, `class_status`, `previous_school_class`, `current_school_class`) VALUES
(122, NULL, 'STD-24-0114', 'ADM-2024-0114', 'Muhammad Haider', '', 'Muhammd Irshad', NULL, '2013-09-06', 'male', 'day_scholar', NULL, '2024-04-24', NULL, '03334-4003884', 'Muhammd Irshad', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Property Dealer', '35202-3047823-7', NULL, NULL, '0334-4003884', NULL, NULL, NULL, NULL, NULL, '6th'),
(123, NULL, 'STD-24-0115', 'ADM-2024-0115', 'Muhammd Hussain', '', 'Maqsood Akbar', NULL, '2013-10-09', 'male', 'border', NULL, '2024-04-24', NULL, '0346-2365037', 'Maqsood Akbar', NULL, 'Karachi', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '42201-0596863-7', NULL, '0312-026008', '0346-2365037', NULL, NULL, NULL, NULL, NULL, '6th'),
(124, NULL, 'STD-24-0116', 'ADM-2024-0116', 'Inam ul Hassan', '', 'Hafiz Saeed ul Hassan', NULL, '2014-02-07', 'male', 'border', NULL, '2024-04-24', NULL, '0304-9191507', 'Hafiz Saeed ul Hassan', NULL, 'Khushab', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Director Minhaj ul Quran USA', '36402-0207667-5', NULL, '0315-3812373', '0349-9191507', NULL, NULL, NULL, NULL, NULL, '6th'),
(125, NULL, 'STD-24-0117', 'ADM-2024-0117', 'Muhammad Shehroz', '', 'Muhammd Faisal', NULL, '2013-07-02', 'male', 'border', NULL, '2024-04-29', NULL, '0343-0662301', 'Muhammd Faisal', NULL, 'Kasur', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Teacher', '35104-0435873-9', NULL, '0325-4662205', '0343-0663201', NULL, NULL, NULL, NULL, NULL, '6th'),
(126, NULL, 'STD-24-0118', 'ADM-2024-0118', 'Ameer Ali', '', 'Farhan', NULL, '2012-05-20', 'male', 'day_scholar', NULL, '2024-04-29', NULL, '0315-0404066', 'Farhan', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Air Freshner Marketing', '35202-2040555-3', NULL, '0300-4759772', '0315-1416066', NULL, NULL, NULL, NULL, NULL, '6th'),
(127, NULL, 'STD-24-0119', 'ADM-2024-0119', 'M. Hasnat Ahmad', '', 'M. Zahoor Ahmad', NULL, '2013-03-13', 'male', 'border', NULL, '2024-04-29', NULL, '0307-1701589', 'M. Zahoor Ahmad', NULL, 'Jhung', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Labour', '33302-1461432-9', NULL, '0313-1707043', '0307-1701589', NULL, NULL, NULL, NULL, NULL, '6th'),
(128, NULL, 'STD-24-0120', 'ADM-2024-0120', 'Yousaf Amir Choudry', '', 'Amir Choudry', NULL, '2004-12-18', 'male', 'border', NULL, '2024-04-29', NULL, '0331-1475143', 'Amir Choudry', NULL, 'Fasialabad', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'USA', '33102-7307976-4', NULL, '0308-2416215', '12153071045', NULL, NULL, NULL, NULL, NULL, NULL),
(129, NULL, 'STD-24-0121', 'ADM-2024-0121', 'Muhammad Ayan', '', 'Khizer Hayat', NULL, '2013-12-03', 'male', 'border', NULL, '2024-04-29', NULL, '0343-7733430', 'Khizer Hayat', NULL, 'Fasialabad', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Farmer', '33101-1772276-9', NULL, '0304-7875165', '0343-7733430', NULL, NULL, NULL, NULL, NULL, '6th'),
(130, NULL, 'STD-24-0122', 'ADM-2024-0122', 'M. Irtaza Mustafa', '', 'Abid Hussain', NULL, '2012-01-07', 'male', 'border', NULL, '2024-04-29', NULL, '0300-6672495', 'Abid Hussain', NULL, 'faisalabad', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '33104-0201237-9', NULL, '0343-8777384', '0300-6672495', NULL, NULL, NULL, NULL, NULL, '6th'),
(131, NULL, 'STD-24-0123', 'ADM-2024-0123', 'M. Ra ziullah', '', 'Adnan Manoor', NULL, '2012-11-28', 'male', 'border', NULL, '2024-04-29', NULL, '0347-6967642', 'Adnan Manoor', NULL, 'Nankana', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Health Department', '35501-0125471-3', NULL, '0331-3438117', '0347-6967642', NULL, NULL, NULL, NULL, NULL, '6th'),
(132, NULL, 'STD-24-0124', 'ADM-2024-0124', 'Muhammad Yazdan', '', 'Khizer Hayat', NULL, '2011-09-14', 'male', 'border', NULL, '2024-04-29', NULL, '0343-7733430', 'Khizer Hayat', NULL, 'faisalabad', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Farmer', '33101-1772276-9', NULL, '0304-7875165', '0343-7733430', NULL, NULL, NULL, NULL, NULL, '6th'),
(133, NULL, 'STD-24-0125', 'ADM-2024-0125', 'M. Awais Ahmad', '', 'Muhammad Shafiq', NULL, '2012-09-12', 'male', 'day_scholar', NULL, '2024-04-29', NULL, '0344-4878805', 'Muhammad Shafiq', NULL, 'Okara', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '35302-8123742-3', NULL, '0320-4671517', '0320-4671517', NULL, NULL, NULL, NULL, NULL, '6th'),
(134, NULL, 'STD-24-0126', 'ADM-2024-0126', 'Ghulam Mustafa', '', 'Tanveer Ahmad', NULL, '2010-03-06', 'male', 'border', NULL, '2024-04-29', NULL, '0301-7282218', 'Tanveer Ahmad', NULL, 'Toba Tak Singh', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Shopkeeper', '33302-3281474-3', NULL, '0301-7282218', '0301-6073168', NULL, NULL, NULL, NULL, NULL, '9th'),
(135, NULL, 'STD-24-0127', 'ADM-2024-0127', 'Muhammad Irtisam Mansoor', '', 'Mansoor Islam', NULL, '2013-07-18', 'male', 'day_scholar', NULL, '2024-04-30', NULL, '0321-4459594', 'Mansoor Islam', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Electrition', '35202-1171323-7', NULL, '0321-4459594', '0321-4459594', NULL, NULL, NULL, NULL, NULL, '6th'),
(136, NULL, 'STD-24-0128', 'ADM-2024-0128', 'Muhammad Awais Baig', '', 'Zaheer Abbas Baig', NULL, '2014-01-03', 'male', 'border', NULL, '2024-04-30', NULL, '0333-8607802', 'Zaheer Abbas Baig', NULL, 'Sialkot', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Alominium Work', '34603-2442709-1', NULL, '0300-6178610', '0333-8607802', NULL, NULL, NULL, NULL, NULL, '6th'),
(137, NULL, 'STD-24-0129', 'ADM-2024-0129', 'Ali Akbar', '', 'Muhammad Saeed Akbar', NULL, '2013-09-01', 'male', 'day_scholar', NULL, '2024-04-30', NULL, '0303-4118669', 'Muhammad Saeed Akbar', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Driver (Islamabad)', '35202-2464352-5', NULL, '0303-4118669', '0323-8169902', NULL, NULL, NULL, NULL, NULL, '6th'),
(138, NULL, 'STD-24-0130', 'ADM-2024-0130', 'Abdul Rehman Aziz', '', 'Mehboob Anwer', NULL, '2013-06-09', 'male', 'day_scholar', NULL, '2024-04-30', NULL, '0304-8204063', 'Mehboob Anwer', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Construction Compony', '36103-1644713-9', NULL, '0321-7890953', '0304-8204063', NULL, NULL, NULL, NULL, NULL, '6th'),
(139, NULL, 'STD-24-0131', 'ADM-2024-0131', 'Ali Shahbaz Rabbani', '', 'Muhammad Shahbaz Rabbani', NULL, '2012-02-20', 'male', 'day_scholar', NULL, '2024-04-30', NULL, '0300-4772138', 'Muhammad Shahbaz Rabbani', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Imamt', '35202-2277468-3', NULL, '0300-4306291', '0300-4772138', NULL, NULL, NULL, NULL, NULL, '6th'),
(140, NULL, 'STD-24-0132', 'ADM-2024-0132', 'Muhammad Najam Mustafa', '', 'Muneer Ahmad', NULL, '2013-12-10', 'male', 'day_scholar', NULL, '2024-05-01', NULL, '0314-44183264', 'Muneer Ahmad', NULL, 'Khaniwal', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Electrition', '36104-9745162-7', NULL, '0303-6738161', '03414-4183264', NULL, NULL, NULL, NULL, NULL, '6th'),
(141, NULL, 'STD-24-0133', 'ADM-2024-0133', 'Muhammad Ahmad Jamshaid', '', 'Waheed Ahmad', NULL, '2011-10-25', 'male', 'border', NULL, '2024-05-01', NULL, '0301-5855851', 'Waheed Ahmad', NULL, 'Jehlum', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Property Dealer', '37303-4171422-7', NULL, NULL, '0301-5855851', NULL, NULL, NULL, NULL, NULL, '6th'),
(142, NULL, 'STD-24-0134', 'ADM-2024-0134', 'Mian Muhammd Shabeer', '', 'Muhammad Younus', NULL, '2012-09-15', 'male', 'day_scholar', NULL, '2024-05-01', NULL, '0322-8000302', 'Muhammad Younus', NULL, 'Okara', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Property Dealer', '35202-9469482-9', NULL, '0300-2446115', '0322-8000302', NULL, NULL, NULL, NULL, NULL, '6th'),
(143, NULL, 'STD-24-0135', 'ADM-2024-0135', 'Muhammd Hassan Talha Kleer', '', 'Usman Ali Kleer', NULL, '2013-06-15', 'male', 'day_scholar', NULL, '2024-05-01', NULL, '0302-6434652', 'Usman Ali Kleer', NULL, 'Gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Farmer', '34104-2201337-7', NULL, '0335-4265938', '0302-6434652', NULL, NULL, NULL, NULL, NULL, '6th'),
(144, NULL, 'STD-24-0136', 'ADM-2024-0136', 'Chaman Abbas', '', 'Muhammad Nawaz', NULL, '2013-01-05', 'male', 'border', NULL, '2024-05-01', NULL, '0346-9429054', 'Muhammad Nawaz', NULL, 'Toba Tak Singh', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Agriculture', '33302-5337320-7', NULL, '0305-7246404', '0305-7246404', NULL, NULL, NULL, NULL, NULL, '6th'),
(145, NULL, 'STD-24-0137', 'ADM-2024-0137', 'Muhammad Abbad Ali', '', 'Muhammad Qasim Ali', NULL, '2012-08-10', 'male', 'border', NULL, '2024-05-01', NULL, '0300-6862919', 'Muhammad Qasim Ali', NULL, 'Jhung', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Homeopthic Doctor', '33203-1431119-5', NULL, '0321-6862919', '0300-6862919', NULL, NULL, NULL, NULL, NULL, '6th'),
(146, NULL, 'STD-24-0138', 'ADM-2024-0138', 'Abdul Hanan', '', 'Muhammad Naeem Bhatti', NULL, '2011-09-30', 'male', 'border', NULL, '2024-05-01', NULL, '0308-3723878', 'Muhammad Naeem Bhatti', NULL, 'Bahawalpur', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'AC Tecnition Saudia', '31202-0252248-9', NULL, '598290808', '0300-8990164', NULL, NULL, NULL, NULL, NULL, '7th'),
(147, NULL, 'STD-24-0139', 'ADM-2024-0139', 'Muhammad Burhan', '', 'Muhammad Naeem Bhatti', NULL, '2010-05-20', 'male', 'border', NULL, '2024-05-01', NULL, '0308-3723878', 'Muhammad Naeem Bhatti', NULL, 'Bahawalpur', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'AC Tecnition Saudia', '31202-0252248-9', NULL, '0300-8990164', '0300-8990164', NULL, NULL, NULL, NULL, NULL, '8th'),
(148, NULL, 'STD-24-0140', 'ADM-2024-0140', 'Muhammad Hammad', '', 'Muhammad Ameer', NULL, '2009-06-13', 'male', 'border', NULL, '2024-05-01', NULL, '0305-9641004', 'Muhammad Ameer', NULL, 'Bahawalpur', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Driver (Islamabad)', '31202-2884622-1', NULL, '0307-7362042', '0305-9641004', NULL, NULL, NULL, NULL, NULL, '8th'),
(149, NULL, 'STD-24-0141', 'ADM-2024-0141', 'Muhammad Muneeb ur Rehman', '', 'Mzahar ul Islam', NULL, '2013-08-01', 'male', 'border', NULL, '2024-05-01', NULL, '0300-4603546', 'Mzahar ul Islam', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Building Material Shop', '35201-2498334-3', NULL, '0300-2920123', '0300-4603546', NULL, NULL, NULL, NULL, NULL, '6th'),
(150, NULL, 'STD-24-0142', 'ADM-2024-0142', 'Atta ul Mustafa Khan', '', 'Ahmad Saeed', NULL, '2012-12-12', 'male', 'border', NULL, '2024-05-01', NULL, '0341-8614548', 'Ahmad Saeed', NULL, 'Bhakhar', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'veternity Doctor', '38101-0588408-7', NULL, '0300-77781198', '0341-8614548', NULL, NULL, NULL, NULL, NULL, '6th'),
(151, NULL, 'STD-24-0143', 'ADM-2024-0143', 'Muhammad Waleed', '', 'Muhammad ilyas', NULL, '2010-11-12', 'male', 'border', NULL, '2024-05-01', NULL, '0345-1945399', 'Muhammad ilyas', NULL, 'Manshera', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, NULL, '13504-0339245-3', NULL, '0314-5078458', '0345-1945399', NULL, NULL, NULL, NULL, NULL, '6th'),
(152, NULL, 'STD-24-0144', 'ADM-2024-0144', 'Hammad ul Hassan', '', 'Muhammad Ishfaq', NULL, '2013-09-13', 'male', 'border', NULL, '2024-05-01', NULL, '0345-1945399', 'Muhammad Ishfaq', NULL, 'Manshera', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'R. Army Person', '13504-2253036-1', NULL, '0314-5078458', '0345-1945399', NULL, NULL, NULL, NULL, NULL, '6th'),
(153, NULL, 'STD-24-0145', 'ADM-2024-0145', 'Muhammad Danish', '', 'Asghar Ali', NULL, '2015-10-29', 'male', 'border', NULL, '2024-05-01', NULL, '0300-6434413', 'Asghar Ali', NULL, 'Gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Insurance Compony', '34104-6434413-7', NULL, '0348-4451606', '0300-6434413', NULL, NULL, NULL, NULL, NULL, '6th'),
(154, NULL, 'STD-24-0146', 'ADM-2024-0146', 'Muhammad Ali Raza', '', 'Hafiz Shabeer Ahmad', NULL, '2011-02-19', 'male', 'border', NULL, '2024-05-01', NULL, '0321-7754748', 'Hafiz Shabeer Ahmad', NULL, 'Mandi Bahauddin', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Nadra Office', '34402-9577066-3', NULL, '0344-6545608', '0321-7754748', NULL, NULL, NULL, NULL, NULL, '7th'),
(155, NULL, 'STD-24-0147', 'ADM-2024-0147', 'Muhammad Bilal Tahir', '', 'Liaqat Ali Amjad', NULL, '2013-05-09', 'male', 'border', NULL, '2024-05-02', NULL, '0304-6859167', 'Liaqat Ali Amjad', NULL, 'Pak Patan', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'PAF', '36402-5679154-5', NULL, '0304-0459737', '0304-6859167', NULL, NULL, NULL, NULL, NULL, '6th'),
(156, NULL, 'STD-24-0148', 'ADM-2024-0148', 'Nalain Ahmad', '', 'Muhammad Ashraf', NULL, '2014-01-17', 'male', 'border', NULL, '2024-05-02', NULL, '0337-6226709', 'Muhammad Ashraf', NULL, 'Jhung', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Agriculture', '33202-2200429-7', NULL, '0331-8789774', '0317-17633996', NULL, NULL, NULL, NULL, NULL, '6th'),
(157, NULL, 'STD-24-0149', 'ADM-2024-0149', 'Abdullah Qasim', '', 'Muhammad Qasim', NULL, '2013-12-11', 'male', 'border', NULL, '2024-05-02', NULL, '0333-6435178', 'Muhammad Qasim', NULL, 'Rajanpur', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Carpenter', '32403-8860652-3', NULL, '0333-6435178', '0310-6195178', NULL, NULL, NULL, NULL, NULL, '6th'),
(158, NULL, 'STD-24-0150', 'ADM-2024-0150', 'Muhammad Suleman', '', 'Muhammad Naeem', NULL, '2013-11-24', 'male', 'day_scholar', NULL, '2024-05-02', NULL, '0324-4007824', 'Muhammad Naeem', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Ambreoidery', '35201-8840305-5', NULL, '0300-4553465', '0324-4007824', NULL, NULL, NULL, NULL, NULL, '5th'),
(159, NULL, 'STD-24-0151', 'ADM-2024-0151', 'Muhammad Amar Sultan', '', 'Muhammad Tahir Rafiq', NULL, '2013-12-10', 'male', 'day_scholar', NULL, '2024-05-02', NULL, '0331-4915892', 'Muhammad Tahir Rafiq', NULL, 'faisalabad', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Nizam ul Madaris (Account Office)', '33105-3485042-5', NULL, '0301-5573163', '0331-4915892', NULL, NULL, NULL, NULL, NULL, '6th'),
(160, NULL, 'STD-24-0152', 'ADM-2024-0152', 'Faiz Mateen', '', 'Muhammad Mateen', NULL, '2013-06-22', 'male', 'day_scholar', NULL, '2024-05-02', NULL, '0324-4575471', 'Muhammad Mateen', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Pepsi Factory Worker', '35200-1463634-9', NULL, '0323-6264970', '0323-6264970', NULL, NULL, NULL, NULL, NULL, '6th'),
(161, NULL, 'STD-24-0153', 'ADM-2024-0153', 'Sardar Muhammad Abdullah', '', 'Muhammad Imran Wiki', NULL, '2015-05-28', 'male', 'border', NULL, '2024-05-03', NULL, '0324-4323651', 'Muhammad Imran Wiki', NULL, 'Kasur', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '35102-0582340-5', NULL, '0300-4266402', '0324-4323651', NULL, NULL, NULL, NULL, NULL, '6th'),
(162, NULL, 'STD-24-0154', 'ADM-2024-0154', 'Mujtaba Ahmad Khan', '', 'Shereen Khan', NULL, '2014-09-23', 'male', 'border', NULL, '2024-05-04', NULL, '0346-7894448', 'Shereen Khan', NULL, 'Dera Ismail Khan', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'R. Government Officer', '12103-1496267-7', NULL, '0335-6170422', '0335-6170422', NULL, NULL, NULL, NULL, NULL, '6th'),
(163, NULL, 'STD-24-0155', 'ADM-2024-0155', 'Muhammad Zaid', '', 'Muhammad Tahir Iqbal', NULL, '2012-06-06', 'male', 'day_scholar', NULL, '2024-05-06', NULL, '0300-4526526', 'Muhammad Tahir Iqbal', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '35202-8539485-9', NULL, '0310-9997010', '0300-4526526', NULL, NULL, NULL, NULL, NULL, '6th'),
(164, NULL, 'STD-24-0156', 'ADM-2024-0156', 'Muhammad Mian Malik', '', 'Salman Khalid', NULL, '2013-07-11', 'male', 'border', NULL, '2024-05-06', NULL, '0305-4800480', 'Salman Khalid', NULL, 'Bahawalpur', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Health Department', '31201-8809189-3', NULL, '0300-9684159', '0302-8121111', NULL, NULL, NULL, NULL, NULL, '7th'),
(165, NULL, 'STD-24-0157', 'ADM-2024-0157', 'Muhammad Hussain Fareed', '', 'Ghulam Fareed', NULL, '2012-12-17', 'male', 'day_scholar', NULL, '2024-05-06', NULL, '0321-4354152', 'Ghulam Fareed', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '35202-4718826-1', NULL, '0313-6407551', '0321-4354152', NULL, NULL, NULL, NULL, NULL, '6th'),
(166, NULL, 'STD-24-0158', 'ADM-2024-0158', 'Muhammad Ali Raza', '', 'Hafiz Ahsan Ali', NULL, '2013-02-25', 'male', 'border', NULL, '2024-05-06', NULL, '0310-3394311', 'Hafiz Ahsan Ali', NULL, 'Bhakhar', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Imamt', '38101-2114450-5', NULL, '0324-3506402', '0310-3394311', NULL, NULL, NULL, NULL, NULL, '6th'),
(167, NULL, 'STD-24-0159', 'ADM-2024-0159', 'Muhammad Abdullah', '', 'Khizer Hayat', NULL, '2013-01-25', 'male', 'border', NULL, '2024-05-06', NULL, '0341-1303022', 'Khizer Hayat', NULL, 'Mandi Bahauddin', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Agriculture', '34403-1889902-9', NULL, '0349-7443828', '0341-1303022', NULL, NULL, NULL, NULL, NULL, '6th'),
(168, NULL, 'STD-24-0160', 'ADM-2024-0160', 'Muhammad Aziz Amjad', '', 'Amjad Nawaz', NULL, '2012-01-03', 'male', 'border', NULL, '2024-05-06', NULL, '0345-5658518', 'Amjad Nawaz', NULL, 'Jehlum', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Farmer', '37301-7167519-7', NULL, '0340-0581175', '0345-5658518', NULL, NULL, NULL, NULL, NULL, '6th'),
(169, NULL, 'STD-24-0161', 'ADM-2024-0161', 'Abdul Reaheem Adnan', '', 'Muhammad Adnan', NULL, '2011-11-17', 'male', 'border', NULL, '2024-05-06', NULL, '0300-4427839', 'Muhammad Adnan', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job (kuwait)', '35202-3918074-5', NULL, '0322-4719024', '0300-4427839', NULL, NULL, NULL, NULL, NULL, '6th'),
(170, NULL, 'STD-24-0162', 'ADM-2024-0162', 'Arslan Mustafa', '', 'Aga Faisal Rehman', NULL, '2014-02-20', 'male', 'day_scholar', NULL, '2024-05-07', NULL, '0321-4457040', 'Aga Faisal Rehman', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'printer Repairing', '35202-2680576-7', NULL, '0300-0418884', '0321-4457040', NULL, NULL, NULL, NULL, NULL, '6th'),
(171, NULL, 'STD-24-0163', 'ADM-2024-0163', 'Muhammad Arshaman Ali', '', 'Muhammad Amjad Ali', NULL, '2012-09-07', 'male', 'day_scholar', NULL, '2024-05-07', NULL, '0304-4109109', 'Muhammad Amjad Ali', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Factory\'s SalesMan', '36402-0748301-9', NULL, '0321-4811606', '0304-4109109', NULL, NULL, NULL, NULL, NULL, '6th'),
(172, NULL, 'STD-24-0164', 'ADM-2024-0164', 'Shahryar', '', 'Umar Ali', NULL, '2010-09-01', 'male', 'day_scholar', NULL, '2024-05-07', NULL, '0301-5533355', 'Umar Ali', NULL, 'Khaniwal', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '36104-6955570-5', NULL, '0306-4348836', '0301-5533355', NULL, NULL, NULL, NULL, NULL, '6th'),
(173, NULL, 'STD-24-0165', 'ADM-2024-0165', 'Muhammad Ahmad', '', 'Muhammad Ashraf', NULL, '2012-02-29', 'male', 'border', NULL, '2024-05-07', NULL, '0300-4823544', 'Muhammad Ashraf', NULL, 'Sheikupura', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Police Department', '35403-1233083-5', NULL, '0300-4823544', '0302-9723268', NULL, NULL, NULL, NULL, NULL, '6th'),
(174, NULL, 'STD-24-0166', 'ADM-2024-0166', 'Muhammad Arham', '', 'abdul Tawab', NULL, '2014-01-03', 'male', 'day_scholar', NULL, '2024-05-08', NULL, '0301-4687341', 'abdul Tawab', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'SDO Wapda', '35200-1466610-5', NULL, '0343-4479344', '0301-4687341', NULL, NULL, NULL, NULL, NULL, '6th'),
(175, NULL, 'STD-24-0167', 'ADM-2024-0167', 'Ali Hassan Abbasi', '', 'Mohsin Shahzad', NULL, '2011-12-05', 'male', 'border', NULL, '2024-05-08', NULL, '0312-5390036', 'Mohsin Shahzad', NULL, 'Rawalpindi', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Paintshop', '37404-9972672-1', NULL, '0312-5364024', '0321-5502717', NULL, NULL, NULL, NULL, NULL, '6th'),
(176, NULL, 'STD-24-0168', 'ADM-2024-0168', 'Ali Shan', '', 'Ahmad Yaar', NULL, '2014-03-01', 'male', 'border', NULL, '2024-05-08', NULL, '0305-5603892', 'Ahmad Yaar', NULL, 'Khaniwal', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'R. Army Person', '36103-6496396-5', NULL, '0307-8757416', NULL, NULL, NULL, NULL, NULL, NULL, '6th'),
(177, NULL, 'STD-24-0169', 'ADM-2024-0169', 'Syed Hassab Musana Shah Gillani', '', 'Syed Noor Ali Shah Gillani', NULL, '2014-06-02', 'male', 'day_scholar', NULL, '2024-05-08', NULL, '0307-4318211', 'Syed Noor Ali Shah Gillani', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '35202-7983473-9', NULL, '0322-4931220', '0307-4318211', NULL, NULL, NULL, NULL, NULL, '6th'),
(178, NULL, 'STD-24-0170', 'ADM-2024-0170', 'Abdul Moeez', '', 'Parvaiz Ahmad', NULL, '2011-11-11', 'male', 'day_scholar', NULL, '2024-05-09', NULL, '0322-1515615', 'Parvaiz Ahmad', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Labour', '35202-0250394-3', NULL, '0324-1046441', '0309-8800829', NULL, NULL, NULL, NULL, NULL, '6th'),
(179, NULL, 'STD-24-0171', 'ADM-2024-0171', 'Ali Hassan Abbasi', '', 'Muhammad Khurram', NULL, '2011-11-30', 'male', 'day_scholar', NULL, '2024-05-10', NULL, '0321-8797206', 'Muhammad Khurram', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Private job', '35202-2817050-9', NULL, NULL, '0324-4608469', NULL, NULL, NULL, NULL, NULL, '6th'),
(180, NULL, 'STD-24-0172', 'ADM-2024-0172', 'Arham Ahmad Khan', '', 'Wahid ullah', NULL, '2011-02-10', 'male', 'day_scholar', NULL, '2024-05-11', NULL, '0322-4269734', 'Wahid ullah', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '35201-0546040-1', NULL, '0323-4865487', '0322-4261734', NULL, NULL, NULL, NULL, NULL, '6th'),
(181, NULL, 'STD-24-0173', 'ADM-2024-0173', 'Muhammad Ahsaan Imdad', '', 'Muhammad Indaad Hussain Shah', NULL, '2014-03-25', 'male', 'border', NULL, '2024-05-13', NULL, '0301-4745768', 'Muhammad Indaad Hussain Shah', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Teacher', '35202-2839339-7', NULL, '0344-4745760', '0301-4745768', NULL, NULL, NULL, NULL, NULL, '6th'),
(182, NULL, 'STD-24-0174', 'ADM-2024-0174', 'Uzair Ali', '', 'Muhammad Akram', NULL, '2008-02-17', 'male', 'border', NULL, '2024-05-14', NULL, '0324-8771601', 'Muhammad Akram', NULL, 'Gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'R. Army Person', '34104-8528959-7', NULL, '0326-0630740', '0324-8771601', NULL, NULL, NULL, NULL, NULL, '9th'),
(183, NULL, 'STD-24-0175', 'ADM-2024-0175', 'Muhammad Ahmad', '', 'Nasir shahzad', NULL, '2007-05-25', 'male', 'border', NULL, '2024-05-14', NULL, '0305-8618198', 'Nasir shahzad', NULL, 'Sialkot', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '34601-5330772-9', NULL, '0320-4090836', '0305-8618198', NULL, NULL, NULL, NULL, NULL, '6th'),
(184, NULL, 'STD-24-0176', 'ADM-2024-0176', 'Saad Ahmad', '', 'Muhammad Arshad', NULL, '2013-09-11', 'male', 'day_scholar', NULL, '2024-05-15', NULL, '0321-4447377', 'Muhammad Arshad', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Medical Billing', '36402-0776870-1', NULL, '0313-8686757', '0321-4447377', NULL, NULL, NULL, NULL, NULL, '6th'),
(185, NULL, 'STD-24-0177', 'ADM-2024-0177', 'Ammad Naeem', '', 'Ch Naeem Rafique', NULL, '2014-08-14', 'male', 'day_scholar', NULL, '2024-05-15', NULL, '0300-4120441', 'Ch Naeem Rafique', NULL, 'Kasur', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, NULL, '35103-1314640-1', NULL, '0323-1638663', '0300-4120441', NULL, NULL, NULL, NULL, NULL, '6th'),
(186, NULL, 'STD-24-0178', 'ADM-2024-0178', 'Muhammad Farhan Ahmad', '', 'Muhammad Adnan', NULL, '2012-05-16', 'male', 'border', NULL, '2024-05-16', NULL, '0300-4427839', 'Muhammad Adnan', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'privatejob (kuwait)', '35202-3918074-5', NULL, '0322-4719024', '0300-4427839', NULL, NULL, NULL, NULL, NULL, '6th'),
(187, NULL, 'STD-24-0179', 'ADM-2024-0179', 'Muhammad Abdul Rehman Saleem', '', 'Muhammad Saleem', NULL, '2013-06-21', 'male', 'border', NULL, '2024-05-20', NULL, '0300-6132708', 'Muhammad Saleem', NULL, 'faisalabad', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Farmer', '33104-4294074-3', NULL, '0302-6439562', '0300-6132708', NULL, NULL, NULL, NULL, NULL, '6th'),
(188, NULL, 'STD-24-0180', 'ADM-2024-0180', 'Muhammad Ahmad Ibrahim', '', 'Masood Asghar', NULL, '2014-11-10', 'male', 'day_scholar', NULL, '2024-05-21', NULL, '0312-8056688', 'Masood Asghar', NULL, 'Dera Ghazi Khan', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Police Department', '32102-9605352-7', NULL, '0312-2220008', '0312-8056688', NULL, NULL, NULL, NULL, NULL, '6th'),
(189, NULL, 'STD-24-0181', 'ADM-2024-0181', 'Muhammad Subhan Ali', '', 'Muhammad Ameen', NULL, '2012-01-20', 'male', 'border', NULL, '2024-05-22', NULL, '0344-5557436', 'Muhammad Ameen', NULL, 'Jehlum', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Grocery Store', '37301-2292625-7', NULL, '0334-8849574', '0344-5557436', NULL, NULL, NULL, NULL, NULL, '6th'),
(190, NULL, 'STD-24-0182', 'ADM-2024-0182', 'Muhammad Ubaidullah roomi', '', 'Roomi Sagheer Ahmad', NULL, '2012-02-13', 'male', 'border', NULL, '2024-05-22', NULL, '0300-4205528', 'Roomi Sagheer Ahmad', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Gourment Contractor', '35201-1202120-3', NULL, '0333-4213767', '0300-4205528', NULL, NULL, NULL, NULL, NULL, '6th'),
(191, NULL, 'STD-24-0183', 'ADM-2024-0183', 'Hammad Asghar', '', 'Asghar Ali', NULL, '2012-08-25', 'male', 'day_scholar', NULL, '2024-05-23', NULL, '0333-4990935', 'Asghar Ali', NULL, 'Okara', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Farmer', '35301-1888175-5', NULL, '0320-0443541', '0333-4990935', NULL, NULL, NULL, NULL, NULL, '6th'),
(192, NULL, 'STD-24-0184', 'ADM-2024-0184', 'Haseeb Ahmad', '', 'Ijaz Ahmad', NULL, '2009-08-13', 'male', 'border', NULL, '2024-05-25', NULL, '0307-6164158', 'Ijaz Ahmad', NULL, 'NaroWal', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Army', '34501-7515203-1', NULL, '0304-8559028', '0304-8559028', NULL, NULL, NULL, NULL, NULL, '9th'),
(193, NULL, 'STD-24-0185', 'ADM-2024-0185', 'Muhammad Ameer Ahmad Hashmi', '', 'Muhammad adnan Ahmad Hashmi', NULL, '2013-09-28', 'male', 'day_scholar', NULL, '2024-05-27', NULL, '0309-0222218', 'Muhammad adnan Ahmad Hashmi', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '35202-1934366-6', NULL, '0316-1711049', '0309-0222218', NULL, NULL, NULL, NULL, NULL, '6th'),
(194, NULL, 'STD-24-0186', 'ADM-2024-0186', 'Ali Haider', '', 'Nadeem Iqbal', NULL, '2011-10-09', 'male', 'border', NULL, '2024-05-30', NULL, '0300-7451513', 'Nadeem Iqbal', NULL, 'Gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Harbalist Shop', '61101-1985264-5', NULL, '0346-6020509', '0317-3049249', NULL, NULL, NULL, NULL, NULL, '6th'),
(195, NULL, 'STD-24-0187', 'ADM-2024-0187', 'Muhammad ALI Hamza', '', 'Nadeem Iqbal', NULL, '2015-03-26', 'male', 'border', NULL, '2024-05-30', NULL, '0300-7451513', 'Nadeem Iqbal', NULL, 'Gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Harbalist Shop', '61101-1985264-5', NULL, '0346-6020509', '0317-3049249', NULL, NULL, NULL, NULL, NULL, '6th'),
(196, NULL, 'STD-24-0188', 'ADM-2024-0188', 'Muhammad Hammad Saleem', '', 'Saleem Abbas', NULL, '2010-06-29', 'male', 'day_scholar', NULL, '2024-05-30', NULL, '0301-4090160', 'Saleem Abbas', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Broker', '33302-0705256-1', NULL, '0347-496100', '0301-4090160', NULL, NULL, NULL, NULL, NULL, '9th'),
(197, NULL, 'STD-24-0189', 'ADM-2024-0189', 'Muhammad Ahmad', '', 'Qamaq Ali', NULL, '2014-07-03', 'male', 'day_scholar', NULL, '2024-06-01', NULL, '0332-8109959', 'Qamaq Ali', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Education University Admin Officer', '34001-6943408-0', NULL, '0319-1973036', '0332-8109959', NULL, NULL, NULL, NULL, NULL, '6th'),
(198, NULL, 'STD-24-0190', 'ADM-2024-0190', 'Muhammad Hussnain Ali', '', 'Muhammad Kaleem ul allah', NULL, '2013-02-11', 'male', 'day_scholar', NULL, '2024-06-03', NULL, '0302-7383363', 'Muhammad Kaleem ul allah', NULL, 'Okara', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '35302-1985046-9', NULL, '0303-7084864', '0302-7383363', NULL, NULL, NULL, NULL, NULL, '6th'),
(199, NULL, 'STD-24-0191', 'ADM-2024-0191', 'Muhammad Abu Bakr', '', 'Hafiz Mureed Hussain', NULL, '2011-08-17', 'male', 'day_scholar', NULL, '2024-06-04', NULL, '0300-4104163', 'Hafiz Mureed Hussain', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Pharmacy', '38401-0383644-3', NULL, '03325-0492175', '0300-4104163', NULL, NULL, NULL, NULL, NULL, '6th'),
(200, NULL, 'STD-24-0192', 'ADM-2024-0192', 'Muhammad Abdullah', '', 'Muhammad Abid', NULL, '2012-02-27', 'male', 'border', NULL, '2024-06-04', NULL, '0335-3619021', 'Muhammad Abid', NULL, 'NaroWal', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, NULL, '34501-1929425-3', NULL, '0331-9665457', '0335-3619021', NULL, NULL, NULL, NULL, NULL, '6th'),
(201, NULL, 'STD-24-0193', 'ADM-2024-0193', 'Muhammad Amir', '', 'Abdul Khaliq', NULL, '2012-01-01', 'male', 'border', NULL, '2024-06-05', NULL, '0347-6534158', 'Abdul Khaliq', NULL, 'Okara', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Farmer', '35301-2021293-3', NULL, NULL, '0344-6777368', NULL, NULL, NULL, NULL, NULL, '6th'),
(202, NULL, 'STD-24-0194', 'ADM-2024-0194', 'Malik Shehroz Nawaz', '', 'Rab Nawaz', NULL, '2014-06-19', 'male', 'border', NULL, '2024-06-05', NULL, '0344-6777368', 'Rab Nawaz', NULL, 'Okara', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, NULL, '35301-3488664-1', NULL, '0345-7412012', '0344-6777368', NULL, NULL, NULL, NULL, NULL, '6th'),
(203, NULL, 'STD-24-0195', 'ADM-2024-0195', 'Bilal Ahmad', '', 'Ghulam Rasool', NULL, '2008-03-23', 'male', 'day_scholar', NULL, '2024-06-05', NULL, '0327-9401381', 'Ghulam Rasool', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Carpenter', '35202-8128511-5', NULL, NULL, '0327-9401381', NULL, NULL, NULL, NULL, NULL, '6th'),
(204, NULL, 'STD-24-0196', 'ADM-2024-0196', 'Muhammad Junaid Ahsan', '', 'Muhammad Ahsan', NULL, '2010-10-20', 'male', 'border', NULL, '2024-06-05', NULL, '0345-4296474', 'Muhammad Ahsan', NULL, 'Mandi Bahauddin', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'privatejob Dubai', '34403-1217121-9', NULL, '0321-6255114', '0345-4296474', NULL, NULL, NULL, NULL, NULL, '7th'),
(205, NULL, 'STD-24-0197', 'ADM-2024-0197', 'Jawad Mustafa', '', 'Muhammad Jawed', NULL, '2009-11-22', 'male', 'border', NULL, '2024-06-24', NULL, '0307-1478875', 'Muhammad Jawed', NULL, 'Mianwali', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'PAF', '33302-1168861-5', NULL, '0301-9066907', '0333-3960626', NULL, NULL, NULL, NULL, NULL, '7th'),
(206, NULL, 'STD-24-0198', 'ADM-2024-0198', 'Muhammad Adeel Rustam', '', 'Muhammad Rustam Noor', NULL, '2011-03-26', 'male', 'day_scholar', NULL, '2024-06-26', NULL, '0300-4826348', 'Muhammad Rustam Noor', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Driver', '36402-3992165-1', NULL, '0313-4826348', '0300-4826348', NULL, NULL, NULL, NULL, NULL, '6th'),
(207, NULL, 'STD-24-0199', 'ADM-2024-0199', 'Muhammad Huzaifa', '', 'Muhammad Saleem', NULL, '2012-10-08', 'male', 'border', NULL, '2024-06-26', NULL, '0333-3496313', 'Muhammad Saleem', NULL, 'Bhakhar', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Pharmacy', '38101-1939151-1', NULL, '0345-4924224', '0333-3496313', NULL, NULL, NULL, NULL, NULL, '6th'),
(208, NULL, 'STD-24-0200', 'ADM-2024-0200', 'Muhammad Ahmad', '', 'Muhammad Nawaz', NULL, '2011-11-02', 'male', 'day_scholar', NULL, '2024-06-27', NULL, '0321-4176124', 'Muhammad Nawaz', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Poultry FARM', '35202-5468882-7', NULL, '0321-4176124', '0321-9478122', NULL, NULL, NULL, NULL, NULL, '7th'),
(209, NULL, 'STD-24-0201', 'ADM-2024-0201', 'Ali Haider', '', 'Sajjad Hussain', NULL, '2013-03-13', 'male', 'border', NULL, '2024-06-29', NULL, '0345-6914842', 'Sajjad Hussain', NULL, 'Gujrat', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Civil Worker', '34202-2039812-9', NULL, '0300-5447350', '0345-6914842', NULL, NULL, NULL, NULL, NULL, '6th'),
(210, NULL, 'STD-24-0202', 'ADM-2024-0202', 'Muhammd Ahad', '', 'Mazhar Abbas Bhatti', NULL, '2012-12-03', 'male', 'border', NULL, '2024-07-01', NULL, '0346-8690468', 'Mazhar Abbas Bhatti', NULL, 'Hafizabad', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Agriculture', '34302-9839591-', NULL, '0344-6569084', '0346-8690168', NULL, NULL, NULL, NULL, NULL, '6th'),
(211, NULL, 'STD-24-0203', 'ADM-2024-0203', 'Abdul Rehman', '', 'Sabir Ali', NULL, '2012-07-01', 'male', 'border', NULL, '2024-07-01', NULL, '0345-4737853', 'Sabir Ali', NULL, 'Okara', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'درجہ چہارم', '35301-0461912-9', NULL, '0306-4517210', '0345-4737853', NULL, NULL, NULL, NULL, NULL, '6th'),
(212, NULL, 'STD-24-0204', 'ADM-2024-0204', 'Muhammad Mateen', '', 'Shafqat Ali', NULL, '2011-11-27', 'male', 'border', NULL, '2024-07-01', NULL, '0307-4418600', 'Shafqat Ali', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Civil Worker', '35201-1971210-1', NULL, '0300-4477282', '0307-4418600', NULL, NULL, NULL, NULL, NULL, '6th'),
(213, NULL, 'STD-24-0205', 'ADM-2024-0205', 'Ali Abbas', '', 'Muhammad Irshad', NULL, '2012-03-17', 'male', 'day_scholar', NULL, '2024-07-02', NULL, '0321-4851623', 'Muhammad Irshad', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Homeopathic Pharmacy', '35202-3998432-3', NULL, '0322-8074776', '0322-8074776', NULL, NULL, NULL, NULL, NULL, '6th'),
(214, NULL, 'STD-24-0206', 'ADM-2024-0206', 'Rao Muhammad Awais', '', 'Ali Noor', NULL, '2011-11-11', 'male', 'day_scholar', NULL, '2024-07-02', NULL, '0333-4599218', 'Ali Noor', NULL, 'Khaniwal', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '36102-2243098-1', NULL, '0336-1188800', '0333-4599218', NULL, NULL, NULL, NULL, NULL, '7th'),
(215, NULL, 'STD-24-0207', 'ADM-2024-0207', 'Muhammad Soban', '', 'Muhammad Shahzad', NULL, '2011-04-13', 'male', 'day_scholar', NULL, '2024-07-02', NULL, '0316-4587049', 'Muhammad Shahzad', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Garments Cutting', '35200-1500224-7', NULL, '0306-6806228', '0316-4587049', NULL, NULL, NULL, NULL, NULL, '6th'),
(216, NULL, 'STD-24-0208', 'ADM-2024-0208', 'Abdul Hadi', '', 'Muhammad Ali', NULL, '2013-07-02', 'male', 'border', NULL, '2024-07-03', NULL, '0315-2159946', 'Muhammad Ali', NULL, 'Attack', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'R. Army Person (PAF)', '35200-4169661-1', NULL, '0315-2610859', '0315-2159946', NULL, NULL, NULL, NULL, NULL, '6th'),
(217, NULL, 'STD-24-0209', 'ADM-2024-0209', 'Muhammad Yousaf', '', 'Abdul Razzaq', NULL, '2010-10-22', 'male', 'day_scholar', NULL, '2024-07-03', NULL, '0324-4100016', 'Abdul Razzaq', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '35202-2271783-3', NULL, '0301-0770237', '0324-4100016', NULL, NULL, NULL, NULL, NULL, '6th'),
(218, NULL, 'STD-24-0210', 'ADM-2024-0210', 'Abdul Hadi', '', 'Irfan Ahmad', NULL, '2012-04-21', 'male', 'day_scholar', NULL, '2024-07-04', NULL, '0328-8878191', 'Irfan Ahmad', NULL, 'Kasur', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Kriana Merchant', '35102-7219015-5', NULL, '0322-8074776', '0304-4669376', NULL, NULL, NULL, NULL, NULL, '6th'),
(219, NULL, 'STD-24-0211', 'ADM-2024-0211', 'Muhammad Abdullah', '', 'Muhammad Riaz', NULL, '2008-05-24', 'male', 'border', NULL, '2024-07-04', NULL, '0301-6239703', 'Muhammad Riaz', NULL, 'Gujrat', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Farmer', '34201-9573737-1', NULL, '0301-6239703-', '0325-6122649', NULL, NULL, NULL, NULL, NULL, '6th'),
(220, NULL, 'STD-24-0212', 'ADM-2024-0212', 'Muhammad Tahir', '', 'Mureed Hussain', NULL, '2010-10-15', 'male', 'border', NULL, '2024-07-04', NULL, '0307-6882098', 'Mureed Hussain', NULL, 'Multan', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Saudia', '36304-5660488-5', NULL, '0307-6882098', '966599369277', NULL, NULL, NULL, NULL, NULL, '6th'),
(221, NULL, 'STD-24-0213', 'ADM-2024-0213', 'Muhammad Hassan Tahir', '', 'Zaheer Abbas', NULL, '2012-12-20', 'male', 'border', NULL, '2024-07-05', NULL, '0314-7969287', 'Zaheer Abbas', NULL, 'Sheikupura', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', NULL, NULL, '0345-6347025', '3054651343', NULL, NULL, NULL, NULL, NULL, '6th'),
(222, NULL, 'STD-24-0214', 'ADM-2024-0214', 'Muhammad Uzair', '', 'Muhammad Ramzan', NULL, '2012-03-25', 'male', 'border', NULL, '2024-07-05', NULL, '0301-6476738', 'Muhammad Ramzan', NULL, 'Gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '34102-0430069-9', NULL, '0300-8695531', '0336-0499691', NULL, NULL, NULL, NULL, NULL, '6th'),
(223, NULL, 'STD-24-0215', 'ADM-2024-0215', 'Muhammad Abdul Rehman', '', 'Muhammad Aslam', NULL, '2013-10-10', 'male', 'border', NULL, '2024-07-08', NULL, '0322-8000164', 'Muhammad Aslam', NULL, 'Kasur', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'PAF (Despenser)', '35103-3201619-1', NULL, '0314-4030625', '0322-8000164', NULL, NULL, NULL, NULL, NULL, '6th'),
(224, NULL, 'STD-24-0216', 'ADM-2024-0216', 'Syed Ali Imam', '', 'Syed Asghar Ali Shah', NULL, '2011-01-16', 'male', 'day_scholar', NULL, '2024-07-10', NULL, '0300-4614431', 'Syed Asghar Ali Shah', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Construction Worker', '35202-324398-1', NULL, '0300-2905551', '0300-4614431', NULL, NULL, NULL, NULL, NULL, '6th'),
(225, NULL, 'STD-24-0217', 'ADM-2024-0217', 'Muhammad Abdullah', '', 'Muhammad Ramzan', NULL, '2012-01-18', 'male', 'aghosh', NULL, '2024-07-12', NULL, '0302-6859292', 'Muhammad Ramzan', NULL, 'Multan', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Orfan', '32303-5718725-1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '6th'),
(226, NULL, 'STD-24-0218', 'ADM-2024-0218', 'Muhammad Arbab Amir', '', 'Amir Shabir', NULL, '2013-08-31', 'male', 'day_scholar', NULL, '2024-07-15', NULL, '0300-8591614', 'Amir Shabir', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Auto Spair Parts', '35202-3478858-1', NULL, '0311-8573614', '0300-8573614', NULL, NULL, NULL, NULL, NULL, '6th'),
(227, NULL, 'STD-24-0219', 'ADM-2024-0219', 'Muhammad Hamza Imran', '', 'Muhammad Imran', NULL, '2012-07-16', 'male', 'border', NULL, '2024-07-22', NULL, '0304-7999856', 'Muhammad Imran', NULL, 'Toba Tak Singh', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Electrition Saudia', '33302-3696594-9', NULL, '0306-8566332', '0304-7999856', NULL, NULL, NULL, NULL, NULL, '6th'),
(228, NULL, 'STD-24-0220', 'ADM-2024-0220', 'Abdul Subhan Qurashi', '', 'Tariq Mahmood', NULL, '2012-10-24', 'male', 'border', NULL, '2024-07-23', NULL, '0314-3280532', 'Tariq Mahmood', NULL, 'Manshera', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Chief', '13503-0593038-5', NULL, '0311-7308896', '0314-3280532', NULL, NULL, NULL, NULL, NULL, '6th'),
(229, NULL, 'STD-24-0221', 'ADM-2024-0221', 'Hafiz Ali Hussain', '', 'Zakir Hussain', NULL, '2012-03-15', 'male', 'border', NULL, '2024-07-25', NULL, '0348-4408003', 'Zakir Hussain', NULL, NULL, NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Immam Masjid', '33302-5883205-7', NULL, '0317-6292171', '0348-4408003', NULL, NULL, NULL, NULL, NULL, '6th'),
(230, NULL, 'STD-24-0222', 'ADM-2024-0222', 'Muhammad Rayan Khan', '', 'Muhammad Atif Anwer Khan', NULL, '2010-10-13', 'male', 'day_scholar', NULL, '2024-07-29', NULL, '0315-6776777', 'Muhammad Atif Anwer Khan', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Police Constable', '35202-2762084-3', NULL, '0315-6776777', '0323-4474292', NULL, NULL, NULL, NULL, NULL, '6th'),
(231, NULL, 'STD-24-0223', 'ADM-2024-0223', 'Ali Hassan Shafi', '', 'Muhammad Atif', NULL, '2012-12-24', 'male', 'day_scholar', NULL, '2024-07-29', NULL, '0301-4172312', 'Muhammad Atif', NULL, 'Kasur', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Farmer', '35101-2674185-6', NULL, '0303-4191824', '0301-4172312', NULL, NULL, NULL, NULL, NULL, '6th'),
(232, NULL, 'STD-24-0224', 'ADM-2024-0224', 'Muhammad Noor Mustafa', '', 'Safdar Abbas', NULL, '2009-02-27', 'male', 'border', NULL, '2024-07-31', NULL, '0331-7469994', 'Safdar Abbas', NULL, 'Bhakhar', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Imamt', '38104-0859594-7', NULL, '0300-7469994', '0331-7469994', NULL, NULL, NULL, NULL, NULL, '8th'),
(233, NULL, 'STD-24-0225', 'ADM-2024-0225', 'Hasnian Ali', '', 'Iftikhar Saleem', NULL, '2010-07-06', 'male', 'border', NULL, '2024-08-01', NULL, '0321-4448655', 'Iftikhar Saleem', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '35202-7831182-1', NULL, '0321-4312344', '0321-4448655', NULL, NULL, NULL, NULL, NULL, '6th'),
(234, NULL, 'STD-24-0226', 'ADM-2024-0226', 'Muhammad', '', 'Imtiaz Ali', NULL, '2011-01-16', 'male', 'day_scholar', NULL, '2024-08-01', NULL, '0303-8402345', 'Imtiaz Ali', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '35200-1466297-5', NULL, '0307-3688222', '0303-8402345', NULL, NULL, NULL, NULL, NULL, '6th'),
(235, NULL, 'STD-24-0227', 'ADM-2024-0227', 'Ali Hussain', '', 'Zakir Hussain', NULL, '2012-03-15', 'male', 'day_scholar', NULL, '2024-08-02', NULL, '0348-4408003', 'Zakir Hussain', NULL, 'Toba Tak Singh', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Imamt', '33302-5883205-7', NULL, '0348-4408003', '0317-6292171', NULL, NULL, NULL, NULL, NULL, '6th'),
(236, NULL, 'STD-24-0228', 'ADM-2024-0228', 'Muhammad Hammad Mustafa', '', 'Muhammad Hassan Farooq', NULL, '2010-03-03', 'male', 'border', NULL, '2024-08-05', NULL, '0302-4184625', 'Muhammad Hassan Farooq', NULL, 'Sargodha', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'R. Army Person (PAF)', '36402-5721925-3', NULL, '0309-4174625', '0333-4174625', NULL, NULL, NULL, NULL, NULL, '6th'),
(237, NULL, 'STD-24-0229', 'ADM-2024-0229', 'Muhammad Abdul Ayan', '', 'Shahzad Ahmad', NULL, '2015-01-11', 'male', 'aghosh', NULL, '2024-08-05', NULL, '0313-4402771', 'Shahzad Ahmad', NULL, 'Gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Orfan', '34101-2443358-4', NULL, NULL, '0313-4402771', NULL, NULL, NULL, NULL, NULL, '6th'),
(238, NULL, 'STD-24-0230', 'ADM-2024-0230', 'Muhammad Abdullah', '', 'Muhammad Ramzan', NULL, '2012-01-18', 'male', 'aghosh', NULL, '2024-08-05', NULL, '0313-4402771', 'Muhammad Ramzan', NULL, 'Liyya', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Orfan', '32203-5096069-2', NULL, NULL, '0313-4402771', NULL, NULL, NULL, NULL, NULL, '6th'),
(239, NULL, 'STD-24-0231', 'ADM-2024-0231', 'Ahmad Mustafa', '', 'Muhammad Hassan Farooq', NULL, '2011-03-04', 'male', 'border', NULL, '2024-08-05', NULL, '0302-4174625', 'Muhammad Hassan Farooq', NULL, 'Sargodha', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'R. Army Person (PAF)', '36402-5721925-3', NULL, '0309-4174625', '0333-4174625', NULL, NULL, NULL, NULL, NULL, '6th'),
(240, NULL, 'STD-24-0232', 'ADM-2024-0232', 'Hammad Mustafa', '', 'Sabir Hussain Sabri', NULL, '2010-02-17', 'male', 'day_scholar', NULL, '2024-08-07', NULL, '0307-4614710', 'Sabir Hussain Sabri', NULL, 'Multan', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Imamt Hongkong', '35202-5863696-1', NULL, '85253187574', '85253187574', NULL, NULL, NULL, NULL, NULL, '6th');
INSERT INTO `students` (`id`, `user_id`, `student_id`, `admission_no`, `first_name`, `last_name`, `father_name`, `cnic_bform`, `date_of_birth`, `gender`, `student_type`, `class_id`, `admission_date`, `phone`, `guardian_phone`, `guardian_name`, `address`, `city`, `previous_education`, `medical_info`, `status`, `photo`, `created_at`, `updated_at`, `mother_name`, `date_of_admission`, `date_of_leaving`, `reason_of_leaving`, `father_profession`, `father_cnic`, `admission_challan_no`, `guardian_phone_2`, `whatsapp_no`, `previous_result_card`, `total_marks`, `obtained_marks`, `class_status`, `previous_school_class`, `current_school_class`) VALUES
(241, NULL, 'STD-24-0233', 'ADM-2024-0233', 'Muhammad Muneeb Tariq', '', 'Muhammad Tariq Jawed', NULL, '2015-03-04', 'male', 'border', NULL, '2024-08-08', NULL, '0346-5988346', 'Muhammad Tariq Jawed', NULL, 'Bhimber AzadKahsmir', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'government Employee', '81101-0328415-9', NULL, '0341-8905188', '0346-5988346', NULL, NULL, NULL, NULL, NULL, '6th'),
(242, NULL, 'STD-24-0234', 'ADM-2024-0234', 'Muhammad Ayyan Ali', '', 'Muhammad Irfan', NULL, '2012-07-30', 'male', 'border', NULL, '2024-08-12', NULL, '0300-6064548', 'Muhammad Irfan', NULL, 'Sargodha', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '38406-0386867-3', NULL, NULL, '0343-7574500', NULL, NULL, NULL, NULL, NULL, '6th'),
(243, NULL, 'STD-24-0235', 'ADM-2024-0235', 'Abdul Ayyan', '', 'Shahzad Ahmad', NULL, '2015-01-11', 'male', 'aghosh', NULL, '2024-08-12', NULL, '0300-6467313', 'Shahzad Ahmad', NULL, 'Gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Orfan', '34403-7075123-1', NULL, '0308-6866892', NULL, NULL, NULL, NULL, NULL, NULL, '6th'),
(244, NULL, 'STD-24-0236', 'ADM-2024-0236', 'Muhammad Taha Irfan', '', 'Muhammad Irfan', NULL, '2013-01-17', 'male', 'day_scholar', NULL, '2024-08-13', NULL, '', 'Muhammad Irfan', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '35202-3320011-9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '6th'),
(245, NULL, 'STD-24-0237', 'ADM-2024-0237', 'Umair Ali', '', 'Ghulam Hassan', NULL, '2012-12-31', 'male', 'day_scholar', NULL, '2024-08-15', NULL, '0321-6662793', 'Ghulam Hassan', NULL, 'NaroWal', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, NULL, '34501-5528723-9', NULL, '0345-6662793', '0321-6662793', NULL, NULL, NULL, NULL, NULL, '6th'),
(246, NULL, 'STD-24-0238', 'ADM-2024-0238', 'Muhammad Faizan', '', 'Muhammad Asif', NULL, '2010-12-17', 'male', 'border', NULL, '2024-08-15', NULL, '0303-6033074', 'Muhammad Asif', NULL, 'Gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '34104-5118842-9', NULL, '0302-6496147', '0303-6033074', NULL, NULL, NULL, NULL, NULL, '7th'),
(247, NULL, 'STD-24-0239', 'ADM-2024-0239', 'Muhammad Inam Khan', '', 'Rashid Khan', NULL, '2012-09-25', 'male', 'border', NULL, '2024-08-19', NULL, '0304-4555388', 'Rashid Khan', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Salesman', '35201-7422838-7', NULL, '0307-4041403', '0304-4555388', NULL, NULL, NULL, NULL, NULL, '6th'),
(248, NULL, 'STD-24-0240', 'ADM-2024-0240', 'Muhammad Aliyan', '', 'Waheed Ahmad Khan', NULL, '2011-09-06', 'male', 'border', NULL, '2024-08-19', NULL, '0333-2288480', 'Waheed Ahmad Khan', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '42201-5841966-9', NULL, '0344-2426209', '0333-2288480', NULL, NULL, NULL, NULL, NULL, '6th'),
(249, NULL, 'STD-24-0241', 'ADM-2024-0241', 'Syed Hassan Askari Shah', '', 'Naeem Shah', NULL, '2011-11-16', 'male', 'day_scholar', NULL, '2024-08-20', NULL, '0323-4376700', 'Naeem Shah', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '35202-2138092-3', NULL, '0306-4582451', '0323-4376700', NULL, NULL, NULL, NULL, NULL, '6th'),
(250, NULL, 'STD-24-0242', 'ADM-2024-0242', 'Mian Muhammad Atif Basheer', '', 'Mian Muhammad Kashif Bashir', NULL, '2013-07-15', 'male', 'border', NULL, '2024-08-20', NULL, '0300-4386010', 'Mian Muhammad Kashif Bashir', NULL, 'Sheikupura', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '35404-5439451-9', NULL, '0301-4343976', '0300-4386010', NULL, NULL, NULL, NULL, NULL, '6th'),
(251, NULL, 'STD-24-0243', 'ADM-2024-0243', 'M. Moviya Baqir', '', 'Muhammad naveed Bakir', NULL, '2011-10-04', 'male', 'day_scholar', NULL, '2024-08-20', NULL, '0336-4224958', 'Muhammad naveed Bakir', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Uk', '61101-6504346-1', NULL, '0330-2434777', '0336-4224958', NULL, NULL, NULL, NULL, NULL, '7th'),
(252, NULL, 'STD-24-0244', 'ADM-2024-0244', 'Muhammad Khuzaima Nawab', '', 'Muhammad Naveed', NULL, '2012-05-21', 'male', 'day_scholar', NULL, '2024-08-20', NULL, '0331-6948378', 'Muhammad Naveed', NULL, 'Islamabad', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Uk', '61101-4990062-9', NULL, '0336-2444958', '0334-6948378', NULL, NULL, NULL, NULL, NULL, '6th'),
(253, NULL, 'STD-24-0245', 'ADM-2024-0245', 'Ali Raza', '', 'Muhammad Sohail', NULL, '2007-12-14', 'male', 'day_scholar', NULL, '2024-08-21', NULL, '0325-8228189', 'Muhammad Sohail', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Labour', '35202-3022168-9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '8th'),
(254, NULL, 'STD-24-0246', 'ADM-2024-0246', 'Muhammad Musa Imran', '', 'Imran Razzaq', NULL, '2011-02-03', 'male', 'border', NULL, '2024-09-01', NULL, '0336-6881171', 'Imran Razzaq', NULL, 'Sheikupura', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '35404-1503467-3', NULL, '0320-0461855', '0336-6881171', NULL, NULL, NULL, NULL, NULL, '6th'),
(255, NULL, 'STD-24-0247', 'ADM-2024-0247', 'Muhammad Awais', '', 'Zafar Iqbal', NULL, '2013-08-06', 'male', 'day_scholar', NULL, '2024-09-02', NULL, '0309-2929297', 'Zafar Iqbal', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '35202-3971572-9', NULL, '0309-9292925', '0309-2929297', NULL, NULL, NULL, NULL, NULL, '6th'),
(256, NULL, 'STD-24-0248', 'ADM-2024-0248', 'Saad bin Yameen', '', 'Muhammad Yameen', NULL, '2014-08-16', 'male', 'day_scholar', NULL, '2024-09-02', NULL, '0322-4127154', 'Muhammad Yameen', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Paint Worker', '35202-2145118-9', NULL, '0323-9943380', '0322-4127154', NULL, NULL, NULL, NULL, NULL, '7th'),
(257, NULL, 'STD-24-0249', 'ADM-2024-0249', 'Muhammad Umar Naseer', '', 'Naseer Ahmad', NULL, '2012-06-15', 'male', 'border', NULL, '2024-09-02', NULL, '0313-7875121', 'Naseer Ahmad', NULL, 'Sialkot', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '34603-4682795-7', NULL, NULL, '0313-7875121', NULL, NULL, NULL, NULL, NULL, '6th'),
(258, NULL, 'STD-24-0250', 'ADM-2024-0250', 'Muhammad Bazil Raza', '', 'Raza Hussain', NULL, '2010-04-20', 'male', 'border', NULL, '2024-09-02', NULL, '0321-5948406', 'Raza Hussain', NULL, 'Jehlum', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Grocery Store', '37301-3089532-5', NULL, '0336-5848406', '0336-5848406', NULL, NULL, NULL, NULL, NULL, '7th'),
(259, NULL, 'STD-24-0251', 'ADM-2024-0251', 'Muhammad Saad', '', 'Bilal Haider', NULL, '2013-02-11', 'male', 'border', NULL, '2024-09-02', NULL, '0345-6892739', 'Bilal Haider', NULL, 'Jehlum', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Jail Police', '37302-3954090-5', NULL, '0346-2687233', '0342-8285479', NULL, NULL, NULL, NULL, NULL, '6th'),
(260, NULL, 'STD-24-0252', 'ADM-2024-0252', 'Zain ul Abideen', '', 'Liaqat Ali', NULL, '2013-02-19', 'male', 'border', NULL, '2024-09-03', NULL, '0300-5529588', 'Liaqat Ali', NULL, 'Attack', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Graphic Design', '42301-8246286-1', NULL, NULL, '0300-5529588', NULL, NULL, NULL, NULL, NULL, '6th'),
(261, NULL, 'STD-24-0253', 'ADM-2024-0253', 'Fawad Ali', '', 'Rab Nawaz', NULL, '2012-03-17', 'male', 'border', NULL, '2024-09-03', NULL, '0300-5529588', 'Rab Nawaz', NULL, 'Attack', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Saudia Arab', '37106-0203857-8', NULL, '0300-5529588', '0300-5529588', NULL, NULL, NULL, NULL, NULL, '6th'),
(262, NULL, 'STD-24-0254', 'ADM-2024-0254', 'Hasnian Mujtaba', '', 'Abdul Mujtaba', NULL, '2012-07-21', 'male', 'border', NULL, '2024-09-05', NULL, '0305-4555913', 'Abdul Mujtaba', NULL, 'Muzaffergar', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Agriculture', '32304-1574500-7', NULL, '0346-6078341', '0305-4555913', NULL, NULL, NULL, NULL, NULL, '6th'),
(263, NULL, 'STD-24-0255', 'ADM-2024-0255', 'Muhammad Saifulllah Zaffar', '', 'Zaffer ulllah', NULL, '2013-10-08', 'male', 'day_scholar', NULL, '2024-09-07', NULL, '0322-4247688', 'Zaffer ulllah', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'IT Computer', '35202-1912672-3', NULL, '0321-4961151', '0322-4247688', NULL, NULL, NULL, NULL, NULL, NULL),
(264, NULL, 'STD-24-0256', 'ADM-2024-0256', 'Muhammad Ibrahim Khan', '', 'Muhammd Tahir Khan', NULL, '2013-07-25', 'male', 'border', NULL, '2024-09-09', NULL, '0305-8501071', 'Muhammd Tahir Khan', NULL, 'Abotabad', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'FBR Retired', '13101-1006761-7', NULL, '0342-5637449', '0305-8501071', NULL, NULL, NULL, NULL, NULL, '6th'),
(265, NULL, 'STD-24-0257', 'ADM-2024-0257', 'Muhammad Sikender Hayat', '', 'Muhammad Ajmal', NULL, '2008-12-03', 'male', 'border', NULL, '2024-09-10', NULL, '0308-6101632', 'Muhammad Ajmal', NULL, 'Gujranwala', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Imamt', '34101-3479037-9', NULL, '0343-6045065', '0342-6190172', NULL, NULL, NULL, NULL, NULL, '6th'),
(266, NULL, 'STD-24-0258', 'ADM-2024-0258', 'Zeeshan Mujtaba', '', 'Ghulam Abbas Mujtaba', NULL, '2010-04-23', 'male', 'day_scholar', NULL, '2024-09-13', NULL, '0304-3944440', 'Ghulam Abbas Mujtaba', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '33301-3568776-9', NULL, '0349-7707067', '0304-3944440', NULL, NULL, NULL, NULL, NULL, '6th'),
(267, NULL, 'STD-24-0259', 'ADM-2024-0259', 'Muhammad Abu Bakr', '', 'Ghulam Abbas Mujtaba', NULL, '2011-10-10', 'male', 'day_scholar', NULL, '2024-09-13', NULL, '0304-3944440', 'Ghulam Abbas Mujtaba', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job', '33301-3568776-9', NULL, '0349-7707067', '0304-3944440', NULL, NULL, NULL, NULL, NULL, '6th'),
(268, NULL, 'STD-24-0260', 'ADM-2024-0260', 'Shahzaib Ali', '', 'Muhammad Jaffer', NULL, '2014-02-16', 'male', 'day_scholar', NULL, '2024-09-14', NULL, '0301-7335720', 'Muhammad Jaffer', NULL, 'Okara', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Labour', '35301-1981554-1', NULL, '0303-7827841', '0301-7335720', NULL, NULL, NULL, NULL, NULL, '6th'),
(269, NULL, 'STD-24-0261', 'ADM-2024-0261', 'Ali Ahmad', '', 'Ahmad yaar', NULL, '2015-10-02', 'male', 'border', NULL, '2024-09-23', NULL, '0306-7824566', 'Ahmad yaar', NULL, 'Khaniwal', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Teacher', '36103-5500307-3', NULL, '0300-8391496', '0306-7824566', NULL, NULL, NULL, NULL, NULL, '6th'),
(270, NULL, 'STD-24-0262', 'ADM-2024-0262', 'Muhammad Rahan Ishfaq', '', 'Muhammad Ishfaq', NULL, '2012-08-01', 'male', 'border', NULL, '2024-10-01', NULL, '966598194114', 'Muhammad Ishfaq', NULL, 'Rahim Yaar Khan', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job Saudia', '31301-3611100-9', NULL, '0311-6223301', '0345-0500003', NULL, NULL, NULL, NULL, NULL, '6th'),
(271, NULL, 'STD-24-0263', 'ADM-2024-0263', 'Muhammad Farhan ishfaq', '', 'Muhammad Ishfaq', NULL, '2010-11-06', 'male', 'border', NULL, '2024-10-01', NULL, '966598194114', 'Muhammad Ishfaq', NULL, 'Rahim Yaar Khan', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'private job Saudia', '31301-3611100-9', NULL, '0311-6223301', '0345-0500003', NULL, NULL, NULL, NULL, NULL, '6th'),
(272, NULL, 'STD-24-0264', 'ADM-2024-0264', 'Abdul Rehman', '', 'Muhammad Ashraf', NULL, '2014-01-10', 'male', 'border', NULL, '2024-10-01', NULL, '', 'Muhammad Ashraf', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '35102-1050923-1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '6th'),
(273, NULL, 'STD-24-0265', 'ADM-2024-0265', 'Saqib Ali', '', 'Shahid Mahmood', NULL, '2012-01-18', 'male', 'day_scholar', NULL, '2024-10-01', NULL, '0301-6822386', 'Shahid Mahmood', NULL, 'Kasur', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, NULL, '35103-1700938-', NULL, NULL, '0301-6822386', NULL, NULL, NULL, NULL, NULL, '6th'),
(274, NULL, 'STD-24-0266', 'ADM-2024-0266', 'Muhammad Arshad', '', 'Muhammad Mehboob', NULL, '2012-07-16', 'male', 'border', NULL, '2024-10-02', NULL, '0340-0094449', 'Muhammad Mehboob', NULL, 'Sargodha', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '38403-2071856-9', NULL, '337619950089', '33761995089', NULL, NULL, NULL, NULL, NULL, '6th'),
(275, NULL, 'STD-24-0267', 'ADM-2024-0267', 'Muhammad Roman', '', 'Khizer Hayat', NULL, '2012-06-17', 'male', 'day_scholar', NULL, '2024-10-08', NULL, '0307-2837187', 'Khizer Hayat', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Property Dealer', '35203-1171281-5', NULL, '0308-4802836', '0307-2837187', NULL, NULL, NULL, NULL, NULL, '6th'),
(276, NULL, 'STD-24-0268', 'ADM-2024-0268', 'Ahmad Burhan', '', 'Muhammad shaban daar', NULL, '2013-11-04', 'male', 'border', NULL, '2024-10-09', NULL, '0321-9550254', 'Muhammad shaban daar', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Tailor Master', '37405-5420137-4', NULL, '0322-5373065', '0324-4329612', NULL, NULL, NULL, NULL, NULL, '6th'),
(277, NULL, 'STD-24-0269', 'ADM-2024-0269', 'Muhammad ahmad', '', 'Ghulam Murtaza', NULL, '2013-07-11', 'male', 'border', NULL, '2024-10-18', NULL, '0301-4646476', 'Ghulam Murtaza', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Pharmacy', '33302-2204923-3', NULL, '0321-8492515', '0301-4646476', NULL, NULL, NULL, NULL, NULL, '6th'),
(278, NULL, 'STD-24-0270', 'ADM-2024-0270', 'Nauman Ali Hafeez', '', 'Mian Abdul Hafeez', NULL, '2013-10-02', 'male', 'day_scholar', NULL, '2024-11-04', NULL, '0309-3522574', 'Mian Abdul Hafeez', NULL, 'Lahore', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Bike Spairparts', '35200-1413337-1', NULL, '0323-4767066', '0323-4767066', NULL, NULL, NULL, NULL, NULL, '6th'),
(279, NULL, 'STD-24-0271', 'ADM-2024-0271', 'Ayan Shahid', '', 'Shahid Hussain', NULL, '2011-01-10', 'male', 'day_scholar', NULL, '2024-11-18', NULL, '0316-7604848', 'Shahid Hussain', NULL, 'GUJRAT', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Own Buisness', '34201-6113482-7', NULL, '0332-2466464', '0316-7604848', NULL, NULL, NULL, NULL, NULL, '7th'),
(280, NULL, 'STD-24-0272', 'ADM-2024-0272', 'Mudasir Iqbal', '', 'Muhammad iqbal', NULL, '2010-06-08', 'male', 'border', NULL, '2024-11-29', NULL, '0308-7218747', 'Muhammad iqbal', NULL, 'Khaniwal', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Farmer', '36104-6383025-1', NULL, '0348-2706450', '0308-7218747', NULL, NULL, NULL, NULL, NULL, '6th'),
(281, NULL, 'STD-24-0273', 'ADM-2024-0273', 'Noor ul Hassan', '', 'Abdul Shakoor', NULL, '2010-12-30', 'male', 'border', NULL, '2024-11-29', NULL, '0300-6891694', 'Abdul Shakoor', NULL, 'Khaniwal', NULL, NULL, 'active', NULL, '2026-04-06 07:59:15', '2026-04-06 07:59:15', NULL, NULL, NULL, NULL, 'Electronics Shop', '36104-6131327-7', NULL, '0308-2350868', '0300-6891694', NULL, NULL, NULL, NULL, NULL, '6th');

-- --------------------------------------------------------

--
-- Table structure for table `student_attendance`
--

CREATE TABLE `student_attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','leave','late') NOT NULL,
  `remarks` text DEFAULT NULL,
  `marked_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_attendance`
--

INSERT INTO `student_attendance` (`id`, `student_id`, `class_id`, `attendance_date`, `status`, `remarks`, `marked_by`, `created_at`) VALUES
(1, 2, 1, '2025-10-27', 'present', '', 1, '2025-10-27 08:15:36'),
(2, 1, 1, '2025-10-27', 'absent', '', 1, '2025-10-27 08:15:36'),
(3, 4, 1, '2025-11-12', 'present', '', 1, '2025-11-11 19:15:48'),
(4, 2, 1, '2025-11-12', 'present', '', 1, '2025-11-11 19:15:48'),
(5, 2, 1, '2025-12-02', 'present', '', 1, '2025-12-02 06:17:42');

-- --------------------------------------------------------

--
-- Table structure for table `student_behavior_reports`
--

CREATE TABLE `student_behavior_reports` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `report_date` date NOT NULL,
  `report_type` enum('internal','parent','ptm') NOT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_behavior_reports`
--

INSERT INTO `student_behavior_reports` (`id`, `student_id`, `class_id`, `report_date`, `report_type`, `summary`, `details`, `created_by`, `created_at`) VALUES
(1, 5, 2, '2025-11-19', 'internal', 'Fight with teacher', 'Fight with teacher', 1, '2025-11-19 09:10:17');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'school_name', 'Minhaj Institute of Qirat & Tajweed', 'School Name', '2025-10-27 07:54:33'),
(2, 'school_address', 'Enter School Address', 'School Address', '2025-10-27 07:54:33'),
(3, 'school_phone', '+92-XXX-XXXXXXX', 'School Contact Number', '2025-10-27 07:54:33'),
(4, 'school_email', 'info@miqt.edu', 'School Email', '2025-10-27 07:54:33'),
(5, 'academic_year', '2024-2025', 'Current Academic Year', '2025-10-27 07:54:33'),
(6, 'attendance_time', '08:00', 'Daily Attendance Time', '2025-10-27 07:54:33');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `teacher_id` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `cnic` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female') NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `joining_date` date NOT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `employment_type` enum('full_time','part_time','contract') DEFAULT 'full_time',
  `status` enum('active','inactive','on_leave') DEFAULT 'active',
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reference_name` varchar(100) DEFAULT NULL,
  `reference_number` varchar(50) DEFAULT NULL,
  `past_history` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `user_id`, `teacher_id`, `first_name`, `last_name`, `father_name`, `cnic`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `city`, `qualification`, `specialization`, `joining_date`, `salary`, `employment_type`, `status`, `photo`, `created_at`, `updated_at`, `reference_name`, `reference_number`, `past_history`) VALUES
(1, NULL, 'TCH-001', 'Ahmad', 'Khan', 'Muhammad Khan', NULL, NULL, 'male', '0300-1234567', 'ahmad@miqt.edu', NULL, NULL, 'MA Islamic Studies', 'Qirat & Tajweed', '2024-01-01', NULL, 'full_time', 'active', NULL, '2025-10-27 07:58:16', '2025-10-27 07:58:16', NULL, NULL, NULL),
(2, NULL, 'TCH-002', 'Fatima', 'Ahmed', 'Ahmed Ali', NULL, NULL, 'female', '0301-2345678', 'fatima@miqt.edu', NULL, NULL, 'Shahadat-ul-Aalamiya', 'Hifz & Tajweed', '2024-01-01', NULL, 'full_time', 'active', NULL, '2025-10-27 07:58:16', '2025-10-27 07:58:16', NULL, NULL, NULL),
(3, NULL, 'TCH-643718D5ED', 'Ghulam', 'Mujtaba', 'Ghulam Murtaza', '35202-9071952-1', '1999-09-01', 'male', '03314057324', '', 'House#651, Sector 2, D-1 Township L\r\n365-M-ModelTown, Lahore', 'Lahore', 'MA Islamic Studies', 'Quirat', '2025-10-29', 25000.00, 'full_time', 'active', '692707a2ab8a5_1764165538.png', '2025-10-29 17:28:30', '2025-11-26 13:58:58', NULL, NULL, NULL),
(4, NULL, 'TCH-232289782B', 'Qari Muhammad', 'Abu bakar', 'Abdullah', '3130287439573', '1993-04-05', 'male', '03337482326', '', 'Sadhoki Cantt Lahore', 'Lahore', 'Hifz and 8th pass', 'Yes', '2025-10-13', 0.00, 'full_time', 'active', '', '2025-12-20 07:01:27', '2025-12-20 07:01:27', NULL, NULL, NULL),
(5, NULL, 'TCH-C7A5AC2080', 'Qari Muhammad', 'Masood', 'Nasrullah Khan', '3830170765923', '2004-01-06', 'male', '03229328996', '', 'Sadhoki Cantt Lahore', 'Mianwali', '8th Pass', 'Tajveed', '2025-07-01', 0.00, 'full_time', 'active', '', '2025-12-20 07:04:36', '2025-12-20 07:05:02', NULL, NULL, NULL),
(6, NULL, 'TCH-3B77A62AE8', 'Qari Muhammad', 'Irfan', 'Muhammad Ikram', '3110292582723', '1998-01-01', 'male', '03075870971', '', 'po bhdera chistian', 'Bahawalnagar', 'Tajweed', 'Tajveed', '2025-10-16', 0.00, 'full_time', 'active', '', '2025-12-20 07:32:33', '2025-12-20 07:32:33', NULL, NULL, NULL),
(7, NULL, 'TCH-DDD9487577', 'Qari Muhammad', 'Imran', 'Riaz Hussain', '3120343852567', '1995-10-01', 'male', '03002383217', '', 'PO jamalpur Bahawalpur', 'bahawalpur', 'Middle', 'Tajweed', '2025-08-06', 0.00, 'full_time', 'active', '', '2025-12-20 07:39:24', '2025-12-20 07:39:24', NULL, NULL, NULL),
(9, NULL, 'TCH-0DAE38D504', 'Qari Abdullah', 'Basheer', 'Shah Muhammad', '3120323890363', '1988-01-01', 'male', '03046112646', '', 'PO Ghazikhnana Hasilpur', 'bahawalpur', 'Middle', 'Hifz', '2023-09-11', 0.00, 'full_time', 'active', '', '2025-12-20 07:42:50', '2025-12-20 07:42:50', NULL, NULL, NULL),
(10, NULL, 'TCH-1D10D3B5F9', 'Qari Yasir', 'Ahmad', 'Muhammad Haneef', '3520236724795', '1992-04-25', 'male', '03074723720', '', 'street 5Muhalla Muslim Park Bank Stop Lahore', 'Lahore', 'Matric', 'Tajweed', '2025-07-01', 0.00, 'full_time', 'active', '', '2025-12-20 07:52:17', '2025-12-20 07:52:17', NULL, NULL, NULL),
(11, NULL, 'TCH-6286A1674A', 'Qari Anees', 'ur Rehman', 'Ghulam Fareed', '3120199041091', '1990-01-01', 'male', '03077769458', '', 'PO Matheeji Ahmadpur East', 'Bahawalpur', 'Middle', 'Tajweed', '2022-09-15', 0.00, 'full_time', 'active', '', '2025-12-20 07:56:03', '2025-12-20 07:56:03', NULL, NULL, NULL),
(12, NULL, 'TCH-DE00DECB2B', 'Qari Muhammad', 'Sufian', 'Muhammad Saleem', '312031103421', '2002-03-01', 'male', '03218304689', '', 'PO Khas Hasilpur', 'Bahawalpur', 'Primary', 'Tajweed', '2023-07-10', 0.00, 'full_time', 'active', '', '2025-12-20 08:00:13', '2025-12-20 08:01:42', NULL, NULL, NULL),
(13, NULL, 'TCH-1790D6D852', 'Qari Abdul', 'Latif', 'M. Haneef Khan', '3220297330671', '2002-07-03', 'male', '03047368636', '', 'PO Qazi Shahani, karor lal eesan', 'Liya', 'Hifz', 'Qirat', '2023-12-01', 0.00, 'full_time', 'active', '', '2025-12-20 08:06:56', '2025-12-20 08:06:56', NULL, NULL, NULL),
(14, NULL, 'TCH-C9E78ACFEE', 'Qari Muhammade', 'Ilyas', 'Altaf Hussain', '3230376131237', '2009-01-05', 'male', '03214570845', '', 'Po Nonari, Muzaffargar', 'Muzaffergar', 'Middle', 'Tajweed', '2025-05-01', 0.00, 'full_time', 'active', '', '2025-12-20 08:09:32', '2025-12-20 08:09:32', NULL, NULL, NULL),
(15, NULL, 'TCH-6DCFF6AB06', 'Qari Muhammad', 'Fiaz', 'Falak shair', '3120326948859', '1998-07-14', 'male', '03017383825', '', 'PO Jamalpur Ghazi Khanana Hasilpur', 'Bahawalpur', 'Matric', 'Hifz', '2024-10-01', 0.00, 'full_time', 'active', '', '2025-12-20 08:12:52', '2025-12-20 08:12:52', NULL, NULL, NULL),
(16, NULL, 'TCH-B79E44A7A1', 'Qari Muhammad', 'Arif', 'Muhammad Suleman', '3130210578199', '2025-11-16', 'male', '03147675817', '', 'Bait Imam Baksh, liaqatpur', 'Raheem Yar Khan', 'Middle', 'Hifz', '2025-04-01', 0.00, 'full_time', 'active', '', '2025-12-20 08:18:41', '2025-12-20 08:18:41', NULL, NULL, NULL),
(17, NULL, 'TCH-E0B0FF8AA4', 'Qari Muhammad', 'Waseem', 'Aijaz Hussain', '3230408310301', '1987-03-31', 'male', '03014849528', '', 'PO Mojiwala Qadirpur, Muzaffargar', 'Muzaffargar', 'Middle', 'Tajweed', '2025-07-01', 0.00, 'full_time', 'active', '', '2025-12-20 08:24:15', '2025-12-20 08:24:15', NULL, NULL, NULL),
(18, NULL, 'TCH-466BA148A5', 'Qari Ikram', 'Ullah', 'Gulzar Hussain', '3820206853723', '1998-03-23', 'male', '03029869059', '', 'PO Khatwan, Noorpur', 'Khushab', 'Middle', 'Tajweed', '2025-07-01', 0.00, 'full_time', 'active', '', '2025-12-20 08:27:34', '2025-12-20 08:27:34', NULL, NULL, NULL),
(19, NULL, 'TCH-D265EBF708', 'Qari Muhammad', 'Rafaqat', 'Noor Muhammad', '3220346462829', '1999-09-07', 'male', '03076166465', '', 'PO heera Mines, Liya', 'Liya', 'M.phil', 'Hifz', '2025-09-01', 0.00, 'full_time', 'active', '', '2025-12-20 08:31:01', '2025-12-20 08:31:01', NULL, NULL, NULL),
(20, NULL, 'TCH-6C0B2D80D3', 'Qari Muhammad', 'Amir', 'Akhtar Hussain', '3230247374121', '1997-12-08', 'male', '03062665849', '', 'Rompur 3, PO Jatoi, Jatoi', 'Jatoi', 'Middle', 'Tajweed', '0001-01-01', 0.00, 'full_time', 'active', '', '2025-12-20 08:34:29', '2025-12-20 08:34:29', NULL, NULL, NULL),
(21, NULL, 'TCH-FD940E8C0D', 'Qari Muhammad', 'Muzammil', 'Munir Hussain', '3810282558075', '2001-01-14', 'male', '03444914455', '', 'PO Sakha Shah Dagr, DryaKhan', 'Bakhar', 'Matric', 'Tajweed', '0001-01-01', 0.00, 'full_time', 'active', '', '2025-12-20 08:37:37', '2025-12-20 08:37:37', NULL, NULL, NULL),
(22, NULL, 'TCH-EA6EA91414', 'Qari Tanveer', 'Ahmad', 'Mushtaq Ahmad', '3220216605449', '1992-02-16', 'male', '03062836242', '', 'Po Khas, karor lal eesan', 'Liya', 'Middle', 'Tajweed', '2025-09-02', 0.00, 'full_time', 'active', '', '2025-12-20 08:41:25', '2025-12-20 08:41:25', NULL, NULL, NULL),
(23, NULL, 'TCH-682B4415AA', 'Qari Muhammad', 'Abrar', 'Muhammad Nawaz', '3120367473081', '1998-01-01', 'male', '03069313833', '', 'PO Jamalpur Laddan, Hasilpur', 'Bahawalpur', 'Primary', 'Tajweed', '2025-11-01', 0.00, 'full_time', 'active', '', '2025-12-20 08:43:54', '2025-12-20 08:43:54', NULL, NULL, NULL),
(24, NULL, 'TCH-FCBACABE7A', 'Qari Sardar Inaam Ullah', 'Khan', 'Hidaiat Khan', '3740505522061', '2000-05-25', 'male', '03421086508', '', 'H No sn-915Muhalla Shamsabad, KalaKhan', 'Rawalpindi', 'Matric', 'Tajweed', '2021-06-16', 0.00, 'full_time', 'active', '', '2025-12-20 09:42:24', '2025-12-20 09:42:24', NULL, NULL, NULL),
(25, NULL, 'TCH-54E6DE0D1A', 'Qari Abdul Khaliq', 'Qammer', 'Khuda Baksh', '3610102435491', '1974-01-01', 'male', '0000000000', '', 'Chak # 129/10, Jahania, Khaniwal', 'Khanewal', 'Hifz', 'Tajweed', '2003-11-17', 0.00, 'full_time', 'active', '', '2025-12-20 09:50:19', '2025-12-20 09:50:19', NULL, NULL, NULL),
(26, NULL, 'TCH-19F69648A3', 'Qari Muhammad', 'Tayyab', 'Abdul Ghafoor', '36602535559981', '1995-01-02', 'male', '03009133249', '', 'PO Jld Jee, Meelsi, Wahari', 'Wahari', 'Matric', 'Hifz', '2017-11-09', 0.00, 'full_time', 'active', '', '2025-12-20 09:53:40', '2025-12-20 09:53:40', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_attendance`
--

CREATE TABLE `teacher_attendance` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','leave','half_day') NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `marked_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teacher_attendance`
--

INSERT INTO `teacher_attendance` (`id`, `teacher_id`, `attendance_date`, `status`, `check_in`, `check_out`, `remarks`, `marked_by`, `created_at`) VALUES
(1, 1, '2025-10-29', 'present', '10:40:00', '22:40:00', NULL, NULL, '2025-10-29 17:40:39'),
(2, 2, '2025-10-29', 'present', '10:40:00', '22:40:00', NULL, NULL, '2025-10-29 17:40:39'),
(3, 3, '2025-10-29', 'present', '10:40:00', '22:40:00', NULL, NULL, '2025-10-29 17:40:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `full_name` varchar(200) NOT NULL,
  `role` enum('principal','vice_principal','coordinator','teacher','admin','student','parent','staff') NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'principal', '$2y$10$o6eJNYT0.HLn2ffuzz8BBuY5EnVUxhZZ1Gq5/mZ6QBLFl9AkCOEiS', 'principal@miqt.edu', 'Principal Admin', 'principal', 'active', '2025-10-27 07:54:33', '2025-10-27 07:55:28'),
(2, 'vice_principal', '$2y$10$o6eJNYT0.HLn2ffuzz8BBuY5EnVUxhZZ1Gq5/mZ6QBLFl9AkCOEiS', 'vice@miqt.edu', 'Vice Principal', 'vice_principal', 'active', '2025-10-27 07:54:33', '2025-10-27 07:55:28'),
(3, 'coordinator', '$2y$10$o6eJNYT0.HLn2ffuzz8BBuY5EnVUxhZZ1Gq5/mZ6QBLFl9AkCOEiS', 'coordinator@miqt.edu', 'Academic Coordinator', 'coordinator', 'active', '2025-10-27 07:54:33', '2025-10-27 07:55:28'),
(4, 'Tayyab Ameen', '$2y$10$nKxXG4tmpF2mmzS6BaFIQuWwBI323K2edfK2Knn.tKPr7HKZ3ZK72', 'htayyabameen4948@gmail.com', 'Muhammad Tayyab Ameen', 'student', 'active', '2025-12-10 04:29:51', '2025-12-10 04:29:51'),
(5, 'Hasnainalishahzad', '$2y$10$MDWUMa0ZiXHihetNlNvt7u8yN.CcWJXcpnxcCGPjpRek2XfSqhGSC', '', 'Hasnain ali Shahzad', 'student', 'active', '2025-12-20 06:55:50', '2025-12-20 06:55:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_events`
--
ALTER TABLE `academic_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_date` (`event_date`),
  ADD KEY `event_type` (`event_type`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_teacher_id` (`class_teacher_id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_type_id` (`exam_type_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_result` (`exam_id`,`student_id`,`subject_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `exam_subjects`
--
ALTER TABLE `exam_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `exam_types`
--
ALTER TABLE `exam_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `juz_reference`
--
ALTER TABLE `juz_reference`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `juz_number` (`juz_number`);

--
-- Indexes for table `manzil_records`
--
ALTER TABLE `manzil_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `parent_id` (`parent_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `parent_student_relation`
--
ALTER TABLE `parent_student_relation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_relation` (`parent_id`,`student_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `quran_ayahs`
--
ALTER TABLE `quran_ayahs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `surah_ayah_unique` (`surah_id`,`ayah_number`),
  ADD KEY `surah_id` (`surah_id`);

--
-- Indexes for table `quran_juz`
--
ALTER TABLE `quran_juz`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quran_progress`
--
ALTER TABLE `quran_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `juz_number` (`juz_number`);

--
-- Indexes for table `quran_surahs`
--
ALTER TABLE `quran_surahs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `surah_number` (`surah_number`);

--
-- Indexes for table `sabak_records`
--
ALTER TABLE `sabak_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `sabqi_records`
--
ALTER TABLE `sabqi_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `admission_no` (`admission_no`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `student_attendance`
--
ALTER TABLE `student_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`student_id`,`attendance_date`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `marked_by` (`marked_by`);

--
-- Indexes for table `student_behavior_reports`
--
ALTER TABLE `student_behavior_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_id` (`teacher_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `teacher_attendance`
--
ALTER TABLE `teacher_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_teacher_attendance` (`teacher_id`,`attendance_date`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `marked_by` (`marked_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_events`
--
ALTER TABLE `academic_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `exam_subjects`
--
ALTER TABLE `exam_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exam_types`
--
ALTER TABLE `exam_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `juz_reference`
--
ALTER TABLE `juz_reference`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `manzil_records`
--
ALTER TABLE `manzil_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parent_student_relation`
--
ALTER TABLE `parent_student_relation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quran_ayahs`
--
ALTER TABLE `quran_ayahs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quran_juz`
--
ALTER TABLE `quran_juz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `quran_progress`
--
ALTER TABLE `quran_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quran_surahs`
--
ALTER TABLE `quran_surahs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `sabak_records`
--
ALTER TABLE `sabak_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sabqi_records`
--
ALTER TABLE `sabqi_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=282;

--
-- AUTO_INCREMENT for table `student_attendance`
--
ALTER TABLE `student_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_behavior_reports`
--
ALTER TABLE `student_behavior_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `teacher_attendance`
--
ALTER TABLE `teacher_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`class_teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exams_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exams_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD CONSTRAINT `exam_results_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_results_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_results_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `exam_subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_results_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exam_subjects`
--
ALTER TABLE `exam_subjects`
  ADD CONSTRAINT `exam_subjects_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `manzil_records`
--
ALTER TABLE `manzil_records`
  ADD CONSTRAINT `manzil_records_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `manzil_records_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `parents`
--
ALTER TABLE `parents`
  ADD CONSTRAINT `parents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `parent_student_relation`
--
ALTER TABLE `parent_student_relation`
  ADD CONSTRAINT `parent_student_relation_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parent_student_relation_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quran_ayahs`
--
ALTER TABLE `quran_ayahs`
  ADD CONSTRAINT `quran_ayahs_ibfk_1` FOREIGN KEY (`surah_id`) REFERENCES `quran_surahs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quran_progress`
--
ALTER TABLE `quran_progress`
  ADD CONSTRAINT `quran_progress_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quran_progress_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `quran_progress_ibfk_3` FOREIGN KEY (`juz_number`) REFERENCES `quran_juz` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sabak_records`
--
ALTER TABLE `sabak_records`
  ADD CONSTRAINT `sabak_records_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sabak_records_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sabqi_records`
--
ALTER TABLE `sabqi_records`
  ADD CONSTRAINT `sabqi_records_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sabqi_records_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_ibfk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `student_attendance`
--
ALTER TABLE `student_attendance`
  ADD CONSTRAINT `student_attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_attendance_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_attendance_ibfk_3` FOREIGN KEY (`marked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `student_behavior_reports`
--
ALTER TABLE `student_behavior_reports`
  ADD CONSTRAINT `student_behavior_reports_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_behavior_reports_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_behavior_reports_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `teacher_attendance`
--
ALTER TABLE `teacher_attendance`
  ADD CONSTRAINT `teacher_attendance_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_attendance_ibfk_2` FOREIGN KEY (`marked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
