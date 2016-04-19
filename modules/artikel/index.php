<?php
include_once("../../admin/shared/include/environment.inc.php");

$module_name = getParentDir($_SERVER['SCRIPT_FILENAME']);

if(checkPermission("user",$_SESSION['cms_user']['user_id'],"module",$module_name)==false){
	die("Sie haben keine Berechtigung auf dieses Element zuzugreifen!");
}

if($_SERVER['REQUEST_METHOD'] == "POST" && $_FILES['csvdata']['size'] > 0){
	include_once("import.inc.php");
}

//filter results/////////////////////////////
if(!isset($_SESSION['filter'])){
	$_SESSION['filter'] = array();
	//default filter
	$_SESSION['filter']['category'] = "%";
	
}
//set filters
if(isset($_GET['filter'])){
	foreach($_GET['filter'] as $key=>$val){
		if(!empty($val)){
			$_SESSION['filter'][$key] = $val;			
		}
		else{
			$_SESSION['filter'][$key] = "%";	
		}
			
	}	
}
else{
	
}

//get avail years
$query = "SELECT DISTINCT EXTRACT(YEAR FROM news_date) AS news_year FROM _cms_modules_news_ ORDER BY news_year ASC";
$result = mysqli_query($_SESSION['conn'], $query);
if(mysqli_num_rows($result) > 0){
	$year_array = array();
	while($row = mysqli_fetch_assoc($result)){
		if($row['news_year'] != 0){
			$year_array[] = $row['news_year'];	
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
			
			$url = $row['entry_id'];
			echo "<option value=\"".$url."\" ".($url == $entry_url?"selected=\"selected\"":"").">".($indent.$row['entry_name'])."</option>";
									
			if(mysqli_num_rows($result_sub)>0){
				$indent.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				getPageEntries($row['entry_id'],$mode,$entry_url,$indent);
				$indent=substr($indent,0,strlen($indent)-strlen("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"));
			}
		}
	}
}

$query_filter = "";

//get entries
$bg_color="#E0E0E0";
function getEntries($entry_parent_id,$indent=25){
	global $bg_color, $module_name, $query_filter, $p_array;
	$output_str = "";
	$query="SELECT * FROM _cms_modules_news_ WHERE entry_deleted=0";
	//filter entries
	if(isset($_SESSION['filter']) && sizeof($_SESSION['filter']) > 0){
		/*
		$query_filter.= " AND (";
		$query_filter.= "news_date BETWEEN ";
		$query_filter.= "'".$_SESSION['filter']['year_start']."-".$_SESSION['filter']['month_start']."-01'";
		$query_filter.= " AND";
		$query_filter.= "'".$_SESSION['filter']['year_end']."-".$_SESSION['filter']['month_end']."-31'";
		$query_filter.= ")";
		*/
		$query_filter.= " AND (entry_parent_id LIKE '".$_SESSION['filter']['category']."')";
		
		
	}
	else{
		//$query_filter.= " AND news_end>=CURDATE()";	
	}
	
	if(isset($_GET['filter']['search']) && !empty($_GET['filter']['search'])){
		$query.= " AND (";
		$query.= " news_headline LIKE '%".mysqli_real_escape_string($_SESSION['conn'], trim($_GET['filter']['search']))."%'";
		$query.= " OR news_teaser LIKE '%".mysqli_real_escape_string($_SESSION['conn'], trim($_GET['filter']['search']))."%'";
		$query.= " OR news_text LIKE '%".mysqli_real_escape_string($_SESSION['conn'], trim($_GET['filter']['search']))."%'";
		$query.= ")";
	}
	else{
		$query.= $query_filter;
	}
	
	if($_SESSION['filter']['category'] == 5){
		$query.= " ORDER BY entry_sequence ASC, entry_id ASC";
	
	}
	else{
		$query.= " ORDER BY news_date ASC";
	}
	
	//echo $query;
	
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		while($row=mysqli_fetch_assoc($result)){
			$row['news_headline'] = json_decode($row['news_headline'], true);
			$row['news_teaser'] = json_decode($row['news_teaser'], true);
			$row['news_text'] = json_decode($row['news_text'], true);
			
			if($bg_color=="#E0E0E0"){
				$bg_color="#EBEBEB";
			}
			else{
				$bg_color="#E0E0E0";
			}
			
			//check for image/media files
			$query = "SELECT COUNT(entry_id) as file_count FROM _cms_hp_files_ WHERE entry_parent_id='".mysqli_real_escape_string($_SESSION['conn'], $row["entry_id"])."' AND file_cat2='news' AND (file_cat3='flyerbild' OR file_cat3='flyerpdf' OR file_cat3='fotos' OR file_cat3='downloads')";
			$result_files = mysqli_query($_SESSION['conn'], $query);
			if(mysqli_num_rows($result_files) > 0){
				$row_files = mysqli_fetch_assoc($result_files);		
			}
			$output_str.= "<tr style=\"background-color: ".$bg_color."\" onmouseover=\"myColor=this.style.backgroundColor;this.style.backgroundColor='#FFFFFF'\" onmouseout=\"this.style.backgroundColor=myColor\">";
			$output_str.= "<td nowrap=\"nowrap\">";
			$output_str.= "<a href=\"edit.php?entry_id=".$row['entry_id']."\"><img src=\"../../admin/shared/images/editable.gif\" alt=\"\" title=\"Galerie bearbeiten\" /></a>";  
			if(checkPermission("user",$_SESSION['cms_user']['user_id'],"module",$module_name,"function","del")){
				$output_str.= "<img src=\"../../admin/shared/images/delete.gif\" alt=\"\" title=\"Eintrag löschen\" onclick=\"MODULE_deleteEntry(".$row['entry_id'].",this.parentNode.parentNode);\" />";
			}
			$output_str.= "</td>";			
			$output_str.= "<td>".(formatDate2Local($row['news_date'],"dd.mm.YYYY",false,false,false))."</td>";	
			$output_str.= "<td>".($p_array[$row['entry_parent_id']])."</td>";
			
			$output_str.= "<td title=\"".stripslashes($row['news_teaser']['de'])."\">".($row['news_headline']['de']==""?stripslashes($row['news_text']['de']):stripslashes($row['news_headline']['de'])).(isset($row_files) && $row_files['file_count'] > 0?" (".$row_files['file_count'].")":"")."</td>";
			
			$output_str.= "<td>";
			$output_str.= $row['last_change'];
			$output_str.= "</td>";
			$output_str.= "</tr>";			
		}
	}
	
	return $output_str;
}

$result_html = getEntries(0);
$_SESSION['query_filter'] = $query_filter;
$month_array = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");

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

<body id="content" onload="SHARED_showFieldSetSwitch('form0');SHARED_scrollTBody('listTable',SHARED_getAvailHeight(document.body,'listTable'));">
<h2><?php echo strtoupper($module_name);?></h2>
<?php
	if(checkPermission("user",$_SESSION['cms_user']['user_id'],"module",$module_name,"function","new")){
	?>
<div id="saveTools">
	<button type="button" onclick="location.href='edit.php?entry_id=new'">Neuer Eintrag</button>
     <?php
        //if($_SESSION['cms_user']['user_id'] == 1)
		{
		?>
        
        <?php
		}
		?>
</div>
<?php
    	}
	?>

<fieldset id="Eintraege_bearbeiten">
<table cellpadding="0" cellspacing="0" class="table">
	<thead>
    <tr class="filterRow">
    	<td colspan="8">
        <form action="index.php" method="get" enctype="multipart/form-data" style="display: inline;">
            <?php
    if(isset($_SESSION['fid'])){
		?>
         <input type="hidden" name="fid" value="<?php echo htmlspecialchars($_SESSION['fid'])?>" />
		<?php	
	}
	?>
            
          
           <select name="filter[category]" style="width: auto;" onchange="this.parentNode.submit();">
               <option value="%">alle Kategorien</option>
			   <?php
               getPageEntries(0, "", $_SESSION['filter']['category']);
			   ?>
            </select>
            
            
           
        </form>
       
        </td>
    </tr>
    <tr>
    	<th style="width: 60px">Aktionen</th>
        <th style="width: 100px">Datum</th>
       <th style="width: 100px">Kategorie</th>
        <th style="width: 300px">Headline</th>
        <th style="width: auto">geändert</th>
        
    </tr>
    </thead>
    <tbody>
		
	<?php
	echo $result_html;
	?>
    </tbody>
    <tfoot>
    </tfoot>
</table>
</fieldset>
<?php /*
<fieldset id="Datenimport">
<p>
<strong>Achtung! Beim Import einer CSV-Datei werden alle bestehenden Einträge gelöscht!</strong>
</p>

<p>
<input type="file" name="csvdata" /><br />
<button type="submit" style="margin-left: 0">Import starten</button>
</p>
</fieldset>
*/
?>


</body>
</html>
