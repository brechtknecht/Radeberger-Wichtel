<?php
function showModule(){
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		submitForm();	
	}
	
	if(isset($_GET['art_id'])){
		return modShowArticle(intval($_GET['art_id']));
	}
	else{
		return modListArticles();
	}
}

function modListArticles(){
	
	$module="news";
	$dir="modules/";
	$query = "SELECT perm_arg_value FROM _cms_perm_";
	$query.= " WHERE perm_cat='page'";
	$query.= " AND user_id='".mysqli_real_escape_string($_SESSION['conn'], $_GET['entry_id'])."'";
	$query.= " AND perm_value='artikel'";
	$query.= " AND perm_arg_name='kategorie'";
	
	$category_array = array();
	
	$result = mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result) > 0){
		
		while($row_kat = mysqli_fetch_assoc($result)){
			$category_array[] = $row_kat['perm_arg_value'];	
			$category = $row_kat['perm_arg_value'];		
		}
		
		
	}
	
	$query = "SELECT perm_arg_value FROM _cms_perm_";
	$query.= " WHERE perm_cat='page'";
	$query.= " AND user_id='".mysqli_real_escape_string($_SESSION['conn'], $_GET['entry_id'])."'";
	$query.= " AND perm_value='artikel'";
	$query.= " AND perm_arg_name='mode'";
	$result = mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result) == 1){
		$row_kat = mysqli_fetch_assoc($result);	
		$viewmode = $row_kat['perm_arg_value'];
	}
	
	if(!isset($viewmode))
		$viewmode="";	
	$output_str="<div id=\"modContent\" class=\"".htmlspecialchars($viewmode)."\">";
	//get entries
	
	$query="SELECT *";
	$query.= ", (SELECT file_save_name FROM _cms_hp_files_ t2 WHERE t2.entry_parent_id=t1.entry_id AND t2.file_cat1='module' AND t2.file_cat2='news' AND t2.file_cat3='fotos' ORDER BY file_sequence ASC LIMIT 1) AS entry_img";
	$query.= " FROM _cms_modules_news_ t1";
	$query.= " WHERE";
	$query.= " FIND_IN_SET(entry_parent_id, '".implode(",", $category_array)."')";
	/*
	if($category != "0"){
		$query.= " entry_parent_id='".$category."'";
	}
	else{
		$query.= " news_show_on_startpage=1";	
	}
	*/
	$query.= " AND NOW()>=news_start AND news_end>=NOW()";
	$query.= " AND entry_deleted=0";
	$query.= " ORDER BY news_sticky DESC, news_date DESC";
	//echo $query;
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		$media_array = array();
		
		$output_str.= "<div id=\"modArticles\">";
		while($row=mysqli_fetch_assoc($result)){
			$row['news_headline'] = json_decode($row['news_headline'], true);
			$row['news_teaser'] = json_decode($row['news_teaser'], true);
			$row['news_text'] = json_decode($row['news_text'], true);
			
			$output_str.= "<article>";
						
			if(!empty($row['videos'])){
				$output_str.= "<video autoplay loop style=\"background-image: url(video/Wichtel_in_Produktion.jpg);\" width=\"980\" height=\"540\"  onclick=\"if(/Android/.test(navigator.userAgent))this.play();\">";
				$output_str.= "<source src=\"video/Wichtel_in_Produktion.mp4\" type=\"video/mp4\" />";
				$output_str.= "<source src=\"video/Wichtel_in_Produktion.webm\" type=\"video/webm\" />";
				$output_str.= "<source src=\"video/Wichtel_in_Produktion.ogv\" type=\"video/ogg\" />";
				
				$output_str.= "</video>";
				/*
				//$output_str.= "<script src=\"video/html5ext.js\" type=\"text/javascript\"></script>";
				*/
				
				
			}
									
			if(!empty($row['news_text'])){
				$output_str.= "<div class=\"articleTeaser\">";
				$output_str.= "<figure>";
				$output_str.= "<div></div>";
				//contact?
				if($category == 5){
					$output_str.= "<iframe src=\"https://www.google.com/maps/embed?pb=!1m12!1m8!1m3!1d10013.534423708796!2d13.896386999999999!3d51.13827680000001!3m2!1i1024!2i768!4f13.1!2m1!1s01454+Radeberg+OT+Liegau-Augustusbad+Friedensstra%C3%9Fe+20!5e0!3m2!1sde!2sde!4v1414579641245\" width=\"392\" height=\"688\" frameborder=\"0\" style=\"border:0\"></iframe>";
				}
				else{
					$output_str.= "<img src=\"files/".$row['entry_img']."\" alt=\"\">";	
				}
				
				$output_str.= "</figure>";
				$output_str.= "<div>";
				$output_str.= "<div class=\"modArticleText\">";
				//$output_str.= "<h4>".stripslashes($row['news_headline'])."</h4>";
				$output_str.= $row['news_text'][$_SESSION['page_language']];
				$output_str.= "</div>";
				$output_str.= "</div>";
				
				$output_str.="</div>";
				
				
			}
			
			if($category == 5){
				
				$output_str.= outputFile("modules/formulare/forms/kontaktformular.php");		
			}
			
			
			
			
			$output_str.= "</article>";
		}
		$output_str.="</div>";
				
	}
	else{
		//$output_str.="<p>Momentan sind noch keine Inhalte hinterlegt.</p>";
	}
	$output_str.="</div>";
	return $output_str;
}

function modShowArticle($entry_id){
	$output_str = "";
	$output_str.= "<div id=\"modArticleDetail\">";
	//company
	$query = "SELECT * FROM _cms_modules_news_ WHERE entry_id='".mysqli_real_escape_string($_SESSION['conn'], $entry_id)."' LIMIT 1";
	if($result = mysqli_query($_SESSION['conn'], $query)){
		if($row = mysqli_fetch_assoc($result)){
			//get images
			$query = "SELECT";
			$query.= " entry_id AS entry_id";
			$query.= ", file_save_name AS file_save_name";
			$query.= " FROM _cms_hp_files_ WHERE file_cat1='module' AND file_cat2='news' AND file_cat3='fotos' AND entry_parent_id='".mysqli_real_escape_string($_SESSION['conn'], $row['entry_id'])."' ORDER BY file_sequence ASC";
			
			//text
			$output_str.= "<article>";
			$output_str.= "<span class=\"toggleText\">x</span>";
			$output_str.= "<h4>".htmlspecialchars($row['news_headline'])."</h4>";
			$output_str.= ($row['news_text']);
			$output_str.= "</article>";
										
			if($result_img = mysqli_query($_SESSION['conn'], $query)){
				$img_array = array();
				while($row_img = mysqli_fetch_assoc($result_img)){
					$img_array[] = $row_img;	
				}	
				
				
				
				//slider
				$output_str.= "<div id=\"slider\">";
				$output_str.= makeSliderContent($img_array);	
				$output_str.= "</div>";
				
			}
		}
	}
	
	$output_str.= "</div>";
	
	return $output_str;
}

function modGetYTVideo($id){
	$v_data = false;
	
	if($ch = curl_init()){
			curl_setopt($ch, CURLOPT_URL, "https://gdata.youtube.com/feeds/api/videos/".trim($id)."?v=2&alt=json");
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			$v_data = curl_exec($ch);
			$v_data = json_decode($v_data, true);	
			curl_close($ch);
	}
	/*
	if($v_data = @file_get_contents("https://gdata.youtube.com/feeds/api/videos/".trim($id)."?v=2&alt=jsonc")){
		$v_data = json_decode($v_data, true);	
		return $v_data;
	}
	else{
		if($ch = curl_init()){
			curl_setopt($ch, CURLOPT_URL, "https://gdata.youtube.com/feeds/api/videos/".trim($id)."?v=2&alt=json");
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			$v_data = curl_exec($ch);
			$v_data = json_decode($v_data, true);	
			curl_close($ch);
		}
		 
		
	}
	*/
	return $v_data;
	
}

function submitForm(){
	
	$mailto = "info@radeberger-wichtel.de";
	$msg = "";
	if(isset($_POST['error_array'])){
		unset($_POST['error_array']);
	}
	$_POST['error_array'] = array();
	
	foreach($_POST as $key=>$val){
		$val = preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "",$val);
		if(strstr($key,"_req") && $val == ""){
			array_push($_POST['error_array'],$key);
		}
		if($val != "" && $key!="error_array" && $key != "pid"){
			if(is_array($_POST[$key])){
				foreach($_POST[$key] as $key2=>$val2){
					$msg.= strtoupper(str_replace("_"," ",str_replace("_req","",$key))).": ".$val2."\n";	
				}		
			}
			else{
				$msg.= strtoupper(str_replace("_"," ",str_replace("_req","",$key))).": ".$val."\n";	
			}
		}
	}
	
	
	if(sizeof($_POST['error_array']) == 0){
		if(!isset($mail)){
			$mail=mail($mailto,utf8_decode($_SESSION['subject']),utf8_decode($msg),"FROM:".$_POST['email_req']);
			header("Location: vielen_dank,19.php");
			exit();
			
		}
	}
	else{
		//header("Location: kontakt,5.php#formular");
	}
}
?>
