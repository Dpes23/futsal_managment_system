-- Update user ID numbering to start from 100
-- Run this script to update existing user IDs and set auto_increment starting point

USE futsal_booking;

-- Update existing user IDs to start from 100
UPDATE users SET id = id + 99 WHERE id < 100;

-- Set the auto_increment starting point for new users
ALTER TABLE users AUTO_INCREMENT = 200;

-- Show the updated user list
SELECT id, username, full_name, email, mobile FROM users ORDER BY id ASC;
