<?php
include("../../shared/include/environment.inc.php");
isLoggedIn();
if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['save'])){
	include("save.inc.php");
}
$query="SELECT setting_key,setting_value FROM _cms_settings_";
$result=mysqli_query($_SESSION['conn'], $query);
if(mysqli_num_rows($result)>0){
	$var_array=array();
	while($row=mysqli_fetch_assoc($result)){
		$var_array[$row['setting_key']]=$row['setting_value'];	
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>cMSP :: Settings :: Metatags</title>
<script type="text/javascript" src="../../shared/javascript/functions.js"></script>
<link rel="stylesheet" href="../../shared/css/styles.css" />
</head>

<body id="content">
<h2>Titel und Metatags festlegen</h2>
<div id="saveTools">
<button type="button" onclick="SHARED_submitForm(document.form0,'Änderungen übernehmen?');">Änderungen übernehmen</button>
</div>
<?php
if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['save']))
	{
	echo "<span class=\"showState\">Ihre Änderungen wurden gespeichert</span>";
	}
?>
<form action="#" method="post" name="form0">
	<?php
    if(isset($_SESSION['fid'])){
		?>
         <input type="hidden" name="fid" value="<?php echo htmlspecialchars($_SESSION['fid'])?>" />
		<?php	
	}
	?>
    <input type="hidden" name="save" value="1" />
    <table cellpadding="0" cellspacing="0" class="table">
		<tr>
        	<th style="width: 100px">Element</th><th style="width: 50px">Inhalt</th><th>Beschreibung</th>
        </tr>
        <tr>
        	<td>Seitentitel</td>
            <td><textarea name="entry_title" rows="1"><?php echo $var_array['entry_title'];?></textarea></td><td>Der in der oberen Browserleiste angezeigte Seitentitel</td>
        </tr>
        <tr>
        	<td>Beschreibung</td>
            <td><textarea name="entry_meta_description" rows="2"><?php echo $var_array['entry_meta_description'];?></textarea></td><td>Kurzer Beschreibungstext der Seite (suchmaschinenrelevant)</td>
        </tr>
        <tr>
        	<td>Keywords</td>
            <td><textarea name="entry_meta_keywords" rows="3"><?php echo $var_array['entry_meta_keywords'];?></textarea></td><td>Stichworte, mit Kommata getrennt, die den Inhalt der Seite zusammenfassen (suchmaschinenrelevant)</td>
        </tr>
      </table>
     
</form>
</html>
