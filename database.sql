-- Database Schema for Jaspel Automation System (JAS)
CREATE DATABASE IF NOT EXISTS `rsgm_jaspel` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rsgm_jaspel`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default administrator (admin & admin1 / admin123)
-- Password hash generated using password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$vY3vM2l6QvJqYFwzM9K4eOpO9x7G4jB.b5nK2yB39tU14p11y.2gG', 'admin'),
(2, 'admin1', '$2y$10$vY3vM2l6QvJqYFwzM9K4eOpO9x7G4jB.b5nK2yB39tU14p11y.2gG', 'admin')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- 2. Departments Table
CREATE TABLE IF NOT EXISTS `departments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `department_name` VARCHAR(100) NOT NULL UNIQUE,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default departments (Generic)
INSERT INTO `departments` (`id`, `department_name`, `status`) VALUES
(1, 'Poli Umum', 'active'),
(2, 'Poli Gigi', 'active'),
(3, 'Instalasi Radiologi', 'active')
ON DUPLICATE KEY UPDATE `department_name`=`department_name`;

-- 3. DPJP (Doctor) Table
CREATE TABLE IF NOT EXISTS `dpjp` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `doctor_name` VARCHAR(150) NOT NULL,
  `department_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  UNIQUE KEY `doctor_dept_unique` (`doctor_name`, `department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. DPJP Aliases Table
CREATE TABLE IF NOT EXISTS `dpjp_aliases` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `dpjp_id` INT NOT NULL,
  `alias_name` VARCHAR(150) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`dpjp_id`) REFERENCES `dpjp` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Settings Table
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(50) NOT NULL UNIQUE,
  `setting_value` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default settings (Generic)
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('JASPEL_PERCENTAGE', '20'),
('RKG_JASPEL_PERCENTAGE', '15'),
('MAX_UPLOAD_SIZE', '20'),
('ALLOWED_EXTENSION', 'xlsx'),
('APP_NAME', 'Sistem Jaspel'),
('COMPANY', 'Rumah Sakit Umum')
ON DUPLICATE KEY UPDATE `setting_key`=`setting_key`;

-- 6. Import History Table
CREATE TABLE IF NOT EXISTS `import_history` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `file_name` VARCHAR(255) NOT NULL,
  `output_file` VARCHAR(255) NOT NULL,
  `total_rows` INT DEFAULT 0,
  `success_rows` INT DEFAULT 0,
  `failed_rows` INT DEFAULT 0,
  `total_departments` INT DEFAULT 0,
  `total_doctors` INT DEFAULT 0,
  `total_jaspel` DECIMAL(15,2) DEFAULT 0.00,
  `duration_seconds` DECIMAL(8,2) DEFAULT 0.00,
  `file_size_mb` DECIMAL(8,2) DEFAULT 0.00,
  `imported_by` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`imported_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Import Errors Table
CREATE TABLE IF NOT EXISTS `import_errors` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `history_id` INT NOT NULL,
  `row_number` INT NOT NULL,
  `doctor_name` VARCHAR(255) NOT NULL,
  `error_message` TEXT NOT NULL,
  FOREIGN KEY (`history_id`) REFERENCES `import_history` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Audit Logs Table
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `details` TEXT NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. System Logs Table
CREATE TABLE IF NOT EXISTS `system_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `severity` VARCHAR(20) DEFAULT 'error',
  `error_message` TEXT NOT NULL,
  `stack_trace` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

