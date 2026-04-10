-- Migration: Add Staff Role to Users Table
-- Date: 2025-01-XX
-- Description: Adds 'staff' role to users table enum

-- Update users table to include staff role
ALTER TABLE `users` 
MODIFY COLUMN `role` ENUM('principal','vice_principal','coordinator','teacher','admin','student','parent','staff') NOT NULL;

