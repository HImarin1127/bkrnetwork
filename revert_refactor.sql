-- =================================================================
-- REVERT USERS TABLE REFACTORING SCRIPT
-- =================================================================
-- Description: This script reverts the changes made by refactor_users_table.sql
--              in case of a partial or failed execution. It drops the new
--              username-based columns from all affected tables.
--              It does NOT restore the users.id column, assuming the
--              main script failed before that point.
-- =================================================================

USE `bkrnetwork`;

SET FOREIGN_KEY_CHECKS=0;

-- Drop the new username-based columns if they exist
ALTER TABLE `equipment_bookings` DROP COLUMN IF EXISTS `user_username`;
ALTER TABLE `form_submissions` DROP COLUMN IF EXISTS `user_username`, DROP COLUMN IF EXISTS `processed_by_username`;
ALTER TABLE `meeting_room_bookings` DROP COLUMN IF EXISTS `user_username`;
ALTER TABLE `pdf_announcement_views` DROP COLUMN IF EXISTS `user_username`;
ALTER TABLE `announcements` DROP COLUMN IF EXISTS `author_username`, DROP COLUMN IF EXISTS `published_by_username`;
ALTER TABLE `announcement_logs` DROP COLUMN IF EXISTS `action_by_username`;
ALTER TABLE `incoming_mail_records` DROP COLUMN IF EXISTS `registrar_username`;

SET FOREIGN_KEY_CHECKS=1;

-- =================================================================
-- END OF REVERT SCRIPT
-- ================================================================= 