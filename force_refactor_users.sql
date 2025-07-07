-- =================================================================
-- FORCE REFACTOR USERS TABLE SCRIPT
-- =================================================================
-- Description: This script forcefully modifies the `users` table to
--              make `username` the primary key by dropping the `id`
--              column. It does NOT handle related tables.
-- WARNING: This script will likely produce errors for steps that
--          have already been completed. The main goal is for the
--          final `ADD PRIMARY KEY` step to succeed.
-- =================================================================

USE `bkrnetwork`;

-- Disable foreign key checks to allow dropping a primary key that might be referenced.
SET FOREIGN_KEY_CHECKS=0;

-- The following operations are designed to be run on a database in an
-- unknown state. Some commands are expected to fail if the operation
-- has already been completed in a previous run.

-- Step 1: Drop the primary key constraint if it exists. (Already completed)
-- ALTER TABLE `users` DROP PRIMARY KEY;

-- Step 2: Drop the 'id' column. (Already completed)
-- ALTER TABLE `users` DROP COLUMN `id`;

-- Step 3: Drop the unique index on `username` if it exists. (Already completed)
-- ALTER TABLE `users` DROP INDEX `username`;

-- Step 4: Add `username` as the new primary key.
-- This is the final goal.
ALTER TABLE `users` ADD PRIMARY KEY (`username`);

-- Re-enable foreign key checks.
SET FOREIGN_KEY_CHECKS=1; 