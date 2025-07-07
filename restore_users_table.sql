-- =================================================================
-- RESTORE USERS TABLE SCRIPT
-- =================================================================
-- Description: This script drops the existing `users` table and rebuilds it
--              with the structure and content provided by the user.
--              This is intended to fix a corrupted state from a failed
--              migration.
-- =================================================================

USE `bkrnetwork`;

-- Drop the table if it exists to ensure a clean start
DROP TABLE IF EXISTS `users`;

-- Create the table with the original structure (id as PK)
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `department` varchar(50) DEFAULT NULL,
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

-- Insert the data from the user's screenshot
INSERT INTO `users` (`username`, `password`, `name`, `email`, `role`, `auth_source`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2', '最高系統管理員-系統管理員', 'admin@republic.com.tw', 'admin', 'local'),
('beiywu', 'LDAP_AUTH_ONLY', '一般-資訊部-吳貝怡', 'beiywu@bookrep.com.tw', 'user', 'ldap'),
('editor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2', '一般-編輯人員', 'editor@republic.com.tw', 'user', 'local'),
('finance_manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2', '一般-??????', 'finance@republic.com.tw', 'user', 'local'),
('hr_manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2', '一般-?????????', 'hr@republic.com.tw', 'user', 'local'),
('it_staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2', '一般-??????', 'it@republic.com.tw', 'user', 'local'),
('ldapGA', 'LDAP_AUTH_ONLY', '一般-總務人資部-陳延勳', 'ldapGA@bookrep.com.tw', 'user', 'ldap'),
('ldapmuma', 'LDAP_AUTH_ONLY', '一般-木馬-駒哥', 'ldapmuma@bookrep.com.tw', 'user', 'ldap'),
('ldapnormal', 'LDAP_AUTH_ONLY', '一般-陳延勳', 'ldapnormal@bookrep.com.tw', 'user', 'ldap'),
('ldapuser', 'LDAP_AUTH_ONLY', '一般-陳延勳', 'ldapuser@bookrep.com.tw', 'user', 'ldap'),
('ldapyen', 'LDAP_AUTH_ONLY', '一般-財務部-阿勳', 'ldapyen@bookrep.com.tw', 'user', 'ldap'),
('test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2', '一般-測試使用者', 'test@republic.com.tw', 'user', 'local'),
('tina861025', 'LDAP_AUTH_ONLY', '一般-資訊部-徐婷婷', 'tina861025@bookrep.com.tw', 'user', 'ldap'),
('willkao', 'LDAP_AUTH_ONLY', '一般-資訊部-高正安', 'willkao@bookrep.com.tw', 'user', 'ldap');

-- =================================================================
-- END OF RESTORE SCRIPT
-- ================================================================= 