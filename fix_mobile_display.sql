-- Complete mobile number fix
-- Run this entire script to fix mobile number display

USE futsal_booking;

-- First, check current state
SELECT 'Current users table:' as info;
SELECT id, username, full_name, mobile FROM users ORDER BY id ASC;

-- Force update mobile numbers for all users
UPDATE users SET mobile = '9841234567' WHERE username = 'admin';
UPDATE users SET mobile = '9856789012' WHERE username = 'john_doe';
UPDATE users SET mobile = '9765432109' WHERE username = 'jane_smith';

-- Check if there are any users without mobile numbers
SELECT 'Users without mobile numbers:' as info;
SELECT id, username, full_name, mobile FROM users WHERE mobile IS NULL OR mobile = '';

-- Show final result
SELECT 'Final users table:' as info;
SELECT id, username, full_name, mobile FROM users ORDER BY id ASC;
