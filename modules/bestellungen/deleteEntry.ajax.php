<?php
if(isset($_POST['entry_id']) && !empty($_POST['entry_id'])){
	include("../../admin/shared/include/environment.inc.php");
	if(!isset($_SESSION['cms_user']) || checkPermission("user",$_SESSION['cms_user']['user_id'],"module","bestellungen")==false){
		die("Sie haben keine Berechtigung auf dieses Element zuzugreifen!");
	}
	else{
		$_POST['entry_id']=intval($_POST['entry_id']);
		$query="UPDATE _cms_modules_orders_ SET status='del' WHERE entry_id=".$_POST['entry_id']." LIMIT 1";
		$result=mysqli_query($_SESSION['conn'], $query);
		$query="UPDATE _cms_modules_orders_products_ SET status='del' WHERE pid=".$_POST['entry_id'];
		$result=mysqli_query($_SESSION['conn'], $query);
	}
	
}
?>