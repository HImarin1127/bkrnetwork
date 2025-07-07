-- =================================================================
-- REMOVE EXTRA USER COLUMNS SCRIPT
-- =================================================================
-- Description: This script removes all columns from 'department'
--              downwards in the `users` table as per user request.
-- WARNING: This will break application features related to roles,
--          status, and LDAP integration.
-- =================================================================

USE `bkrnetwork`;

-- Disable foreign key checks to avoid errors when dropping columns
-- that might be part of a foreign key relationship (e.g., department_id).
SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE `users`
  DROP COLUMN `department`,
  DROP COLUMN `phone`,
  DROP COLUMN `title`,
  DROP COLUMN `role`,
  DROP COLUMN `status`,
  DROP COLUMN `auth_source`,
  DROP COLUMN `created_at`,
  DROP COLUMN `updated_at`,
  DROP COLUMN `ldap_uid`,
  DROP COLUMN `department_id`;

-- Re-enable foreign key checks.
SET FOREIGN_KEY_CHECKS=1; 