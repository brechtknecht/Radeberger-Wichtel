<?php
$mod_param_array['filter']=array();
//get galleries	
$query_mod_param="SELECT entry_id, entry_name FROM _cms_modules_galleries_ ORDER BY entry_name ASC";
$result_mod_param=mysqli_query($_SESSION['conn'], $query_mod_param);

while($row_mod_param=mysqli_fetch_assoc($result_mod_param)){
	$mod_param_array['filter'][] = array($row_mod_param['entry_id'], $row_mod_param['entry_name']);
}
?>