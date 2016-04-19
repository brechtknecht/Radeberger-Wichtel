<?php
function imageViewer($entry_id = 0, $file_cat1 = "", $file_cat2 = "", $file_cat3 = "", $max_width = 0, $sequence = array()){
	$output_str = "";
	//get images
	$query = "SELECT";
	$query.= " file_save_name AS file_save_name";
	$query.= ", file_img_width AS file_img_width";
	$query.= ", file_img_height AS file_img_height";
	$query.= ", file_real_name AS file_real_name";
	$query.= ", file_desc AS file_desc";
	$query.= " FROM _cms_hp_files_";
	$query.= " WHERE entry_parent_id=".$entry_id." AND file_ext='jpg' AND file_img_width>500";
	if(!empty($file_cat1)){
		$query.= " AND file_cat1='".$file_cat1."'";
	} 
	if(!empty($file_cat2)){
		$query.= " AND file_cat2='".$file_cat2."'";
	} 
	if(!empty($file_cat3)){
		$query.= " AND file_cat3='".$file_cat3."'";
	} 
	if($max_width > 0){
		//$query.= " AND file_img_width=".$max_width;
	}
	//$query.= " ORDER BY file_sequence ASC, entry_id ASC";
	$query.= " ORDER BY file_sequence ASC, file_real_name ASC";
	
	$result = mysqli_query($_SESSION['conn'], $query);
	
	if(mysqli_num_rows($result) > 0){
	    $i = 0;
		while($row = mysqli_fetch_assoc($result)){
			if($i == 0){
				$output_str.= "<a href=\"files/".$row['file_save_name']."\" rel=\"gallery\" class=\"gallery\">";
				$output_str.= "<img src=\"files/".$row['file_save_name']."\" alt=\"\" />";
				$output_str.= "<span>&gt;&gt;&gt;</span>";
				$output_str.= "</a>";			
			}
			else{
				$output_str.= "<a style=\"display: none;\" href=\"files/".$row['file_save_name']."\" class=\"gallery\" rel=\"gallery\">".$row['file_real_name']."</a>";	
			}
			$i++;
		}
		
		$output_str.= "<script type=\"text/javascript\">";
   		$output_str.= "$('a.gallery').colorbox({rel:'gallery', maxHeight: \"90%\", maxWidth: \"90%\"});"; 
    	$output_str.= "</script>";
		
		
	}
	
	return $output_str;
}

function getImagePageId($id){
	if(!empty($id)){
		$id = intval($id);
		$query = "SELECT";
		$query.= " entry_id AS entry_id";
		$query.= ", entry_parent_id AS entry_parent_id";
		$query.= ", (SELECT COUNT(t2.entry_id) FROM _cms_hp_files_ t2 WHERE t2.file_cat1='page'";
		$query.= " AND t2.entry_parent_id=".$id." AND t2.file_ext='jpg' AND t2.file_img_width>500) AS img_count";
		$query.= " FROM _cms_hp_navigation_ t1";
		$query.= " WHERE t1.entry_id=".$id; 
		
		$result = mysqli_query($_SESSION['conn'], $query);
		
		if(mysqli_num_rows($result) > 0){
			$row = mysqli_fetch_assoc($result);
			if($row['img_count'] > 0){
				return $id;
			}
			else{
				if($row['entry_parent_id'] > 0){
					return getImagePageId($row['entry_parent_id']);	
				}
			}
		}
		return $id;
		
	
	}	
}
?>