<?php
include_once("../../../../admin/shared/include/environment.inc.php");
$module_name = getParentDir($_SERVER['SCRIPT_FILENAME']);
if(checkPermission("user",$_SESSION['cms_user']['user_id'],"module",$module_name)==false){
	die("Sie haben keine Berechtigung auf dieses Element zuzugreifen!");
}
if($_SERVER['REQUEST_METHOD']=="POST"){
	include("save.inc.php");
}

//get data	
if(isset($_GET['entry_id'])){
	$query="SELECT * FROM _cms_modules_produkte_kategorien_ WHERE entry_id=".intval($_GET['entry_id'])." LIMIT 1";
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		$row=mysqli_fetch_assoc($result);
		$row['entry_name'] = json_decode($row['entry_name'], true);
		$row['entry_desc'] = json_decode($row['entry_desc'], true);
	}
}

//get entries for position
function getEntries($entry_parent_id=0,$indent){
	$query="SELECT entry_id,entry_parent_id,entry_name, entry_sequence";
	$query.=" FROM _cms_modules_produkte_kategorien_ WHERE entry_parent_id=".$entry_parent_id." ORDER BY entry_sequence ASC";
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		while($row=mysqli_fetch_assoc($result)){
			$result_sub=mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_modules_produkte_kategorien_ WHERE entry_parent_id=".$row['entry_id']." ORDER BY entry_name ASC");
			echo "<option value=\"".$row['entry_sequence']."|".$row['entry_parent_id']."|".$row['entry_id']."\">".($indent.$row['entry_name'])."</option>";
			if(mysqli_num_rows($result_sub)>0){
				$indent.="-----";
				getEntries($row['entry_id'],$indent);
				$indent=substr($indent,0,strlen($indent)-strlen("-----"));
			}
		}
	}
}

//get pages
function getPageEntries($entry_parent_id = 0,$mode = "",$entry_url = "",$indent = ""){
	
	$query="SELECT entry_id,entry_parent_id,entry_name,entry_sequence";
	$query.=" FROM _cms_hp_navigation_ WHERE entry_parent_id=".$entry_parent_id;
	$query.=" ORDER BY entry_sequence ASC";
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		while($row=mysqli_fetch_assoc($result)){
			$result_sub=mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_hp_navigation_ WHERE entry_parent_id=".$row['entry_id']." AND entry_deleted=0 ORDER BY entry_sequence");
			
			$url = $row['entry_name'].",".$row['entry_id'].".php";
			echo "<option value=\"".$url."\" ".($url == $entry_url?"selected=\"selected\"":"").">".($indent.$row['entry_name'])."</option>";
									
			if(mysqli_num_rows($result_sub)>0){
				$indent.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				getPageEntries($row['entry_id'],$mode,$entry_url,$indent);
				$indent=substr($indent,0,strlen($indent)-strlen("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"));
			}
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>zeemes : web content management system : modules : kategorien : edit</title>
<script type="text/javascript" src="../../../../admin/shared/javascript/functions.js">
</script>
<script type="text/javascript" src="../../../../admin/pages/javascript/functions.js">
</script>
<link rel="stylesheet" href="../../../../admin/shared/css/styles.css" />
</head>

<body id="content" onload="SHARED_showFieldSetSwitch('form0');">
<h2><?php echo strtoupper($module_name);?>: <?php echo isset($row['entry_name'])?$row['entry_name']['de']:"neuen Eintrag anlegen";?></h2>
<div id="saveTools">
	<button type="button" onclick="SHARED_submitForm(document.form0,'Änderungen übernehmen?')">Änderungen übernehmen</button>
  	<button type="button" onclick="location.href='index.php'">zurück zur Liste</button>
</div><?php
if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['save'])){
	echo "<span class=\"showState\">Ihre Änderungen wurden gespeichert</span>";
}
?>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" id="form0" name="form0" enctype="multipart/form-data" onSubmit="return SHARED_submitForm(this,'Änderungen übernehmen?')">
  <input type="hidden" name="entry_id" value="<?php echo $_GET['entry_id'];?>" />
  <table cellpadding="0" cellspacing="0" class="table">
    <thead>
      <tr>
        <th style="width: 100px;">&nbsp;</th>
        <th style="width: auto;">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>letzte Aktualisierung</td>
        <td style="padding-left: 5px"><?php echo isset($row['last_change'])?$row['last_change']:"";?><?php echo isset($row['entry_last_usr'])?" von ".getUserName($row['entry_last_usr']):"Datensatz noch nicht angelegt";?></td>
      </tr>
      <tr>
        <tr>
        <td>Position</td>
        <td>
        	<select name="position_mode" style="width: auto;">
            	<option value="">Position</option>
                <option value="before">vor</option>
                <option value="behind">nach</option>
                <option value="submenu">Unterkategorie von</option>
            </select>
            <select name="new_position" style="width: auto;margin-left:5px;">
          		<option value="">bezogen auf</option>
			<?php
			  getEntries(0);
			?>
        	</select>        </td>
      </tr>
       <tr>
      	<td colspan="2" class="betweenHeader">
        	Deutsch</td>
      </tr>
       <tr>
        <td>Name</td>
        <td>
        	<input type="text" name="entry_name[de]" value="<?php echo isset($row['entry_name'])?stripslashes(str_replace("\"","'",$row['entry_name']['de'])):"";?>" />        </td>
      </tr>
      <tr>
        <td>Beschreibung</td>
        <td>
        	<textarea name="entry_desc[de]" rows="10" cols="100"><?php echo isset($row['entry_desc'])?stripslashes($row['entry_desc']['de']):"";?></textarea>       </td>
      </tr>
      
       <tr>
      	<td colspan="2" class="betweenHeader">
        	Englisch</td>
      </tr>
       <tr>
        <td>Name</td>
        <td>
        	<input type="text" name="entry_name[en]" value="<?php echo isset($row['entry_name'])?stripslashes(str_replace("\"","'",$row['entry_name']['en'])):"";?>" />        </td>
      </tr>
      <tr>
        <td>Beschreibung</td>
        <td>
        	<textarea name="entry_desc[en]" rows="10" cols="100"><?php echo isset($row['entry_desc'])?stripslashes($row['entry_desc']['en']):"";?></textarea>       </td>
      </tr>
      
     
       <tr>
        <td>Zielseite</td>
        <td>
        	
            <select name="entry_url" style="width: auto">
               <?php
               echo getPageEntries(0,"",$row['entry_url']);
			   ?>
               
                            
            </select>
        </td>
      </tr>
      
      <?php
	  if(isset($row)){
	  ?>
      <tr>
      	<td colspan="2" class="betweenHeader">
        	Bilder</td>
      </tr>
      <tr>
        <td>Bild bearbeiten</td>
        <td>
        <button type="button" onclick="window.open('../../../../admin/filemanager?entry_id=<?php echo isset($row['entry_id'])?$row['entry_id']:"";?>&amp;mode=image&amp;file_cat1=module&amp;file_cat2=produktkategorien&amp;max_width=600&amp;sessionid=<?php echo session_id();?>','Filemanager','width=750,height=550')">Dateien bearbeiten</button>        </td>
      </tr>
      <?php
      }
	  ?>
	
      </tbody>
    <tfoot>
    </tfoot>
  </table>
  
  
  
 
    
</form>
</body>
</html>
