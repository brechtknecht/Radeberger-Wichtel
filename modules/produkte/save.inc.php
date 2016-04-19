<?php
//no external call
if(defined("internalCall")){
	if(isset($_POST['entry_id'])){
		if($_POST['entry_id']=="new"){
			$query="INSERT INTO _cms_modules_produkte_ SET";	
		}
		else{
			$query="UPDATE _cms_modules_produkte_ SET";	
		}
		
		if(isset($_POST['entry_preis'])){
			$_POST['entry_preis'] = str_replace(",",".",$_POST['entry_preis']);
		}
		if(!isset($_POST['entry_preis_select'])){
			$_POST['entry_preis_select'] = "0";
		}
					
		$query.=" entry_name='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_name'])."',";
		$query.=" entry_nummer='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_nummer'])."',";
		$query.=" entry_preis='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_preis'])."',";
		$query.=" entry_video='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_video'])."',";
		$query.=" entry_preis_select='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_preis_select'])."',";
		if(isset($_POST['entry_kategorie']) && is_array($_POST['entry_kategorie'])){
			$query.=" entry_kategorie='|".mysqli_real_escape_string($_SESSION['conn'], implode("|",$_POST['entry_kategorie']))."|',";
		}
		else{
			$query.=" entry_kategorie='',";
		}
		$query.=" entry_desc='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_desc'])."',";
		$query.=" entry_last_usr='".mysqli_real_escape_string($_SESSION['conn'], $_SESSION['cms_user']['user_id'])."',";
		$query.=" last_change=NOW()";
		if($_POST['entry_id']!="new"){
			$query.=" WHERE entry_id=".intval($_POST['entry_id'])." LIMIT 1";
		}
		//echo $query;
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
				$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_modules_produkte_ SET entry_sequence=entry_sequence+1 WHERE entry_sequence>=".$new_sequence);
				$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_modules_produkte_ SET entry_sequence=".$new_sequence.", entry_parent_id=".$new_parent." WHERE entry_id=".$_POST['entry_id']);
			}
			if($_POST['position_mode']=="behind"){
				$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_modules_produkte_ SET entry_sequence=entry_sequence-1 WHERE entry_sequence<=".$new_sequence);
				$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_modules_produkte_ SET entry_sequence='".$new_sequence."', entry_parent_id='".$new_parent."' WHERE entry_id=".$_POST['entry_id']);
				
			}
			if($_POST['position_mode']=="submenu"){
				$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_modules_produkte_ SET entry_parent_id=".$new_id." WHERE entry_id=".$_POST['entry_id']);
			}
		}
		//neu nummerieren
		$result=mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_modules_produkte_ ORDER BY entry_sequence ASC,entry_name ASC");
		$i=1;
		while($row=mysqli_fetch_assoc($result)){
			$query=mysqli_query($_SESSION['conn'], "UPDATE _cms_modules_produkte_ SET entry_sequence=".$i." WHERE entry_id='".$row['entry_id']."'");
			$i++;
		}	
		
		
		$_GET=$_POST;
	}
	//save module settings
	if(isset($_POST['module_settings']) && is_array($_POST['module_settings'])){
		if($file = fopen("module_settings.xml","w+")){
			$text = "<xml>";
			foreach($_POST['module_settings'] as $key=>$val){
				
				$text.= "<".$key.">";
				$text.= "<![CDATA[".$val."]]>";
				$text.= "</".$key.">";
			}
			$text.= "</xml>";
			fwrite($file,$text);
			fclose($file);
		}
		else{
			echo "Datei 'module_settings.xml' konnte nicht angelegt werden.";
		}
	}
}
else{
	die("Fehler!");
}
?>