-- ALTER TABLES
-- alter productid-->pid
-- ALTER TABLE basket change productid pid int(11);
-- ALTER TABLE basket change reserved pcs int(11);
-- ALTER TABLE stock add column reserved int(11);
-- ALTER TABLE basket add column purchased enum('Y','N') default 'N';
ALTER TABLE users change password apikey varchar (60);