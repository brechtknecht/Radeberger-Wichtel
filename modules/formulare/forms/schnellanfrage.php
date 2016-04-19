<form action="#" method="post" class="contentForm">
	<?php
if(isset($_POST['schnellanfrage']['error_msg']) && !empty($_POST['schnellanfrage']['error_msg'])){
	echo "<p class=\"errorMsg\">".htmlspecialchars($_POST['schnellanfrage']['error_msg'])."</p>";	
}
?>
    <input type="hidden" name="form_id" value="schnellanfrage" />
	<fieldset>
    	<legend>Ihre Kontaktdaten</legend>
        <label for="name">Name *</label>
        <input type="text" name="schnellanfrage[name_req]" value="<?php echo isset($_POST['schnellanfrage']['name_req'])?htmlspecialchars($_POST['schnellanfrage']['name_req']):""?>" <?php echo isset($_POST['schnellanfrage']['error_array'])&&in_array("name_req",$_POST['schnellanfrage']['error_array'])?"class=\"errorInput\"":""?> />
        <label for="email_req">eMail *</label>
        <input type="text" name="schnellanfrage[email_req]" value="<?php echo isset($_POST['schnellanfrage']['email_req'])?htmlspecialchars($_POST['schnellanfrage']['email_req']):""?>" <?php echo isset($_POST['schnellanfrage']['error_array'])&&in_array("email_req",$_POST['schnellanfrage']['error_array'])?"class=\"errorInput\"":""?> />
    </fieldset>
    
    <fieldset>
    	<legend>Ihre Nachricht *</legend>
        <textarea rows="4" cols="50" name="schnellanfrage[nachricht_req]" <?php echo isset($_POST['schnellanfrage']['error_array'])&&in_array("nachricht_req",$_POST['schnellanfrage']['error_array'])?"class=\"errorInput\"":""?>><?php echo isset($_POST['schnellanfrage']['nachricht_req'])?htmlspecialchars($_POST['schnellanfrage']['nachricht_req']):""?></textarea>
    </fieldset>
    
    <p style="text-align: center;"><button type="submit">Absenden</button></p>
</form>


