-- =================================================================
-- database_rebuild.sql
-- Description: Drops and rebuilds the entire bkrnetwork database structure.
-- Warning: This script will completely erase all data in the bkrnetwork database.
-- =================================================================

-- Drop the database if it exists to ensure a clean start
DROP DATABASE IF EXISTS `bkrnetwork`;

-- Create the new database
CREATE DATABASE `bkrnetwork` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Switch to the new database
USE `bkrnetwork`;

-- =================================================================
-- Table structure for table `announcements`
-- =================================================================
CREATE TABLE `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `status` enum('draft','published') DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `published_by` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `attachment_url` varchar(500) DEFAULT NULL,
  `attachment_name` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_group_announcement` tinyint(1) DEFAULT 0,
  `department_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `published_by` (`published_by`),
  KEY `author_id` (`author_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `announcement_logs`
-- =================================================================
CREATE TABLE `announcement_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `announcement_id` int(11) NOT NULL,
  `action` enum('created','updated','published','unpublished','deleted') NOT NULL,
  `action_by` int(11) NOT NULL,
  `action_details` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `announcement_id` (`announcement_id`),
  KEY `action_by` (`action_by`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `announcement_types`
-- =================================================================
CREATE TABLE `announcement_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_code` (`type_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `department_contacts`
-- =================================================================
CREATE TABLE `department_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(100) NOT NULL,
  `building` varchar(50) DEFAULT NULL,
  `floor_number` int(11) NOT NULL,
  `extension_range` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `departments`
-- =================================================================
CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `employee_seats`
-- =================================================================
CREATE TABLE `employee_seats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_name` varchar(50) NOT NULL,
  `floor_number` int(11) NOT NULL,
  `seat_number` varchar(20) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `extension_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `equipment_bookings`
-- =================================================================
CREATE TABLE `equipment_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `equipment_name` varchar(100) NOT NULL,
  `booking_date` date NOT NULL,
  `return_date` date NOT NULL,
  `purpose` text NOT NULL,
  `status` enum('pending','approved','rejected','returned') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `extension_numbers`
-- =================================================================
CREATE TABLE `extension_numbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `extension_number` varchar(20) NOT NULL,
  `employee_name` varchar(50) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `floor_info`
-- =================================================================
CREATE TABLE `floor_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `floor_number` int(11) NOT NULL,
  `floor_name` varchar(100) NOT NULL,
  `floor_description` text DEFAULT NULL,
  `floor_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `form_submissions`
-- =================================================================
CREATE TABLE `form_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `form_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `form_data` longtext NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `processed_by` (`processed_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `holiday_calendar`
-- =================================================================
CREATE TABLE `holiday_calendar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(4) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `fetch_date` timestamp NULL DEFAULT NULL,
  `holiday_data` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `year` (`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `incoming_mail_records`
-- =================================================================
CREATE TABLE `incoming_mail_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tracking_number` varchar(255) DEFAULT NULL,
  `mail_type` varchar(50) NOT NULL,
  `sender_name` varchar(100) NOT NULL,
  `sender_company` varchar(200) DEFAULT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `recipient_department` varchar(100) DEFAULT NULL,
  `received_date` date NOT NULL,
  `received_time` time DEFAULT NULL,
  `content_description` text DEFAULT NULL,
  `urgent` tinyint(1) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `status` varchar(50) DEFAULT '已收件',
  `registrar_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `tracking_number` (`tracking_number`),
  KEY `received_date` (`received_date`),
  KEY `status` (`status`),
  KEY `registrar_id` (`registrar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `mail_records`
-- =================================================================
CREATE TABLE `mail_records` (
  `mail_code` varchar(50) NOT NULL,
  `mail_type` varchar(50) NOT NULL,
  `sender_name` varchar(100) NOT NULL,
  `sender_ext` varchar(20) DEFAULT NULL,
  `receiver_name` varchar(100) NOT NULL,
  `receiver_address` text NOT NULL,
  `receiver_phone` varchar(20) DEFAULT NULL,
  `declare_department` varchar(100) DEFAULT NULL,
  `registrar_id` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`mail_code`),
  KEY `registrar_id` (`registrar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `mail_records_backup`
-- =================================================================
CREATE TABLE `mail_records_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `mail_code` varchar(50) DEFAULT NULL,
  `mail_type` varchar(50) NOT NULL,
  `sender_name` varchar(100) NOT NULL,
  `sender_ext` varchar(20) DEFAULT NULL,
  `receiver_name` varchar(100) NOT NULL,
  `receiver_address` text NOT NULL,
  `receiver_phone` varchar(20) DEFAULT NULL,
  `declare_department` varchar(100) DEFAULT NULL,
  `item_count` int(11) DEFAULT 1,
  `postage` decimal(10,2) DEFAULT 0.00,
  `tracking_number` varchar(100) DEFAULT NULL,
  `status` enum('草稿','已送出','已寄達') DEFAULT '已送出',
  `notes` text DEFAULT NULL,
  `registrar_id` int(11) DEFAULT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `meeting_room_bookings`
-- =================================================================
CREATE TABLE `meeting_room_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `purpose` text NOT NULL,
  `attendees_count` int(11) DEFAULT 1,
  `status` enum('pending','approved','rejected','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `pdf_announcement_categories`
-- =================================================================
CREATE TABLE `pdf_announcement_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `pdf_announcement_views`
-- =================================================================
CREATE TABLE `pdf_announcement_views` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `announcement_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `announcement_id` (`announcement_id`),
  KEY `user_id` (`user_id`),
  KEY `viewed_at` (`viewed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `pdf_announcements`
-- =================================================================
CREATE TABLE `pdf_announcements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_path` (`file_path`),
  KEY `category` (`category`),
  KEY `display_order` (`display_order`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `users`
-- =================================================================
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `department` enum('總管理處','資訊部','財務部','發行部') DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `status` enum('active','inactive') DEFAULT 'active',
  `auth_source` enum('local','ldap') DEFAULT 'local',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ldap_uid` varchar(100) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Note on `users`.department: The enum values were garbled in the screenshot ('??????','???','???','???').
-- I have used placeholders. Please update them with correct department names.


-- =================================================================
-- Table structure for table `users_backup`
-- =================================================================
CREATE TABLE `users_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `department` enum('總管理處','資訊部','財務部','發行部') DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `status` enum('active','inactive') DEFAULT 'active',
  `auth_source` enum('local','ldap') DEFAULT 'local',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ldap_uid` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- Table structure for table `users_new`
-- =================================================================
CREATE TABLE `users_new` (
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 