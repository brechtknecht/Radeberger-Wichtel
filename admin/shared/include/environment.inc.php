<?php
error_reporting(0);
//ini_set("display_errors", 0);
//session
session_start();
// /session
header("Content-Type: text/html; charset=utf-8");
 
//global vars
if(!isset($_SESSION['global_vars']['path_to_root'])){
	$path="http://".$_SERVER['HTTP_HOST'];
	$path.=$_SERVER['SCRIPT_NAME'];
	$path=substr($path,0,strpos($path,"admin/"));
	$_SESSION['global_vars']['path_to_root']=$path;
}

/*
//form csrf protection
if(!isset($_SESSION['fid'])){
	$_SESSION['fid'] = md5(time());
}

if(isset($_REQUEST['fid'])){
	if($_REQUEST['fid'] === $_SESSION['fid']){
		$_SESSION['fid'] = md5(time());	
	}
	else{
		header("Location:".$_SERVER['HTTP_REFERER']);
		exit();
	}
}

$_SESSION['fid'] = md5(time());
*/

if(!isset($_SESSION['global_vars']['max_history'])){
	$_SESSION['global_vars']['max_history']=5;
}
// /global vars

//include
include("functions.inc.php");
include("db.inc.php");
include("helper.inc.php");

//check permission
if(!isset($_SESSION['cms_user']) && !strstr($_SERVER['PHP_SELF'],"login") && !strstr($_SERVER['PHP_SELF'],"admin/index.php")){
	header("Location: http://www.radeberger-wichtel.de/admin/");
	exit();
}

function isLoggedIn(){
	if(!isset($_SESSION['cms_user'])){
		echo "<script type=\"text/javascript\">parent.location.href='http://www.radeberger-wichtel.de/admin/'</script>";
	}
}

//ini settings
ini_set('arg_separator.output', '&amp;');
// /ini settings

//connect db
$username="dbo601025058";
$password="5r5zn1oi42TMHTyv";
$host="db601025058.db.1and1.com";
$database="db601025058";

$_SESSION['conn'] = mysqli_connect($host, $username, $password);
mysqli_query($_SESSION['conn'], "SET CHARACTER SET 'utf8'");
mysqli_set_charset($_SESSION['conn'], "utf8");
//select db
mysqli_select_db($_SESSION['conn'], $database);
// /connect db

//define constant -> checking for internal call
define("internalCall",true);

//set languages array
$result_lang=mysqli_query($_SESSION['conn'], "SELECT setting_value FROM _cms_settings_ WHERE setting_key='languages' LIMIT 1");
$error = mysqli_error($_SESSION['conn']);
$_SESSION['lang_array']=array();
if(empty($error)){
	if(mysqli_num_rows($result_lang)>0){
		$row_lang=mysqli_fetch_assoc($result_lang);
		$tmp=explode("|",$row_lang['setting_value']);
		foreach($tmp as $val){
			if(!empty($val) && !strstr($val,"!")){
				array_push($_SESSION['lang_array'],$val);
			}
		}
	}
}
?>
