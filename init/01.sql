CREATE DATABASE IF NOT EXISTS `poracle_database`;
CREATE USER IF NOT EXISTS 'poracle_user'@'%' IDENTIFIED BY 'S0mePassw0rd';
GRANT ALL PRIVILEGES ON poracle_database.* TO 'poracle_user'@'%';