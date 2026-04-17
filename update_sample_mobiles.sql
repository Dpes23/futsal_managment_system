-- Update existing sample users with mobile numbers
-- Run this script to add mobile numbers to existing users

USE futsal_booking;

-- Update existing users with mobile numbers
UPDATE users SET mobile = '9841234567' WHERE username = 'admin';
UPDATE users SET mobile = '9856789012' WHERE username = 'john_doe';
UPDATE users SET mobile = '9765432109' WHERE username = 'jane_smith';

-- Show updated users
SELECT id, username, full_name, mobile FROM users ORDER BY id ASC;
