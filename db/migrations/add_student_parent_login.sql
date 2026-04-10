-- Migration: Add Student and Parent Login Support
-- Date: 2025-01-XX
-- Description: Adds student and parent roles, links students to users, creates parents table

-- Step 1: Update users table to include student and parent roles
ALTER TABLE `users` 
MODIFY COLUMN `role` ENUM('principal','vice_principal','coordinator','teacher','admin','student','parent') NOT NULL;

-- Step 2: Add user_id to students table to link students to login accounts
ALTER TABLE `students` 
ADD COLUMN `user_id` INT(11) DEFAULT NULL AFTER `id`,
ADD KEY `user_id` (`user_id`),
ADD CONSTRAINT `students_ibfk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Step 3: Create parents table
CREATE TABLE IF NOT EXISTS `parents` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL,
  `parent_id` VARCHAR(50) NOT NULL,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `cnic` VARCHAR(20) DEFAULT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `relation` ENUM('father','mother','guardian','other') DEFAULT 'father',
  `occupation` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('active','inactive') DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parent_id` (`parent_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `parents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 4: Create parent_student_relation table to link parents with their children
CREATE TABLE IF NOT EXISTS `parent_student_relation` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `parent_id` INT(11) NOT NULL,
  `student_id` INT(11) NOT NULL,
  `relation_type` ENUM('father','mother','guardian','other') DEFAULT 'father',
  `is_primary` TINYINT(1) DEFAULT 0 COMMENT 'Primary contact parent',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_relation` (`parent_id`, `student_id`),
  KEY `parent_id` (`parent_id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `parent_student_relation_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `parent_student_relation_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

