<?php
include_once("../../admin/shared/include/environment.inc.php");
$module_name = getParentDir($_SERVER['SCRIPT_FILENAME']);
if(strstr($module_name,"\\")){
	$module_name = substr($module_name,strrpos($module_name,"\\")+1);
}

if(checkPermission("user",$_SESSION['cms_user']['user_id'],"module",$module_name)==false){
	die("Sie haben keine Berechtigung auf dieses Element zuzugreifen!");
}
if($_SERVER['REQUEST_METHOD']=="POST"){
	include("save.inc.php");
}

//kategorieon
$query="SELECT entry_id, entry_name FROM _cms_modules_produkte_kategorien_ ORDER BY entry_name ASC";
$result_kategorien=mysqli_query($_SESSION['conn'], $query);

function makeCatList($table = "", $field = "", $entry_parent_id = 0, $output_str = "", $current_parent = 0){
	global $row;
	//$output_str = "";
	if(!empty($table)){
		$query_filter = "SELECT entry_id AS entry_id, entry_name AS entry_name,";
		$query_filter.= "(SELECT COUNT(entry_id) FROM ".$table." t2 WHERE t2.entry_parent_id=t1.entry_id) AS sub_count";
		$query_filter.= " FROM ".$table." t1 WHERE entry_parent_id=".$entry_parent_id." ORDER BY entry_name ASC";
		$result_filter=mysqli_query($_SESSION['conn'], $query_filter);
		if(mysqli_num_rows($result_filter) > 0){
			$output_str.= "<ul>";
					
			while($row_filter = mysqli_fetch_assoc($result_filter)){
				if($entry_parent_id == 0){
					$current_parent = $row_filter['entry_id'];
				}
				$output_str.= "<li>";	
				$output_str.= "<label ".(isset($_SESSION['filter'][$field."_filter"][$current_parent]) && in_array($row_filter['entry_id'],$_SESSION['filter'][$field."_filter"][$current_parent])?"style=\"font-weight: bold\"":"").">";	
				$output_str.= "<input type=\"checkbox\" name=\"";
				$output_str.= "entry_kategorie[]";
				$output_str.= "\" value=\"".$row_filter['entry_id']."\" style=\"width: auto; vertical-align: bottom\" ".(isset($row['entry_kategorie'])&&strstr($row['entry_kategorie'],"|".$row_filter['entry_id']."|")?"checked=\"checked\"":"")." />";
				if($row_filter['sub_count'] > 0){
					//$output_str.= "<strong>";	
				}
				$output_str.= $row_filter['entry_name'];
				if($row_filter['sub_count'] > 0){
					//$output_str.= "</strong>";	
				}
				$output_str.= "</label>";
				if($row_filter['sub_count'] > 0){
					$output_str.= makeCatList($table, $field, $row_filter['entry_id'], "", $current_parent);
				}	
				$output_str.= "</li>";	
			}
			$output_str.= "</ul>";
			
		}
	}
	
	return $output_str;
}


//get data	
if(isset($_GET['entry_id']) && $_GET['entry_id']!="new"){
	$_GET['entry_id'] = intval($_GET['entry_id']);
	$query="SELECT * FROM _cms_modules_produkte_ WHERE entry_id=".($_GET['entry_id'])." LIMIT 1";
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		$row=mysqli_fetch_assoc($result);
	}
}

//get entries for position
function getEntries($entry_parent_id=0,$indent){
	$query="SELECT entry_id, entry_parent_id, entry_name, entry_sequence";
	$query.=" FROM _cms_modules_produkte_ WHERE entry_parent_id=".$entry_parent_id." ORDER BY entry_name ASC";
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		while($row=mysqli_fetch_assoc($result)){
			$result_sub=mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_modules_produkte_ WHERE entry_parent_id=".$row['entry_id']." ORDER BY entry_name ASC");
			echo "<option value=\"".$row['entry_sequence']."|".$row['entry_parent_id']."|".$row['entry_id']."\">".($indent.$row['entry_name'])."</option>";
			if(mysqli_num_rows($result_sub)>0){
				$indent.="-----";
				getEntries($row['entry_id'],$indent);
				$indent=substr($indent,0,strlen($indent)-strlen("-----"));
			}
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>zeemes : web content management system : modules : <?php echo $module_name;?> : edit</title>
<script type="text/javascript" src="../../admin/shared/javascript/functions.js">
</script>
<script type="text/javascript" src="../../admin/pages/javascript/functions.js">
</script>
<link rel="stylesheet" href="../../admin/shared/css/styles.css" />
<link rel="stylesheet" href="css/edit_styles.css" />
</head>

<body id="content" onload="SHARED_showFieldSetSwitch('form0');">
<h2><?php echo strtoupper($module_name);?>: <?php echo isset($row['title'])?$row['title']:"neuen Eintrag anlegen";?></h2>
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
  <fieldset id="Produktinformationen">
  <table cellpadding="0" cellspacing="0" class="table">
    <thead>
      <tr>
        <th width="218" style="width: 200px;">&nbsp;</th>
        <th style="width: auto;">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>letzte Aktualisierung</td>
        <td><?php echo isset($row['last_change'])?$row['last_change']:"";?><?php echo isset($row['entry_last_usr'])?" von ".getUserName($row['entry_last_usr']):"Datensatz noch nicht angelegt";?></td>
      </tr>
      <tr>
      	<td colspan="2" class="betweenHeader">
        	Produktangaben       </td>
      </tr>
       
      <tr>
        <td>Name</td>
        <td>
        	<textarea rows="3" name="entry_name"><?php echo isset($row['entry_name'])?stripslashes(htmlspecialchars($row['entry_name'])):"";?></textarea>        </td>
       </tr>
       <tr>
        <tr>
        <td>Position</td>
        <td>
        	<select name="position_mode" style="width: auto;">
            	<option value="">Position</option>
                <option value="before">vor</option>
                <option value="behind">nach</option>
                
            </select>
            <select name="new_position" style="width: auto;margin-left:5px;">
          		<option value="">bezogen auf</option>
			<?php
			  getEntries(0);
			?>
        	</select>        </td>
      </tr>
       <tr>
        <td>Bestellnummer</td>
        <td>
        	<input type="text" name="entry_nummer" value="<?php echo isset($row['entry_nummer'])?stripslashes(htmlspecialchars($row['entry_nummer'])):"";?>" />        </td>
       </tr>
       <tr>
        <td>Preis €</td>
        <td>
        	<input type="text" name="entry_preis" value="<?php echo isset($row['entry_preis'])?stripslashes(htmlspecialchars($row['entry_preis'])):"";?>" />        </td>
       </tr>
       <tr>
        <td>Preis variabel (z.B. für Gutschein)</td>
        <td>
        	<input type="checkbox" name="entry_preis_select" value="1" <?php echo isset($row['entry_preis_select']) && $row['entry_preis_select'] == 1?"checked=\"checked\"":"";?> style="width: auto;" />
        </td>
       </tr>
     
      <tr>
        <td>Bemerkungen</td>
        <td>
        	<textarea name="entry_desc" rows="10" cols="100"><?php echo isset($row['entry_desc'])?stripslashes($row['entry_desc']):"";?></textarea>       </td>
      </tr>
      <?php
	  if(isset($row)){
	  ?>
      <tr>
      	<td colspan="2" class="betweenHeader">
        	Bilder</td>
      </tr>
      <tr>
        <td>Produktvideo</td>
        <td>
        	<input type="text" name="entry_video" value="<?php echo isset($row['entry_video'])?stripslashes(htmlspecialchars($row['entry_video'])):"";?>" />        </td>
       </tr>
      <tr>
        <td>Bilder bearbeiten</td>
        <td>
        <button type="button" onclick="window.open('../../admin/filemanager?entry_id=<?php echo isset($row['entry_id'])?$row['entry_id']:"";?>&amp;mode=image&amp;file_cat1=module&amp;file_cat2=produkte&amp;max_width=900&amp;sessionid=<?php echo session_id();?>','Filemanager','width=750,height=550')">Dateien bearbeiten</button>        </td>
      </tr>
      <?php
      }
	  ?>
      
      
      
     </tbody>
   </table>
   </fieldset>
   <fieldset id="Kategoriezuordnung">
   <table cellpadding="0" cellspacing="0" class="table">
   
      <tr>
      	<td colspan="2">
        <ul id="filterList">
        <li>
		<?php
       echo makeCatList("_cms_modules_produkte_kategorien_", "", 0);
		?>
        </li>
        </ul>
        </td>
      </tr>
    </table>
    </fieldset>
 
  
  
  
    
</form>
</body>
</html>
