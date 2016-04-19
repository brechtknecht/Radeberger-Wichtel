<?php
function checkVar($key){
	if(isset($_REQUEST[$key])){
		if(!empty($_REQUEST[$key])){
			return $_REQUEST[$key];	
		}	
	}
	return false;	
} 
?>