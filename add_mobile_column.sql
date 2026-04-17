-- Add mobile column to existing users table
-- Run this script to add the mobile column to your existing database

USE futsal_booking;

-- Add mobile column if it doesn't exist
ALTER TABLE users ADD COLUMN mobile VARCHAR(20) DEFAULT NULL;

-- Update existing sample users with mobile numbers
UPDATE users SET mobile = '9841234567' WHERE username = 'admin';
UPDATE users SET mobile = '9856789012' WHERE username = 'john_doe';
UPDATE users SET mobile = '9765432109' WHERE username = 'jane_smith';

-- Show the updated table structure
DESCRIBE users;
