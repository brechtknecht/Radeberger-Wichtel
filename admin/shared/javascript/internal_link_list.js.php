<?php
include("../include/environment.inc.php");
echo "var tinyMCELinkList = new Array(";
function getEntries($entry_parent_id,$indent=""){
	$query = "SELECT"; 
	$query.= " entry_id AS entry_id";
	$query.= " ,entry_parent_id AS entry_parent_id";
	$query.= " ,entry_name AS entry_name";
	$query.= " ,last_change AS last_change";
	$query.= " ,entry_last_usr AS entry_last_usr";
	$query.= " ,entry_inc_module AS entry_inc_module";
	$query.= " ,(SELECT COUNT(entry_id) FROM _cms_hp_navigation_ t2 WHERE t2.entry_parent_id=t1.entry_id) AS sub_count";
	$query.= " ,(SELECT entry_navi_name FROM _cms_hp_pages_ t3 WHERE t3.entry_parent_id=t1.entry_id AND entry_state='public' LIMIT 1) AS navi_name";
	$query.= " FROM _cms_hp_navigation_ t1 WHERE entry_parent_id=".$entry_parent_id; 
	$query.= " ORDER BY entry_sequence ASC";
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		
		while($row=mysqli_fetch_assoc($result)){
			if($row['sub_count'] > 0){
				$has_sub = 1;
			}
			echo "[\"".$indent.addslashes($row['entry_name'])."\", \"".make_html_file($row['navi_name']).",".$row['entry_id'].".php\"],";
			
			if(isset($has_sub) && $has_sub==1){
				$indent.="-----";
				getEntries($row['entry_id'],$indent);
				$indent=substr($indent,0,strlen($indent)-5);
			}
			
		}
		
	}
}
getEntries(0);
echo "[\"\",\"\"]);";

function make_html_file($word){
	$word = str_replace("ä","ae",$word);
	$word = str_replace("ö","oe",$word);
	$word = str_replace("ü","ue",$word);
	$word = str_replace("Ä","ae",$word);
	$word = str_replace("Ö","oe",$word);
	$word = str_replace("Ü","ue",$word);
	$word = str_replace(" ","-",$word);
	$word = str_replace("ß","ss",$word);
	$word = str_replace("/","_",$word);
	$word = strtolower($word);
	return $word;
}

?>

