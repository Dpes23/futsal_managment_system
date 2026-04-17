-- Direct command to add mobile column
-- Run this in your MySQL client immediately

USE futsal_booking;

-- Add the mobile column
ALTER TABLE users ADD COLUMN mobile VARCHAR(20) DEFAULT NULL;

-- Verify the column was added
DESCRIBE users;
