<?php
include("../../shared/include/connect_db.inc.php");
//create module table
$sql = <<<QUERY
CREATE TABLE IF NOT EXISTS `_cms_modules_contacts_` (
  `entry_id` int(11) NOT NULL,
  `main_category` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `vorname` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `plz` varchar(255) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `kundennummer` int(11) NOT NULL,
  `telefon` varchar(255) NOT NULL,
  `mobil` varchar(255) NOT NULL,
  `fax` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `entry_last_usr` int(11) NOT NULL,
  `last_change` datetime NOT NULL,
  PRIMARY KEY  (`entry_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
QUERY;

$result = mysqli_query($_SESSION['conn'], $sql);
$error = mysqli_error($_SESSION['conn']);

if(!empty($error)){
	echo $error;
}
?>
