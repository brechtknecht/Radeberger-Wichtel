<?php
function getSliderImages($entry_id){
	//get filter if exists
	$filter_array = array();
	$query = "SELECT entry_inc_gallery FROM _cms_hp_navigation_ WHERE entry_id='".mysqli_real_escape_string($_SESSION['conn'], $entry_id)."'";
	if($result = mysqli_query($_SESSION['conn'], $query)){
		if(mysqli_num_rows($result) == 1){
			$row = mysqli_fetch_assoc($result);
			$filter_array[] = $row['entry_inc_gallery'];	
			
			if(sizeof($filter_array) > 0){
				if(!empty($filter_array[0])){
					//get images
					$query = "SELECT";
					$query.= " entry_id AS entry_id";
					$query.= ", file_save_name AS file_save_name";
					//$query.= ", file_img_copyright AS slogan";
					//$query.= ", (SELECT description FROM _cms_hp_files_desc_ WHERE entry_parent_id=_cms_hp_files_.entry_id AND language='de') AS file_desc_de";
					//$query.= ", (SELECT description FROM _cms_hp_files_desc_ WHERE entry_parent_id=_cms_hp_files_.entry_id AND language='".$_SESSION['page_language']."') AS file_desc_lang";
					//$query.= ", (SELECT entry_description FROM _cms_modules_galleries_ WHERE entry_id=".$gallery_id.") AS gallery_desc";
					$query.= " FROM _cms_hp_files_ WHERE file_cat1='module' AND file_cat2='galleries' AND entry_parent_id='".mysqli_real_escape_string($_SESSION['conn'], $filter_array[0])."' ORDER BY file_sequence ASC, file_real_name ASC";
										
					if($result_img = mysqli_query($_SESSION['conn'], $query)){
						$img_array = array();
							while($row_img = mysqli_fetch_assoc($result_img)){
								$img_array[] = $row_img;	
							}	
							return makeSliderContent($img_array);	
						}
				}
			}
		}
	}
	return "";
			
}

function makeSliderContent($img_array){
		$output_str = "";
		
		if(is_array($img_array) && sizeof($img_array) > 0){
		
			$output_str.= "<ul id=\"sliderContent\">";
						
			foreach($img_array as $row){
				$output_str.= "<li>";	
				$output_str.= "<img src=\"files/".$row['file_save_name']."\" alt=\"\" />";
				$output_str.= "</li>";		
			}	
			$output_str.= "</ul>";
			//slider navigation
			if(sizeof($img_array) > 1){
				$output_str.= "<a href=\"#\" id=\"slideFwd\"></a>";
				$output_str.= "<a href=\"#\" id=\"slideBwd\"></a>";
			}
		}
	
	return $output_str;	
}


?>