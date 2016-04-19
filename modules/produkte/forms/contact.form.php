<?php
include("modules/formulare/languages/".$_SESSION['page_language'].".inc.php");
?>
<span>Bitte füllen Sie alle mit * gekennzeichneten Felder aus.</span>
<form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'];?>" method="post" class="contentForm">

<fieldset>
	<legend>Ihre Kontaktdaten</legend>
	<p <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("vorname_req",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?>><label for="vorname_req"><?php echo $first_name;?>*</label><input name="vorname_req" id="vorname_req" type="text" value="<?php echo isset($_SESSION['page_user']['vorname_req'])?$_SESSION['page_user']['vorname_req']:""?>"  /></p>

<p <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("strasse_req",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?>><label for="strasse_req"><?php echo $street;?>*</label><input name="strasse_req" id="strasse_req" type="text" value="<?php echo isset($_SESSION['page_user']['strasse_req'])?$_SESSION['page_user']['strasse_req']:""?>"  /></p>


<p <?php echo isset($_SESSION['page_user']['error_array'])&& (in_array("plz_req",$_SESSION['page_user']['error_array']) || in_array("ort_req",$_SESSION['page_user']['error_array'])) ?"class=\"errorInput\"":""?>><label for="plz_req"><?php echo $zip;?>/<?php echo $city;?>*</label><input name="plz_req" id="plz_req" type="text" value="<?php echo isset($_SESSION['page_user']['plz_req'])?$_SESSION['page_user']['plz_req']:""?>" style="width: 80px;" /><input name="ort_req" id="ort_req" type="text" value="<?php echo isset($_SESSION['page_user']['ort_req'])?$_SESSION['page_user']['ort_req']:""?>" style="width: 200px;" /></p>

<p <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("email_req",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?>><label for="email_req"><?php echo $email;?>*</label><input name="email_req" id="email_req" type="text" value="<?php echo isset($_SESSION['page_user']['email_req'])?$_SESSION['page_user']['email_req']:""?>"  /></p>
</fieldset>

<fieldset>
<p><label for="bemerkungen">Bemerkungen</label><textarea name="bemerkungen" id="bemerkungen" rows="5" cols="20"><?php echo isset($_SESSION['page_user']['bemerkungen'])?$_SESSION['page_user']['bemerkungen']:"";?></textarea></p>
</fieldset>

<fieldset>
<p><label for="versand">Versand</label>
<select name="versand" id="versand">
    <option value="postversand" <?php echo isset($_SESSION['page_user']['versand']) && $_SESSION['page_user']['versand']=="postversand"?"selected=\"selected\"":"";?>>Postversand</option>
    <option value="abholung" <?php echo isset($_SESSION['page_user']['versand']) && $_SESSION['page_user']['versand']=="abholung"?"selected=\"selected\"":"";?>>Abholung</option>
</select>
</p>
</fieldset>

<fieldset>
<p><label for="bezahlung"><?php echo $payment;?>*</label>
<select name="bezahlung" id="bezahlung" onchange="modAccountData()">
    <option value="bankeinzug" <?php echo isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung']=="bankeinzug"?"selected=\"selected\"":"";?>>Bankeinzug</option>
    <option value="vorkasse" <?php echo isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung']=="vorkasse"?"selected=\"selected\"":"";?>>Vorkasse</option>
    <?php /*
    <option value="paypal" <?php echo isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung']=="paypal"?"selected=\"selected\"":"";?>>PayPal</option>
	*/ ?>
       
</select>
</p>


<div id="account_data" <?php echo isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung']=="bankeinzug"?"style=\"display: block;\"":"";?>>
    <p style="padding-left: 132px;">Nur bei Bezahloption "Bankeinzug"</p>
     <p  <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("einzug_bestaetigt",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?>><label for="einzug_bestaetigt" style="height: 90px">&nbsp;</label>
    	<input type="checkbox" name="einzug_bestaetigt" value="ja"  <?php echo (isset($_SESSION['page_user']['einzug_bestaetigt']) && $_SESSION['page_user']['einzug_bestaetigt']=="ja"?"checked=\"checked\"":"");?> style="width: auto" />
        Ich ermächtige Herrn Karl-Heinz Pinkert, einmalig eine Zahlung von meinem (unserem) Konto mittles Lastschrift einzuziehen. Zugleich weise ich mein (unser) Kreditinstitut an, die von Herrn Karl-Heinz Pinkert auf mein (unser) Konto gezogene Lastschrift einzulösen.  
    </p>
    <p <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("kontoinhaber",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?>><label for="kontoinhaber">Kontoinhaber*</label>
    	<input type="text" name="kontoinhaber" value="<?php echo isset($_SESSION['page_user']['kontoinhaber'])?$_SESSION['page_user']['kontoinhaber']:""?>"  />
    </p>
    <p <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("iban",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?>><label for="bankverbindung">IBAN*</label>
    	<input type="text" name="iban" value="<?php echo isset($_SESSION['page_user']['iban'])?$_SESSION['page_user']['iban']:""?>"  />
    </p>
    <p <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("bic",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?>><label for="bic">BIC (nur bei Bestellungen aus dem Ausland)</label>
    	<input type="text" name="bic" value="<?php echo isset($_SESSION['page_user']['bic'])?$_SESSION['page_user']['bic']:""?>"  />
    </p>
    
</div>

<fieldset>


<p><button type="submit" style="margin-left: 130px; width: auto"><?php echo $proceed;?></button></p>
</form>
