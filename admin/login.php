<?php
$base_url="http://".$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'],"login"));
include("shared/include/environment.inc.php");
if($_SERVER['REQUEST_METHOD']=="POST"){
	$result=mysqli_query($_SESSION['conn'], "SELECT entry_id,user_name,user_fname,user_role,user_supervisor,user_last_login FROM _cms_hp_user_ WHERE md5(user_login)='".md5($_POST['username'])."' AND user_password='".md5($_POST['password'])."' AND user_type='intern' LIMIT 1");
	if(mysqli_num_rows($result)>0){
		$row=mysqli_fetch_assoc($result);
		//user data
		$_SESSION['cms_user']['user_id']=$row['entry_id'];
		$_SESSION['cms_user']['user_name']=$row['user_fname']." ".$row['user_name'];
		$_SESSION['cms_user']['user_role']=$row['user_role'];
		$_SESSION['cms_user']['user_supervisor']=$row['user_supervisor'];
		$_SESSION['cms_user']['user_last_login']=$row['user_last_login'];
		$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_hp_user_ SET user_last_login=NOW(),user_login_count=user_login_count+1 WHERE entry_id='".mysqli_real_escape_string($_SESSION['conn'], $row['entry_id'])."'");
		//get permissions
		if($_SESSION['cms_user']['user_role']!="Administrator"){
			//pages
			$_SESSION['cms_user']['pages']=array();
			$query="SELECT perm_value FROM _cms_perm_ WHERE perm_cat='user' AND user_id=".$_SESSION['cms_user']['user_id']." AND perm_name='page'";
			$result=mysqli_query($_SESSION['conn'], $query);
			if(mysqli_num_rows($result)>0){
				while($row=mysqli_fetch_assoc($result)){
					$_SESSION['cms_user']['pages'][]=$row['perm_value'];
				}
			}
			//modules
			$_SESSION['cms_user']['modules']=array();
			$query="SELECT perm_value, perm_arg_name, perm_arg_value FROM _cms_perm_ WHERE perm_cat='user' AND user_id=".$_SESSION['cms_user']['user_id']." AND perm_name='module'";
			$result=mysqli_query($_SESSION['conn'], $query);
			if(mysqli_num_rows($result)>0){
				while($row=mysqli_fetch_assoc($result)){
					if(!empty($row['perm_arg_name']) && !empty($row['perm_arg_value'])){
						$_SESSION['cms_user']['modules'][$row['perm_value']][] = array($row['perm_arg_name'], $row['perm_arg_value']);
					}
				}
			}
		}
	}
	
}
else{
	unset($_SESSION['cms_user']);
	session_destroy();
}

//echo print_r($_SESSION['cms_user']['modules']);
header("Location:".$base_url);
?>