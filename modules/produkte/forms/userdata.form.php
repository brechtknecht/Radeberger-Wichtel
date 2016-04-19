<?php

$module_settings = getXMLNodeContent("modules/produkte/module_settings.xml");

if(isset($_SESSION['page_user']['error_msg']) && !empty($_SESSION['page_user']['error_msg'])){
	echo "<p class=\"errorMsg\">".htmlspecialchars($_SESSION['page_user']['error_msg'])."</p>";	
}
?>
<form action="zusammenfassung,16.php?sum" method="post" class="contentForm">
	<fieldset>
    	<legend><::ihre kontaktdaten::></legend>
        <label for="name"><::ihr name::>*</label>
        <input type="text" name="page_user[name_req]" value="<?php echo isset($_SESSION['page_user']['name_req'])?htmlspecialchars($_SESSION['page_user']['name_req']):""?>" <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("name_req",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?> />
        <label for="plz_req"><::plz::> *</label>
        <input type="text" name="page_user[plz_req]" value="<?php echo isset($_SESSION['page_user']['plz_req'])?htmlspecialchars($_SESSION['page_user']['plz_req']):""?>" <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("plz_req",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?> />
        <label for="ort_req"><::ort::> *</label>
        <input type="text" name="page_user[ort_req]" value="<?php echo isset($_SESSION['page_user']['ort_req'])?htmlspecialchars($_SESSION['page_user']['ort_req']):""?>" <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("ort_req",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?> />
        <label for="strasse_req"><::straße::> *</label>
        <input type="text" name="page_user[strasse_req]" value="<?php echo isset($_SESSION['page_user']['strasse_req'])?htmlspecialchars($_SESSION['page_user']['strasse_req']):""?>" <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("strasse_req",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?> />
        <label for="email_req"><::email::> *</label>
        <input type="text" name="page_user[email_req]" value="<?php echo isset($_SESSION['page_user']['email_req'])?htmlspecialchars($_SESSION['page_user']['email_req']):""?>" <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("email_req",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?> />
    </fieldset>
    
    <fieldset>
     <legend><::versandoptionen::></legend>
   
    <select name="page_user[versand]" id="versand">
        <option value="postversand" <?php echo isset($_SESSION['page_user']['versand']) && $_SESSION['page_user']['versand']=="postversand"?"selected=\"selected\"":"";?>><::postversand::></option>
        <option value="abholung" <?php echo isset($_SESSION['page_user']['versand']) && $_SESSION['page_user']['versand']=="abholung"?"selected=\"selected\"":"";?>><::abholung::></option>
    </select>
    
    <div id="versandRegion">
   	 <p>
     <label for="versand_region"><::versandregion auswählen::></label>
      <select name="page_user[versand_region]">
	  <?php
		$region_shipping = json_decode($module_settings['shipping'], true);
		
		$regions = array(
			"deutschland"=>"<::deutschland::>",
			"eu"=>"<::eu::>",
			"outside_eu"=>"<::outside_eu::>"
		);
		foreach($regions as $key=>$val){
			?>
            <option value="<?php echo $key?>" <?php echo isset($_SESSION['page_user']['versand_region']) && $_SESSION['page_user']['versand_region'] == $key?"selected=\"selected\"":"";?>><?php echo $val." | <::versandkosten::>: ".number_format($region_shipping[0][$key], 2, ",", ".")." € ";?></option>
            <?php
		}
		?>
        </select>
        </p>
    </div>
    
    </fieldset>
    
    <fieldset>
    <legend><::bezahloptionen::></legend>
    	
    	<select name="page_user[bezahlung]" id="bezahlung" onchange="modAccountData(this)">
       	 	<option value="vorkasse" <?php echo isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung']=="vorkasse"?"selected=\"selected\"":"";?>><::vorkasse::></option>
          <option value="bankeinzug" <?php echo isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung']=="bankeinzug"?"selected=\"selected\"":"";?>><::bankeinzug::></option>
         
		  
          <option value="kreditkarte" <?php echo isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung']=="kreditkarte"?"selected=\"selected\"":"";?>><::kreditkarte::></option>
	
     
       </select>
       
       <div id="bankeinzug" <?php echo isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung'] == "bankeinzug"?"class=\"active\"":"";?>>
        <h4><::nur bei bezahloption bankeinzug::></h4>
         <p <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("einzug_bestaetigt",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?>><label for="einzug_bestaetigt" class="checkboxLabel">
            <input type="checkbox" name="page_user[einzug_bestaetigt]" value="ja"  <?php echo (isset($_SESSION['page_user']['einzug_bestaetigt']) && $_SESSION['page_user']['einzug_bestaetigt']=="ja"?"checked=\"checked\"":"");?> style="width: auto" />
            Ich ermächtige Herrn Karl-Heinz Pinkert, einmalig eine Zahlung von meinem (unserem) Konto mittles Lastschrift einzuziehen. Zugleich weise ich mein (unser) Kreditinstitut an, die von Herrn Karl-Heinz Pinkert auf mein (unser) Konto gezogene Lastschrift einzulösen.  
            </label>
        </p>
        <p <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("kontoinhaber",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?>><label for="kontoinhaber"><::kontoinhaber::>*</label>
            <input type="text" name="page_user[kontoinhaber]" value="<?php echo isset($_SESSION['page_user']['kontoinhaber'])?$_SESSION['page_user']['kontoinhaber']:""?>"  />
        </p>
        <p <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("iban",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?>><label for="bankverbindung">IBAN*</label>
            <input type="text" name="page_user[iban]" value="<?php echo isset($_SESSION['page_user']['iban'])?$_SESSION['page_user']['iban']:""?>"  />
        </p>
        <p <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("bic",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?>><label for="bic">BIC (<::nur bei bestellungen aus dem ausland::>)</label>
            <input type="text" name="page_user[bic]" value="<?php echo isset($_SESSION['page_user']['bic'])?$_SESSION['page_user']['bic']:""?>"  />
        </p>
    
</div>
<!--
<div id="kreditkarte" <?php echo isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung'] == "kreditkarte"?"class=\"active\"":"";?>>
	<h4><::nur bei bezahloption kreditkarte::></h4>
    <p <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("name_on_card",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?>><label for="name_on_card"><::karteninhaber::>*</label>
            <input type="text" name="page_user[name_on_card]" value="<?php echo isset($_SESSION['page_user']['name_on_card'])?$_SESSION['page_user']['name_on_card']:""?>"  />
        </p>
        
        <p <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("card_number",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?>><label for="card_number"><::kartennummer::>*</label>
            <input type="text" name="page_user[card_number]" value="<?php echo isset($_SESSION['page_user']['card_number'])?$_SESSION['page_user']['card_number']:""?>"  />
        </p>
        
        <p>
        <label for="valid_to_month"><::gültig bis::>* <?php echo $_SESSION['page_user']['valid_to_month'];?></label>
        <select name="page_user[valid_to_month]">
        	<?php
            for($i = 1; $i <= 12; $i++){
				?>
                <option value="<?php echo $i;?>" <?php echo isset($_SESSION['page_user']['valid_to_month']) && $_SESSION['page_user']['valid_to_month'] == $i?"selected=\"selected\"":""?>><?php echo $i;?></option>
                <?php	
			}
			?>
        </select>
        <select name="page_user[valid_to_year]">
        	<?php
            for($i = date("Y"); $i <= date("Y", strtotime("today +20 years")); $i++){
				?>
                <option value="<?php echo $i;?>" <?php echo isset($_SESSION['page_user']['valid_to_year']) && $_SESSION['page_user']['valid_to_year'] == $i?"selected=\"selected\"":""?>><?php echo $i;?></option>
                <?php	
			}
			?>
        </select>
        </p>
        
        <p <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("ccv",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?>><label for="ccv">CCV*</label>
            <input type="text" name="page_user[ccv]" value="<?php echo isset($_SESSION['page_user']['ccv'])?$_SESSION['page_user']['ccv']:""?>"  />
        </p>
</div>
//-->
    </fieldset>
       
    <fieldset>
    	<legend><::anmerkungen::></legend>
        <textarea rows="5" cols="50" name="page_user[anmerkungen]" <?php echo isset($_SESSION['page_user']['error_array'])&&in_array("anmerkungen",$_SESSION['page_user']['error_array'])?"class=\"errorInput\"":""?>><?php echo isset($_SESSION['page_user']['anmerkungen'])?htmlspecialchars($_SESSION['page_user']['anmerkungen']):""?></textarea>
    </fieldset>
    
    <button type="submit"><::zusammenfassung::></button>
    <a href="warenkorb,12.php?cart"><::warenkorb bearbeiten::></a>
        
</form>


