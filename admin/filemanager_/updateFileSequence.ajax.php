<?php
include("../shared/include/environment.inc.php");
if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['sequence'])){
	$id_array=explode("|",$_POST['sequence']);
	$i=0;
	foreach($id_array as $entry_id){
		if(!empty($entry_id)){
			$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_hp_files_ SET file_sequence=".$i." WHERE entry_id=".intval($entry_id)." LIMIT 1");
			$i+=1;
		}
	}
}
?>