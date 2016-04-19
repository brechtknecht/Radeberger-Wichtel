<?php
include_once("../shared/include/environment.inc.php");
foreach($_FILES as $file){
	$pathparts = pathinfo($file['name']);
	$ext = strtolower($pathparts["extension"]);
	if(isset($_REQUEST['rname']) && !empty($_REQUEST['rname'])){
	    $new_name = $_REQUEST['rname'];
	}
	else{
	    $id = (uniqueID());
	    
	    $new_name = $id.".".$ext;
	}
	
	$real_name = $file['name'];
	$target_dir = "../../files/";
	if(isset($_REQUEST['file_cat_new']) && !empty($_REQUEST['file_cat_new'])){
		$_REQUEST['file_cat1']=$_REQUEST['file_cat_new'];
	}
	//allowed file types
	if(isset($_GET['file_types'])){
		if(strstr($_GET['file_types'],",")){
			$file_types_array = explode(",",$_GET['file_types']);
			
		}
		else{
			$file_types_array = array($_GET['file_types']);
		}
		if(!in_array($ext,$file_types_array)){
			exit();
		}
	}
	
	if(move_uploaded_file($file['tmp_name'],$target_dir.$new_name)){
		
		//copy if png to remove alpha channel
		if($ext == "png"){
			//copyImage($target_dir.$new_name, $target_dir.$new_name);
		}
		
		$_REQUEST['max_width'] = isset($_REQUEST['max_width'])?$_REQUEST['max_width']:0;
		$_REQUEST['max_height'] = isset($_REQUEST['max_height'])?$_REQUEST['max_height']:0;
		
		
		//resize
		if($_REQUEST['mode'] == "image" && (isset($_REQUEST['max_width']) && $_REQUEST['max_width']!=0) || (isset($_REQUEST['max_height']) && $_REQUEST['max_height']!=0)){
			resizeImage($target_dir.$new_name,$_REQUEST['max_width'],$_REQUEST['max_height']);
		}

		//write in db
		//replace file?
		if(isset($_REQUEST['rid']) && !empty($_REQUEST['rid'])){
		    $_REQUEST['rid'] = intval($_REQUEST['rid']);
		    $query = "UPDATE _cms_hp_files_ SET";
		}
		else{
		    $query = "INSERT INTO _cms_hp_files_ SET";
		}

		$query.= " entry_parent_id='".mysqli_real_escape_string($_SESSION['conn'], $_REQUEST['entry_id'])."',";
		$query.= " file_save_name='".mysqli_real_escape_string($_SESSION['conn'], $new_name)."',";
		$query.= " file_real_name='".mysqli_real_escape_string($_SESSION['conn'], $real_name)."',";
		$query.= " file_size='".filesize($target_dir.$new_name)."',";
		$query.= " file_ext='".$ext."',";
		if($_REQUEST['mode'] == "image" && ($ext == "jpg" || $ext == "jpeg" || $ext == "gif" || $ext == "png" || $ext == "swf" || $ext == "flv")){
			$img_size = getimagesize($target_dir.$new_name);
			$img_width = $img_size[0];
			$img_height = $img_size[1];
			$query.= " file_img_width=".$img_width.",";
			$query.= " file_img_height=".$img_height.",";
			$query.= " file_type='image',";
		}
		else{
			$query.= " file_type='".$_REQUEST['mode']."',";
		}
		$query.= " file_desc='".(isset($_REQUEST['file_desc'])?mysqli_real_escape_string($_SESSION['conn'], $_REQUEST['file_desc']):"")."',";
		$query.= " file_cat1='".(isset($_REQUEST['file_cat1'])?mysqli_real_escape_string($_SESSION['conn'], $_REQUEST['file_cat1']):"")."',";
		$query.= " file_cat2='".(isset($_REQUEST['file_cat2'])?mysqli_real_escape_string($_SESSION['conn'], $_REQUEST['file_cat2']):"")."',";
		$query.= " file_cat3='".(isset($_REQUEST['file_cat3'])?mysqli_real_escape_string($_SESSION['conn'], $_REQUEST['file_cat3']):"")."',";
		$query.= " last_change=NOW()";

		if(isset($_REQUEST['rid']) && !empty($_REQUEST['rid'])){
		    $query.= ", file_exported=0";
		    $query.= " WHERE entry_id= ".$_REQUEST['rid']." LIMIT 1";
		}

		$result = mysqli_query($_SESSION['conn'], $query);
	}
}	

?>
