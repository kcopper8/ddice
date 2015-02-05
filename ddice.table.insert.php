<?
// require fix for use
$ddice_db_id = "database_id";
$ddice_db_password = "database_password";
$ddice_db_database = "database_dbname";
// require fix for use


$ddice_conn = mysql_connect('localhost', $ddice_db_id, $ddice_db_password);
mysql_select_db ($ddice_db_database, $ddice_conn);

$table_query = "CREATE TABLE IF NOT EXISTS `ddice` (
  `key` varchar(50) NOT NULL,
  `value` varchar(100) DEFAULT NULL,
  `type` varchar(100) NOT NULL,
  `reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rolled_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`key`)
) ;";


?>