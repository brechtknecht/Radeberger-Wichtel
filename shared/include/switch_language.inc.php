<?php 
function showLanguageSwitch(){
	$output_str = "";
	if($result_flag = mysqli_query($_SESSION['conn'], "SELECT setting_value FROM _cms_settings_ WHERE setting_key='languages' LIMIT 1")){
		if($row_flag = mysqli_fetch_assoc($result_flag)){
			$output_str.= "<ul id=\"switchLanguage\">"; 
			$tmp = explode("|",$row_flag['setting_value']);	
			$url = $_SERVER['QUERY_STRING'];
			//echo $url;
			$regex = "/[&|?]entry_lang=[en|de]/";
			$url = preg_replace($regex, $url, "");
			//echo $url;
			foreach($tmp as $lang){
				if(!empty($lang) && !strstr($lang,"!")){
					
					$output_str.= "<li ".(isset($_SESSION['page_language']) && $_SESSION['page_language'] == $lang?"class=\"active\"":"").">";
					$output_str.= "<a href=\"".$url.(strstr($url, "?")?"&amp;":"?")."entry_lang=".$lang."\">";
					$output_str.= $lang;
					$output_str.= "</a>";
					$output_str.= "</li>";
				}
			}
			$output_str.= "</ul>";
		}
	}
	
	return 	$output_str;
}