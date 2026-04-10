-- ============================================
-- MIQT Database Schema
-- Minhaj Institute of Qirat & Tajweed
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create Database
CREATE DATABASE IF NOT EXISTS `miqt_school` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `miqt_school`;

-- ============================================
-- Users and Authentication Tables
-- ============================================

-- Users Table
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `full_name` varchar(200) NOT NULL,
  `role` enum('principal','vice_principal','coordinator','teacher','admin') NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Admin Users (Password: admin123)
INSERT INTO `users` (`username`, `password`, `email`, `full_name`, `role`, `status`) VALUES
('principal', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'principal@miqt.edu', 'Principal Admin', 'principal', 'active'),
('vice_principal', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vice@miqt.edu', 'Vice Principal', 'vice_principal', 'active'),
('coordinator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'coordinator@miqt.edu', 'Academic Coordinator', 'coordinator', 'active');

-- ============================================
-- HR Module Tables
-- ============================================

-- Teachers/Staff Table
CREATE TABLE `teachers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `address` text,
  `city` varchar(100) DEFAULT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `joining_date` date NOT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `employment_type` enum('full_time','part_time','contract') DEFAULT 'full_time',
  `status` enum('active','inactive','on_leave') DEFAULT 'active',
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `teacher_id` (`teacher_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Student Module Tables
-- ============================================

-- Classes/Groups Table
CREATE TABLE `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(100) NOT NULL,
  `class_teacher_id` int(11) DEFAULT NULL,
  `capacity` int(11) DEFAULT 30,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `class_teacher_id` (`class_teacher_id`),
  CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`class_teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Students Table
CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(50) NOT NULL,
  `admission_no` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `father_name` varchar(100) NOT NULL,
  `cnic_bform` varchar(20) DEFAULT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `student_type` enum('day_scholar','boarder','orphan') NOT NULL DEFAULT 'day_scholar',
  `class_id` int(11) DEFAULT NULL,
  `admission_date` date NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `guardian_phone` varchar(20) NOT NULL,
  `guardian_name` varchar(100) NOT NULL,
  `address` text,
  `city` varchar(100) DEFAULT NULL,
  `previous_education` text,
  `medical_info` text,
  `status` enum('active','inactive','graduated','left') DEFAULT 'active',
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`),
  UNIQUE KEY `admission_no` (`admission_no`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Attendance Module Tables
-- ============================================

-- Student Attendance Table
CREATE TABLE `student_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','leave','late') NOT NULL,
  `remarks` text,
  `marked_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_attendance` (`student_id`, `attendance_date`),
  KEY `student_id` (`student_id`),
  KEY `class_id` (`class_id`),
  KEY `marked_by` (`marked_by`),
  CONSTRAINT `student_attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_attendance_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_attendance_ibfk_3` FOREIGN KEY (`marked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Teacher Attendance Table
CREATE TABLE `teacher_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','leave','half_day') NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `remarks` text,
  `marked_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_teacher_attendance` (`teacher_id`, `attendance_date`),
  KEY `teacher_id` (`teacher_id`),
  KEY `marked_by` (`marked_by`),
  CONSTRAINT `teacher_attendance_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teacher_attendance_ibfk_2` FOREIGN KEY (`marked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Quran Progress Module Tables
-- ============================================

-- Quran Juz/Para Details
CREATE TABLE `quran_juz` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `juz_number` int(11) NOT NULL,
  `juz_name` varchar(100) NOT NULL,
  `start_surah` varchar(100) NOT NULL,
  `end_surah` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Quran 30 Juz Data
INSERT INTO `quran_juz` (`juz_number`, `juz_name`, `start_surah`, `end_surah`) VALUES
(1, 'الٓمٓ', 'Al-Fatihah', 'Al-Baqarah 141'),
(2, 'سَيَقُولُ', 'Al-Baqarah 142', 'Al-Baqarah 252'),
(3, 'تِلْكَ ٱلرُّسُلُ', 'Al-Baqarah 253', 'Al-Imran 92'),
(4, 'لَن تَنَالُوا۟', 'Al-Imran 93', 'An-Nisa 23'),
(5, 'وَٱلْمُحْصَنَٰتُ', 'An-Nisa 24', 'An-Nisa 147'),
(6, 'لَّا يُحِبُّ ٱللَّهُ', 'An-Nisa 148', 'Al-Ma\'idah 81'),
(7, 'وَإِذَا سَمِعُوا۟', 'Al-Ma\'idah 82', 'Al-An\'am 110'),
(8, 'وَلَوْ أَنَّنَا', 'Al-An\'am 111', 'Al-A\'raf 87'),
(9, 'قَالَ ٱلْمَلَأُ', 'Al-A\'raf 88', 'Al-Anfal 40'),
(10, 'وَٱعْلَمُوٓا۟', 'Al-Anfal 41', 'At-Tawbah 92'),
(11, 'يَعْتَذِرُونَ', 'At-Tawbah 93', 'Hud 5'),
(12, 'وَمَا مِنْ دَآبَّةٍ', 'Hud 6', 'Yusuf 52'),
(13, 'وَمَآ أُبَرِّئُ', 'Yusuf 53', 'Ibrahim 52'),
(14, 'رُبَمَا', 'Al-Hijr 1', 'An-Nahl 128'),
(15, 'سُبْحَٰنَ ٱلَّذِىٓ', 'Al-Isra 1', 'Al-Kahf 74'),
(16, 'قَالَ أَلَمْ', 'Al-Kahf 75', 'Ta-Ha 135'),
(17, 'ٱقْتَرَبَ لِلنَّاسِ', 'Al-Anbiya 1', 'Al-Hajj 78'),
(18, 'قَدْ أَفْلَحَ', 'Al-Mu\'minun 1', 'Al-Furqan 20'),
(19, 'وَقَالَ ٱلَّذِينَ', 'Al-Furqan 21', 'An-Naml 55'),
(20, 'أَمَّنْ خَلَقَ', 'An-Naml 56', 'Al-Ankabut 45'),
(21, 'ٱتْلُ مَآ أُوحِىَ', 'Al-Ankabut 46', 'Al-Ahzab 30'),
(22, 'وَمَن يَقْنُتْ', 'Al-Ahzab 31', 'Ya-Sin 27'),
(23, 'وَمَآ لِىَ', 'Ya-Sin 28', 'Az-Zumar 31'),
(24, 'فَمَنْ أَظْلَمُ', 'Az-Zumar 32', 'Fussilat 46'),
(25, 'إِلَيْهِ يُرَدُّ', 'Fussilat 47', 'Al-Jathiyah 37'),
(26, 'حمٓ', 'Al-Ahqaf 1', 'Adh-Dhariyat 30'),
(27, 'قَالَ فَمَا', 'Adh-Dhariyat 31', 'Al-Hadid 29'),
(28, 'قَدْ سَمِعَ', 'Al-Mujadila 1', 'At-Tahrim 12'),
(29, 'تَبَارَكَ ٱلَّذِى', 'Al-Mulk 1', 'Al-Mursalat 50'),
(30, 'عَمَّ', 'An-Naba 1', 'An-Nas 6');

-- Student Quran Progress (Sabak, Sabqi, Manzil)
CREATE TABLE `quran_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `remarks` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `juz_number` (`juz_number`),
  CONSTRAINT `quran_progress_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quran_progress_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quran_progress_ibfk_3` FOREIGN KEY (`juz_number`) REFERENCES `quran_juz` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Quran Surahs
CREATE TABLE IF NOT EXISTS `quran_surahs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `surah_number` int(11) NOT NULL,
  `surah_name_ar` varchar(100) DEFAULT NULL,
  `surah_name_en` varchar(100) NOT NULL,
  `ayah_count` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `surah_number` (`surah_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Quran Ayahs
CREATE TABLE IF NOT EXISTS `quran_ayahs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `surah_id` int(11) NOT NULL,
  `ayah_number` int(11) NOT NULL,
  `text_ar` text DEFAULT NULL,
  `page_number` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `surah_ayah_unique` (`surah_id`, `ayah_number`),
  KEY `surah_id` (`surah_id`),
  CONSTRAINT `quran_ayahs_ibfk_1` FOREIGN KEY (`surah_id`) REFERENCES `quran_surahs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sabak Details (New Lesson)
CREATE TABLE `sabak_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `record_date` date NOT NULL,
  `juz_number` int(11) DEFAULT NULL,
  `surah_name` varchar(100) NOT NULL,
  `page_from` int(11) NOT NULL,
  `page_to` int(11) NOT NULL,
  `lines_memorized` int(11) DEFAULT NULL,
  `performance_rating` decimal(3,2) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `remarks` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `teacher_id` (`teacher_id`),
  CONSTRAINT `sabak_records_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sabak_records_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sabqi Details (Recent Revision)
CREATE TABLE `sabqi_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `record_date` date NOT NULL,
  `juz_number` int(11) DEFAULT NULL,
  `surah_name` varchar(100) NOT NULL,
  `page_from` int(11) NOT NULL,
  `page_to` int(11) NOT NULL,
  `accuracy_percentage` decimal(5,2) DEFAULT NULL,
  `mistakes_count` int(11) DEFAULT 0,
  `teacher_id` int(11) DEFAULT NULL,
  `remarks` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `teacher_id` (`teacher_id`),
  CONSTRAINT `sabqi_records_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sabqi_records_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Manzil Details (Complete Revision)
CREATE TABLE `manzil_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `record_date` date NOT NULL,
  `juz_from` int(11) NOT NULL,
  `juz_to` int(11) NOT NULL,
  `completion_time` int(11) DEFAULT NULL COMMENT 'Time in minutes',
  `accuracy_percentage` decimal(5,2) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `remarks` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `teacher_id` (`teacher_id`),
  CONSTRAINT `manzil_records_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `manzil_records_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Exam and Results Module Tables
-- ============================================

-- Exam Types
CREATE TABLE `exam_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_name` varchar(100) NOT NULL,
  `exam_type` enum('monthly','quarterly','half_yearly','annual','special') NOT NULL,
  `description` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Exam Types
INSERT INTO `exam_types` (`exam_name`, `exam_type`, `description`, `status`) VALUES
('Monthly Test', 'monthly', 'Monthly evaluation test', 'active'),
('Quarterly Examination', 'quarterly', 'Quarterly examination', 'active'),
('Half Yearly Examination', 'half_yearly', 'Half yearly examination', 'active'),
('Annual Examination', 'annual', 'Annual final examination', 'active'),
('Special Test', 'special', 'Special evaluation test', 'active');

-- Exams Schedule
CREATE TABLE `exams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_type_id` int(11) NOT NULL,
  `exam_title` varchar(200) NOT NULL,
  `exam_date` date NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `total_marks` int(11) DEFAULT 100,
  `passing_marks` int(11) DEFAULT 40,
  `status` enum('scheduled','ongoing','completed','cancelled') DEFAULT 'scheduled',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `exam_type_id` (`exam_type_id`),
  KEY `class_id` (`class_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exams_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exams_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exam Subjects
CREATE TABLE `exam_subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `total_marks` int(11) NOT NULL,
  `passing_marks` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_id` (`exam_id`),
  CONSTRAINT `exam_subjects_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Student Exam Results
CREATE TABLE `exam_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `obtained_marks` decimal(5,2) DEFAULT NULL,
  `grade` varchar(10) DEFAULT NULL,
  `remarks` text,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_result` (`exam_id`, `student_id`, `subject_id`),
  KEY `exam_id` (`exam_id`),
  KEY `student_id` (`student_id`),
  KEY `subject_id` (`subject_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `exam_results_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_results_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_results_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `exam_subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_results_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- System Logs and Activity
-- ============================================

-- Activity Logs
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `description` text,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- System Settings
CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `description` text,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Settings
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `description`) VALUES
('school_name', 'Minhaj Institute of Qirat & Tajweed', 'School Name'),
('school_address', 'Enter School Address', 'School Address'),
('school_phone', '+92-XXX-XXXXXXX', 'School Contact Number'),
('school_email', 'info@miqt.edu', 'School Email'),
('academic_year', '2024-2025', 'Current Academic Year'),
('attendance_time', '08:00', 'Daily Attendance Time');

COMMIT;
