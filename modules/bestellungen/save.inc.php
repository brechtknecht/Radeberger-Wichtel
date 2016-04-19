<?php
//no external call
if(defined("internalCall")){
	if(isset($_POST['entry_id'])){
		if($_POST['entry_id']=="new"){
			$query="INSERT INTO _cms_modules_orders_ SET";	
		}
		else{
			$query="UPDATE _cms_modules_orders_ SET";	
		}
		$query.= " name='".mysqli_real_escape_string($_SESSION['conn'], $_POST['name'])."'";
		$query.= ", vorname='".mysqli_real_escape_string($_SESSION['conn'], $_POST['vorname'])."'";
		$query.= ", strasse='".mysqli_real_escape_string($_SESSION['conn'], $_POST['strasse'])."'";
		$query.= ", plz='".mysqli_real_escape_string($_SESSION['conn'], $_POST['plz'])."'";
		$query.= ", ort='".mysqli_real_escape_string($_SESSION['conn'], $_POST['ort'])."'";
		$query.= ", email='".mysqli_real_escape_string($_SESSION['conn'], $_POST['email'])."'";
		$query.= ", kontoinhaber='".mysqli_real_escape_string($_SESSION['conn'], $_POST['kontoinhaber'])."'";
		$query.= ", kontonummer='".mysqli_real_escape_string($_SESSION['conn'], $_POST['kontonummer'])."'";
		$query.= ", bankleitzahl='".mysqli_real_escape_string($_SESSION['conn'], $_POST['bankleitzahl'])."'";
		$query.= ", status='".mysqli_real_escape_string($_SESSION['conn'], $_POST['status'])."'";
				
		if($_POST['entry_id']!="new"){
			$query.=" WHERE entry_id=".intval($_POST['entry_id'])." LIMIT 1";
		}
		$result=mysqli_query($_SESSION['conn'], $query);
		echo mysqli_error($_SESSION['conn']);
		
		if($_POST['entry_id']=="new"){
			$_POST['entry_id']=mysqli_insert_id($_SESSION['conn']);
		}
		
		$query = "SELECT * FROM _cms_modules_orders_products_ WHERE pid=".$_POST['entry_id'];
		$result = mysqli_query($_SESSION['conn'], $query);
		while($row = mysqli_fetch_assoc($result)){
			if(isset($_POST['anzahl_'.$row['entry_id']])){
				$result_sub = mysqli_query($_SESSION['conn'], "UPDATE _cms_modules_orders_products_ SET anzahl=".$_POST['anzahl_'.$row['entry_id']]." WHERE entry_id=".$row['entry_id']);
			}
		}
		
		$_GET=$_POST;
	}
}
else{
	die("Fehler!");
}
?>