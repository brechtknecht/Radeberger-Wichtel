<?php
include("../../shared/include/connect_db.inc.php");
//create module table
$sql = <<<QUERY
CREATE TABLE IF NOT EXISTS `_cms_modules_galleries_` (
  `entry_id` int(11) NOT NULL auto_increment,
  `entry_category` varchar(255) NOT NULL default '',
  `entry_name` varchar(255) NOT NULL default '',
  `entry_description` text NOT NULL,
  `entry_last_usr` int(11) NOT NULL default '0',
  `last_change` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`entry_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

QUERY;

$result = mysqli_query($sql);
$error = mysqli_error($_SESSION['conn']);

if(!empty($error)){
	echo $error;
}
?>
