<?php
function insertUpdateTable($tablename, $uid, $field_array, $user_id){
	if(!empty($tablename) && !empty($uid) && !empty($field_array) && !empty($user_id)){
		$query = "";
		if($uid != "new"){
			$query.= "UPDATE ";	
		}
		else{
			$query.= "INSERT INTO ";
		}	
		$query.= mysqli_real_escape_string($_SESSION['conn'], $tablename)." SET ";
		foreach($field_array as $field){
			if(isset($_POST[$field])){
				$query.= mysqli_real_escape_string($_SESSION['conn'], $field)."=\"".mysqli_real_escape_string($_SESSION['conn'], $_POST[$field])."\", ";	
			}
		}
		$query.= "last_change=NOW(), ";
		$query.= "entry_last_usr=\"".mysqli_real_escape_string($_SESSION['conn'], $user_id)."\" ";
		if($uid != "new"){
			$query.= " WHERE entry_id=\"".mysqli_real_escape_string($_SESSION['conn'], $uid)."\"";	
		}
		
		if($result = mysqli_query($_SESSION['conn'], $query)){
			return $result;	
		}
		else{
			echo mysqli_error($_SESSION['conn']);
		}
	}
	return false;
}

function changeSequence($tablename, $mode, $uid, $new_position, $new_parent, $new_id){
	if($mode == "before" || $mode == "behind"){
		$query = "UPDATE ".mysqli_real_escape_string($_SESSION['conn'], $tablename)." SET ";
		if($mode == "before"){
			$query.= "entry_sequence=entry_sequence+1 ";
			$query.= " WHERE entry_sequence>='".mysqli_real_escape_string($_SESSION['conn'], $new_position)."'";
		}
		if($mode == "behind"){
			$query.= "entry_sequence=entry_sequence-1 ";
			$query.= " WHERE entry_sequence<='".mysqli_real_escape_string($_SESSION['conn'], $new_position)."'";
		}
		mysqli_query($_SESSION['conn'], $query);
		mysqli_query($_SESSION['conn'], "UPDATE _cms_modules_galleries_ SET entry_sequence='".mysqli_real_escape_string($_SESSION['conn'], $new_position)."', entry_parent_id='".mysqli_real_escape_string($_SESSION['conn'], $new_parent)."' WHERE entry_id=".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_id']));
	}
	
	if($mode == "submenu"){
		mysqli_query($_SESSION['conn'], "UPDATE _cms_modules_galleries_ SET entry_parent_id='".mysqli_real_escape_string($_SESSION['conn'], $new_id)."' WHERE entry_id='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_id'])."'");
	}	
}
?>