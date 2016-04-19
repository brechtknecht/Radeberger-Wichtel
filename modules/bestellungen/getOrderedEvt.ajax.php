<?php
session_start();
if(isset($_SESSION['order']['ordered_evt'])){
	$output_str = "";
	foreach($_SESSION['order']['ordered_evt'] as $key=>$val){
		$output_str.= $key.":".$val."|";	
	}
	echo $output_str;
}
else{
	echo session_id();
}
?>