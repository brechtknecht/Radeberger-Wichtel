<?php
$mod_param_array['kategorie']=array();
$mod_param_array['kategorie'][] = array("0", "Startseite");
//get pages
function getModEntries($entry_parent_id=0,$mode="",$entry_id=0,$indent=""){
	global $mod_param_array;
	$query="SELECT entry_id,entry_parent_id,entry_name,entry_sequence";
	$query.=" FROM _cms_hp_navigation_ WHERE entry_parent_id=".$entry_parent_id;
	$query.=" ORDER BY entry_sequence ASC";
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		while($row=mysqli_fetch_assoc($result)){
			$result_sub=mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_hp_navigation_ WHERE entry_parent_id=".$row['entry_id']." AND entry_deleted=0 ORDER BY entry_sequence");
			$row['entry_name'] = str_replace("\"", "", $row['entry_name']);
			$mod_param_array['kategorie'][] = array($row['entry_id'], $indent.$row['entry_name']);
			//echo "<option value=\"".$row['entry_id']."\" ".($entry_id==$row['entry_id']?"selected=\"selected\"":"").">".($indent.$row['entry_name'])."</option>";
										
			if(mysqli_num_rows($result_sub)>0){
				$indent.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				getModEntries($row['entry_id'],$mode,$entry_id,$indent);
				$indent=substr($indent,0,strlen($indent)-strlen("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"));
			}
		}
	}
}

getModEntries(0);

$mod_param_array['mode'] = array();
$mod_param_array['mode'][] = array("agentur", "Anzeigemodus: Agentur");
$mod_param_array['mode'][] = array("team", "Anzeigemodus: Team");


?>