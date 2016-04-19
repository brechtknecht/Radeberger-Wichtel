<?php
//no external call
if(defined("internalCall")){
	if(isset($_POST['entry_id'])){
		//prepare data
		$_POST['news_start'] = substr($_POST['news_start'],6,4)."-".substr($_POST['news_start'],3,2)."-".substr($_POST['news_start'],0,2);
		if(!empty($_POST['news_end'])){
			if($_POST['news_end'] == "00.00.0000") {
				$_POST['news_end'] = "31.12.2999";
			}
			$_POST['news_end'] = substr($_POST['news_end'],6,4)."-".substr($_POST['news_end'],3,2)."-".substr($_POST['news_end'],0,2);
		}
		else{
			unset($_POST['news_end']);
		}
		if(!empty($_POST['news_date_end'])){
			$_POST['news_date_end'] = substr($_POST['news_date_end'],6,4)."-".substr($_POST['news_date_end'],3,2)."-".substr($_POST['news_date_end'],0,2);
		}
		
		$_POST['news_date'] = substr($_POST['news_date'],6,4)."-".substr($_POST['news_date'],3,2)."-".substr($_POST['news_date'],0,2);
		
		if(isset($_POST['entry_parent_id_new']) && !empty($_POST['entry_parent_id_new'])){
			$_POST['entry_parent_id'] = $_POST['entry_parent_id_new'];
		}
		if(!isset($_POST['news_show_on_startpage'])){
			$_POST['news_show_on_startpage'] = "0";
		}
		if(!isset($_POST['news_sticky'])){
			$_POST['news_sticky'] = "0";
		}
		
		$_POST['news_headline'] = json_encode($_POST['news_headline']);
		$_POST['news_teaser'] = json_encode($_POST['news_teaser']);
		$_POST['news_text'] = json_encode($_POST['news_text']);
		
		$fieldArray = array();
		$fieldArray[] = "entry_parent_id";
		$fieldArray[] = "news_date";
		$fieldArray[] = "news_date_end";
		$fieldArray[] = "news_time";
		$fieldArray[] = "news_location";
		$fieldArray[] = "news_start";
		$fieldArray[] = "news_end";
		$fieldArray[] = "news_headline";
		$fieldArray[] = "news_teaser";
		$fieldArray[] = "news_text";
		$fieldArray[] = "news_css_class";
		$fieldArray[] = "videos";
		$fieldArray[] = "news_show_on_startpage";
		$fieldArray[] = "news_sticky";
		
		if($result = insertUpdateTable("_cms_modules_news_", $_POST['entry_id'], $fieldArray, $_SESSION['cms_user']['user_id'])){
			if($_POST['entry_id'] == "new"){
				$_POST['entry_id'] = mysqli_insert_id($_SESSION['conn']);
			}
			
			//change position/sequence
			if(isset($_POST['new_position'], $_POST['position_mode'])){
				if(!empty($_POST['new_position']) && !empty($_POST['position_mode'])){
					//split new_position
					$position_array = explode("|",$_POST['new_position']);
					$new_sequence = $position_array[0];
					$new_parent = $position_array[1];
					$new_id = $position_array[2];
					
					changeSequence("_cms_modules_news_", $_POST['position_mode'], $_POST['entry_id'], $new_sequence, $new_parent, $new_id);
				}
			}
		}
		$_GET = $_POST;
	}
}
else{
	die("Fehler!");
}
?>