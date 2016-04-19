<?php
include_once("../../admin/shared/include/environment.inc.php");

if(!isset($_REQUEST['entry_id'])){
	header("Location: index.php");
	exit();	
}

$module_name = getParentDir($_SERVER['SCRIPT_FILENAME']);

if($_SERVER['REQUEST_METHOD']=="POST"){
	include("save.inc.php");
}

//get categories
$query="SELECT DISTINCT entry_category FROM _cms_modules_galleries_";
$result_cat=mysqli_query($_SESSION['conn'], $query);

//get data	
if(isset($_GET['entry_id']) && $_GET['entry_id'] != "new"){
	$query="SELECT * FROM _cms_modules_galleries_ WHERE entry_id='".mysqli_real_escape_string($_SESSION['conn'], $_GET['entry_id'])."' LIMIT 1";
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		$row=mysqli_fetch_assoc($result);
	}
}

//get enties for position
function getEntries($entry_parent_id = 0, $indent = ""){
	$query = "SELECT entry_id,entry_parent_id,entry_name, entry_sequence";
	$query.= " FROM _cms_modules_galleries_ WHERE entry_parent_id=".$entry_parent_id;
	if(isset($_GET['entry_id']) && $_GET['entry_id'] != "new"){
		$query.= " AND entry_id!='".mysqli_real_escape_string($_SESSION['conn'], $_GET['entry_id'])."'";
	}
	$query.= " ORDER BY entry_sequence ASC, entry_name ASC";
	echo $query;
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		while($row = mysqli_fetch_assoc($result)){
			$result_sub = mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_modules_galleries_ WHERE entry_parent_id='".mysqli_real_escape_string($_SESSION['conn'], $row['entry_id'])."' ORDER BY entry_name ASC");
			echo "<option value=\"".$row['entry_sequence']."|".$row['entry_parent_id']."|".$row['entry_id']."\">".($indent.$row['entry_name'])."</option>";
			if(mysqli_num_rows($result_sub)>0){
				$indent.="-----";
				getEntries($row['entry_id'],$indent);
				$indent=substr($indent,0,strlen($indent)-strlen("-----"));
			}
		}
	}
}


//get users
$result_usr = mysqli_query($_SESSION['conn'], "SELECT entry_id AS entry_id, CONCAT(user_name,', ',user_fname) AS user_name FROM _cms_hp_user_ WHERE user_type='intern' ORDER BY user_name ASC, user_fname ASC");


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
</head>

<body id="content" onload="SHARED_showFieldSetSwitch('form0');">
<h2><?php echo strtoupper($module_name);?>: <?php echo isset($row['entry_name'])?$row['entry_name']:"neuen Eintrag anlegen";?></h2>
<div id="saveTools">
	<button type="button" onclick="SHARED_submitForm(document.form0,'Änderungen übernehmen?')">Änderungen übernehmen</button>
  	<button type="button" onclick="location.href='index.php'">zurück zur Liste</button>
</div>
<?php
if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['save'])){
	echo "<span class=\"showState\">Ihre Änderungen wurden gespeichert</span>";
}
?>
<form action="#" method="post" id="form0" name="form0" enctype="multipart/form-data" onSubmit="return SHARED_submitForm(this,'Änderungen übernehmen?')">
  <?php
    if(isset($_SESSION['fid'])){
		?>
         <input type="hidden" name="fid" value="<?php echo htmlspecialchars($_SESSION['fid'])?>" />
		<?php	
	}
	?>
  <input type="hidden" name="entry_id" value="<?php echo htmlspecialchars($_GET['entry_id']);?>" />
  <table cellpadding="0" cellspacing="0" class="table">
    <thead>
    </thead>
    <tbody>
     <tr>
        <td style="width: 200px;">letzte Aktualisierung</td>
        <td><?php echo isset($row['last_change'])?$row['last_change']:"";?><?php echo isset($row['entry_last_usr'])?" von ".getUserName($row['entry_last_usr']):"Datensatz noch nicht angelegt";?></td>
      </tr>
      <?php
      if(isAdmin()){
	  ?>
      <tr>
      	<td>Eigentümer des Datensatzes</td>
        <td>
        	<select name="entry_last_usr">
            	<?php
                if(mysqli_num_rows($result_usr) > 0){
					while($row_usr = mysqli_fetch_assoc($result_usr)){
				?>
                	<option value="<?php echo $row_usr['entry_id'];?>" <?php echo isset($row['entry_last_usr']) && $row_usr['entry_id'] == $row['entry_last_usr']?"selected=\"selected\"":""; ?>><?php echo $row_usr['user_name'];?></option>
                <?php
					}
				}
				?>
            </select>        </td>
      </tr>
      <?php
	  }
	  ?>
       <tr>
         <td colspan="2" class="betweenHeader">&nbsp;</td>
       </tr>
      <tr>
        <td>Kategorie</td>
        <td>
        	<select name="entry_category" style="width: auto">
            	<option value="">Kategorie wählen&nbsp;&nbsp;&nbsp;</option>
                <?php
                if(mysqli_num_rows($result_cat)>0){
					while($row_cat=mysqli_fetch_assoc($result_cat)){
					?>
                    <option value="<?php echo $row_cat['entry_category']?>" <?php echo isset($row['entry_category'])&&$row['entry_category']==$row_cat['entry_category']?"selected=\"selected\"":"";?>><?php echo $row_cat['entry_category']?></option>
                    <?php
					}
				}
				?>
            </select>&nbsp;oder neu&nbsp;
            <input type="text" name="entry_category_new" value="" style="width: 120px;" />        </td>
      </tr>
       <tr>
        <td>Position/Hierarchie</td>
        <td>
        	<select name="position_mode" style="width: auto;">
            	<option value="">Position</option>
                <option value="before">vor</option>
                <option value="behind">nach</option>
                <option value="submenu">Untergalerie von</option>
            </select>
            <select name="new_position" style="width: auto;margin-left:5px;">
          		<option value="">bezogen auf</option>
			<?php
			  
			  if(isset($_SESSION['cms_user']['modules']) && isset($_SESSION['cms_user']['module_filter']) && is_array($_SESSION['cms_user']['module_filter'])){
					foreach($_SESSION['cms_user']['module_filter'] as $val){
						$entry_name = strtoupper(getFieldContent("_cms_modules_dokumente_kategorien_","entry_name","entry_id",$val));
						?>
						<optgroup label="<?php echo $entry_name;?>">
						<?php
						
						getEntries($val);
						?>
                        </optgroup>
						<?php
						
					}
				}
				else{
					
					getEntries(0);
					
				}
	
			  
			?>
        	</select>        </td>
      </tr>
      <tr>
        <td>Titel</td>
        <td>
        	<input type="text" name="entry_name" value="<?php echo isset($row['entry_name'])?stripslashes($row['entry_name']):"";?>" />        </td>
      </tr>
       <tr>
        <td>Beschreibung</td>
        <td>
        	<textarea name="entry_description" style="height: 100px;"><?php echo isset($row['entry_description'])?stripslashes($row['entry_description']):"";?></textarea>        </td>
      </tr>
       <tr>
         <td colspan="2" class="betweenHeader">&nbsp;</td>
       </tr>
       
     
     
      <tr>
        <td>Bilder</td>
        <td>
         <?php
	  if(isset($row['entry_id'])){
	  ?>
        <button type="button" onclick="window.open('../../admin/filemanager?entry_id=<?php echo isset($row['entry_id'])?$row['entry_id']:"";?>&amp;mode=image&amp;file_cat1=module&amp;file_cat2=galleries&amp;max_width=0&amp;max_height=0','Filemanager','width=750,height=550')">Bilder bearbeiten</button>
          <?php
	 }
	 else{
	 	?>
        Die Galerie muss angelegt sein, bevor Bilder hinzugefügt werden können (=> "Änderungen übernehmen").
        <?php
	 }
	 ?> 
        </td>
      </tr>
     
      <tr>
      	<td>Videos: YouTube ID (1 pro Zeile)
        
        </td>
        <td>
        	<textarea name="videos" rows="6" cols="50"><?php echo isset($row['videos'])?stripslashes($row['videos']):"";?></textarea>
        </td>
      </tr>
     </tbody>
    <tfoot>
    </tfoot>
  </table>
  
</form>
</body>
</html>
