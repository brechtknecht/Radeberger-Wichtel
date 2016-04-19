<?php
include("../../shared/include/environment.inc.php");
isLoggedIn();
if(!checkPermission("user",$_SESSION['cms_user']['user_id'],"page",$_REQUEST['entry_id']) && !checkPermission("user",$_SESSION['cms_user']['user_id'],"page","all")){
	die("Sie haben keine Berechtigung auf dieses Element zuzugreifen!");
}
if($_SERVER['REQUEST_METHOD']=="POST"){
	include("save.inc.php");
}
//get page data
if(isset($_GET['entry_id']) && !empty($_GET['entry_id'])){
	if($_GET['entry_id']!="new"){
		$query="SELECT";
		$query.=" _cms_hp_navigation_.entry_name as entry_name,";
		//$query.=" _cms_hp_navigation_.entry_deeplink as entry_deeplink,";
		$query.=" _cms_hp_navigation_.last_change as last_change,"; 
		$query.=" _cms_hp_navigation_.entry_last_usr as entry_last_usr,"; 
		$query.=" _cms_hp_navigation_.entry_shortcut as entry_shortcut,"; 
		$query.=" _cms_hp_navigation_.entry_start as entry_start,"; 
		$query.=" _cms_hp_navigation_.entry_style as entry_style,"; 
		$query.=" _cms_hp_navigation_.entry_custom_css as entry_custom_css,"; 
		$query.=" _cms_hp_navigation_.entry_user_role as entry_user_role,"; 
		$query.=" _cms_hp_navigation_.entry_bg_image as entry_bg_image,"; 
		$query.=" _cms_hp_navigation_.entry_active as entry_active,"; 
		$query.=" _cms_hp_navigation_.entry_template as entry_template,"; 
		$query.=" _cms_hp_navigation_.multilingual as multilingual,"; 
		//$query.=" _cms_hp_navigation_.show_subnavi as show_subnavi,"; 
		//$query.=" _cms_hp_navigation_.show_in_add_navi as show_in_add_navi,"; 
		$query.=" _cms_hp_navigation_.entry_direct_link as entry_direct_link,"; 
		$query.=" _cms_hp_navigation_.entry_direct_link_target as entry_direct_link_target,"; 
		$query.=" _cms_hp_navigation_.entry_inc_module_mode as entry_inc_module_mode,"; 
		$query.=" _cms_hp_navigation_.entry_inc_module_target as entry_inc_module_target,"; 
		$query.=" _cms_hp_navigation_.entry_inc_gallery as entry_inc_gallery,"; 
		$query.=" _cms_hp_navigation_.entry_inc_sound as entry_inc_sound,"; 
		$query.=" _cms_hp_navigation_.entry_inc_sound_volume as entry_inc_sound_volume"; 
		//$query.=" (SELECT entry_id FROM _cms_hp_pages_ WHERE _cms_hp_pages_.entry_parent_id=_cms_hp_navigation_.entry_id AND _cms_hp_pages_.entry_lang='de' AND (_cms_hp_pages_.entry_state='public' OR _cms_hp_pages_.entry_state='entwurf') ORDER BY _cms_hp_pages_.entry_state DESC LIMIT 1) as page_version_id";
		/*
		$query.=" _cms_hp_pages_.entry_title as entry_title,"; 
		$query.=" _cms_hp_pages_.entry_meta_description as entry_meta_description,"; 
		$query.=" _cms_hp_pages_.entry_meta_keywords as entry_meta_keywords"; 
		*/
		$query.=" FROM _cms_hp_navigation_ INNER JOIN _cms_hp_pages_";
		$query.=" ON _cms_hp_navigation_.entry_id=_cms_hp_pages_.entry_parent_id";
		$query.=" WHERE _cms_hp_navigation_.entry_id=".intval($_GET['entry_id']);
		$query.=" AND entry_lang='de'";
		$query.=" LIMIT 1";
		
		$result=mysqli_query($_SESSION['conn'], $query);
		echo mysqli_error($_SESSION['conn']);
		if(mysqli_num_rows($result)>0){
			if($row=mysqli_fetch_assoc($result)){
				$query = "SELECT entry_id FROM _cms_hp_pages_ WHERE entry_parent_id='".mysqli_real_escape_string($_SESSION['conn'], $_GET['entry_id'])."' AND entry_lang='de' AND entry_state='public' LIMIT 1";
				$result_tmp=mysqli_query($_SESSION['conn'], $query);
				
				if(mysqli_num_rows($result_tmp)==1){
					$row_tmp=mysqli_fetch_assoc($result_tmp);
					$row['page_version_id']=$row_tmp['entry_id'];
				}
			}
		}
		else{
			die("Fehler! Seite nicht gefunden.");
		}
	}
}
else{
	die("Fehler! Es wurde keine Seite übergeben");
}

//get versions
if(isset($_GET['entry_id']) && !empty($_GET['entry_id']) && $_GET['entry_id']!="new"){
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
			$versions_array[$row_versions['entry_lang']][$row_versions['entry_id']]['changed_by']=$row_versions['changed_by'];
			$versions_array[$row_versions['entry_lang']][$row_versions['entry_id']]['last_change']=$row_versions['last_change'];
			$versions_array[$row_versions['entry_lang']][$row_versions['entry_id']]['entry_state']=$row_versions['entry_state'];
			$versions_array[$row_versions['entry_lang']][$row_versions['entry_id']]['entry_id']=$row_versions['entry_id'];
		}
	}
}
	
//get templates
$dir="../../../shared/templates/";
$template_array=array();
$fd=opendir($dir);
while($template=readdir($fd)){
	if($template!="."&&$template!=".." && $template!=""){
		array_push($template_array,$template);
	}
}
sort($template_array);
closedir($fd);

//get sounds
$dir="../../../shared/audio/";
if(is_dir($dir)){
	$sound_array=array();
	$fd=opendir($dir);
	while($sound=readdir($fd)){
		if(strstr($sound,".mp3")){
			array_push($sound_array,$sound);
		}
	}
	sort($template_array);
	closedir($fd);
}


//get navi styles
$dir="../../../shared/css/custom_styles/";
if(is_dir($dir)){
	$navi_styles_array=array();
	$fp=opendir($dir);
	while($css_file=readdir($fp)){
		if(strstr($css_file,".css")){
			array_push($navi_styles_array,str_replace(".css","",$css_file));
		}
	}
}

//get background images if gallery exists
$result_bg = mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_modules_galleries_ WHERE entry_name='Seitenhintergründe' LIMIT 1");
	
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
		
			if($mode=="shortcut"){
				echo "<option value=\"".$row['entry_id']."\" ".($entry_id==$row['entry_id']?"selected=\"selected\"":"").">".($indent.$row['entry_name'])."</option>";
			}
			else{
				echo "<option value=\"".$row['entry_sequence']."|".$row['entry_parent_id']."|".$row['entry_id']."\">".($indent.$row['entry_name'])."</option>";
			}
								
			if(mysqli_num_rows($result_sub)>0){
				$indent.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				getEntries($row['entry_id'],$mode,$entry_id,$indent);
				$indent=substr($indent,0,strlen($indent)-strlen("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"));
			}
		}
	}
}

//get user roles
$query="SELECT setting_id, setting_value, last_change, entry_last_usr FROM _cms_settings_ WHERE setting_key='user_role'";
$query.=" ORDER BY setting_value ASC";
$result_user_role = mysqli_query($_SESSION['conn'], $query);

//get galleries	
$query="SELECT * FROM _cms_modules_galleries_ WHERE entry_category='Slideshow' ORDER BY entry_name ASC";
$result_galleries=mysqli_query($_SESSION['conn'], $query);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>zeemes : web content management system : pages : organize</title>
<script type="text/javascript" src="../../shared/javascript/functions.js">
</script>
<script type="text/javascript" src="../javascript/functions.js">
</script>
<link rel="stylesheet" href="../../shared/css/styles.css" />
</head>

<body id="content" onload="SHARED_showFieldSetSwitch('form0');">
<h2>Seite verwalten: <?php echo isset($row['entry_name'])?$row['entry_name']:"neue Seite anlegen";?></h2>
<div id="saveTools">
	<button type="button" onclick="SHARED_submitForm(document.form0,'Änderungen übernehmen?');">Änderungen übernehmen</button>
  <button type="button" onclick="location.href='../'">zurück zur Liste</button>
  <?php
  if(isset($_GET['entry_id']) && $_GET['entry_id']!="new"){
  ?>
  	<button type="button" onclick="location.href='../editor/index.php?entry_id=<?php echo $_GET['entry_id'];?>&page_version_id=<?php echo $row['page_version_id'];?>'">Inhalt editieren</button>
    <button type="button" onclick="location.href='index.php?entry_id=new'">Neuer Eintrag</button>
  <?php
  }
  ?>
</div>
<?php
if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['save'])){
	echo "<span class=\"showState\">Ihre Änderungen wurden gespeichert</span>";
}
?>
<form action="#" method="post" id="form0" name="form0">
  <?php
    if(isset($_SESSION['fid'])){
		?>
         <input type="hidden" name="fid" value="<?php echo htmlspecialchars($_SESSION['fid'])?>" />
		<?php	
	}
	?>
  <input type="hidden" name="entry_id" value="<?php echo htmlspecialchars($_GET['entry_id']);?>" />
   <input type="hidden" name="save" value="<?php echo $_GET['entry_id'];?>" />
  <fieldset id="Übersicht">
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
        <td style="padding-left: 5px"><?php echo isset($row['last_change'])?$row['last_change']:"";?><?php echo isset($row['entry_last_usr'])?" von ".getUserName($row['entry_last_usr']):"";?></td>
      </tr>
      <tr>
        <td>interner Name</td>
        <td><input type="text" name="entry_name" value="<?php echo isset($row['entry_name'])?htmlspecialchars($row['entry_name']):"";?>" onchange="ORGANIZE_setFieldValue(document.form0,'entry_navi_name_',this.value,true)" /></td>
      </tr>
      <?php
      /*
	  ?>
      <tr>
        <td>Alias URL</td>
        <td><input type="text" name="entry_deeplink" value="<?php echo isset($row['entry_deeplink'])?htmlspecialchars($row['entry_deeplink']):"";?>" /></td>
      </tr>
      <?php
	  */
	  if(isset($navi_styles_array) && sizeof($navi_styles_array)>0){
	  ?>
      <tr>
        <td>Karikatur</td>
        <td>
        	<select name="entry_custom_css">
            	<option value="">keine Karikatur</option>
			<?php
			foreach($navi_styles_array as $val){
			?>
            	<option value="<?php echo $val;?>" <?php echo isset($row['entry_custom_css'])&&$row['entry_custom_css']==$val?"selected=\"selected\"":"";?>><?php echo $val;?></option>
            <?php
			}
			?>
            </select>        </td>
      </tr>
      <?php
	  }
	  ?>
      <?php
	  if(isset($result_bg) && mysqli_num_rows($result_bg)>0){
	 	
		$row_bg=mysqli_fetch_assoc($result_bg);
		$result_bg_files=mysqli_query($_SESSION['conn'], "SELECT file_save_name,file_real_name FROM _cms_hp_files_ WHERE entry_parent_id='".mysqli_real_escape_string($_SESSION['conn'], $row_bg['entry_id'])."' AND file_cat1='module' AND file_cat2='galleries'");
		if(mysqli_num_rows($result_bg_files)>0)
			{
	  ?>
      <tr>
        <td>Hintergrundbild</td>
        <td>
        	<select name="entry_bg_image">
            	<option value="">kein Hintergrundbild</option>
			<?php
			while($row_bg=mysqli_fetch_assoc($result_bg_files)){
			?>
            	<option value="<?php echo $row_bg['file_save_name'];?>" <?php echo isset($row['entry_bg_image'])&&$row['entry_bg_image']==$row_bg['file_save_name']?"selected=\"selected\"":"";?>><?php echo $row_bg['file_real_name'];?></option>
            <?php
			}
			?>
            </select>        </td>
      </tr>
      <?php
	  	}
	  }
	  ?>
        <?php
	  if(isset($result_galleries) && mysqli_num_rows($result_galleries)>0){
	 	
		
		
	  ?>
      <tr>
        <td>Slideshow</td>
        <td>
        	<select name="entry_inc_gallery">
            	<option value="">keine Slideshow</option>	
			<?php
			while($row_bg=mysqli_fetch_assoc($result_galleries)){
			?>
            	<option value="<?php echo $row_bg['entry_id'];?>" <?php echo isset($row['entry_inc_gallery'])&&$row['entry_inc_gallery']==$row_bg['entry_id']?"selected=\"selected\"":"";?>><?php echo $row_bg['entry_name'];?></option>
            <?php
			}
			?>
            </select>        </td>
      </tr>
      <?php
	  	
	  }
	  ?>
      <tr>
        <td>Position/Hierarchie</td>
        <td>
        	<select name="position_mode" style="width: auto;">
            	<option value="">Position</option>
                <option value="before">vor</option>
                <option value="behind">nach</option>
                <option value="submenu">Unterseite von</option>
            </select>
          <br />
      	<select name="new_position" style="width: auto;margin-top: 3px;">
          		<option value="">bezogen auf</option>
			<?php
			  getEntries(0);
			?>
        	</select>        </td>
      </tr>
      <tr>
        <td>Startseite</td>
        <td><input type="checkbox" name="entry_start" value="1" <?php echo isset($row['entry_start'])&&$row['entry_start']==1?"checked=\"checked\"":""; ?> style="width: 30px" /></td>
      </tr>
      <tr>
        <td>aktiv</td>
        <td><input type="checkbox" name="entry_active" value="1" <?php echo isset($row['entry_active'])&&$row['entry_active']==1?"checked=\"checked\"":""; ?> style="width: 30px" /></td>
      </tr>
      <tr style="display: none;">
        <td>Subnavigation zeigen</td>
        <td><input type="checkbox" name="show_subnavi" value="1" <?php echo isset($row['show_subnavi'])&&$row['show_subnavi']==1?"checked=\"checked\"":""; ?> style="width: 30px" /></td>
      </tr>
      <?php
      if(isset($row['show_in_add_navi'])){	
	  ?>
      <tr>
        <td>in Seitennavi zeigen</td>
        <td><input type="checkbox" name="show_in_add_navi" value="1" <?php echo isset($row['show_in_add_navi'])&&$row['show_in_add_navi']==1?"checked=\"checked\"":""; ?> style="width: 30px" /></td>
      </tr>
      <?php
      }
	  ?>
      <tr>
      	<td>Template</td>
        <td>
        <select name="entry_template" style="width: auto">
              <?php
			foreach($template_array as $template)
				{
				if(strstr($template,"tmpl"))
					{
				?>
              <option value="<?php echo $template?>" <?php echo isset($row['entry_template'])&&$row['entry_template']==$template?"selected=\"selected\"":"";?>> 
              <?php echo substr($template,0,strpos($template,"."));?>&nbsp;&nbsp;&nbsp;              </option>
              <?php
					}
				}
			?>
            </select>        </td>
      </tr>
      <tr>
      	<td>Zielfenster</td>
        <td>
        <select name="entry_direct_link_target" style="width: auto">
            	<option value="_self" <?php echo isset($row['entry_direct_link_target'])&&$row['entry_direct_link_target']=="_self"?"selected=\"selected\"":"";?>>Seite im gleichen Fenster öffnen&nbsp;&nbsp;&nbsp;</option>
                <option value="_blank" <?php echo isset($row['entry_direct_link_target'])&&$row['entry_direct_link_target']=="_blank"?"selected=\"selected\"":"";?>>Seite in neuem Fenster öffnen</option>
            </select>       
        </td>
      </tr>
      <?php
      /*
	  ?>
      <tr>
        <td>Zugriff</td>
        <td>
        	<select name="entry_user_role[]" multiple="multiple">
            	<option value="">Keine Einschränkung</option>
				<?php
				if(mysqli_num_rows($result_user_role)>0){
					while($row_user_role=mysqli_fetch_assoc($result_user_role)){
					?>
                     <option value="<?php echo $row_user_role['setting_id']?>" <?php echo isset($row['entry_user_role'])&&strstr($row['entry_user_role'],"|".$row_user_role['setting_id']."|")?"selected=\"selected\"":"";?>><?php echo $row_user_role['setting_value']?></option>
                    <?php
					}
				}
			?>
            </select>        </td>
      </tr>
	  <?php
      */
	  ?>
      
      <tr>
        <td>Shortcut</td>
        <td>
        	<select name="entry_shortcut" style="width: auto;">
          		<option value="">Zielseite</option>
              <?php
			  getEntries(0,"shortcut",$row['entry_shortcut']);
			?>
        	</select>        </td>
      </tr>
      <tr>
        <td>Externer Link</td>
        <td>
        	<input type="text" name="entry_direct_link" value="<?php echo isset($row['entry_direct_link'])?$row['entry_direct_link']:"";?>" style="width: 200px;" />&nbsp;
			 </td>
      </tr>
       <tr>
        <td>CSS-Angaben (Link)</th>
      <td><input type="text" name="entry_style" value="<?php echo isset($row['entry_style'])?$row['entry_style']:""; ?>" /></th>      </tr>
     </tbody>
    <tfoot>
    </tfoot>
  </table>
  </fieldset>
  
  <fieldset id="Navigation, Suchmaschinenangaben">
  <table cellpadding="0" cellspacing="0" class="table">
    <thead>
     
      <tr>
        <th style="width: 100px;">&nbsp;</th>
        <th style="width: auto;">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
    <?php
    foreach($_SESSION['lang_array'] as $language){
    if($_GET['entry_id']!="new"){
		$query_meta="SELECT entry_navi_name, entry_navi_desc,entry_title,entry_meta_description,entry_meta_keywords FROM _cms_hp_pages_ WHERE entry_parent_id=".$_GET['entry_id']." AND entry_lang='".$language."' AND entry_state='public' LIMIT 1";
		$result_meta=mysqli_query($_SESSION['conn'], $query_meta);
		echo mysqli_error($_SESSION['conn']);
		if(isset($result_meta) && mysqli_num_rows($result_meta)>0){
			$row_meta=mysqli_fetch_assoc($result_meta);
		}
	}
	else{
		$query_meta="SELECT setting_key,setting_value FROM _cms_settings_";
		$result_meta=mysqli_query($_SESSION['conn'], $query_meta);
		if(mysqli_num_rows($result_meta)>0){
			$row_meta=array();
			while($tmp=mysqli_fetch_assoc($result_meta)){
				$row_meta[$tmp['setting_key']]=$tmp['setting_value'];
			}
		}
	}
	
    ?>
      <tr>
      	<td colspan="2" class="betweenHeader"><?php echo strtoupper($language); ?></td>
      </tr>
      <tr>
        <td>Navigationsname</td>
        <td><textarea name="entry_navi_name_<?php echo $language ?>"><?php echo isset($row_meta['entry_navi_name'])?stripslashes($row_meta['entry_navi_name']):"";?></textarea></td>
      </tr>
      <tr>
        <td>Beschreibung</td>
        <td><textarea name="entry_navi_desc_<?php echo $language ?>"><?php echo isset($row_meta['entry_navi_desc'])?stripslashes($row_meta['entry_navi_desc']):"";?></textarea></td>
      </tr>
      <tr>
        <td>Seitentitel</td>
        <td><textarea name="entry_title_<?php echo $language ?>"><?php echo isset($row_meta['entry_title'])?stripslashes($row_meta['entry_title']):"";?></textarea></td>
      </tr>
      <tr>
        <td>Metatag: Description</td>
        <td><textarea name="entry_meta_description_<?php echo $language ?>"><?php echo isset($row_meta['entry_meta_description'])?stripslashes($row_meta['entry_meta_description']):"";?></textarea></td>
      </tr>
      <tr>
        <td>Metatag: Keywords</td>
        <td><textarea name="entry_meta_keywords_<?php echo $language ?>"><?php echo isset($row_meta['entry_meta_keywords'])?stripslashes($row_meta['entry_meta_keywords']):"";?></textarea></td>
      </tr>
      <?php
	unset($row_meta);
	} ?>
     </tbody>
    <tfoot>
    </tfoot>
  </table>
  </fieldset>
  <fieldset id="Modul/Galerie einfügen">
  <table cellpadding="0" cellspacing="0" class="table">
    <thead>
      <tr>
        <th style="width: 100px;">Modul</th>
        <th style="width: auto;">Filter </th>
      </tr>
    </thead>
    <tbody>
	    <tr>
			<td>
				<select name="page_perm_modules" style="width:auto" onchange="SHARED_setInnerHTML('moduleSettings.ajax.php','moduleSettings','POST','entry_id=<?php echo $_GET['entry_id']; ?>&module='+this.value);">
					<option value="">kein Modul</option>
					<?php 
					$dir="../../../modules/";
					$fd=opendir($dir);
					$i=0;
					while($mods=readdir($fd)){
						if($mods!="."&&$mods!=".."){
						?>
						<option value="<?php echo $mods; ?>" <?php echo (checkPermission("page",$_GET['entry_id'],"module",$mods,"","")==true)?"selected=\"selected\"":"";?>><?php echo ucfirst($mods);?></option>
						<?php
						}
					}
					rewinddir($fd);
					?>
				</select>            </td>
			<td id="moduleSettings">
			<?php
			while($mods=readdir($fd)){
				if($mods!="."&&$mods!=".." && checkPermission("page",$_GET['entry_id'],"module",$mods,"","")==true){
					$_POST['module']=$mods;
					include("moduleSettings.ajax.php");
				}
			}
			?>           </td>
		</tr>
	    <tr>
	      <td>Anzeigebereich</td>
	      <td>
          	<?php
            $contentareas_array = array("contentarea1");
			if($contentareas = file_get_contents("../../../shared/templates/".$row['entry_template'])){
				$contentareas = preg_match_all("/contentarea[1-9]*/", $contentareas	, $matches);
				if(sizeof($matches[0]) > 0){
					$contentareas_array = array();
					foreach($matches[0] as $match){
						 $contentareas_array[] = $match;		
					}	
					sort($contentareas_array);
				}
			}
			else{
					
			}
			?>
            <select name="entry_inc_module_target" style="width:auto" >
            <?php
            foreach($contentareas_array as $val){
				?>
                <option value="<?php echo $val?>" <?php echo isset($row['entry_inc_module_target'])&&$row['entry_inc_module_target']==$val?"selected=\"selected\"":""?>><?php echo $val;?></option>
                <?php	
			}
			?>    
		  </select>          </td>
      </tr>
        <tr>
	      <td>Anzeigeoptionen</td>
	      <td>
          	<select name="entry_inc_module_mode" style="width:auto" >
                <option value="behind" <?php echo isset($row['entry_inc_module_mode'])&&$row['entry_inc_module_mode']=="behind"?"selected=\"selected\"":""?>>Modul nach Seiteninhalt ausgeben</option>
                <option value="before" <?php echo isset($row['entry_inc_module_mode'])&&$row['entry_inc_module_mode']=="before"?"selected=\"selected\"":""?>>Modul vor Seiteninhalt ausgeben</option>
                <option value="replace"<?php echo isset($row['entry_inc_module_mode'])&&$row['entry_inc_module_mode']=="replace"?"selected=\"selected\"":""?>>Seiteninhalt mit Modul ersetzen</option>
		  </select>          </td>
      </tr>
      
     <?php /*
	 <tr>
      	<td colspan="2" id="betweenHeader"><strong>Galerie einfügen</strong></td>
      </tr>
      <tr>
	      <td>Galerie auswählen</td>
	      <td>
          	
            <select name="entry_inc_gallery" style="width:auto" >
                <option value="">....</option>
                <?php
				if(mysqli_num_rows($result_galleries)>0){
					while($row_galleries=mysqli_fetch_assoc($result_galleries)){
						?>
                        <option value="<?php echo $row_galleries['entry_id'];?>" <?php echo isset($row['entry_inc_gallery'])&&$row['entry_inc_gallery']==$row_galleries['entry_id']?"selected=\"selected\"":""?>><?php echo $row_galleries['entry_name'];?>&nbsp;&nbsp;&nbsp;</option>
                        <?php
					}
				}
			?>
		  </select>          </td>
      </tr>
      <tr>
        <td>Hintergrundsound</td>
        <td>
        <select name="entry_inc_sound" style="width:auto" >
                <option value="">....</option>
                <?php
				foreach($sound_array as $val){
						?>
                        <option value="<?php echo $val;?>" <?php echo isset($row['entry_inc_sound'])&&$row['entry_inc_sound']==$val?"selected=\"selected\"":""?>><?php echo $val;?>&nbsp;&nbsp;&nbsp;</option>
                        <?php
					}
				
			?>
		  </select>&nbsp;
          <input type="text" name="entry_inc_sound_volume" value="<?php echo isset($row['entry_inc_sound_volume'])?$row['entry_inc_sound_volume']:"";?>" style="width: 50px" />
        </td>
      </tr>
	  */
	  ?>
   </tbody>
    <tfoot>
    </tfoot>
  </table>
  </fieldset>
 
  <fieldset id="Versionen">
  <table cellpadding="0" cellspacing="0" class="table">
    <thead>
      <tr>
        <th style="width: 200px;">Version vom</th>
        <th style="width: 50px;">Status</th>
        <th style="width: 200px;">bearbeitet von</th>
        <th style="width: auto;">online</th>
      </tr>
    </thead>
    <tbody>
	    <?php
		if(isset($versions_array) && sizeof($versions_array)>0){
			foreach($_SESSION['lang_array'] as $val){
				?>
                <tr>
                	<td colspan="4" class="betweenHeader"><?php echo strtoupper($val);?></td>
                </tr>
                <?php 
				foreach($versions_array[$val] as $versions){
				?>
                	<tr>
                    	<td><a href="../../../index.php?entry_id=<?php echo $_GET['entry_id'];?>&amp;page_version_id=<?php echo $versions['entry_id'];?>" target="_blank"><?php echo $versions['last_change'];?></a></td>
                    	<td><?php echo $versions['entry_state'];?></td>
                    	<td><?php echo getUserName($versions['changed_by']);?>&nbsp;</td>
                    	<td><input type="radio" name="entry_state_<?php echo $val;?>" value="<?php echo $versions['entry_id'];?>" style="width: auto" <?php echo $versions['entry_state']=="public"?"checked=\"checked\"":"";?> /></td>
                	</tr>
                <?php
				}
			}
		}
		?>
    </tbody>
    <tfoot>
    </tfoot>
  </table>
  </fieldset>
  
  
</form>
</body>
</html>
