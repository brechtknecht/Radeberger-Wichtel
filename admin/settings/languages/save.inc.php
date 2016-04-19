<?php
//no external call
if(defined("internalCall")){
	if(isset($_SESSION['lang_array'])){
		unset($_SESSION['lang_array']);
	}
	$_SESSION['lang_array']=array();
	$save_str="|";
	foreach($_POST as $key=>$val){
		if(strstr($key,"lang_")){
			$save_str.=$val."|";
			array_push($_SESSION['lang_array'],$val);
		}
	}
	$save_str.="!".$_POST['default_lang']."!";
	$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_settings_ SET setting_value='".mysqli_real_escape_string($_SESSION['conn'], $save_str)."' WHERE setting_key='languages'");
}
else{
	die("Error!");
}
?>