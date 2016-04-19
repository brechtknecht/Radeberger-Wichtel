<?php
function showModule(){
	
	if(isset($_GET['gid'])){
		return modListImages(intval($_GET['gid']));
	}
	//get filter if exists
	$filter_array = array();
	$query="SELECT perm_value, perm_arg_name, perm_arg_value FROM _cms_perm_ WHERE perm_cat='page' AND user_id=".$_GET['entry_id']." AND perm_name='module' AND perm_value='galerien'";
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		
		while($row=mysqli_fetch_assoc($result)){
			if($row['perm_arg_name'] == "filter"){
				$filter_array[] = $row['perm_arg_value'];	
			}
		}
		if(sizeof($filter_array) > 0){
			if(!empty($filter_array[0])){
				return modListImages($filter_array[0]);		
			}
		}
		
	}
	return modListGalleries();		
	
}

function modListGalleries(){
	$output_str = "<div id=\"modContent\">";
	if(1 == 1){
		//gallery 
		$query = "SELECT";
		$query.= " entry_id AS entry_id";
		$query.= ", entry_name AS entry_name";
		$query.= ", entry_description AS entry_description";
		$query.= ", (SELECT file_save_name FROM _cms_hp_files_ t2 WHERE t2.file_cat1='module' AND t2.file_cat2='galleries' AND t2.entry_parent_id=t1.entry_id ORDER BY t2.file_real_name ASC LIMIT 1) AS image";
		$query.= " FROM _cms_modules_galleries_ t1";
		$query.= " ORDER BY entry_sequence ASC";
		
		if($result = mysqli_query($_SESSION['conn'], $query)){
			$output_str.= "<ul class=\"modGalleryList\">";
			while($row_gallery = mysqli_fetch_assoc($result)){
				$output_str.= "<li>";
				$output_str.= "<a class=\"link\" href=\"galerie,".$_GET['entry_id'].".php?gid=".$row_gallery['entry_id']."\">";
				$output_str.= "<img src=\"files/".$row_gallery['image']."\" alt=\"\" />";
				$output_str.= "<strong>".htmlspecialchars($row_gallery['entry_name'])."</strong>";		
				$output_str.= "<span>".htmlspecialchars($row_gallery['entry_description'])."</span>";	
				$output_str.= "</a>";
				$output_str.= "</li>";	
			}
			$output_str.= "</ul";
			$output_str.= "</div>";
			//$output_str.= "<h3>Galerie: ".htmlspecialchars($row_gallery['entry_name'])."</h3>";
		}
	}
	$output_str.= "</div>";
	return $output_str;
}

function modListImages($gallery_id = 0){
	$output_str = "<div id=\"modContent\">";
	if($gallery_id != 0){
		//gallery 
		$query = "SELECT";
		$query.= " entry_id AS entry_id";
		$query.= ", entry_name AS entry_name";
		$query.= ", entry_description AS entry_description";
		$query.= ", videos AS videos";
		$query.= " FROM _cms_modules_galleries_ t1";
		$query.= " WHERE entry_id=".$gallery_id;
		//echo $query;
		if($result = mysqli_query($_SESSION['conn'], $query)){
			$row_gallery = mysqli_fetch_assoc($result);
			
		}
		
		//list videos
		if(!empty($row_gallery['videos'])){
			$video_array = explode("\r\n", $row_gallery['videos']);
			if(sizeof($video_array) > 0){
				$output_str.= "<h3>Videos</h3>";
				$output_str.= "<ul class=\"modVideoList\">";
				foreach($video_array as $val){
					if(!empty($val)){
						$output_str.= "<li>";
						//get video data -> youtube api
						$v_data = file_get_contents("http://gdata.youtube.com/feeds/api/videos/".trim($val)."?v=2&alt=jsonc");
						$v_data = json_decode($v_data, true);
						$output_str.= "<a href=\"http://www.youtube.com/embed/".trim($val)."?autoplay=1\" class=\"videoPopup\" target=\"_blank\">";
						$output_str.= "<img src=\"".$v_data['data']['thumbnail']['hqDefault']."\" alt=\"\" />";
						$output_str.= "<span class=\"imgCaption\">".htmlspecialchars($v_data['data']['title'])."</span>";
						$output_str.= "</a>";	
						
						$output_str.= "<li>";
					}
				}
				$output_str.= "</ul>";	
			}
		}
			
		//list images
		$query = "SELECT";
		$query.= " entry_id AS entry_id";
		$query.= ", file_save_name AS file_save_name";
		$query.= ", (SELECT description FROM _cms_hp_files_desc_ WHERE entry_parent_id=_cms_hp_files_.entry_id AND language='de') AS file_desc_de";
		//$query.= ", (SELECT description FROM _cms_hp_files_desc_ WHERE entry_parent_id=_cms_hp_files_.entry_id AND language='".$_SESSION['page_language']."') AS file_desc_lang";
		//$query.= ", (SELECT entry_description FROM _cms_modules_galleries_ WHERE entry_id=".$gallery_id.") AS gallery_desc";
		$query.= " FROM _cms_hp_files_ WHERE file_cat1='module' AND file_cat2='galleries' AND entry_parent_id=".$gallery_id." ORDER BY file_real_name ASC";
		$result_img = mysqli_query($_SESSION['conn'], $query);
		if(mysqli_num_rows($result_img) > 1){
        	if(isset($video_array) && sizeof($video_array) > 0){
				$output_str.= "<h3>Bilder</h3>";
			}
			$output_str.= "<ul class=\"modImageList\">";
			while($row_img = mysqli_fetch_assoc($result_img)){
				$output_str.= "<li>";
				$output_str.= "<a rel=\"gal\" class=\"colorbox\" href=\"files/".$row_img['file_save_name']."\" title=\"".stripslashes(htmlspecialchars($row_img['file_desc_de']))."\" class=\"colorbox\">";
				$output_str.= "<img src=\"files/".$row_img['file_save_name']."\" alt=\"\" />";	
				$output_str.= "</a>";
				$output_str.= "</li>";
			}
			$output_str.= "</ul>";
				
						
            //$output_str.= "<a href=\"".$path."\">zurück</a>";
        }
		else{
			if(!isset($video_array) || sizeof($video_array) == 0)
			$output_str.= "<p>In dieser Galerie wurden noch keine Bilder hinterlegt.</p>";
		}
	}
    $output_str.= "</div>";
	/*
	$output_str.= "<script type=\"text/javascript\">";
	$output_str.= "$.colorbox.remove();";
	$output_str.= "$('a.colorbox').colorbox({rel:'gal', maxWidth: '90%', maxHeight: '90%'});";
	$output_str.= "$('a.videoPopup').colorbox({width:640, height:500, iframe: true});";
    $output_str.= "</script>";
	*/
	return $output_str;
}




?>