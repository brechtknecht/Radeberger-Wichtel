<?php
if(isset($_POST['entry_id']) && !empty($_POST['entry_id'])){
	include("../../shared/include/environment.inc.php");
	if(!checkPermission("user",$_SESSION['cms_user']['user_id'],"page",$_REQUEST['entry_id']) && !checkPermission("user",$_SESSION['cms_user']['user_id'],"page","all")){
		die("Sie haben keine Berechtigung auf dieses Element zuzugreifen!");
	}
	$_POST['entry_id']=intval($_POST['entry_id']);
	$query="DELETE FROM _cms_hp_navigation_ WHERE entry_id=".$_POST['entry_id']." OR entry_parent_id=".$_POST['entry_id'];
	$result=mysqli_query($_SESSION['conn'], $query);
	$query="DELETE FROM _cms_hp_pages_ WHERE entry_parent_id=".$_POST['entry_id'];
	$result=mysqli_query($_SESSION['conn'], $query);
}
?>