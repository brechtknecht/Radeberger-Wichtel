<?php
include("../shared/include/environment.inc.php");
if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['entry_id'], $_POST['entry_parent_id'])){
	if($result = mysqli_query($_SESSION['conn'], "CREATE TEMPORARY TABLE zeemes_tmp SELECT * FROM _cms_hp_files_ WHERE entry_id = ".intval($_POST['entry_id']))){
		if($result2 = mysqli_query($_SESSION['conn'], "UPDATE zeemes_tmp SET entry_id = NULL, entry_parent_id = '".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_parent_id'])."', file_cat1='".$_POST['file_cat1']."', file_cat2='".$_POST['file_cat2']."', file_cat3='".$_POST['file_cat3']."'")){
			if($result3 = mysqli_query($_SESSION['conn'], "INSERT INTO _cms_hp_files_ SELECT * FROM zeemes_tmp")){
				echo "OK";		
			}
		}	
	}

}
?>