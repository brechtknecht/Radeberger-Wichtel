<?php
include_once("../../admin/shared/include/environment.inc.php");
$module_name = getParentDir($_SERVER['SCRIPT_FILENAME']);
if(strstr($module_name,"\\")){
	$module_name = substr($module_name,strrpos($module_name,"\\")+1);
}

if($_SERVER['REQUEST_METHOD']=="POST"){
	include("save.inc.php");
}

if(checkPermission("user",$_SESSION['cms_user']['user_id'],"module",$module_name)==false){
	die("Sie haben keine Berechtigung auf dieses Element zuzugreifen!");
}

//get module settings
$module_settings = getXMLNodeContent("module_settings.xml");

//filter results
if(isset($_GET['filter_kategorie'])){
	if(!empty($_GET['filter_kategorie'])){
		$_SESSION['filter_kategorie']=$_GET['filter_kategorie'];
	}
	else{
		unset($_SESSION['filter_kategorie']);
	}
}

if(isset($_GET['start_results'])){
	$_SESSION['start_results']=intval($_GET['start_results']);
}
else{
	$_SESSION['start_results']=0;
}



//get categories
$cat_array = array();
$result = mysqli_query($_SESSION['conn'], "SELECT entry_name, entry_id FROM _cms_modules_produkte_kategorien_ ORDER BY entry_sequence ASC");
while($row = mysqli_fetch_assoc($result)){
	$row['entry_name'] = json_decode($row['entry_name'], true);
	$cat_array[$row['entry_id']] = $row['entry_name']['de'];
}

//get entries
$bg_color="#E0E0E0";
function getEntries($entry_parent_id,$indent=25){
	global $bg_color,$count, $cat_array;
	
	$query = "SELECT";
	$query.= " entry_id AS entry_id";
	$query.= ", entry_name AS entry_name";
	$query.= ", entry_nummer AS entry_nummer";
	$query.= ", entry_preis AS entry_preis";
	$query.= ", last_change AS last_change";
	$query.= ", entry_kategorie AS entry_kategorie";
	$query.= " FROM _cms_modules_produkte_";
	
	if(isset($_SESSION['filter_kategorie']) && !empty($_SESSION['filter_kategorie'])){
		$query.=" WHERE entry_kategorie LIKE '%|".$_SESSION['filter_kategorie']."|%'";	
	}
	
	
	$query.=" ORDER BY entry_name ASC LIMIT ";
	$query.=$_SESSION['start_results'].",";	
	$query.="100";
	
	//echo $query;
	
	$result=mysqli_query($_SESSION['conn'], $query);
	echo mysqli_error($_SESSION['conn']);
	if(mysqli_num_rows($result)>0){
		while($row=mysqli_fetch_assoc($result)){
			$row['entry_kategorie'] = explode("|",$row['entry_kategorie']);
			
			if($bg_color=="#E0E0E0"){
				$bg_color="#EBEBEB";
			}
			else{
				$bg_color="#E0E0E0";
			}
			echo "<tr style=\"background-color: ".$bg_color."\" onmouseover=\"myColor=this.style.backgroundColor;this.style.backgroundColor='#FFFFFF'\" onmouseout=\"this.style.backgroundColor=myColor\">";
			echo "<td nowrap=\"nowrap\" class=\"editEntry\">";
			echo "<a href=\"edit.php?entry_id=".$row['entry_id']."\"><img src=\"../../admin/shared/images/editable.gif\" alt=\"\" title=\"Eintrag bearbeiten\" /></a>";  
			echo "<img src=\"../../admin/shared/images/delete.gif\" alt=\"\" title=\"Eintrag löschen\" onclick=\"MODULE_deleteEntry(".$row['entry_id'].",this.parentNode.parentNode);\" />";
			echo "</td>";		
			echo "<td>";
			foreach($row['entry_kategorie'] as $val){
				if(!empty($val)){
					echo stripslashes($cat_array[$val])."<br />";
				}
			}
			echo "</td>";
			echo "<td>".stripslashes($row['entry_name'])."</td>";
			echo "<td>".stripslashes($row['entry_nummer'])."</td>";
			echo "<td>".stripslashes($row['entry_preis'])." €</td>";
			echo "<td>".stripslashes($row['last_change'])."</td>";
			
			echo "</tr>";			
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

<body id="content" onload="SHARED_showFieldSetSwitch('form0');SHARED_scrollTBody('listTable',SHARED_getAvailHeight(document.body,'form0'));">
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
    <button type="button" onclick="SHARED_submitForm(document.form0,'Änderungen übernehmen?')">Änderungen übernehmen</button>
</div>
<?php
    	}
	?>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" id="form0" name="form0">
<fieldset id="Eintraege_auflisten">
<table cellpadding="0" cellspacing="0" id="listTable" class="table">
<thead>
    
  <tr class="filterRow">
   	<th colspan="6">
 

          
                <select name="filter_kategorie" id="filter_kategorie" style="width: auto" onchange="location.href='index.php?filter_kategorie=' + this.value">
                	<option value="">Alle Kategorien anzeigen</option>
                    <?php
                    foreach($cat_array as $key=>$val){
						echo "<option value=\"".$key."\"";
						echo ($key == $_SESSION['filter_kategorie'] ? "selected=\"selected\"":"");
						echo ">".$val;
						echo "</option>";	
					}
					?>
            
      </select>    
        </th>
        
   	</tr> 
    <tr>
        <th colspan="6">
        <?php
       $query = "SELECT";
	$query.= " entry_id AS entry_id";
	$query.= " FROM _cms_modules_produkte_";
	
	if(isset($_SESSION['filter_kategorie']) && !empty($_SESSION['filter_kategorie'])){
		$query.=" WHERE entry_kategorie='".$_SESSION['entry_kategorie']."'";	
	}
	
		$result_count=mysqli_query($_SESSION['conn'], $query);
		$count=mysqli_num_rows($result_count);
		$start=$count/100;
		for($i=0;$i<$start;$i++){
			echo "<a href=\"index.php?start_results=".(($i)*100)."\" class=\"resultLink".(isset($_SESSION['start_results']) && $_SESSION['start_results'] == ($i*100)?"Active":"")."\">".($i*100)."-".(($i*100)+100)."</a>";
			if($i<$start-1){
				echo " | ";
			}
		}
			
		echo " von ".$count." Einträgen";
		?>
        </th>
   	</tr>     
    <tr class="headRow">
    	<th style="width: 40px">&nbsp;</th>
        <th style="width: 100px">Kategorie</th>
        <th style="width: 250px">Name</th>
        <th style="width: 250px">Bestellnummer</th>
        <th style="width: 80px">Preis</th>
    	<th style="width: auto">letzte Änderung</th>
        
    </tr>
    
    </thead>
    <tbody>
		
	<?php
	getEntries(0);
	?>
    </tbody>
</table>
</fieldset>
<fieldset id="Modulvorgaben">
<table cellpadding="0" cellspacing="0" class="table">
	<thead>
      <tr>
        <th style="width: 100px;">&nbsp;</th>
        <th style="width: auto;">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
		<tr>
        	<td>Empfänger eMail</td>
            <td>
            	<input name="module_settings[mailto]" type="text" value="<?php echo isset($module_settings['mailto'])?$module_settings['mailto']:"";?>" />            </td>
       </tr>
       <tr>
        	<td>Absender eMail</td>
            <td>
            	<input name="module_settings[mailfrom]" type="text" value="<?php echo isset($module_settings['mailfrom'])?$module_settings['mailfrom']:"";?>" />            </td>
       </tr>
       <tr>
        	<td>Versandkosten</td>
            <td>
            	<textarea name="module_settings[shipping]" cols="50" rows="5"><?php echo isset($module_settings['shipping'])?$module_settings['shipping']:"";?></textarea>          </td>
       </tr>
       <tr>
         <td>Lieferzeit in Werktagen</td>
         <td><input name="module_settings[shipping_time]" type="text" value="<?php echo isset($module_settings['shipping_time'])?$module_settings['shipping_time']:"";?>" /></td>
       </tr>
       <tr>
        	<td>Betreff Bestätigungsmail</td>
            <td>
            	<input name="module_settings[subject]" type="text" value="<?php echo isset($module_settings['subject'])?$module_settings['subject']:"";?>" />            </td>
       </tr>
       <tr>
        	<td>Text Bestätigungsmail allgemein</td>
            <td>
            	<textarea name="module_settings[mailtext]" cols="50" rows="10"><?php echo isset($module_settings['mailtext'])?$module_settings['mailtext']:"";?></textarea>            </td>
       </tr>
        <tr>
        	<td>Text Bestätigungsmail Vorkasse</td>
            <td>
            	<textarea name="module_settings[mailtext_vorkasse]" cols="50" rows="10"><?php echo isset($module_settings['mailtext_vorkasse'])?$module_settings['mailtext_vorkasse']:"";?></textarea>            </td>
       </tr>
       <tr>
        	<td>Text Bestätigungsmail PayPal</td>
            <td>
            	<textarea name="module_settings[mailtext_paypal]" cols="50" rows="10"><?php echo isset($module_settings['mailtext_paypal'])?$module_settings['mailtext_paypal']:"";?></textarea>            </td>
       </tr>
       <tr>
        	<td>Text Bestätigungsmail Lastschrift</td>
            <td>
            	<textarea name="module_settings[mailtext_lastschrift]" cols="50" rows="10"><?php echo isset($module_settings['mailtext_lastschrift'])?$module_settings['mailtext_lastschrift']:"";?></textarea>            </td>
       </tr>
       <tr>
        	<td>Text Bestätigungsmail Kreditkarte</td>
            <td>
            	<textarea name="module_settings[mailtext_kreditkarte]" cols="50" rows="10"><?php echo isset($module_settings['mailtext_kreditkarte'])?$module_settings['mailtext_kreditkarte']:"";?></textarea>            </td>
       </tr>
       
       <tr>
        	<td>Schlußtext Bestätigungsmail</td>
            <td>
            	<textarea name="module_settings[mailtext_ende]" cols="50" rows="10"><?php echo isset($module_settings['mailtext_ende'])?$module_settings['mailtext_ende']:"";?></textarea>            </td>
       </tr>
	</tbody>
    <tfoot>
    </tfoot>
</table>
</fieldset>
</form>
</body>
</html>
