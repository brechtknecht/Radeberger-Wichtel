<?php
//no external call
if(defined("internalCall")){
	
	if(isset($_POST['entry_id'])){
		//prepare 
		if(isset($_POST['entry_category_new']) && !empty($_POST['entry_category_new'])){
			$_POST['entry_category'] = $_POST['entry_category_new'];	
		}
		
		$fieldArray = array();
		$fieldArray[] = "entry_category";
		$fieldArray[] = "entry_name";
		$fieldArray[] = "entry_description";
		$fieldArray[] = "videos";
		
		if($result = insertUpdateTable("_cms_modules_galleries_", $_POST['entry_id'], $fieldArray, $_SESSION['cms_user']['user_id'])){
			if($_POST['entry_id'] == "new"){
				$_POST['entry_id'] = mysqli_insert_id($_SESSION['conn']);
			}
					
			//change position/sequence
			if(isset($_POST['new_position'], $_POST['position_mode'])){
				if(!empty($_POST['new_position']) && !empty($_POST['position_mode'])){
					//split new_position
					$position_array = explode("|",$_POST['new_position']);
					$new_sequence = $position_array[0];
					$new_parent = $position_array[1];
					$new_id = $position_array[2];
					
					changeSequence("_cms_modules_galleries_", $_POST['position_mode'], $_POST['entry_id'], $new_sequence, $new_parent, $new_id);
				}
			}
		}
		$_GET = $_POST;
	}
}
else{
	die("Fehler!");
}
?>