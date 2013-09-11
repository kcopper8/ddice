CREATE TABLE IF NOT EXISTS `ddice` (
  `key` varchar(50) NOT NULL,
  `value` varchar(100) DEFAULT NULL,
  `type` varchar(100) NOT NULL,
  `reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rolled_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`key`)
) ;