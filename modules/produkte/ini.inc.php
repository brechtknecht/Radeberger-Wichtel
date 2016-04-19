<?php
$mod_param_array = array();
$mod_param_array['category'] = array();

$query="SELECT entry_id,entry_parent_id,entry_name,entry_kategorie,last_change";
$query.=" FROM _cms_modules_produkte_kategorien_";
$query.=" ORDER BY entry_sequence ASC";

if($result_filter = mysqli_query($_SESSION['conn'], $query)){
	while($row_filter = mysqli_fetch_assoc($result_filter)){
		$mod_param_array['category'][] = array($row_filter['entry_id'], $row_filter['entry_name']);
	}
}
?>