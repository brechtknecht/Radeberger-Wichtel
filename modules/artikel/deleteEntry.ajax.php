<?php
if(isset($_POST['entry_id']) && !empty($_POST['entry_id'])){
	include("../../admin/shared/include/environment.inc.php");
	if(checkPermission("user",$_SESSION['cms_user']['user_id'],"module","news")==false){
		die("Sie haben keine Berechtigung auf dieses Element zuzugreifen!");
	}
	else{
		$_POST['entry_id']=intval($_POST['entry_id']);
		$query="UPDATE _cms_modules_news_ SET entry_deleted=1 WHERE entry_id=".$_POST['entry_id']." LIMIT 1";
		$result=mysqli_query($_SESSION['conn'], $query);
	}
	
}
?>