<?php
function slideShow($entry_id = 0, $file_cat1 = "", $file_cat2 = "", $file_cat3 = "", $max_width = 0, $sequence = array()){
	$output_str = "";
	//get images
	$query = "SELECT";
	$query.= " file_save_name AS file_save_name";
	$query.= ", file_img_width AS file_img_width";
	$query.= ", file_img_height AS file_img_height";
	$query.= ", file_desc AS file_desc";
	$query.= " FROM _cms_hp_files_";
	$query.= " WHERE entry_parent_id=".$entry_id;
	if(!empty($file_cat1)){
		$query.= " AND file_cat1='".$file_cat1."'";
	} 
	if(!empty($file_cat2)){
		$query.= " AND file_cat2='".$file_cat2."'";
	} 
	
	if($max_width > 0){
		$query.= " AND file_img_width>=".$max_width;
	}
	$query.= " ORDER BY RAND() LIMIT 10";
	
	$result = mysqli_query($_SESSION['conn'], $query);
	$count = mysqli_num_rows($result);
	if($count > 0){
		
		$output_str.= "<ul id=\"slideImagesList\">";
		
		while($row = mysqli_fetch_assoc($result)){
			$output_str.= "<li><img src=\"files/".$row['file_save_name']."\" alt=\"\" /></li>";
			
		}
		$output_str.= "</ul>";
	}
	
	return $output_str;
}

function pageBackground($gallery_id){
	$output_str = "";
	$query = "SELECT";
	$query.= " file_save_name AS file_save_name";
	$query.= " FROM _cms_hp_files_";
	$query.= " WHERE entry_parent_id=".$gallery_id;
	$query.= " AND file_cat1='module'";
	$query.= " AND file_cat2='galleries'";
	$query.= " ORDER BY RAND() LIMIT 1";
	
	if($result = mysqli_query($_SESSION['conn'], $query)){
		if($row = mysqli_fetch_assoc($result)){
			$output_str.= "<script type=\"text/javascript\">";		
			$output_str.= "var pageBackground=\"files/".$row['file_save_name']."\";";		
			$output_str.= "</script>";		
		}	
	}	
	return $output_str;
}
?>