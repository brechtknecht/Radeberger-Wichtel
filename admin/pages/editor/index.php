<?php
include("../../shared/include/environment.inc.php");
isLoggedIn();
if(!checkPermission("user",$_SESSION['cms_user']['user_id'],"page",$_REQUEST['entry_id']) && !checkPermission("user",$_SESSION['cms_user']['user_id'],"page","all")){
	die("Sie haben keine Berechtigung auf dieses Element zuzugreifen!");
}
if($_SERVER['REQUEST_METHOD']=="POST"){
	include("save.inc.php");
}
//get page name
if(isset($_GET['entry_id'])){
	$query="SELECT entry_name FROM _cms_hp_navigation_ WHERE entry_id=".intval($_GET['entry_id'])." LIMIT 1";
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		$row=mysqli_fetch_assoc($result);
	}
	//$$result=mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_hp_pages_ WHERE entry_parent_id=".intval($_GET['entry_id'])." AND entry_stateentry_lang='de' ");
}

//get versions
if(isset($_GET['entry_id']) && !empty($_GET['entry_id'])){
	$query="SELECT";
	$query.=" _cms_hp_pages_.changed_by as changed_by,";
	$query.=" _cms_hp_pages_.entry_id as entry_id,";
	$query.=" _cms_hp_pages_.last_change as last_change,";
	$query.=" _cms_hp_pages_.entry_lang as entry_lang,"; 
	$query.=" _cms_hp_pages_.entry_state as entry_state"; 
	$query.=" FROM _cms_hp_navigation_ INNER JOIN _cms_hp_pages_";
	$query.=" ON _cms_hp_navigation_.entry_id=_cms_hp_pages_.entry_parent_id";
	$query.=" WHERE _cms_hp_navigation_.entry_id=".intval($_GET['entry_id']);
	
	$query.=" ORDER BY _cms_hp_pages_.last_change DESC";
	$result_versions=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result_versions)>0){
		$versions_array=array();
		while($row_versions=mysqli_fetch_assoc($result_versions)){
			if(!isset($versions_array[$row_versions['entry_lang']])){
				$versions_array[$row_versions['entry_lang']]=array();
			}
			if(isset($_SESSION['cms_user']['user_role']) && $_SESSION['cms_user']['user_role']=="Redakteur" && ($row_versions['entry_state']!="entwurf" && $row_versions['entry_state']!="public")){
				continue;
			}
			$versions_array[$row_versions['entry_lang']][$row_versions['entry_id']]['changed_by']=$row_versions['changed_by'];
			$versions_array[$row_versions['entry_lang']][$row_versions['entry_id']]['last_change']=$row_versions['last_change'];
			$versions_array[$row_versions['entry_lang']][$row_versions['entry_id']]['entry_state']=$row_versions['entry_state'];
			$versions_array[$row_versions['entry_lang']][$row_versions['entry_id']]['entry_id']=$row_versions['entry_id'];
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>zeemes | web content management system | pages : editor</title>
<script type="text/javascript" src="../../shared/javascript/functions.js"></script>
<script type="text/javascript" src="../javascript/functions.js"></script>
<link rel="stylesheet" href="../../shared/css/styles.css" />
<style type="text/css">
#editorFrame	{
	width: 100%;
	height: 1px;
}
</style>
</head>

<body onload="EDITOR_setHeight();">
<form name="saveForm" action="#" method="post" style="display: none;">
	<?php
    if(isset($_SESSION['fid'])){
		?>
         <input type="hidden" name="fid" value="<?php echo htmlspecialchars($_SESSION['fid'])?>" />
		<?php	
	}
	?>
    <input type="hidden" name="entry_id" value="<?php echo htmlspecialchars($_GET['entry_id']);?>" />
    <input type="hidden" name="page_version_id" value="<?php echo $_GET['page_version_id'];?>" />
    <input type="hidden" name="content" value="" />
    <input type="hidden" name="boxes" value="" />
    <input type="hidden" name="publish" value="0" />
</form>
<h2>Seite editieren: <?php echo isset($row['entry_name'])?$row['entry_name']:"";?></h2>
<div id="saveTools">
	
    <?php
	if($_SESSION['cms_user']['user_role']=="Chefredakteur" || $_SESSION['cms_user']['user_role']=="Administrator"){
	?>
    <button type="button" onclick="EDITOR_saveContent(1)">Veröffentlichen</button> 
    <?php
	}
	?>
    <button type="button" onclick="location.href='../'">zurück zur Liste</button>
    <?php
  if(isset($_GET['entry_id']) && $_GET['entry_id']!="new"){
  ?>
  	<button type="button" onclick="location.href='../organize/index.php?entry_id=<?php echo $_GET['entry_id'];?>'">Seite verwalten</button>
    <button type="button" onclick="location.href='../organize/index.php?entry_id=new'">Neuer Eintrag</button>
  <?php
  }
  ?>
</div>
<div style="padding: 3px; padding-left: 5px; border-bottom: 2px solid #FFFFFF;background-color: #CCCCCC;">
	Version wählen: <select style="width: 500px" onchange="EDITOR_loadPageVersion(<?php echo $_GET['entry_id']?>,this.value);">
    	<option value="">verfügbare Versionen</option>
        <?php
		if(isset($versions_array) && sizeof($versions_array)>0){
			foreach($_SESSION['lang_array'] as $val){
				?>
                <optgroup label="Sprache: <?php echo $val;?>">
                <?php 
				foreach($versions_array[$val] as $versions){
				?>
                	<option value="<?php echo $versions['entry_id'];?>" <?php echo isset($_GET['page_version_id'])&&$_GET['page_version_id']==$versions['entry_id']?"selected=\"selected\"":"";?>><?php echo $versions['last_change'];?>, Status: <?php echo $versions['entry_state'];?>, geändert von: <?php echo getUserName($versions['changed_by']);?></option>
                <?php
				}
			?>
			</optgroup>
			<?php
            }
		}
		?>
    </select>
</div>

<iframe id="editorFrame" name="editorFrame" src="../../../index.php?entry_id=<?php echo $_GET['entry_id']?>&amp;page_version_id=<?php echo $_GET['page_version_id'];?>&amp;page_mode=edit" frameborder="0"></iframe>
</div>
</body>
</html>
