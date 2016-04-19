<?php
include("../shared/include/environment.inc.php");
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['desc']) && isset($_POST['entry_id'], $_POST['language'])){
	$_POST['desc'] = preg_replace("/\r|\n/s","",$_POST['desc']);
	$result = mysqli_query($_SESSION['conn'], "DELETE FROM _cms_hp_files_desc_ WHERE entry_parent_id=".intval($_POST['entry_id'])." AND language='".$_POST['language']."'");
	$query = "INSERT INTO _cms_hp_files_desc_ SET entry_parent_id=".intval($_POST['entry_id']).",description='".mysqli_real_escape_string($_SESSION['conn'], $_POST['desc'])."',language='".mysqli_real_escape_string($_SESSION['conn'], $_POST['language'])."'";
	$result = mysqli_query($_SESSION['conn'], $query);
	echo $query;
}
else{
	echo "Error";
}
?>