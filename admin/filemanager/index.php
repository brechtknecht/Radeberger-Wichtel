<?php
include("../shared/include/environment.inc.php");

//allowed file types
if(isset($_GET['file_types'])){
    if(strstr($_GET['file_types'],",")){
		$file_types_array=explode(",",$_GET['file_types']);
	}
	else{
		$file_types_array=array($_GET['file_types']);
	}
}

$query = "SELECT *";
foreach($_SESSION['lang_array'] as $val){
	$query.= ", (SELECT description FROM _cms_hp_files_desc_ t2 WHERE t2.entry_parent_id=t1.entry_id AND language='".mysqli_real_escape_string($_SESSION['conn'], $val)."' LIMIT 1) AS desc_".$val;
}
$query.= " FROM _cms_hp_files_ t1 WHERE";
if(isset($_GET['entry_id']) && !empty($_GET['entry_id'])){
	$query.= " entry_parent_id='".mysqli_real_escape_string($_SESSION['conn'], $_GET['entry_id'])."'";	
}
else{
	$query.= " entry_parent_id!=0";
}
 
if(isset($file_types_array) && sizeof($file_types_array)>0){
	$query.=" AND (";
	$i=0;
	foreach($file_types_array as $file_type){
		$query.="file_ext='".$file_type."'";
		if($i<sizeof($file_types_array)-1){
			$query.=" OR ";
		}
		$i++;
	}
	$query.=")";
}
else{
	$query.=" AND file_type='".mysqli_real_escape_string($_SESSION['conn'], $_GET['mode'])."'";
}
if(isset($_GET['file_cat1'])){
	$query.=" AND file_cat1='".mysqli_real_escape_string($_SESSION['conn'], $_GET['file_cat1'])."'";
}
if(isset($_GET['file_cat2'])){
	$query.=" AND file_cat2='".mysqli_real_escape_string($_SESSION['conn'], $_GET['file_cat2'])."'";
}
if(isset($_GET['file_cat3'])){
	$query.=" AND file_cat3='".mysqli_real_escape_string($_SESSION['conn'], $_GET['file_cat3'])."'";
}

$query.=" ORDER BY file_sequence ASC, entry_id DESC";

$result = mysqli_query($_SESSION['conn'], $query);

if(mysqli_num_rows($result) > 0){
 
    $img_array = array();
    while($row = mysqli_fetch_assoc($result)){
		$img_array[] = $row;
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>MediaManager</title>
<script type="text/javascript">
//make image array
var imgArray = new Array();
<?php
if(isset($img_array)){
	foreach($img_array as $row){
		?>
		//image data
		imgArray["f_<?php echo htmlspecialchars($row['entry_id']);?>"] = new Object();
		imgArray["f_<?php echo htmlspecialchars($row['entry_id']);?>"]['id'] = "<?php echo addslashes(htmlspecialchars($row['entry_id']));?>";
		imgArray["f_<?php echo htmlspecialchars($row['entry_id']);?>"]['last_change'] = "<?php echo (htmlspecialchars($row['last_change']));?>";
		imgArray["f_<?php echo htmlspecialchars($row['entry_id']);?>"]['file_type'] = "<?php echo addslashes(htmlspecialchars($row['file_type']));?>";
		imgArray["f_<?php echo htmlspecialchars($row['entry_id']);?>"]['file_ext'] = "<?php echo addslashes(htmlspecialchars($row['file_ext']));?>";
		imgArray["f_<?php echo htmlspecialchars($row['entry_id']);?>"]['file_real_name'] = "<?php echo addslashes(htmlspecialchars($row['file_real_name']));?>";
		imgArray["f_<?php echo htmlspecialchars($row['entry_id']);?>"]['file_save_name'] = "<?php echo addslashes(htmlspecialchars($row['file_save_name']));?>";
		imgArray["f_<?php echo htmlspecialchars($row['entry_id']);?>"]['file_size'] = "<?php echo addslashes(htmlspecialchars($row['file_size']/1048576));?>";
		imgArray["f_<?php echo htmlspecialchars($row['entry_id']);?>"]['file_img_width'] = <?php echo intval($row['file_img_width']);?>;
		imgArray["f_<?php echo htmlspecialchars($row['entry_id']);?>"]['file_img_height'] = <?php echo intval($row['file_img_height']);?>;
		<?php
		//file descriptions
		foreach($_SESSION['lang_array'] as $val){
			?>
			imgArray["f_<?php echo htmlspecialchars($row['entry_id']);?>"]['desc_<?php echo $val;?>'] = "<?php echo addslashes(htmlspecialchars($row['desc_'.$val]))?>";	
			<?php
		}
	}
}
?>
//set vars from query
var entryParentId = <?php echo !empty($_REQUEST['entry_id'])?$_REQUEST['entry_id']:-1;?>;
var fileCat1 = "<?php echo isset($_REQUEST['file_cat1'])?$_REQUEST['file_cat1']:"";?>";
var fileCat2 = "<?php echo isset($_REQUEST['file_cat2'])?$_REQUEST['file_cat2']:"";?>";
var fileCat3 = "<?php echo isset($_REQUEST['file_cat3'])?$_REQUEST['file_cat3']:"";?>";
var call = "<?php echo isset($_REQUEST['call'])?$_REQUEST['call']:"";?>";
var langArray = new Array();
<?php
foreach($_SESSION['lang_array'] as $val){
	?>
	langArray.push("<?php echo $val;?>");
	<?php
}

$rootURL = "http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
$rootURL = str_replace("index.php", "", $rootURL);
?>

var rootURL = "<?php echo $rootURL;?>";
</script>
<?php
	if(isset($_REQUEST['call']) && $_REQUEST['call']=="tinymce"){
	?>
		<script type="text/javascript" src="../shared/javascript/tiny_mce/tiny_mce_popup.js"></script>
<?php
	}
?>

<link rel="stylesheet" type="text/css" href="css/styles.css" />

</head>

<body>
    <h1>MediaManager v2</h1>
    <div id="content">
	<ul id="fileList"></ul>
	<div id="fileContent">
	    <div id="mainActions">
		<h2>Allgemeine Aktionen</h2>
		<?php
        /*
		?>
        <ul>
		    <li><img src="images/image_add.png" alt="" title="Datei hinzufügen" onclick="addFile()" /></li>
		    <li><img src="images/delete.png" alt="" title="alle Dateien löschen" onclick="deleteAllFiles()" /></li>
		</ul>
		*/
		?>
        <ul>
		    
            <li style="display: none;"><button type="button" onclick="addFile('form')">Datei(en) hinzufügen</button></li>
             <li ><button type="button" onclick="addFile('form')">Datei(en) hinzufügen</button></li>
			  <li><button type="button" onclick="deleteAllFiles()">Alle angezeigten Dateien löschen</button></li>
		</ul>
	    </div>
	    <div id="fileActions">
		<h2>Dateieigenschaften</h2>
		<p id="fileInfo">Keine Datei ausgewählt</p>
	    </div>
	    
	</div>
    </div>
    <div id="uploadContainer"></div>
    
    <script type="text/javascript" src="javascript/functions_neu.js"></script>
    <script src="../shared/javascript/functions.js" type="text/javascript"></script>
    
    <?php
	if(isset($_REQUEST['call']) && $_REQUEST['call']=="tinymce"){
	?>
		<script type="text/javascript">
         
        
        var FileBrowserDialogue = {
            init : function () {
                // Here goes your code for setting your custom things onLoad.
            },
            mySubmit : function (file_name, desc) {
                
                var url = "";
                <?php
                    if(isset($_GET['url']) && $_GET['url'] == "full"){
                    ?>
                    url+= "<?php echo $_SESSION['global_vars']['path_to_root'];?>";
                    <?php
                    }
                    
                    ?>
                    url+= "files/";
                    <?php
                    if(isset($_GET['mode']) && ($_GET['mode'] == "image" || $_GET['mode'] == "media")){
                    ?>
                    url+= file_name;
                    <?php
                    }
                    if(isset($_GET['mode']) && $_GET['mode']=="file"){
                    ?>
                    url+= "download.php?file_name=" + file_name;
                    <?php
                    }
                    ?>
                    var win = tinyMCEPopup.getWindowArg("window");
            
                    // insert information now
                    win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = url;
                    if(win.document.getElementById("alt")){
                        win.document.getElementById("alt").value = desc;
                    }
                    if(win.document.getElementById("linktitle")){
                        win.document.getElementById("linktitle").value = desc;
                    }
            
                    <?php
                    if(isset($_GET['mode']) && $_GET['mode'] == "image"){
                    ?>
                    // for image browsers: update image dimensions
                    if (win.ImageDialog.getImageData) win.ImageDialog.getImageData();
                    if (win.ImageDialog.showPreviewImage) win.ImageDialog.showPreviewImage(url);
                    <?php
                    }
                    ?>
            
                    // close popup window
                    tinyMCEPopup.close();
                }
            }
            
        tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);
        
       
        </script>
    <?php
	}
	?>
    
</body>
</html>
