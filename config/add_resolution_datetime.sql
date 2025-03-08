ALTER TABLE violation_reports
ADD COLUMN resolution_datetime DATETIME DEFAULT NULL AFTER status; 