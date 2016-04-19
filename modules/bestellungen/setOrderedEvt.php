<?php
session_start();

if(isset($_REQUEST['evt_orders'])){
	include("../../shared/include/connect_db.inc.php");
	if(!isset($_SESSION['order'])){
		$_SESSION['order'] = array();
	}
	
	$query = "SELECT evt_id AS evt_id, anzahl AS anzahl, (SELECT entry_parent_id FROM _cms_modules_veranstaltungen_ WHERE entry_id=evt_id AND entry_parent_id!=0) AS has_parent FROM _cms_modules_orders_events_ WHERE";
	$order_array = explode("|",$_REQUEST['evt_orders']);
	if($order_array[sizeof($order_array)-1] == ""){
		array_pop($order_array);
	}
	
	
	$_SESSION['order']['ordered_evt'] = array();
	
	for($i = 0; $i<sizeof($order_array); $i++){
		if(!empty($order_array[$i])){
			$_SESSION['order']['ordered_evt'][$order_array[$i]] = 0;
			$query.= " entry_id=".$order_array[$i];
			if($i<sizeof($order_array)-1){
				$query.=" OR";
			}
		}
	}
	//echo $query;
	$result = mysqli_query($_SESSION['conn'], $query);
	if(mysqli_error($_SESSION['conn']) == ""){
		if(mysqli_num_rows($result)>0){
			
			while($row = mysqli_fetch_assoc($result)){
				if(isset($row['has_parent']) && !empty($row['has_parent'])){
					$evt_id = $row['has_parent'];
				}
				else{
					$evt_id = $row['evt_id'];
				}
				
				$_SESSION['order']['ordered_evt'][$evt_id]+= $row['anzahl'];
			}
		}
	}
}
?>