<?php
header("Content-type: text/xml");
include("../shared/include/environment.inc.php");
if(isset($_POST['entry_parent_id'], $_POST['file_cat1'], $_POST['file_cat2'])){
    $_POST['entry_parent_id'] = intval($_POST['entry_parent_id']);
    $query = ("SELECT entry_id, file_save_name FROM _cms_hp_files_ WHERE file_cat1='".mysqli_real_escape_string($_SESSION['conn'], $_POST['file_cat1'])."' AND file_cat2='".mysqli_real_escape_string($_SESSION['conn'], $_POST['file_cat2'])."' AND file_cat3='".mysqli_real_escape_string($_SESSION['conn'], $_POST['file_cat3'])."' AND entry_parent_id=".$_POST['entry_parent_id']);
	$result = mysqli_query($_SESSION['conn'], $query);
	echo mysqli_error($_SESSION['conn']);
	if(mysqli_num_rows($result)>0){
        while($row = mysqli_fetch_assoc($result)){
			$del_file="../../files/".$row['file_save_name'];
			if(file_exists($del_file)){
				unlink($del_file);
			}
			
			
		}
		$result = mysqli_query($_SESSION['conn'], "DELETE FROM _cms_hp_files_ WHERE entry_parent_id=".($_POST['entry_parent_id'])." AND file_cat1='".mysqli_real_escape_string($_SESSION['conn'], $_POST['file_cat1'])."' AND file_cat2='".mysqli_real_escape_string($_SESSION['conn'], $_POST['file_cat2'])."'");
		echo mysqli_error($_SESSION['conn']);
		echo "OK";
	}
    
}
?>
