-- =================================================================
-- USERS TABLE REFACTORING SCRIPT
-- =================================================================
-- Description: This script refactors the `users` table to use `username`
-- as the primary key instead of `id`. It also migrates all
-- foreign key references in other tables.
--
-- WARNING: BACK UP YOUR DATABASE BEFORE RUNNING THIS SCRIPT.
-- =================================================================

USE `bkrnetwork`;

-- Disable foreign key checks to avoid errors during modification
SET FOREIGN_KEY_CHECKS=0;

-- == Step 1: Add new username-based columns to referencing tables ==
-- For each table that references `users.id`, add a new `..._username` column.
-- The following two lines are commented out because they were successfully executed in a previous partial run.
-- ALTER TABLE `announcements` ADD `author_username` VARCHAR(50);
-- ALTER TABLE `announcements` ADD `published_by_username` VARCHAR(50);
-- The following tables do not exist in the current schema, so they are commented out.
-- ALTER TABLE `meeting_room_bookings` ADD `user_username` VARCHAR(50);
-- ALTER TABLE `equipment_bookings` ADD `user_username` VARCHAR(50);
-- ALTER TABLE `mail_records` ADD `registered_by_username` VARCHAR(50);
-- ALTER TABLE `leave_requests` ADD `user_username` VARCHAR(50);

-- == Step 2: Migrate data from old ID-based columns to new username-based columns ==
-- Populate the new `..._username` columns with the corresponding usernames from the `users` table.
UPDATE `announcements` a JOIN `users` u ON a.author_id = u.id SET a.author_username = u.username;
UPDATE `announcements` a JOIN `users` u ON a.published_by = u.id SET a.published_by_username = u.username;
-- UPDATE `meeting_room_bookings` mrb JOIN `users` u ON mrb.user_id = u.id SET mrb.user_username = u.username;
-- UPDATE `equipment_bookings` eb JOIN `users` u ON eb.user_id = u.id SET eb.user_username = u.username;
-- UPDATE `mail_records` mr JOIN `users` u ON mr.registered_by_id = u.id SET mr.registered_by_username = u.username;
-- UPDATE `leave_requests` lr JOIN `users` u ON lr.user_id = u.id SET lr.user_username = u.username;

-- == Step 3: Refactor the `users` table ==
-- Drop the old primary key, remove the `id` column, and set `username` as the new primary key.
-- We must drop the auto_increment property before dropping the key.
ALTER TABLE `users` MODIFY `id` INT(11) NOT NULL;
ALTER TABLE `users` DROP PRIMARY KEY;
ALTER TABLE `users` DROP COLUMN `id`;
ALTER TABLE `users` DROP INDEX `username`;
ALTER TABLE `users` ADD PRIMARY KEY (`username`);

-- == Step 4: Drop old ID-based columns from referencing tables ==
-- Remove the now-redundant `..._id` foreign key columns.
ALTER TABLE `announcements` DROP COLUMN `author_id`;
ALTER TABLE `announcements` DROP COLUMN `published_by`;
-- ALTER TABLE `meeting_room_bookings` DROP COLUMN `user_id`;
-- ALTER TABLE `equipment_bookings` DROP COLUMN `user_id`;
-- ALTER TABLE `mail_records` DROP COLUMN `registered_by_id`;
-- ALTER TABLE `leave_requests` DROP COLUMN `user_id`;

-- == Step 5: Drop foreign key constraints that reference the old `id` column
-- The following lines are commented out as they were successfully executed in a previous partial run.
-- ALTER TABLE `announcements` DROP FOREIGN KEY `announcements_ibfk_1`;
-- ALTER TABLE `announcements` DROP FOREIGN KEY `announcements_ibfk_2`;
-- ALTER TABLE `meeting_room_bookings` DROP FOREIGN KEY `meeting_room_bookings_ibfk_1`;
-- ALTER TABLE `equipment_bookings` DROP FOREIGN KEY `equipment_bookings_ibfk_1`;
-- ALTER TABLE `mail_records` DROP FOREIGN KEY `mail_records_ibfk_1`;
-- ALTER TABLE `leave_requests` DROP FOREIGN KEY `leave_requests_ibfk_1`;

-- Enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;

-- == Step 6: Add new foreign key constraints ==
ALTER TABLE `announcements` ADD CONSTRAINT `fk_author_username` FOREIGN KEY (`author_username`) REFERENCES `users` (`username`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `announcements` ADD CONSTRAINT `fk_published_by_username` FOREIGN KEY (`published_by_username`) REFERENCES `users` (`username`) ON DELETE SET NULL ON UPDATE CASCADE;
-- ALTER TABLE `meeting_room_bookings` ADD CONSTRAINT `fk_booking_user_username` FOREIGN KEY (`user_username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;
-- ALTER TABLE `equipment_bookings` ADD CONSTRAINT `fk_equipment_user_username` FOREIGN KEY (`user_username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;
-- ALTER TABLE `mail_records` ADD CONSTRAINT `fk_mail_registered_by_username` FOREIGN KEY (`registered_by_username`) REFERENCES `users` (`username`) ON DELETE SET NULL ON UPDATE CASCADE;
-- ALTER TABLE `leave_requests` ADD CONSTRAINT `fk_leave_user_username` FOREIGN KEY (`user_username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

-- =================================================================
-- END OF SCRIPT
-- =================================================================

-- =================================================================
-- SCRIPT EXECUTION FINISHED
-- =================================================================
-- Next Step: Update the application code (PHP files) to use usernames
-- instead of IDs for all user-related operations.
-- ================================================================= 