<?php
//general functions
//read file and output content
function outputFile($file){
	global $var_array;
	ob_start(); 
    include ($file); 
    $content=ob_get_contents(); 
    ob_end_clean(); 
	return $content;
}

function isAdmin(){
	if(isset($_SESSION['cms_user']['user_role']) && $_SESSION['cms_user']['user_role'] == "Administrator"){
		return true;
	}
	else{
		return false;
	}
}

function addDeepLink($deeplink, $url){
	if(!empty($deeplink) && !empty($url)){
		$query = "REPLACE INTO _cms_hp_deeplinks_ (deeplink, url, last_change, entry_last_usr) VALUES ('".mysql_real_escape_string($deeplink)."', '".mysql_real_escape_string($url)."', NOW(), ".$_SESSION['cms_user']['user_id'].")";
		if($result = mysql_query($query)){
			writeHtaccess();
			return true;	
		}
	}	
	return false;
}

function removeDeepLink($url){
	if(!empty($url)){
		$query = "DELETE FROM _cms_hp_deeplinks_ WHERE url='".mysql_real_escape_string($url)."'";
		if($result = mysql_query($query)){
			writeHtaccess();
			return true;
		}
	}	
	return false;
}

function writeHtaccess(){
	//deeplinks -> in htaccess schreiben
	$htaccess = "/home/khdn/www.khdn.de/.htaccess";
	if(file_exists($htaccess)){
				
		//get deeplinks from table
		$deeplinks_txt = "#deeplinks\r\n";
		$deeplinks_txt.= "#generated by zeemes cms\r\n";
		$result = mysql_query("SELECT deeplink, url FROM _cms_hp_deeplinks_");
		while($row = mysql_fetch_assoc($result)){
			$deeplinks_txt.= "RewriteRule ^".$row['deeplink']."$ ".$row['url']."\r\n";
		}
		$deeplinks_txt.= "#end deeplinks\r\n";
		
		$htcontent = file_get_contents($htaccess);
		$htcontent_start = "";
		$htcontent_end = "";
				
		if(strstr($htcontent, "#deeplinks")){
			$htcontent = explode("#deeplinks", $htcontent);
			$htcontent_start = $htcontent[0];
			if(isset($htcontent[1]) && strstr($htcontent[1], "#end deeplinks")){
				$deeplinks = explode("#end deeplinks", $htcontent[1]);
				$htcontent_end = $deeplinks[1];
			}
		}
		$htcontent = trim($htcontent_start)."\r\n";
		$htcontent.= trim($htcontent_end)."\r\n\r\n";
		$htcontent.= $deeplinks_txt;
		
		if($fd =  fopen($htaccess, "w+")){
			fwrite($fd, $htcontent);
			fclose($fd);
		}
	}
}

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
	
function checkPermission($perm_cat="",$user_id=0,$perm_name="",$perm_value="",$perm_arg_name="",$perm_arg_value=""){
	//get user role
	$result=mysqli_query($_SESSION['conn'], "SELECT user_role FROM _cms_hp_user_ WHERE entry_id='".$user_id."' LIMIT 1");
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
		if(!empty($perm_arg_name)){
			$query.=" AND perm_arg_name='".mysqli_real_escape_string($_SESSION['conn'], $perm_arg_name)."'";
		}
		if(!empty($perm_arg_value)){
			$query.=" AND perm_arg_value='".mysqli_real_escape_string($_SESSION['conn'], $perm_arg_value)."'";
		}
		$query.=" LIMIT 1";
		if(mysqli_num_rows(mysqli_query($_SESSION['conn'], $query))>0){
			return true;
			
		}
		else{
			return false;
		}
	}
}

function getParentDir($file = ""){
	if(!empty($file)){
		$dir_name = pathinfo($file);
		$dir_name = $dir_name['dirname'];
		$dir_name = substr($dir_name, strrpos($dir_name,"/")+1);
		return $dir_name;
	}
}

function getFieldContent($table="",$field_name="",$key="",$search=""){
	$query = ("SELECT ".$field_name." FROM ".$table." WHERE ".$key."='".$search."' LIMIT 1");
	$result = mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		$row = mysqli_fetch_assoc($result);
		return $row[$field_name];
	}
	else{
		return "not found";
	}
}
	
function formatDate2Local() {
   global $lang;
   
   $n_arg=func_num_args();
   $a_arg=func_get_args();
   $stdDate=$a_arg[0];
   $localFormat=$a_arg[1];
   $retTime=$a_arg[2];
   $timeOnly=$a_arg[3];
   $sepChars=$a_arg[4];
   
   $sepChars=array('-','.','/',':',',');
   $localFormat=strtolower($localFormat); 
   
   if(eregi('0000',$stdDate))  return strtr($localFormat,'yYmMdDHis','000000000'); // IF std date is 0 return 0's in local format

   /* If time is included then isolate */
   if(strchr($stdDate,':')){
      list($stdDate,$stdTime) = explode(' ',$stdDate);
	  if($timeOnly) return $stdTime; /* If time only is needed */
   }

   $stdArray=explode('-',$stdDate);
   
   /* Detect time separator and explode localFormat */
   for($i=0;$i<sizeof($sepChars);$i++){
     if(strchr($localFormat,$sepChars[$i])){
	    $localSeparator=$sepChars[$i];
        $localArray=explode($localSeparator,$localFormat);
		break;
	 }
   }
   
   for($i=0;$i<3;$i++){
     if($localArray[$i]=='yyyy') $localArray[$i]=$stdArray[0];
	  elseif($localArray[$i]=='mm') $localArray[$i]=$stdArray[1];
	    elseif($localArray[$i]=='dd') $localArray[$i]=$stdArray[2];
   }
   
   //if ($lang=='de') $stdTime=strtr($stdTime,':','.'); // This is a hard coded time  format translator for german "de" language
   
   if($retTime) return implode($localSeparator,$localArray).' '.$stdTime;
   else return implode($localSeparator,$localArray);
}

function uniqueID($length=8, $chars='') { 
    $uid = "";
	$length = empty($length) ? 8 : $length; 
    $length = $length > 64 ? 64 : $length; 

    if(!is_array($chars) || (is_array($chars) && empty($chars))) { 
        for($i=65;$i<=90;$i++){ 
            $chars[] = chr($i); 
        }	 
        for($i=97;$i<=122;$i++){ 
            $chars[] = chr($i); 
        } 
		$chars[] = '_'; 
    } 

    $c = count($chars); 
    for($i=0;$i<$length;$i++){ 
        $uid .= $chars[rand(0, $c-1)]; 
    } 
    //$uid=$uid.microtime();
	return $uid; 
} 

function writeInTable($table,$id){
	//build query
	$query="";
	
	//insert or update
	//update
	if($id!="new"){
		$query.="UPDATE ".$table." SET ";
	}
	//insert
	else{
		$query.="INSERT INTO ".$table." SET ";
	}
	
	//get column names	
	$result_cols=mysqli_query($_SESSION['conn'], "SHOW COLUMNS FROM ".$table);
	while($row_cols=mysqli_fetch_row($result_cols)){
		if($row_cols[0]!="entry_id" && $row_cols[0]!="last_change" && isset($_POST[$row_cols[0]]) && $_POST[$row_cols[0]]!="undefined"){
			if(strstr($row_cols[1],"float")){
				$_POST[$row_cols[0]]=str_replace(",",".",$_POST[$row_cols[0]]);
			}
			$query.=$row_cols[0]."="."'".mysqli_real_escape_string($_SESSION['conn'], $_POST[$row_cols[0]])."', ";
		}
	}
	
	$query.="last_change=NOW() ";
	
	//update
	if($id!="new"){
		$query.="WHERE entry_id=".$_POST['entry_id'];
	}
		
	//echo $query;
	
	$result=mysqli_query($_SESSION['conn'], $query);
	
	if($id=="new"){
		$_POST['entry_id']=mysqli_insert_id($_SESSION['conn']);
	}
		
	if(isset($_POST['tmp_id']) && $_POST['tmp_id']!=""){
		$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_hp_images_ SET img_parent=".$_POST['entry_id']." WHERE img_parent=".$_POST['tmp_id']);
	}
	return $_POST['entry_id'];
}

function parseInfoXML($xml_file){
	//parse info xml
	$xml=simplexml_load_file($xml_file);
	$output_str="";
	$output_str.="<li title=\"".$xml->description[0]."\">";
	$output_str.="<span onclick=\"SHARED_showChilds(this.parentNode)\">".$xml->title[0]."</span>";
	$output_str.="<ul>";
	foreach($xml->file as $file){
	$output_str.="<li title=\"".$file->description."\" onclick=\"SHARED_setInnerHTML('".$xml->path[0].$file->path."','content','POST','".$file->variables."')\">".$file->title."</li>";
	}
	$output_str.="</ul>";
	$output_str.="</li>";
	echo $output_str;
}

function getUserName($entry_id=false){
	if($entry_id!=false){
		$entry_id=intval($entry_id);
		$result=mysqli_query($_SESSION['conn'], "SELECT user_name,user_fname FROM _cms_hp_user_ WHERE entry_id=".$entry_id." LIMIT 1");
		if(mysqli_num_rows($result)>0){
			$row=mysqli_fetch_assoc($result);
			return $row['user_fname']." ".$row['user_name'];
		}
	}
	return false;
}

function outputVar($array=array(),$key="",$else="&nbsp;"){
	if(is_array($array)){
		if(isset($array[$key])){
			return $array[$key];
		}
		else{
			if($else!=""){
				return $else;
			}
		}
	}
	else{
		if($else!=""){
			return $else;
		}
	}
}

function getPageVersionId($entry_parent_id,$state){
	$query="SELECT entry_id FROM _cms_hp_pages_ WHERE entry_parent_id=".$entry_parent_id;
	$query.=" AND entry_state='".$state."' AND changed_by=".$_SESSION['cms_user']['user_id'];
	$query.=" LIMIT 1";
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)==0){
		return getPageVersionId($entry_parent_id,"public");
	}
	else{
		$row=mysqli_fetch_assoc($result);
		return $row['entry_id'];
	}
}

function resizeImage($image_file,$max_width=0,$max_height=0){
	if(file_exists($image_file)){
		$image = $image_file;
		/*
		$image = new Imagick($image_file);
		$height = $image->getImageHeight(); 
		$width = $image->getImageWidth(); 
		
		if($max_width > 0 && $width >= $height){
			 $image->scaleImage($max_width, 0); 	
		}
		if($max_height > 0 && $height > $width){
			 $image->scaleImage($max_height, 0); 	
		}
		
		$image->writeImage($image_file); 
		$image->destroy(); 
		*/
		
		$img_data=getimagesize($image);
		$current_width=$img_data[0];
		$current_height=$img_data[1];
		
		if($max_width>0 && $current_width>$max_width){
			$new_width=$max_width;
			$new_height=$max_width*$current_height/$current_width;
		}
		if($max_height>0 && $current_height>$max_height){
			$new_height=$max_height;
			$new_width=$max_height*$current_width/$current_height;
		}
		
		if(isset($new_width) && !empty($new_width) && isset($new_height) && !empty($new_height)){
			$current_image=imagecreatefromjpeg($image);
			$new_image=imagecreatetruecolor($new_width,$new_height);
			$background_color = imagecolorallocate ($new_image, 255, 255, 255);
			imagefill($new_image,0,0,$background_color);
			imagecopyresampled($new_image,$current_image,0,0,0,0,$new_width,$new_height,$current_width,$current_height);
			unlink($image);
			imagejpeg($new_image,$image,90);
			imagedestroy($new_image);
			imagedestroy($current_image);
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


// /general functions
?>