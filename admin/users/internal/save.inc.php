<?php
//no external call
if(defined("internalCall")){
	if(isset($_POST['user_password']) && !empty($_POST['user_password'])){
		$_POST['user_clear_password']=$_POST['user_password'];
		$_POST['user_password']=md5($_POST['user_password']);
	}
	
	if($_POST['entry_id']=="new"){
		$query="INSERT INTO _cms_hp_user_ SET";	
	}
	else{
		$query="UPDATE _cms_hp_user_ SET";	
	}
	$query.= " user_name='".mysqli_real_escape_string($_SESSION['conn'], $_POST['user_name'])."'";
	$query.= ", user_fname='".mysqli_real_escape_string($_SESSION['conn'], $_POST['user_fname'])."'";
	$query.= ", user_email='".mysqli_real_escape_string($_SESSION['conn'], $_POST['user_email'])."'";
	$query.= ", user_type='".mysqli_real_escape_string($_SESSION['conn'], $_POST['user_type'])."'";
	$query.= ", user_login='".mysqli_real_escape_string($_SESSION['conn'], $_POST['user_login'])."'";
	if(isset($_POST['user_password']) && !empty($_POST['user_password'])){
		$query.= ", user_password='".mysqli_real_escape_string($_SESSION['conn'], $_POST['user_password'])."'";
		$query.= ", user_clear_password='".mysqli_real_escape_string($_SESSION['conn'], $_POST['user_clear_password'])."'";
	}
	$query.= ", user_role='".mysqli_real_escape_string($_SESSION['conn'], $_POST['user_role'])."'";
	$query.= ", user_supervisor='".mysqli_real_escape_string($_SESSION['conn'], $_POST['user_supervisor'])."'";
	//$query.= ", entry_last_usr='".mysqli_real_escape_string($_SESSION['conn'], $_SESSION['cms_user']['user_id'])."'";
	$query.= ", last_change=NOW()";
	
	if($_POST['entry_id']!="new"){
		$query.=" WHERE entry_id='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_id'])."' LIMIT 1";
	}
	//echo $query;
	$result=mysqli_query($_SESSION['conn'], $query);
	echo mysqli_error($_SESSION['conn']);
	if($_POST['entry_id']=="new"){
		$_POST['entry_id']=mysqli_insert_id($_SESSION['conn']);
	}
		
	//save permissions
	//del old
	$result=mysqli_query($_SESSION['conn'], "DELETE FROM _cms_perm_ WHERE perm_cat='user' AND user_id='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_id'])."'");
	
	if(isset($_POST['user_perm_pages_edit']) && is_array($_POST['user_perm_pages_edit'])){
		foreach($_POST['user_perm_pages_edit'] as $val){
			if(!empty($val)){
				$result=mysqli_query($_SESSION['conn'], "INSERT INTO _cms_perm_ SET perm_cat='user',user_id='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_id'])."',perm_name='page',perm_value='".mysqli_real_escape_string($_SESSION['conn'], $val)."',last_change=NOW()");
			}
		}
	}
	
	//modules
	foreach($_POST as $key=>$val){
		if(strstr($key,"user_perm_modules_edit") && !strstr($key,"arg_name") && !strstr($key,"arg_value")){
			if(is_array($val)){
				foreach($val as $key2=>$val2){
					$result=mysqli_query($_SESSION['conn'], "INSERT INTO _cms_perm_ SET perm_cat='user',user_id='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_id'])."',perm_name='module',perm_value='".mysqli_real_escape_string($_SESSION['conn'], $val2)."', perm_arg_name='function', perm_arg_value='".mysqli_real_escape_string($_SESSION['conn'], $key2)."',last_change=NOW()");	
				}
			}
			else{
				$result=mysqli_query($_SESSION['conn'], "INSERT INTO _cms_perm_ SET perm_cat='user',user_id='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_id'])."',perm_name='module',perm_value='".mysqli_real_escape_string($_SESSION['conn'], $val)."',last_change=NOW()");
			}
		}
		if(strstr($key,"user_perm_modules_filter")){
			if(is_array($val)){
				foreach($val as $key2=>$val2){
					if(is_array($val2)){
						foreach($val2 as $val3){
							if(strstr($val3,"|")){
								$val3_array = explode("|",$val3);
								$result=mysqli_query($_SESSION['conn'], "INSERT INTO _cms_perm_ SET perm_cat='user',user_id='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_id'])."',perm_name='module',perm_value='".mysqli_real_escape_string($_SESSION['conn'], $key2)."',perm_arg_name='".mysqli_real_escape_string($_SESSION['conn'], $val3_array[0])."', perm_arg_value='".mysqli_real_escape_string($_SESSION['conn'], $val3_array[1])."', last_change=NOW()");
							}
						}
					}
					else{
						if(strstr($val2,"|")){
							$val2_array = explode("|",$val2);
							$result=mysqli_query($_SESSION['conn'], "INSERT INTO _cms_perm_ SET perm_cat='user',user_id='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_id'])."',perm_name='module',perm_value='".mysqli_real_escape_string($_SESSION['conn'], $key2)."',perm_arg_name='".mysqli_real_escape_string($_SESSION['conn'], $val2_array[0])."', perm_arg_value='".mysqli_real_escape_string($_SESSION['conn'], $val2_array[1])."', last_change=NOW()");
						}
					}
				}
			}
		}
	}
	
	$_GET=$_POST;
}
else{
	die("Fehler!");
}
?>