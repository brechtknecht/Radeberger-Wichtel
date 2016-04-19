<?php
function showCalendar(){
	$query = "SELECT";
	$query.= " entry_id AS entry_id";
	$query.= ", news_headline AS title";
	$query.= ", news_date AS entry_date";
	$query.= ", news_teaser AS teaser";
	$query.= ", entry_parent_id AS category";
	//images
	$query.= ", (SELECT file_save_name FROM _cms_hp_files_ t2 WHERE t2.file_cat1='module' AND t2.file_cat2='news' AND t2.entry_parent_id=t1.entry_id ORDER BY t2.file_sequence ASC LIMIT 1) AS image";
	$query.= ", (SELECT COUNT(entry_id) FROM _cms_hp_files_ t2 WHERE t2.file_cat1='module' AND t2.file_cat2='news' AND t2.entry_parent_id=t1.entry_id) AS image_count";
	
	$query.= " FROM _cms_modules_news_ t1";
	$query.= " WHERE entry_parent_id='24'";
		
	//echo $query;
	$result = mysqli_query($_SESSION['conn'], $query);
	
	$entry_array = array();
	$date_array = array();
	while($row = mysqli_fetch_assoc($result)){
		$date_array[] = array($row['entry_date'],"termine,24.php#ne".$row['entry_id'], $row['entry_id'], $row['title']); 	
		$entry_array[] = $row; 	
	}
	
		
	if(!isset($_SESSION['year'], $_SESSION['month'])){
		$_SESSION['year'] = date("Y");
		$_SESSION['month'] = date("m") - 1;
	}
		
	if(isset($_GET['dir']) && $_GET['dir'] == "up"){
		$_SESSION['month']+= 1;
		if($_SESSION['month'] == 12){
			$_SESSION['month'] = 0;
			$_SESSION['year']+= 1;
		}		
	}
	if(isset($_GET['dir']) && $_GET['dir'] == "down"){
		$_SESSION['month']-= 1;
		if($_SESSION['month'] == -1){
			$_SESSION['month'] = 11;
			$_SESSION['year']-= 1;
		}		
	}
		
	//if($_GET['m'] == 11)
		
	$output_str = "";
	$output_str.= "<div id=\"calendar\">";
	$calendar = new Calendar;
	$calendar -> setMonthCount(1);
	$calendar -> setStartMonth($_SESSION['month']);
	$calendar -> setYear($_SESSION['year']);
	$calendar -> setMonthNames(array("Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember"));
	$calendar -> setDayNames(array("Mo","Di","Mi","Do","Fr","Sa","So"));
	$calendar -> setBookingArray($date_array);
	$output_str.= $calendar -> makeCal(); 
		
	//month navigation
	$output_str.= "<ul>";
	$output_str.= "<li><a href=\"termine,".$_GET['entry_id'].".php?dir=down\">&lt;&lt;</a></li>";
	$output_str.= "<li><a href=\"termine,".$_GET['entry_id'].".php?dir=up\">&gt;&gt;</a></li>";
	$output_str.= "</ul>";
		
	
	$output_str.= "</div>";
	return $output_str;
}
?>