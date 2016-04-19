<?php
//no external call
if(defined("internalCall")){
	if(isset($_POST['entry_id'])){
		if($_POST['entry_id']=="new"){
			$query="INSERT INTO _cms_modules_produkte_kategorien_ SET";	
		}
		else{
			$query="UPDATE _cms_modules_produkte_kategorien_ SET";	
		}
					
		$query.=" entry_name='".mysqli_real_escape_string($_SESSION['conn'], json_encode($_POST['entry_name']))."',";
		$query.=" entry_desc='".mysqli_real_escape_string($_SESSION['conn'], json_encode($_POST['entry_desc']))."',";
		$query.=" entry_url='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_url'])."',";
		$query.=" entry_last_usr=".$_SESSION['cms_user']['user_id'].",";
		$query.=" last_change=NOW()";
		if($_POST['entry_id']!="new"){
			$query.=" WHERE entry_id=".intval($_POST['entry_id'])." LIMIT 1";
		}
		$result=mysqli_query($_SESSION['conn'], $query);
		echo mysqli_error($_SESSION['conn']);
		if($_POST['entry_id']=="new"){
			$_POST['entry_id']=mysqli_insert_id($_SESSION['conn']);
		}
				
		//change position/sequence
		if($_POST['new_position']!="" && $_POST['position_mode']!=""){
			//split new_position
			$position_array=explode("|",$_POST['new_position']);
			$new_sequence=$position_array[0];
			$new_parent=$position_array[1];
			$new_id=$position_array[2];
			
			if($_POST['position_mode']=="before"){
				$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_modules_produkte_kategorien_ SET entry_sequence=entry_sequence+1 WHERE entry_sequence>=".$new_sequence);
				$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_modules_produkte_kategorien_ SET entry_sequence=".$new_sequence.", entry_parent_id=".$new_parent." WHERE entry_id=".$_POST['entry_id']);
			}
			if($_POST['position_mode']=="behind"){
				$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_modules_produkte_kategorien_ SET entry_sequence=entry_sequence-1 WHERE entry_sequence<=".$new_sequence);
				$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_modules_produkte_kategorien_ SET entry_sequence=".$new_sequence.", entry_parent_id=".$new_parent." WHERE entry_id=".$_POST['entry_id']);
			}
			if($_POST['position_mode']=="submenu"){
				$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_modules_produkte_kategorien_ SET entry_parent_id=".$new_id." WHERE entry_id=".$_POST['entry_id']);
			}
		}
		//neu nummerieren
		$result=mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_modules_produkte_kategorien_ ORDER BY entry_sequence ASC,entry_id ASC");
		$i=1;
		while($row=mysqli_fetch_assoc($result)){
			$query=mysqli_query($_SESSION['conn'], "UPDATE _cms_modules_produkte_kategorien_ SET entry_sequence=".$i." WHERE entry_id='".$row['entry_id']."'");
			$i++;
		}	
		
		$_GET=$_POST;
	}
}
else{
	die("Fehler!");
}
?>