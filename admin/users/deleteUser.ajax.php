<?php
if(isset($_POST['entry_id']) && !empty($_POST['entry_id'])){
	include("../shared/include/environment.inc.php");
	if(!isset($_SESSION['cms_user']['user_role']) || (isset($_SESSION['cms_user']['user_role']) && $_SESSION['cms_user']['user_role']!="Administrator")){
		die("Sie haben keine Berechtigung auf dieses Element zuzugreifen!");
	}
	$_POST['entry_id']=intval($_POST['entry_id']);
	$query="DELETE FROM _cms_hp_user_ WHERE entry_id=".$_POST['entry_id']." LIMIT 1";
	$result=mysqli_query($_SESSION['conn'], $query);
}
?>