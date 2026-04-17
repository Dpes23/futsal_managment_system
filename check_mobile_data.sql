-- Check mobile data in users table
-- Run this to see current mobile numbers

USE futsal_booking;

-- Check current mobile numbers
SELECT id, username, full_name, mobile FROM users ORDER BY id ASC;

-- Check if mobile column exists
DESCRIBE users;

-- If mobile numbers are NULL, update them
UPDATE users SET mobile = '9841234567' WHERE username = 'admin' AND mobile IS NULL;
UPDATE users SET mobile = '9856789012' WHERE username = 'john_doe' AND mobile IS NULL;
UPDATE users SET mobile = '9765432109' WHERE username = 'jane_smith' AND mobile IS NULL;

-- Show updated users again
SELECT id, username, full_name, mobile FROM users ORDER BY id ASC;
