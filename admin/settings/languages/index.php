<?php
include("../../shared/include/environment.inc.php");
isLoggedIn();
//get saved languages
if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['save'])){
	include("save.inc.php");
}
$result_lang=mysqli_query($_SESSION['conn'], "SELECT setting_value FROM _cms_settings_ WHERE setting_key='languages' LIMIT 1");
$var_array=mysqli_fetch_assoc($result_lang);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>zeemes : web content management system : settings : languages</title>
<script type="text/javascript" src="../../shared/javascript/functions.js"></script>
<link rel="stylesheet" href="../../shared/css/styles.css" />
</head>

<body id="content">
<h2>Sprachen auswählen</h2>
<div id="saveTools">
<button type="button" onclick="SHARED_submitForm(document.form0,'Änderungen übernehmen?');">Änderungen übernehmen</button>
</div>
<?php
if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['save'])){
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
		<thead>
        <tr>
        	<th style="width: 30px;">Symbol</th>
            <th style="width: 100px">Sprache</th>
            <th style="width: 50px">aktiv</th>
            <th>default</th>
        </tr>
        </thead>
        <tbody>
        <tr>
        	<td><img src="images/de.gif" alt="deutsch" /></td>
            <td>deutsch</td>
            <td><input type="checkbox" name="lang_de" value="de" style="width: 30px" <?php echo strstr($var_array['setting_value'],'de|')?"checked=\"checked\"":""?> /></td>
            <td><input type="radio" name="default_lang" value="de" style="width: 30px" title="Die hier ausgewählte Sprache wird angezeigt, wenn keine Sprachauswahl erfolgt ist" <?php echo strstr($var_array['setting_value'],'!de!')?"checked=\"checked\"":"";?> /></td>
        </tr>
        <tr>
        	<td><img src="images/en.gif" alt="englisch" /></td>
            <td>englisch</td>
            <td><input type="checkbox" name="lang_en" value="en" style="width: 30px" <?php echo strstr($var_array['setting_value'],'en|')?"checked=\"checked\"":""?> /></td>
            <td><input type="radio" name="default_lang" value="en" style="width: 30px" title="Die hier ausgewählte Sprache wird angezeigt, wenn keine Sprachauswahl erfolgt ist" <?php echo strstr($var_array['setting_value'],'!en!')?"checked=\"checked\"":"";?> /></td>
        </tr>
        <tr>
        	<td><img src="images/cz.gif" alt="tschechisch" /></td>
            <td>tschechisch</td>
            <td><input type="checkbox" name="lang_cz" value="cz" style="width: 30px" <?php echo strstr($var_array['setting_value'],'cz|')?"checked=\"checked\"":""?> /></td>
            <td><input type="radio" name="default_lang" value="cz" style="width: 30px" title="Die hier ausgewählte Sprache wird angezeigt, wenn keine Sprachauswahl erfolgt ist" <?php echo strstr($var_array['setting_value'],'!cz!')?"checked=\"checked\"":"";?> /></td>
        <tr>
        <tr>
        	<td><img src="images/pl.gif" alt="polnisch" /></td>
            <td>polnisch</td>
            <td><input type="checkbox" name="lang_pl" value="pl" style="width: 30px" <?php echo strstr($var_array['setting_value'],'pl|')?"checked=\"checked\"":""?> /></td>
            <td><input type="radio" name="default_lang" value="pl" style="width: 30px" title="Die hier ausgewählte Sprache wird angezeigt, wenn keine Sprachauswahl erfolgt ist" <?php echo strstr($var_array['setting_value'],'!pl!')?"checked=\"checked\"":"";?> /></td>
        </tr>
        <tr>
        	<td><img src="images/it.gif" alt="italienisch" /></td>
            <td>italienisch</td>
            <td><input type="checkbox" name="lang_it" value="it" style="width: 30px" <?php echo strstr($var_array['setting_value'],'it|')?"checked=\"checked\"":""?> /></td>
            <td><input type="radio" name="default_lang" value="it" style="width: 30px" title="Die hier ausgewählte Sprache wird angezeigt, wenn keine Sprachauswahl erfolgt ist" <?php echo strstr($var_array['setting_value'],'!it!')?"checked=\"checked\"":"";?> /></td>
        </tr>
        <tr>
        	<td><img src="images/fr.gif" alt="französisch" /></td>
            <td>französisch</td>
            <td><input type="checkbox" name="lang_fr" value="fr" style="width: 30px" <?php echo strstr($var_array['setting_value'],'fr|')?"checked=\"checked\"":""?> /></td>
            <td><input type="radio" name="default_lang" value="fr" style="width: 30px" title="Die hier ausgewählte Sprache wird angezeigt, wenn keine Sprachauswahl erfolgt ist" <?php echo strstr($var_array['setting_value'],'!fr!')?"checked=\"checked\"":"";?> /></td>
        </tr>
        <tr>
        	<td><img src="images/es.gif" alt="spanisch" /></td>
            <td>spanisch</td>
            <td><input type="checkbox" name="lang_es" value="es" style="width: 30px" <?php echo strstr($var_array['setting_value'],'es|')?"checked=\"checked\"":""?> /></td>
            <td><input type="radio" name="default_lang" value="es" style="width: 30px" title="Die hier ausgewählte Sprache wird angezeigt, wenn keine Sprachauswahl erfolgt ist" <?php echo strstr($var_array['setting_value'],'!es!')?"checked=\"checked\"":"";?> /></td>
        </tr>
        <tr>
        	<td><img src="images/sk.gif" alt="slowakisch" /></td>
            <td>slowakisch</td>
            <td><input type="checkbox" name="lang_sk" value="sk" style="width: 30px" <?php echo strstr($var_array['setting_value'],'sk|')?"checked=\"checked\"":""?> /></td>
            <td><input type="radio" name="default_lang" value="sk" style="width: 30px" title="Die hier ausgewählte Sprache wird angezeigt, wenn keine Sprachauswahl erfolgt ist" <?php echo strstr($var_array['setting_value'],'!sk!')?"checked=\"checked\"":"";?> /></td>
        </tr>
        <tr>
        	<td><img src="images/slo.gif" alt="slowenisch" /></td>
            <td>slowenisch</td>
            <td><input type="checkbox" name="lang_slo" value="slo" style="width: 30px" <?php echo strstr($var_array['setting_value'],'slo|')?"checked=\"checked\"":""?> /></td>
            <td><input type="radio" name="default_lang" value="slo" style="width: 30px" title="Die hier ausgewählte Sprache wird angezeigt, wenn keine Sprachauswahl erfolgt ist" <?php echo strstr($var_array['setting_value'],'!slo!')?"checked=\"checked\"":"";?> /></td>
        </tr>
       </tbody>
       <tfoot></tfoot>
     </table>
     
</form>
</html>
