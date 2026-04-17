-- Direct mobile fix - copy and paste this entire block in phpMyAdmin

USE futsal_booking;

UPDATE users SET mobile = '9841234567' WHERE username = 'admin';
UPDATE users SET mobile = '9856789012' WHERE username = 'john_doe';  
UPDATE users SET mobile = '9765432109' WHERE username = 'jane_smith';

-- Check result
SELECT username, mobile FROM users;
