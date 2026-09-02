-- NETPRO CRM (ISP Management OS) - MySQL Database Schema
CREATE DATABASE IF NOT EXISTS `netpro_crm_isp` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `netpro_crm_isp`;

-- 1. Users & Roles
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `role` ENUM('super_admin', 'noc', 'cs', 'billing', 'technician') DEFAULT 'super_admin',
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Internet Packages
CREATE TABLE IF NOT EXISTS `packages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `speed_mbps` INT NOT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  `default_ppn_mode` ENUM('include', 'exclude') DEFAULT 'include',
  `category` ENUM('home', 'soho', 'corporate') DEFAULT 'home',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Customers
CREATE TABLE IF NOT EXISTS `customers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `cid` VARCHAR(30) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `nik` VARCHAR(20) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `email` VARCHAR(100),
  `address` TEXT NOT NULL,
  `gps_lat` DECIMAL(10,8),
  `gps_lng` DECIMAL(11,8),
  `package_id` INT,
  `ppn_scheme` ENUM('include', 'exclude') DEFAULT 'include',
  `auth_method` ENUM('pppoe', 'hotspot', 'static') DEFAULT 'pppoe',
  `status` ENUM('active', 'suspend', 'churn') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`package_id`) REFERENCES `packages`(`id`) ON DELETE SET NULL
);

-- 4. Billing Invoices
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `invoice_no` VARCHAR(50) NOT NULL UNIQUE,
  `customer_id` INT NOT NULL,
  `billing_period` VARCHAR(20) NOT NULL,
  `dpp_amount` DECIMAL(12,2) NOT NULL,
  `ppn_amount` DECIMAL(12,2) NOT NULL,
  `ppn_mode` ENUM('include', 'exclude') DEFAULT 'include',
  `total_amount` DECIMAL(12,2) NOT NULL,
  `due_date` DATE NOT NULL,
  `paid_date` DATETIME NULL,
  `status` ENUM('unpaid', 'paid', 'overdue', 'cancelled') DEFAULT 'unpaid',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE
);

-- Insert Sample Initial Data
INSERT IGNORE INTO `packages` (`id`, `name`, `speed_mbps`, `price`, `default_ppn_mode`, `category`) VALUES
(1, 'Home Basic 20M', 20, 150000.00, 'include', 'home'),
(2, 'Home Premium 50M', 50, 250000.00, 'include', 'home'),
(3, 'SOHO Platinum 100M', 100, 500000.00, 'include', 'soho');

INSERT IGNORE INTO `customers` (`id`, `cid`, `name`, `nik`, `phone`, `email`, `address`, `gps_lat`, `gps_lng`, `package_id`, `ppn_scheme`, `status`) VALUES
(1, 'CID-991201', 'Budi Wijaya', '3275010912830001', '081234567890', 'budi@gmail.com', 'Jl. Jatiwaringin Raya No. 12, Bekasi', -6.28910000, 106.91820000, 2, 'include', 'active'),
(2, 'CID-991202', 'PT Niaga Sentra', '012345678901000', '081199887766', 'finance@sentraniaga.co.id', 'Komp. Sentra Niaga Blok C2, Jakarta', -6.27500000, 106.93000000, 3, 'exclude', 'active');
