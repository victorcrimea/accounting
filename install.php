<?
require_once "db.php";

$db->query("CREATE TABLE IF NOT EXISTS `accounting_users` (
  `uid` int(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(128) NOT NULL,
  `surname` varchar(128) NOT NULL,
  `fin_group` int(2) NOT NULL
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';");





?>
