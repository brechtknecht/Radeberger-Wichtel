<?php
include_once("../../admin/shared/include/environment.inc.php");

if(!isset($_REQUEST['entry_id'])){
	header("Location: index.php");
	exit();	
}

$module_name = getParentDir($_SERVER['SCRIPT_FILENAME']);

if(checkPermission("user",$_SESSION['cms_user']['user_id'],"module",$module_name)==false){
	die("Sie haben keine Berechtigung auf dieses Element zuzugreifen!");
}
if($_SERVER['REQUEST_METHOD']=="POST"){
	include("save.inc.php");
}

//get data	
if(isset($_GET['entry_id']) && $_GET['entry_id'] != "new" ){
	$query="SELECT * FROM _cms_modules_news_ WHERE entry_id=".intval($_GET['entry_id'])." LIMIT 1";
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		$row=mysqli_fetch_assoc($result);
		$row['news_headline'] = json_decode($row['news_headline'], true);
		$row['news_teaser'] = json_decode($row['news_teaser'], true);
		$row['news_text'] = json_decode($row['news_text'], true);
		
	}
}

//get pages
function getEntries($entry_parent_id=0,$mode="",$entry_id=0,$indent=""){
	$query="SELECT entry_id,entry_parent_id,entry_name,entry_sequence";
	$query.=" FROM _cms_hp_navigation_ WHERE entry_parent_id=".$entry_parent_id;
	$query.=" ORDER BY entry_sequence ASC";
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		while($row=mysqli_fetch_assoc($result)){
			$result_sub=mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_hp_navigation_ WHERE entry_parent_id=".$row['entry_id']." AND entry_deleted=0 ORDER BY entry_sequence");
			
			$row['entry_name']=str_replace("-||"," ",$row['entry_name']);
			$row['entry_name']=str_replace("||"," ",$row['entry_name']);
		
			echo "<option value=\"".$row['entry_id']."\" ".($entry_id==$row['entry_id']?"selected=\"selected\"":"").">".($indent.$row['entry_name'])."</option>";
										
			if(mysqli_num_rows($result_sub)>0){
				$indent.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				getEntries($row['entry_id'],$mode,$entry_id,$indent);
				$indent=substr($indent,0,strlen($indent)-strlen("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"));
			}
		}
	}
}

//get entries for position
function getPosEntries($entry_parent_id = 0, $indent = ""){
	$query = "SELECT entry_id AS entry_id,entry_parent_id AS entry_parent_id,news_headline AS entry_name, entry_sequence AS entry_sequence";
	$query.= " FROM _cms_modules_news_ WHERE entry_deleted=0 AND entry_parent_id=".$entry_parent_id;
	if(isset($_GET['entry_id']) && $_GET['entry_id'] != "new"){
		$query.= " AND entry_id!='".mysqli_real_escape_string($_SESSION['conn'], $_GET['entry_id'])."'";
	}
	$query.= " ORDER BY entry_sequence ASC, entry_id ASC";
	
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		while($row = mysqli_fetch_assoc($result)){
			$result_sub = mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_modules_galleries_ WHERE entry_parent_id='".mysqli_real_escape_string($_SESSION['conn'], $row['entry_id'])."' ORDER BY entry_name ASC");
			echo "<option value=\"".$row['entry_sequence']."|".$row['entry_parent_id']."|".$row['entry_id']."\">".($indent.$row['entry_name'])."</option>";
			
		}
	}
}


//get categories
$query="SELECT DISTINCT entry_parent_id FROM _cms_modules_news_";
$result_cat=mysqli_query($_SESSION['conn'], $query);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>zeemes : web content management system : modules : <?php echo $module_name;?> : edit</title>
<script type="text/javascript" src="../../admin/shared/javascript/functions.js"></script>
<script type="text/javascript" src="../../admin/pages/javascript/functions.js"></script>
<script type="text/javascript" src="../../admin/shared/javascript/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
		mode : "exact",
		elements: "news_text_de, news_teaser_de,news_text_en, news_teaser_en",
		theme : "advanced",
		popup_css : "",
		content_css : "../../admin/shared/css/cms.css",
		plugins: "table,advimage",
		theme_advanced_blockformats : "p,h4,h5,h6",
		theme_advanced_buttons1 : "bold,|,link,unlink,|,formatselect,|,code",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		entity_encoding : "raw",
		convert_urls : false,
		relative_urls : false,
		remove_script_host : false,
		language : "de",
		file_browser_callback : "myFileBrowser"
		
});

function myFileBrowser(field_name, url, type, win){
	var fileMngrURL="<?php echo $_SESSION['global_vars']['path_to_root'];?>admin/filemanager/index.php?entry_id=<?php echo $_GET['entry_id'];?>&mode="+type+"&call=tinymce&file_cat1=module&file_cat2=news&url=full";
	
	tinyMCE.activeEditor.windowManager.open({
        file : fileMngrURL,
        title : 'My File Browser',
        width : 750,  // Your dimensions may differ - toy around with them!
        height : 550,
        resizable : "yes",
        inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
        close_previous : "no"
    }, {
        window : win,
        input : field_name
    });
    return false;
}
</script>

<link rel="stylesheet" href="../../admin/shared/css/styles.css" />

</head>

<body id="content" onload="SHARED_showFieldSetSwitch('form0');">
<h2><?php echo strtoupper($module_name);?>: <?php echo isset($row['news_headline'])?$row['news_headline']['de']:"neuen Eintrag anlegen";?></h2>
<div id="saveTools">
	<button type="button" onclick="SHARED_submitForm(document.form0,'Änderungen übernehmen?')">Änderungen übernehmen</button>
  	<button type="button" onclick="location.href='index.php'">zurück zur Liste</button>
    <?php
    if(isset($row)){
	?>
    <button type="button" onclick="location.href='edit.php?entry_id=new'">Neuer Eintrag</button>
    <button type="button" onclick="location.href='edit.php?entry_id=<?php echo $_GET['entry_id']?>&amp;duplicate'">Eintrag duplizieren</button>
    <?php
	}
	?>
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
  <input type="hidden" name="entry_id" value="<?php echo !isset($_GET['duplicate'])?$_GET['entry_id']:"new";?>" />
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
        <td style="padding-left: 5px"><?php echo isset($row['last_change']) && $row['last_change'] != "0000-00-00 00:00:00"?$row['last_change'].(isset($row['entry_last_usr'])?" von ".getUserName($row['entry_last_usr']):""):"k.A.";?></td>
      </tr>
      
       <tr><td class="betweenHeader" colspan="2">Kategorie / Startseite / Sticky</td></tr>
      <tr>
        <td>Kategorie</td>
        <td>
        	<select name="entry_parent_id" style="width: auto">
               <?php
               echo getEntries(0,"",$row['entry_parent_id']);
			   ?>
               
                            
            </select>
        </td>
      </tr>
      
      <tr>
        <td>auf Startseite zeigen</td>
        <td>
        	<input style="width: auto;" type="checkbox" name="news_show_on_startpage" value="1" <?php echo isset($row['news_show_on_startpage']) && $row['news_show_on_startpage'] == "1"?"checked=\"checked\"":"";?> />
        </td>
      </tr>
      
      <tr>
        <td>Sticky (permanent an erster Position)</td>
        <td>
        	<input style="width: auto;" type="checkbox" name="news_sticky" value="1" <?php echo isset($row['news_sticky']) && $row['news_sticky'] == "1"?"checked=\"checked\"":"";?> />
        </td>
      </tr>
    
     <?php /*
      <tr>
        <td>Position/Hierarchie</td>
        <td>
        	<select name="position_mode" style="width: auto;">
            	<option value="">Position</option>
                <option value="before">vor</option>
                <option value="behind">nach</option>
               
            </select>
            <select name="new_position" style="width: auto;margin-left:5px;">
          		<option value="">bezogen auf</option>
			<?php
			  
			  
					
					getPosEntries($row['entry_parent_id']);
					
				
	
			  
			?>
        	</select>        </td>
      </tr>
     */?>      
     <tr><td class="betweenHeader" colspan="2">Datum / Anzeigedauer</td></tr>
      <tr>
        <td>Datum </td>
        <td>
        	<input type="text" name="news_date" value="<?php echo isset($row['news_date'])?stripslashes(formatDate2Local($row['news_date'],"dd.mm.YYYY",false,false,false)):date("d.m.Y");?>" style="width: 100px;" />
             
          </td>
      </tr>
      <?php /*
      <tr>
        <td>Uhrzeit(en) </td>
        <td> 
        <textarea name="news_time" rows="3"><?php echo isset($row['news_time'])?stripslashes($row['news_time']):"";?></textarea>
        (optional) 
          </td>
      </tr>
      */?>
      <tr>
        <td>zeigen vom</td>
        <td><input type="text" name="news_start" value="<?php echo isset($row['news_start'])?stripslashes(formatDate2Local($row['news_start'],"dd.mm.YYYY",false,false,false)):date("d.m.Y");?>" style="width: 100px;" /> bis <input type="text" name="news_end" value="<?php echo isset($row['news_end'])?stripslashes(formatDate2Local($row['news_end'],"dd.mm.YYYY",false,false,false)):"";?>" style="width: 100px;" /> tt.mm.jjjj (optional)</td>
      </tr>
       <?php /*
      <tr><td class="betweenHeader" colspan="2">Erscheinungsbild</td></tr>
     
      <tr>
      	<td>Style</td>
        <td>
        	<?php
            $class_array = array();		
			$class_array[] = array("agentur_faehigkeiten", "Agentur: Fähigkeiten");	
			$class_array[] = array("agentur_netzwerk", "Agentur: Netzwerk");	
			$class_array[] = array("agentur_buero", "Agentur: Büro");	
			?>
            <select name="news_css_class">
            	<option value="">default</option>
                <?php
                foreach($class_array as $class){
					 echo "<option value=\"".htmlspecialchars($class[0])."\"";
					 echo isset($row) && $row['news_css_class'] == $class[0]?" selected=\"selected\"":"";
					 echo ">";
					 echo htmlspecialchars($class[1]);
					 echo "</option>";		
				}
				?>
               
               
            </select>
        </td>
      </tr>
      */?>
      <tr><td class="betweenHeader" colspan="2">Titel / Teaser / Text</td></tr>
      <?php
      /*
	  ?>
      <tr>
        <td>Ort</td>
        <td><input type="text" name="news_location" id="news_location" value="<?php echo isset($row['news_location'])?stripslashes($row['news_location']):"";?>" style="width: 500px;" /> (optional)</td>
      </tr>
	  */?>
      <tr><td class="betweenHeader" colspan="2">Deutsch</td></tr>
      <tr>
        <td>Titel</td>
        <td>
        	<input type="text" name="news_headline[de]" id="news_headline_de" value="<?php echo isset($row['news_headline']['de'])?stripslashes(str_replace("\"","'",$row['news_headline']['de'])):"";?>" style="width: 500px;" />        </td>
      </tr>
      
      <tr>
        <td>Kurztext für Startseite</td>
        <td>
        	<textarea name="news_teaser[de]" id="news_teaser_de" style="height: 80px; width: 500px;"><?php echo isset($row['news_teaser']['de'])?stripslashes($row['news_teaser']['de']):"";?></textarea> (optional)        </td>
      </tr>
      <tr>
        <td>Text</td>
        <td>
        	<textarea name="news_text[de]" id="news_text_de" style="height: 500px; width: 500px;"><?php echo isset($row['news_text']['de'])?stripslashes($row['news_text']['de']):"";?></textarea>        </td>
      </tr>
      
      <tr><td class="betweenHeader" colspan="2">Englisch</td></tr>
      <tr>
        <td>Titel</td>
        <td>
        	<input type="text" name="news_headline[en]" id="news_headline_en" value="<?php echo isset($row['news_headline']['en'])?stripslashes(str_replace("\"","'",$row['news_headline']['en'])):"";?>" style="width: 500px;" />        </td>
      </tr>
      
      <tr>
        <td>Kurztext für Startseite</td>
        <td>
        	<textarea name="news_teaser[en]" id="news_teaser_en" style="height: 80px; width: 500px;"><?php echo isset($row['news_teaser']['en'])?stripslashes($row['news_teaser']['en']):"";?></textarea> (optional)        </td>
      </tr>
      <tr>
        <td>Text</td>
        <td>
        	<textarea name="news_text[en]" id="news_text_en" style="height: 500px; width: 500px;"><?php echo isset($row['news_text']['en'])?stripslashes($row['news_text']['en']):"";?></textarea>        </td>
      </tr>
      
      <tr><td class="betweenHeader" colspan="2">Medien (Fotos / Videos)</td></tr>
     <?php
	  if(isset($row['entry_id'])){
	  ?>
      
      <tr>
        <td>Foto Artikel<br />400px breit</td>
        <td><button type="button" onclick="window.open('../../admin/filemanager?entry_id=<?php echo isset($row['entry_id'])?$row['entry_id']:"";?>&amp;mode=image&amp;file_cat1=module&amp;file_cat2=news&amp;file_cat3=fotos&max_width=800&amp;sessionid=<?php echo session_id();?>','Filemanager','width=750,height=550')">Foto Artikel</button>
        
        </td>
      </tr>
      <tr>
        <td>Foto Teaser<br />400px breit</td>
        <td>
        <button type="button" onclick="window.open('../../admin/filemanager?entry_id=<?php echo isset($row['entry_id'])?$row['entry_id']:"";?>&amp;mode=image&amp;file_cat1=module&amp;file_cat2=news&amp;file_cat3=foto_teaser&max_width=800&amp;sessionid=<?php echo session_id();?>','Filemanager','width=750,height=550')">Foto Teaser</button>
        </td>
      </tr>
      <?php /*
      <tr>
        <td>Downloads</td>
        <td><button type="button" onclick="window.open('../../admin/filemanager?entry_id=<?php echo isset($row['entry_id'])?$row['entry_id']:"";?>&amp;mode=file&amp;file_cat1=module&amp;file_cat2=news&amp;file_cat3=downloads','Filemanager','width=750,height=550')">Dateien bearbeiten</button></td>
      </tr>
	  
      <tr>
      	<td>Videos: YouTube ID (1 pro Zeile)
        
        </td>
        <td>
        	<textarea name="videos" rows="6" cols="50"><?php echo isset($row['videos'])?stripslashes($row['videos']):"";?></textarea>
        </td>
      </tr>
      */?>
	   <?php /*
      <tr>
        <td>Links</td>
        <td>
        	<textarea name="news_link" style="height: 100px;"><?php echo isset($row['news_link'])?$row['news_link']:"";?></textarea>
        </td>
      </tr>
	  */?>
     <?php
	 }
	 else{
	?>
    <tr><td colspan="2">Datensatz ist noch nicht angelegt</td></tr>
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
