<?php
if(isset($_REQUEST['html_text'])){
	$text=stripslashes($_REQUEST['html_text']);
	$text=rawurldecode($text);
	//delete class attributes
	//$text=preg_replace("/ class=([_a-zA-Z0-9\"\'])*/","",$text);
	//$text=preg_replace("/ class=\"([_a-zA-Z0-9\"\'])*\"/","",$text);
	$text=str_replace(" class=\"MsoNormal\"","",$text);
	$text=str_replace(" CLASS=\"MsoNormal\"","",$text);
	//delete inline styles
	$text=preg_replace("/style=\"([_a-zA-Z0-9\-\.\;\:\'#[:space:]])*\"/","",$text);
	$text=preg_replace("/&nbsp;/","",$text);
	//delete ms specific tags
	$text=str_replace("<o:p>","",$text);
	$text=str_replace("</o:p>","",$text);
	$text=preg_replace("/<font([_a-zA-Z0-9\-\.\;\:\'\"+=#[:space:]])*>/","",$text);
	$text=preg_replace("/<\/font>/","",$text);
	$text=preg_replace("/<FONT([_a-zA-Z0-9\-\.\;\:\'\"+=#[:space:]])*>/","",$text);
	$text=preg_replace("/<\/FONT>/","",$text);
	$text=preg_replace("/<span([_a-zA-Z0-9\-\.\;\:\'\"+=#[:space:]])*>/","",$text);
	$text=preg_replace("/<\/span>/","",$text);
	$text=preg_replace("/<SPAN([_a-zA-Z0-9\-\.\;\:\'\"+=#[:space:]])*>/","",$text);
	$text=preg_replace("/<\/SPAN>/","",$text);
	$text=preg_replace("/<div([_a-zA-Z0-9\-\.\;\:\'\"+=#[:space:]])*>/","",$text);
	$text=preg_replace("/<\/div>/","",$text);
	$text=preg_replace("/<DIV([_a-zA-Z0-9\-\.\;\:\'\"+=#[:space:]])*>/","",$text);
	$text=preg_replace("/<\/DIV>/","",$text);
	$text=preg_replace("/<\?xml:([_a-zA-Z0-9\-\.\;\:\'\"+=#?&\/[:space:]])*\/>/","",$text);
	//delete empty links
	$text=preg_replace("/<a([_a-zA-Z0-9\-\.\;\:\'\"+=#?&\/[:space:]])*>([[:space:]])*<\/a>/","",$text);
	$text=preg_replace("/<A([_a-zA-Z0-9\-\.\;\:\'\"+=#?&\/[:space:]])*>([[:space:]])*<\/A>/","",$text);
	$text=str_replace("<?","",$text);
	//delete empty paragraphs%u2013
	$text=str_replace("%u2013","-",$text);
	$text=preg_replace("/<p>([[:space:]])*<\/p>/","",$text);
	$text=str_replace("<P>&nbsp;</P>","",$text);
	
	$text=preg_replace("/<!--.*-->/","",$text);
	
	$text=str_replace("\r","",$text);
	$text=str_replace("\n","",$text);
	
	$text=str_replace("<strong>","",$text);
	$text=str_replace("</strong>","",$text);
	
	$text=str_replace("<b>","",$text);
	$text=str_replace("</b>","",$text);
	
		
	echo rawurlencode($text);
}
?>

