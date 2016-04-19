<?php
include_once("../../admin/shared/include/environment.inc.php");

$module_name = getParentDir($_SERVER['SCRIPT_FILENAME']);

//get entries
$bgclass="col1";
function getEntries($entry_parent_id,$indent = 10){
	global $bgclass, $module_name;
	$query = "SELECT * FROM _cms_modules_galleries_ WHERE entry_parent_id=".intval($entry_parent_id)." ORDER BY entry_category ASC, entry_sequence ASC, entry_name ASC";
	$result = mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		while($row=mysqli_fetch_assoc($result)){
			$result_tmp=mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_modules_galleries_ WHERE entry_parent_id=".$row['entry_id']);
			$has_sub = mysqli_num_rows($result_tmp);
			
			if($bgclass=="col2"){
				$bgclass="col1";
			}
			else{
				$bgclass="col2";
			}
			echo "<tr class=\"".$bgclass."\">";
			echo "<td nowrap=\"nowrap\">";
			echo "<a href=\"edit.php?entry_id=".$row['entry_id']."\"><img src=\"../../admin/shared/images/editable.gif\" alt=\"\" title=\"Galerie bearbeiten\" /></a>";  
			if($row['entry_category'] != "System"){
				if(checkPermission("user",$_SESSION['cms_user']['user_id'],"module",$module_name,"function","del")){
					echo "<img src=\"../../admin/shared/images/delete.gif\" alt=\"\" title=\"Eintrag löschen\" onclick=\"MODULE_deleteEntry(".$row['entry_id'].",this.parentNode.parentNode);\" />";
				}
			}
			echo "</td>";		
			echo "<td>".($row['entry_category'])."</td>";
			echo "<td style=\"padding-left:".$indent."px;".($row['entry_parent_id'] == 0?"font-weight: bold":"")."\">".($row['entry_name'])."</td>";
			echo "<td>".(formatDate2Local($row['last_change'],"dd.mm.YYYY",false,false,false))." von ".getUserName($row['entry_last_usr'])."</td>";
			echo "<td>&nbsp;";
			echo "</td>";
			
			echo "</tr>";
			
			if(isset($has_sub) && $has_sub > 0){
				$indent+= 20;
				getEntries($row['entry_id'],$indent);
				$indent-= 20;
			}
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>zeemes : web content management system : modules : <?php echo $module_name;?></title>
<script type="text/javascript" src="../../admin/shared/javascript/functions.js">
</script>
<script type="text/javascript" src="javascript/module_edit.js">
</script>
<link rel="stylesheet" href="../../admin/shared/css/styles.css" />
</head>

<body onload="SHARED_scrollTBody('listTable',SHARED_getAvailHeight(document.body,'listTable'));">
<h2><?php echo strtoupper($module_name);?></h2>
<?php
	if(checkPermission("user",$_SESSION['cms_user']['user_id'],"module",$module_name,"function","new")){
	?>
<div id="saveTools">
	<button type="button" onclick="location.href='edit.php?entry_id=new'">Neuer Eintrag</button>
</div>
<?php
    	}
	?>
<table cellpadding="0" cellspacing="0" id="listTable" class="table">
	<thead>
    <tr>
    	<th style="width: 60px">Aktionen</th>
        <th style="width: 120px">Kategorie</th>
        <th style="width: 300px">Name</th>
    	<th style="width: 250px">letzte Aktualisierung</th>
        <th style="width: auto">Status</th>
        
    </tr>
    </thead>
    <tbody>
		
	<?php
	getEntries(0);
	?>
    </tbody>
  
</table>
</body>
</html>
