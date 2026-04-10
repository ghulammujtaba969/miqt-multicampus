-- Migration: add student_type to students
ALTER TABLE `students`
  ADD COLUMN `student_type` ENUM('day_scholar','boarder','orphan') NOT NULL DEFAULT 'day_scholar'
  AFTER `gender`;

