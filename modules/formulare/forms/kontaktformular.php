<?php
$_SESSION['send_to'] = "kontakt@media-studio-pfund.de";
$_SESSION['subject'] = "Nachricht vom Kontaktformular auf ....de";
?>
<?php
if(isset($_POST['error_msg']) && !empty($_POST['error_msg'])){
	echo "<p class=\"errorMsg\">".htmlspecialchars($_POST['error_msg'])."</p>";	
}
?>
<form action="kontakt,5.php#formular" method="post" id="formular" class="contentForm">
	<h4>Kontaktformular</h4>
	<?php
    if(isset($_SESSION['fid'])){
		?>
         <input type="hidden" name="fid" value="<?php echo htmlspecialchars($_SESSION['fid'])?>" />
		<?php	
	}
	?>
    <fieldset>
    	<legend>Ihre Kontaktdaten</legend>
        <label for="name">Ihr Name *</label>
        <input type="text" name="name_req" value="<?php echo isset($_POST['name_req'])?htmlspecialchars($_POST['name_req']):""?>" <?php echo isset($_POST['error_array'])&&in_array("name_req",$_POST['error_array'])?"class=\"errorInput\"":""?> />
        <label for="email_req">Ihre E-Mailadresse *</label>
        <input type="text" name="email_req" value="<?php echo isset($_POST['email_req'])?htmlspecialchars($_POST['email_req']):""?>" <?php echo isset($_POST['error_array'])&&in_array("email_req",$_POST['error_array'])?"class=\"errorInput\"":""?> />
        <label for="telefon">Ihre Telefonnummer</label>
        <input type="text" name="telefon" value="<?php echo isset($_POST['telefon'])?htmlspecialchars($_POST['telefon']):""?>" <?php echo isset($_POST['error_array'])&&in_array("telefon",$_POST['error_array'])?"class=\"errorInput\"":""?> />
    </fieldset>
    
   
    
    
    
    <fieldset>
    	<legend>Ihre Nachricht *</legend>
        <textarea rows="10" cols="50" name="nachricht_req" <?php echo isset($_POST['error_array'])&&in_array("nachricht_req",$_POST['error_array'])?"class=\"errorInput\"":""?>><?php echo isset($_POST['nachricht_req'])?htmlspecialchars($_POST['nachricht_req']):""?></textarea>
    </fieldset>
    
    <fieldset>
    	<legend>Aktionen</legend>
        <button type="submit">Nachricht senden</button>
        
    </fieldset>
</form>


