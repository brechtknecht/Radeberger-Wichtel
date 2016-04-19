<?php
function getXMLNodeContent($xml_file = NULL){
	if($xml_file != NULL){
		$module_settings = array();
		if(file_exists($xml_file)){
			$xml = simplexml_load_file($xml_file);
			foreach($xml->children() as $child){
				$module_settings[(string)$child->getName()] = stripslashes((string)$child);					
			}
			return $module_settings;
		}
	}
}

//prepare content
function outputContent($contentarea){
	global $row;
	$content = "";
	if(!empty($row['entry_direct_link']) && $row['entry_direct_link_target'] == "iframe"){
		$content = "<iframe src=\"".$row['entry_direct_link']."\"></iframe>";
	}
	else{
		$user_role_array = explode("|",$row['entry_user_role']);
		$in_array = 0;
		$role_count = 0;
		foreach($user_role_array as $val){
			if(!empty($val)){
				$role_count+= 1;
			}
			if(!empty($val) && (isset($_SESSION['ext_user']) && in_array($val,$_SESSION['ext_user']['perm']))){
				$in_array+= 1;	
			}
		}
		//echo $in_array;
		if($role_count == 0 || (isset($_SESSION['ext_user']) && $in_array > 0) || isset($_GET['page_mode'])){
			if($row['entry_html']!=""){
				$content_array=explode("||",$row['entry_html']);
				$del_last=array_pop($content_array);
				//page exists
				if(count($content_array)>0){
					foreach($content_array as $content){
						$content=stripslashes($content);
						if(substr($content,0,strpos($content,"#"))==$contentarea){
							$content=str_replace(substr($content,0,strpos($content,"#")+1),"",$content);
							$content=str_replace("„","\"",$content);
							$content=str_replace("“","\"",$content);
							
							break;
						}
						else{
							$content="";
						}
					}
				}
			}
			//new page
			else{
				$content="";
			}
			//append module
			if($row['entry_inc_module']!="" && !isset($_GET['page_mode'])){
				
				if($contentarea==$row['entry_inc_module_target']){
					$mod_content=showModule();
					if($row['entry_inc_module_mode']=="behind"){
						$content.=$mod_content;
					}
					if($row['entry_inc_module_mode']=="before"){
						$content=$mod_content.$content;
					}
					if($row['entry_inc_module_mode']=="replace" && !empty($mod_content)){
						$content=$mod_content;
					}
				}
			}
			if($row['entry_inc_module']!="" && (isset($_GET['page_mode']) && $_GET['page_mode']=="write")){
				$content="<?php include(\"modules/".$row['entry_inc_module']."/module.inc.php\");?>";
			}
			
			//higlight text -> searchterm
			if(isset($_GET['highlight'])){
				$search=$_GET['highlight'];
				$content=preg_replace("/((<[^>]*)|$search)/ie", '"\2"=="\1"? "\1":"<strong style=\"color: #FF0000\">\1</strong>"', $content);
			}
		}
		else{
			if($contentarea=="contentarea1"){
				$content = loginExtUser($row['entry_user_role']);
			}
			else{
				$content = "";
			}
		}
	}	
	return ($content);
}

function outputBoxContent(){
	global $row;
	$output_str = "";
	if(!empty($row['entry_boxes'])){
		$box_array = explode("|", $row['entry_boxes']);
		foreach($box_array as $box_id){
			if(!empty($box_id)){
				$query = "SELECT entry_id, name, html, pre_def FROM _cms_modules_boxes_ WHERE entry_id='".mysqli_real_escape_string($_SESSION['conn'], $box_id)."' LIMIT 1";
				if($result = mysqli_query($_SESSION['conn'], $query)){
					if($row = mysqli_fetch_assoc($result)){
						
						$pre_def = "";
						//predefined functions?
						if(!empty($row['pre_def']) && file_exists("modules/infoboxen/predefined/".$row['pre_def'])){
							$pre_def = (outputFile("modules/infoboxen/predefined/".$row['pre_def']));	
						}
						
						if($pre_def != "FALSE"){
							$output_str.= "<div class=\"boxContent\" id=\"box_".$row['entry_id']."\">";	
							//$output_str.= "<h3>".stripslashes($row['name'])."</h3>";
							$output_str.= stripslashes($row['html']);
							if(!empty($pre_def)){
								$output_str.= stripslashes($pre_def);
							}
							$output_str.= "</div>";	
						}
					}
				}
			}
		}
	}
	
	return $output_str;
}


function linkText($text) {
  $p[] = '"(( |^)((ftp|http|https){1}://)[-a-zA-Z0-9@:%_+.~#?&//=]+)"i';
  $r[] = '<a href="1" target="_blank">\1</a>';
  $p[] = '"( |^)(www.[-a-zA-Z0-9@:%_+.~#?&//=]+)"i';
  $r[] = '\1<a href="http://\2" target="_blank">\\2</a>';
  $p[] = '"([_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,3})"i';
  $r[] = '<a href="mailto:\1">\\1</a>';
  $text = preg_replace($p, $r, $text);
  return $text;
} 

function checkEmail($adr) {
  $regEx = '^([^\s@,:"<>]+)@([^\s@,:"<>]+\.[^\s@,:"<>.\d]{2,}|(\d{1,3}\.){3}\d{1,3})$';
  return (preg_match("/$regEx/",$adr,$part)) ? $part : false;
}

function returnDayByNumber($day = 0){
	$dayArray = array("Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Sonnabend");
	return $dayArray[$day];
}
	
function loginExtUser($perm=""){
	
	$output_str = "<h2>Login erforderlich</h2>";
	//$output_str.= "<p>Zugangsberechtigt sind Mitarbeiterinnen und Mitarbeiter des Trägerwerk Soziale Dienste in Thüringen e.V.</p>";
	if(!empty($perm)){
		if($_SERVER['REQUEST_METHOD'] != "POST"){
			$output_str.= outputFile("shared/loginExtUser/forms/login.php");
		}
		else{
			if(isset($_POST['error_array'])){
				unset($_POST['error_array']);
			}
			$_POST['error_array']=array();
			
			foreach($_POST as $key=>$val){
				if(strstr($key,"_req") && ($val=="" || $val=="Pflichtfeld!")){
					array_push($_POST['error_array'],$key);
					$_POST[$key]="Pflichtfeld!";
				}
			}
			if(sizeof($_POST['error_array'])==0){
				$query = "SELECT entry_id AS entry_id, CONCAT(user_fname,' ',user_name) AS username, user_role AS user_role, user_last_login AS user_last_login";
				$query.= " FROM _cms_hp_user_ WHERE user_type='extern' AND md5(user_login)='".md5(trim($_POST['username_req']))."' AND user_password='".md5(trim($_POST['password_req']))."' LIMIT 1";
				$result = mysqli_query($_SESSION['conn'], $query);
				
				if(mysqli_num_rows($result) == 1){
					
					$row = mysqli_fetch_assoc($result);
					
					$query_perm = "SELECT setting_perm FROM _cms_settings_ WHERE setting_id=".$row['user_role']." LIMIT 1";
					$result_perm = mysqli_query($_SESSION['conn'], $query_perm);
					
					if(mysqli_num_rows($result_perm) > 0){
						$row_perm = mysqli_fetch_assoc($result_perm);
					}
					
					/*
					//get 1st subpage
					$result_sub = mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_hp_navigation_ WHERE entry_parent_id=".$_GET['entry_id']." ORDER BY entry_sequence ASC LIMIT 1");
					if(mysqli_num_rows($result_sub) == 1){
						$row_sub = mysqli_fetch_assoc($result_sub);
					}
					*/
					
					if(isset($_SESSION['ext_user'])){
						unset($_SESSION['ext_user']);
					}
					$_SESSION['ext_user'] = array();
					$_SESSION['ext_user']['entry_id'] = $row['entry_id'];
					$_SESSION['ext_user']['user_role'] = $row['user_role'];
					$_SESSION['ext_user']['perm'] = explode("|",$row_perm['setting_perm']);
					$_SESSION['ext_user']['perm'][] = $row['user_role'];
					$_SESSION['ext_user']['username'] = $row['username'];
					$_SESSION['ext_user']['last_login'] = $row['user_last_login'];
					$location = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
					header("Location: index.php?entry_id=".$_GET['entry_id']);
					
				}
				else{
					$output_str.= outputFile("shared/loginExtUser/forms/login.php");
				}
			}
			else{
				$output_str.= outputFile("shared/loginExtUser/forms/login.php");
			}
		}
	}
	else{
		die("Page error!");
	}

	return $output_str;
}
	
function showLanguageSwitchOld($lang=""){
	if($lang!=""){
		$output_str="";
		if($lang=="en"){
			if(isset($_SESSION['page_language']) && $_SESSION['page_language']=="de"){
				$output_str.="<a href=\"index.php?entry_id=".$_GET['entry_id']."&amp;entry_lang=en\" style=\"position: absolute; left: 100px;bottom: 40px;text-decoration: none;\">english</a>";
			}
			if(isset($_SESSION['page_language']) && $_SESSION['page_language']=="en"){
				$output_str.="<a href=\"index.php?entry_id=".$_GET['entry_id']."&amp;entry_lang=de\" style=\"position: absolute; left: 100px;bottom: 40px;text-decoration: none;\">german</a>";
			}
			if(isset($_SESSION['page_language']) && $_SESSION['page_language']=="cz"){
				$output_str.="<a href=\"index.php?entry_id=".$_GET['entry_id']."&amp;entry_lang=en\" style=\"position: absolute; left: 100px;bottom: 40px;text-decoration: none;\">anglicky</a>";
			}
		}
		if($lang=="cz"){
			if(isset($_SESSION['page_language']) && $_SESSION['page_language']=="de"){
				$output_str.="<a href=\"index.php?entry_id=".$_GET['entry_id']."&amp;entry_lang=cz\" style=\"position: absolute; right: 100px;bottom: 40px;text-decoration: none;\">česky</a>";
			}
			if(isset($_SESSION['page_language']) && $_SESSION['page_language']=="en"){
				$output_str.="<a href=\"index.php?entry_id=".$_GET['entry_id']."&amp;entry_lang=cz\" style=\"position: absolute; right: 100px;bottom: 40px;text-decoration: none;\">czech</a>";
			}			
			if(isset($_SESSION['page_language']) && $_SESSION['page_language']=="cz"){
				$output_str.="<a href=\"index.php?entry_id=".$_GET['entry_id']."&amp;entry_lang=de\" style=\"position: absolute; right: 100px;bottom: 40px;text-decoration: none;\">německy</a>";
			}
		}
	return $output_str;
	}
	
}
	

function getPageByName($name)
	{
	$result_page=mysqli_query($_SESSION['conn'], "SELECT * FROM _cms_hp_navigation_ WHERE entry_name='".mysqli_real_escape_string($_SESSION['conn'], $name)."' AND entry_deleted=0 LIMIT 1");
	$row_page=mysqli_fetch_assoc($result_page);
	return $row_page['entry_id'];
	}
	
function getPageByModule($module)
	{
	$result_page=mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_hp_navigation_ WHERE entry_inc_module='".mysqli_real_escape_string($_SESSION['conn'], $module)."' AND entry_deleted=0 LIMIT 1");
	$row_page=mysqli_fetch_assoc($result_page);
	return $row_page['entry_id'];
	}
	
function getFieldById($table="",$field="",$id=0){
	$result=mysqli_query($_SESSION['conn'], "SELECT '".mysqli_real_escape_string($_SESSION['conn'], $field)."' FROM ".mysqli_real_escape_string($_SESSION['conn'], $table)." WHERE entry_id='".mysqli_real_escape_string($_SESSION['conn'], $id)."'");
	if(mysqli_num_rows($result)>0){
		$row=mysqli_fetch_assoc($result);
		return $row[$field];
	}
	else{
		return false;
	}
}

function getFieldContent($table="",$field_name="",$key="",$search=""){
	$query = ("SELECT ".mysqli_real_escape_string($_SESSION['conn'], $field_name)." FROM ".mysqli_real_escape_string($_SESSION['conn'], $table)." WHERE ".mysqli_real_escape_string($_SESSION['conn'], $key)."='".mysqli_real_escape_string($_SESSION['conn'], $search)."' LIMIT 1");
	$result = mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		$row = mysqli_fetch_assoc($result);
		return $row[$field_name];
	}
	else{
		return "not found";
	}
}

function getUserName($entry_id=false){
	if($entry_id!=false){
		$entry_id=intval($entry_id);
		$result=mysqli_query($_SESSION['conn'], "SELECT user_name,user_fname FROM _cms_hp_user_ WHERE entry_id='".mysqli_real_escape_string($_SESSION['conn'], $entry_id)."' LIMIT 1");
		if(mysqli_num_rows($result)>0){
			$row=mysqli_fetch_assoc($result);
			return $row['user_fname']." ".$row['user_name'];
		}
	}
	return false;
}

	
function outputFile($file)
	{
	//$content=file_get_contents($file);
	ob_start(); 
    include ($file); 
    $content=ob_get_contents(); 
    ob_end_clean(); 
	return $content;
	}
	


	
function writeInTable($table,$id)
	{
	
	//build query
	$query="";
	
	//insert or update
	//update
	if($id!="new")
		{
		$query.="UPDATE ".mysqli_real_escape_string($_SESSION['conn'], $table)." SET ";
		}
	//insert
	else
		{
		$query.="INSERT INTO ".mysqli_real_escape_string($_SESSION['conn'], $table)." SET ";
		}
	
	//get column names	
	$result_cols=mysqli_query($_SESSION['conn'], "SHOW COLUMNS FROM ".$table);
	while($row_cols=mysqli_fetch_row($result_cols))
		{
		if($row_cols[0]!="entry_id" && $row_cols[0]!="last_change" && $_POST[$row_cols[0]]!="undefined")
			{
			if(strstr($row_cols[1],"float"))
				{
				$_POST[$row_cols[0]]=str_replace(",",".",$_POST[$row_cols[0]]);
				}
			$query.=$row_cols[0]."="."'".mysqli_real_escape_string($_SESSION['conn'], $_POST[$row_cols[0]])."', ";
			}
		}
	
	$query.="last_change=NOW() ";
	
	//update
	if($id!="new")
		{
		$query.="WHERE entry_id='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_id'])."'";
		}
		
	$result=mysqli_query($_SESSION['conn'], $query);
	
	$error=mysqli_error($_SESSION['conn']);
	
	if($id=="new")
		{
		$_POST['entry_id']=mysqli_insert_id($_SESSION['conn']);
		}
		
	if(isset($_POST['tmp_id']) && $_POST['tmp_id']!="")
		{
		$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_hp_images_ SET img_parent='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_id'])."' WHERE img_parent='".mysqli_real_escape_string($_SESSION['conn'], $_POST['tmp_id'])."'");
		}
	return $error;
	}
	
function showBackButton()
	{
	if($_SERVER['HTTP_REFERER'] && $_SERVER['HTTP_REFERER']!="")
		{
		$output_str="<input type=\"button\" value=\"zurück\" style=\"margin: 5px\" onclick=\"history.back()\" />";
		}
	return $output_str;
	}
	
function formatDate($date,$mode="no",$between=".")
	{
	if($mode=="full_month")
		{
		$month_array=array("empty","Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember");
		$format_date=substr($date,8,2).". ".$month_array[intval(substr($date,5,2))]." ".substr($date,0,4);
		}
	else
		{
		$format_date=substr($date,8,2).$between.substr($date,5,2).$between.substr($date,0,4);
		}
	
	return $format_date; 
	}
	
function resizeImage($img_src,$img_target,$new_width=0,$new_height=0)
	{
	if($new_width>0 || $new_height>0)
		{
		$img_data=getimagesize($img_src);
		$img_width=$img_data[0];
		$img_height=$img_data[1];
		}
	//scale to new height
	if($new_width==0 && $new_height>0 && $new_height<$img_height)
		{
		$new_img_width=bcdiv(bcmul($new_height,$img_width),$img_height,0);
		$new_img_height=$new_height;
		}
	//scale to new width
	if($new_width>0 && $new_height==0 && $new_width<$img_width)
		{
		$new_img_height=bcdiv(bcmul($new_width,$img_height),$img_width,0);
		$new_img_width=$new_width;
		}
	if(isset($new_img_height) && isset($new_img_width))
		{
		$old_img=imagecreatefromjpeg($img_src);
		$new_img=imagecreatetruecolor($new_img_width,$new_img_height);
		imagecopyresized($new_img,$old_img,0,0,0,0,$new_img_width,$new_img_height,$img_width,$img_height);
		//delete old image
		if(file_exists($img_target))
			{
			unlink($img_target);
			}
		//save new image
		imagejpeg($new_img,$img_target);
		}
	}
	
function getParentId($entry_id=0)
	{
	if(file_exists("images/page_images/".$entry_id.".jpg"))
		{
		return "images/page_images/".$entry_id.".jpg";
		}
	else
		{
		$result=mysqli_query($_SESSION['conn'], "SELECT entry_id,entry_parent_id FROM _cms_hp_navigation_ WHERE entry_id='".mysqli_real_escape_string($_SESSION['conn'], $entry_id)."' LIMIT 1");
		if(mysqli_num_rows($result)>0)
			{
			$row=mysqli_fetch_assoc($result);
			if(file_exists("images/page_images/".$row['entry_parent_id'].".jpg"))
				{
				return "images/page_images/".$row['entry_parent_id'].".jpg";
				}
			else
				{
				return getParentId($row['entry_parent_id']);
				}
			}
		
		}
	}

function checkPermission($perm_cat="",$user_id=0,$perm_name="",$perm_value="",$perm_arg_name="",$perm_arg_value=""){
	//get user role
	$result=mysqli_query($_SESSION['conn'], "SELECT user_role FROM _cms_hp_user_ WHERE entry_id='".mysqli_real_escape_string($_SESSION['conn'], $user_id)."' LIMIT 1");
	if(mysqli_num_rows($result)>0){
		$row=mysqli_fetch_assoc($result);
	}
	if($perm_cat=="user" && isset($row['user_role']) && $row['user_role']=="Administrator"){
		return true;
	}
	else{
		$query="SELECT entry_id FROM _cms_perm_";
		$query.=" WHERE user_id=".intval($user_id);
		$query.=" AND perm_cat='".mysqli_real_escape_string($_SESSION['conn'], $perm_cat)."'";
		$query.=" AND perm_name='".mysqli_real_escape_string($_SESSION['conn'], $perm_name)."'";
		$query.=" AND perm_value='".mysqli_real_escape_string($_SESSION['conn'], $perm_value)."'";
		$query.=" AND perm_arg_name='".mysqli_real_escape_string($_SESSION['conn'], $perm_arg_name)."'";
		$query.=" AND perm_arg_value='".mysqli_real_escape_string($_SESSION['conn'], $perm_arg_value)."'";
		$query.=" LIMIT 1";
		
		if(mysqli_num_rows(mysqli_query($_SESSION['conn'], $query))>0){
			return true;
			
		}
		else{
			return false;
		}
	}
}

function replaceForbiddenChars($word = ""){
	$word = str_replace("ä","ae",$word);
	$word = str_replace("ö","oe",$word);
	$word = str_replace("ü","ue",$word);
	$word = str_replace("Ä","ae",$word);
	$word = str_replace("Ö","oe",$word);
	$word = str_replace("Ü","ue",$word);
	$word = str_replace(" ","-",$word);
	$word = str_replace("ß","ss",$word);
	$word = str_replace("/","_",$word);
	return strtolower($word);
}

// Browsersprache ermitteln
 function lang_getfrombrowser ($allowed_languages, $default_language, $lang_variable = null, $strict_mode = true) {
         // $_SERVER['HTTP_ACCEPT_LANGUAGE'] verwenden, wenn keine Sprachvariable mitgegeben wurde
         if ($lang_variable === null) {
                 $lang_variable = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
         }
 
         // wurde irgendwelche Information mitgeschickt?
         if (empty($lang_variable)) {
                 // Nein? => Standardsprache zurückgeben
                 return $default_language;
         }
 
         // Den Header auftrennen
         $accepted_languages = preg_split('/,\s*/', $lang_variable);
 
         // Die Standardwerte einstellen
         $current_lang = $default_language;
         $current_q = 0;
 
         // Nun alle mitgegebenen Sprachen abarbeiten
         foreach ($accepted_languages as $accepted_language) {
                 // Alle Infos über diese Sprache rausholen
                 $res = preg_match ('/^([a-z]{1,8}(?:-[a-z]{1,8})*)'.
                                    '(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i', $accepted_language, $matches);
 
                 // war die Syntax gültig?
                 if (!$res) {
                         // Nein? Dann ignorieren
                         continue;
                 }
                 
                 // Sprachcode holen und dann sofort in die Einzelteile trennen
                 $lang_code = explode ('-', $matches[1]);
 
                 // Wurde eine Qualität mitgegeben?
                 if (isset($matches[2])) {
                         // die Qualität benutzen
                         $lang_quality = (float)$matches[2];
                 } else {
                         // Kompabilitätsmodus: Qualität 1 annehmen
                         $lang_quality = 1.0;
                 }
 
                 // Bis der Sprachcode leer ist...
                 while (count ($lang_code)) {
                         // mal sehen, ob der Sprachcode angeboten wird
                         if (in_array (strtolower (join ('-', $lang_code)), $allowed_languages)) {
                                 // Qualität anschauen
                                 if ($lang_quality > $current_q) {
                                         // diese Sprache verwenden
                                         $current_lang = strtolower (join ('-', $lang_code));
                                         $current_q = $lang_quality;
                                         // Hier die innere while-Schleife verlassen
                                         break;
                                 }
                         }
                         // Wenn wir im strengen Modus sind, die Sprache nicht versuchen zu minimalisieren
                         if ($strict_mode) {
                                 // innere While-Schleife aufbrechen
                                 break;
                         }
                         // den rechtesten Teil des Sprachcodes abschneiden
                        array_pop ($lang_code);
                 }
        }
 
         // die gefundene Sprache zurückgeben
         return $current_lang;
}

//delete empty index form array
function removeEmptyIndex($array){
	if(is_array($array)){
		foreach($array as $key=>$val){
			if(empty($val)){
				unset($array[$key]);
			}
		}
	return $array;
	}
	else{
		return false;
	}
}

//compare arrays
function compareArrays($array1, $array2){
	if(is_array($array1) && is_array($array2)){
		foreach($array1 as $val){
			if(in_array($val,$array2)){
				return true;
			}
		}
	}
	return false;
}


?>