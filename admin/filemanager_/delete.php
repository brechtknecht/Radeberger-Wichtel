<?php
header("Content-type: text/xml");
include("../shared/include/environment.inc.php");
if(isset($_POST['entry_id'])){
    $_POST['entry_id'] = intval($_POST['entry_id']);
    $result = mysqli_query($_SESSION['conn'], "SELECT file_save_name FROM _cms_hp_files_ WHERE entry_id=".($_POST['entry_id'])." LIMIT 1");
    if(mysqli_num_rows($result)>0){
        $row = mysqli_fetch_assoc($result);
        $del_file="../../files/".$row['file_save_name'];
        if(file_exists($del_file)){
	    unlink($del_file);
	}
	$result = mysqli_query($_SESSION['conn'], "DELETE FROM _cms_hp_files_ WHERE entry_id=".($_POST['entry_id'])." LIMIT 1");
	echo "OK";
	
	
    }
    else{
        echo "Fehler: Datei konnte nicht gelöscht werden!";
    }
}
?>
