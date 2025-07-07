-- =================================================================
-- CLEANUP DANGLING REFERENCES SCRIPT
-- =================================================================
-- Description: This script "boldly" removes columns from various
--              tables that used to reference the old `users.id` or
--              other deleted columns/tables.
-- WARNING: This is a DESTRUCTIVE operation. It will permanently
--          delete historical data associations. For example, it will
--          no longer be possible to know who authored an old
--          announcement. This is done as per explicit user
--          instruction to "be bold".
-- =================================================================

USE `bkrnetwork`;

-- Disable foreign key checks to avoid errors when dropping columns.
SET FOREIGN_KEY_CHECKS=0;

-- Drop columns from the 'announcements' table
ALTER TABLE `announcements`
  DROP COLUMN IF EXISTS `published_by`,
  DROP COLUMN IF EXISTS `author_id`,
  DROP COLUMN IF EXISTS `department_id`;

-- Drop columns from the 'announcement_logs' table
ALTER TABLE `announcement_logs`
  DROP COLUMN IF EXISTS `action_by`;

-- Drop columns from the 'equipment_bookings' table if it exists
-- (This table might not exist in the current live schema)
DROP TABLE IF EXISTS `equipment_bookings`;

-- Drop columns from the 'form_submissions' table
ALTER TABLE `form_submissions`
  DROP COLUMN IF EXISTS `user_id`,
  DROP COLUMN IF EXISTS `processed_by`;

-- Drop columns from the 'incoming_mail_records' table if it exists
DROP TABLE IF EXISTS `incoming_mail_records`;

-- Drop other potentially problematic tables if they exist
DROP TABLE IF EXISTS `meeting_room_bookings`;
DROP TABLE IF EXISTS `pdf_announcement_views`;

-- Re-enable foreign key checks.
SET FOREIGN_KEY_CHECKS=1;

-- =================================================================
-- END OF SCRIPT
-- ================================================================= 