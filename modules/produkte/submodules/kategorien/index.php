<?php
include_once("../../../../admin/shared/include/environment.inc.php");
$module_name = getParentDir($_SERVER['SCRIPT_FILENAME']);
if(checkPermission("user",$_SESSION['cms_user']['user_id'],"module",$module_name)==false){
	die("Sie haben keine Berechtigung auf dieses Element zuzugreifen!");
}
//get entries
$bg_color="#E0E0E0";
function getEntries($entry_id=0,$entry_parent_id,$indent=0,$mode="table"){
	global $bg_color;
	$query="SELECT entry_id,entry_parent_id,entry_name,entry_kategorie,last_change";
	$query.=" FROM _cms_modules_produkte_kategorien_";
	if($entry_id!=0){
		$query.=" WHERE entry_id=".$entry_id;
	}
	else{
		$query.=" WHERE entry_parent_id=".$entry_parent_id;
	}
	
	$query.=" ORDER BY entry_sequence ASC";
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		while($row=mysqli_fetch_assoc($result)){
			$row['entry_name'] = json_decode($row['entry_name'], true);
			$result_tmp=mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_modules_produkte_kategorien_ WHERE entry_parent_id=".$row['entry_id']);
			if(mysqli_num_rows($result_tmp)>0){
				$has_sub=1;
			}
			if($mode=="table"){
				if($bg_color=="#E0E0E0"){
					$bg_color="#EBEBEB";
				}
				else{
					$bg_color="#E0E0E0";
				}
				echo "<tr style=\"background-color: ".$bg_color."\" onmouseover=\"myColor=this.style.backgroundColor;this.style.backgroundColor='#FFFFFF'\" onmouseout=\"this.style.backgroundColor=myColor\">";
				echo "<td nowrap=\"nowrap\" class=\"editEntry\">";
				echo "<a href=\"edit.php?entry_id=".$row['entry_id']."\"><img src=\"".$_SESSION['global_vars']['path_to_root']."admin/shared/images/editable.gif\" alt=\"\" title=\"Eintrag bearbeiten\" /></a>";  
				echo "<img src=\"".$_SESSION['global_vars']['path_to_root']."admin/shared/images/delete.gif\" alt=\"\" title=\"Eintrag löschen\" onclick=\"MODULE_deleteEntry(".$row['entry_id'].",this.parentNode.parentNode);\" />";
				echo "</td>";
				echo "<td style=\"padding-left:".$indent."px\">".($row['entry_name']['de'])."</td>";
				echo "<td>".(formatDate2Local($row['last_change'],"dd.mm.YYYY",false,false,false))."</td>";
				
				echo "</tr>";
				if(isset($has_sub) && $has_sub==1){
					$indent+=40;
					getEntries(0,$row['entry_id'],$indent,$mode);
					$indent-=40;
				}
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
<script type="text/javascript" src="../../../../admin/shared/javascript/functions.js">
</script>
<script type="text/javascript" src="javascript/module_edit.js">
</script>
<link rel="stylesheet" href="../../../../admin/shared/css/styles.css" />
</head>

<body id="content" onload="SHARED_scrollTBody('listTable',SHARED_getAvailHeight(document.body,'listTable'));">
<h2><?php echo strtoupper($module_name);?></h2>
<?php
if($_SERVER['REQUEST_METHOD']=="POST"){
	echo "<span class=\"showState\">Ihre Änderungen wurden gespeichert</span>";
}
?>
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
   
    <tr class="headRow">
    	<th style="width: 40px">&nbsp;</th>
        <th style="width: 250px">Name</th>
    	<th style="width: auto">letzte Aktualisierung</th>
        
    </tr>
    </thead>
    <tbody>
		
	<?php
	getEntries(isset($_SESSION['filter_page'])?$_SESSION['filter_page']:0,0,25,"table");
	?>
    </tbody>
    
</table>
</body>
</html>
