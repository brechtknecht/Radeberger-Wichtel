<?php
include("modules/produkte/languages/".$_SESSION['page_language'].".inc.php");
?>
<form id="modForm" action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'];?>" method="post">
<table border="0" cellpadding="0" cellspacing="0" width="430">
	
    	
        <tr>
        	<td width="134"><?php echo $name;?>*</td>
       	  <td><input name="name_req" type="text" value="<?php echo isset($_SESSION['page_user']['name_req'])?$_SESSION['page_user']['name_req']:""?>" <?php echo isset($_POST['error_array'])&&in_array("name_req",$_POST['error_array'])?"class=\"errorInput\"":""?> /></td>
      </tr>
        <tr>
        	<td><?php echo $first_name;?>*</td><td><input name="vorname_req" type="text" value="<?php echo isset($_SESSION['page_user']['vorname_req'])?$_SESSION['page_user']['vorname_req']:""?>" <?php echo isset($_POST['error_array'])&&in_array("vorname_req",$_POST['error_array'])?"class=\"errorInput\"":""?> /></td>
        </tr>
         <tr>
        	<td><?php echo $company;?></td><td><input name="firma" type="text" value="<?php echo isset($_SESSION['page_user']['firma'])?$_SESSION['page_user']['firma']:""?>" <?php echo isset($_POST['error_array'])&&in_array("firma",$_POST['error_array'])?"class=\"errorInput\"":""?> /></td>
        </tr>
        <tr>
        	<td><?php echo $street;?>*</td><td><input name="strasse_req" type="text" value="<?php echo isset($_SESSION['page_user']['strasse_req'])?$_SESSION['page_user']['strasse_req']:""?>" <?php echo isset($_POST['error_array'])&&in_array("strasse_req",$_POST['error_array'])?"class=\"errorInput\"":""?> /></td>
        </tr>
        <tr>
        	<td><?php echo $zip;?>/<?php echo $city;?>*</td><td><input name="plz_req" type="text" value="<?php echo isset($_SESSION['page_user']['plz_req'])?$_SESSION['page_user']['plz_req']:""?>" style="width: 80px;" <?php echo isset($_POST['error_array'])&&in_array("plz_req",$_POST['error_array'])?"class=\"errorInput\"":""?> />
            <input name="ort_req" type="text" value="<?php echo isset($_SESSION['page_user']['ort_req'])?$_SESSION['page_user']['ort_req']:""?>" style="width: 210px;" <?php echo isset($_POST['error_array'])&&in_array("ort_req",$_POST['error_array'])?"class=\"errorInput\"":""?> />
            </td>
        </tr>
        <tr>
        	<td><?php echo $phone;?></td><td><input name="telefon" type="text" value="<?php echo isset($_SESSION['page_user']['telefon'])?$_SESSION['page_user']['telefon']:""?>" /></td>
        </tr>
         
        <tr>
        	<td><?php echo $email;?>*</td><td><input name="email_req" type="text" value="<?php echo isset($_SESSION['page_user']['email_req'])?$_SESSION['page_user']['email_req']:""?>" <?php echo isset($_POST['error_array'])&&in_array("email_req",$_POST['error_array'])?"class=\"errorInput\"":""?> /></td>
        </tr>
             
        <tr>
        	<td><?php echo $note;?></td>
            <td>
            	<textarea name="nachricht" rows="4" <?php echo isset($_POST['error_array'])&&in_array("nachricht",$_POST['error_array'])?"class=\"errorInput\"":""?>><?php echo isset($_SESSION['page_user']['nachricht'])?$_SESSION['page_user']['nachricht']:""?></textarea>            </td>
        </tr>
         
        <tr>
          <td>Versand</td>
          <td>
          	<select name="versand" id="versand" style="width: 295px;">
                <option value="postversand" <?php echo isset($_SESSION['page_user']['versand']) && $_SESSION['page_user']['versand']=="postversand"?"selected=\"selected\"":"";?>>Postversand</option>
                <option value="abholung" <?php echo isset($_SESSION['page_user']['versand']) && $_SESSION['page_user']['versand']=="abholung"?"selected=\"selected\"":"";?>>Abholung</option>
   			</select>          </td>
        </tr>
        <tr>
          <td>Bezahlung</td>
          <td>
          	<select name="bezahlung" id="bezahlung" style="width: 295px;">
                <option value="vorkasse" <?php echo isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung']=="vorkasse"?"selected=\"selected\"":"";?>>Vorkasse</option>
                <option value="paypal" <?php echo isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung']=="paypal"?"selected=\"selected\"":"";?>>PayPal</option>
                <?php /*
                <option value="lastschrift" <?php echo isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung']=="lastschrift"?"selected=\"selected\"":"";?>>Lastschrift</option>
				*/ ?>
   			</select>          </td>
        </tr>
        
        <tr>
        <td></td><td style="border-top: 1px solid #CCCCCC; padding-top: 10px;">
        	<button type="button" onclick="history.back()">ZURÜCK</button>
            <button type="submit">ZUSAMMENFASSUNG</button> 
            </td>
        </tr>
</table> 	
</form>