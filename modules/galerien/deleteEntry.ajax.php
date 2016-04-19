<?php
if(isset($_POST['entry_id']) && !empty($_POST['entry_id'])){
	include("../../admin/shared/include/environment.inc.php");
	if(checkPermission("user",$_SESSION['cms_user']['user_id'],"module","galerien")==false){
		die("Sie haben keine Berechtigung auf dieses Element zuzugreifen!");
	}
	else{
		$_POST['entry_id']=intval($_POST['entry_id']);
		$query="DELETE FROM _cms_modules_galleries_ WHERE entry_id=".$_POST['entry_id']." LIMIT 1";
		$result=mysqli_query($_SESSION['conn'], $query);
		$query="SELECT entry_id,file_save_name FROM _cms_hp_files_ WHERE entry_parent_id=".$_POST['entry_id']." AND file_cat1='module' AND file_cat2='galleries'";
		$result=mysqli_query($_SESSION['conn'], $query);
		if(mysqli_num_rows($result)>0){
			while($row=mysqli_fetch_assoc($result)){
				if(file_exists("../../files/".$row['file_save_name'])){
					if(unlink("../../files/".$row['file_save_name'])){
						$result_del=mysqli_query($_SESSION['conn'], "DELETE FROM _cms_hp_files_ WHERE entry_id=".$row['entry_id']);
					}
				}
			}
		}
	}
	
}
?>