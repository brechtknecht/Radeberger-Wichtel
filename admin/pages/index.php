<?php
include("../shared/include/environment.inc.php");
isLoggedIn();
//filter pages
if(isset($_GET['filter_page'])){
	if(!empty($_GET['filter_page'])){
		$_SESSION['filter_page']=intval($_GET['filter_page']);
	}
	else{
		unset($_SESSION['filter_page']);
	}
}

//get pages
$bg_color="#E0E0E0";
function getEntries($entry_id=0,$entry_parent_id,$indent=0,$mode=""){
	global $bg_color;
	$query="SELECT entry_id,entry_parent_id,entry_name,last_change,entry_inc_module,entry_last_usr";
	//$query.=", (SELECT COUNT(entry_id) FROM _cms_perm_ WHERE _cms_perm_.perm_cat='user' AND _cms_perm_.user_id=".$_SESSION['cms_user']['user_id']." AND _cms_perm_.perm_name='page' AND _cms_perm_.perm_value=_cms_hp_navigation_.entry_id) as user_perm";
	//$query.=", (SELECT entry_id FROM _cms_hp_pages_ WHERE _cms_hp_pages_.entry_parent_id=_cms_hp_navigation_.entry_id AND _cms_hp_pages_.entry_lang='de' AND (_cms_hp_pages_.entry_state='public' OR _cms_hp_pages_.entry_state='entwurf') ORDER BY _cms_hp_pages_.entry_state DESC LIMIT 1) as page_version_id";
	$query.=" FROM _cms_hp_navigation_";
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
			$result_tmp=mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_hp_pages_ WHERE entry_parent_id=".$row['entry_id']." AND entry_lang='de' AND (_cms_hp_pages_.entry_state='public' OR _cms_hp_pages_.entry_state='entwurf') ORDER BY entry_state DESC LIMIT 1");
			if(mysqli_num_rows($result_tmp)==1){
				$row_tmp=mysqli_fetch_assoc($result_tmp);
				$row['page_version_id']=$row_tmp['entry_id'];
			}
			$result_tmp=mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_hp_navigation_ WHERE entry_parent_id=".$row['entry_id']." AND entry_deleted=0 ORDER BY entry_sequence");
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
						
				$row['entry_name']=str_replace("-||"," ",$row['entry_name']);
				$row['entry_name']=str_replace("||"," ",$row['entry_name']);
			
				echo "<td nowrap=\"nowrap\">";
				if(checkPermission("user",$_SESSION['cms_user']['user_id'],"page",$row['entry_id']) || checkPermission("user",$_SESSION['cms_user']['user_id'],"page","all")){
					echo "<a href=\"editor/index.php?entry_id=".$row['entry_id']."&amp;page_version_id=".$row['page_version_id']."\"><img src=\"../shared/images/editable.gif\" alt=\"\" title=\"Seiteninhalt bearbeiten\" /></a>";
					echo "<a href=\"organize/index.php?entry_id=".$row['entry_id']."\"><img src=\"../shared/images/organize.gif\" alt=\"\" title=\"Seite verwalten\")\" /></a>";  
					echo "<img src=\"../shared/images/delete.gif\" alt=\"\" title=\"Seite löschen\" onclick=\"ORGANIZE_deletePage(".$row['entry_id'].",this.parentNode.parentNode)\" />";  
				}
				/*
				if(isset($_SESSION['cms_user']) && ($_SESSION['cms_user']['user_role']=="Administrator" || ($_SESSION['cms_user']['user_role']=="Chefredakteur" && isset($row['user_perm']) && $row['user_perm']==1))){
					echo "<a href=\"organize/index.php?entry_id=".$row['entry_id']."\"><img src=\"../shared/images/organize.gif\" alt=\"\" title=\"Seite verwalten\")\" /></a>";  
					echo "<img src=\"../shared/images/delete.gif\" alt=\"\" title=\"Seite löschen\" onclick=\"ORGANIZE_deletePage(".$row['entry_id'].",this.parentNode.parentNode)\" />";
				}
				*/
					
				echo "</td>";
			
				echo "<td".($indent>0?" style=\"padding-left:".$indent."px\"":"").">".($row['entry_name'])."</td>";
				echo "<td>".$row['last_change']." von ".getUserName($row['entry_last_usr'])."</td>";
				echo "<td>";
				echo strtoupper(str_replace("_"," ",$row['entry_inc_module']));
				echo "</td>";
				
				echo "</tr>";
				if(isset($has_sub) && $has_sub==1){
					$indent+=30;
					getEntries(0,$row['entry_id'],$indent,$mode);
					$indent-=30;
				}
			}	
			if($mode=="select"){
				echo "<option value=\"".$row['entry_id']."\" ".(isset($_SESSION['filter_page'])&&$_SESSION['filter_page']==$row['entry_id']?"selected=\"selected\"":"").">";
				echo $indent."&nbsp;".$row['entry_name'];
				echo "</option>";
				if(isset($has_sub) && $has_sub==1){
					$indent.="-----";
					getEntries(0,$row['entry_id'],$indent,$mode);
					$indent=substr($indent,0,strlen($indent)-5);
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
<title>zeemes : web content management system : organize page</title>
<script type="text/javascript" src="../shared/javascript/functions.js"></script>
<script type="text/javascript" src="javascript/functions.js"></script>
<link rel="stylesheet" href="../shared/css/styles.css" />
</head>

<body id="content" onload="SHARED_scrollTBody('listTable',SHARED_getAvailHeight(document.body,'listTable'));">
<h2>Seiten bearbeiten</h2>
<div id="saveTools">
	<button type="button" onclick="location.href='organize/index.php?entry_id=new'" style="margin: 0">Neuer Eintrag</button>
</div>
<table cellpadding="0" cellspacing="0" id="listTable" class="table">
	<thead>
    <tr class="filterRow">
    	<th style="width: 300px" colspan="4">
        	<form action="index.php" method="get">
               <?php
    if(isset($_SESSION['fid'])){
		?>
         <input type="hidden" name="fid" value="<?php echo htmlspecialchars($_SESSION['fid'])?>" />
		<?php	
	}
	?>
                <select name="filter_page" style="width: auto" onchange="this.parentNode.submit()">
                    <option value="">alle Seiten anzeigen</option>
                    <?php
                    getEntries(0,0,"","select");
                    ?>
                </select>
            </form>
        </th>
   	</tr>
    <tr>
    	<th style="width: 60px">Aktionen</th>
        <th style="width: 400px">Name</th>
    	<th style="width: 300px">letzte Aktualisierung</th>
        <th style="width: auto">Modul</th>
        
    </tr>
    </thead>
    <tbody>
	<?php
	getEntries(isset($_SESSION['filter_page'])?$_SESSION['filter_page']:0,0,0,"table");
	?>
    </tbody>
    
</table>
</body>
</html>
