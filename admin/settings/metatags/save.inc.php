<?php
//no external call
if(defined("internalCall"))
	{
	$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_settings_ SET setting_value='".$_POST['entry_title']."' WHERE setting_key='entry_title' LIMIT 1");
	$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_settings_ SET setting_value='".$_POST['entry_meta_description']."' WHERE setting_key='entry_meta_description' LIMIT 1");
	$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_settings_ SET setting_value='".$_POST['entry_meta_keywords']."' WHERE setting_key='entry_meta_keywords' LIMIT 1");
	}
else
	{
	die("Error!");
	}
?>