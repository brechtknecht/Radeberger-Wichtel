<?php
//get contentarea css styles
$css_file="../../../shared/css/styles.css";
$css_file=file($css_file);
$css_array=array();
$open=0;
//allowed properties
$prop_array=array("font-family","font-size","line-height","padding","margin","width","height","background-color","background-image","background-repeat","background-position");
foreach($css_file as $val){
	if(strstr($val,"#contentarea")){
		$open=1;
		$id=str_replace("{","",$val);
		$id=str_replace("#","",$id);
		$id=trim($id);
		if(strstr($id," ")){
			$open=0;
		}
		else{
			$css_array[$id]="";
		}
		
	}
	if(strstr($val,"}")){
		$open=0;
	}
	if($open==1){
		if($val!="" && !strstr($val,"#contentarea") && checkInArray($prop_array,$val)){
			$rule=str_replace("{","",$val);
			$rule=trim($rule);
			if(!strstr($rule,";")){
				$rule=$rule.";";
			}
			$css_array[$id].=$rule;
		}
	}
}
?>