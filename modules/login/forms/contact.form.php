<?php
include("modules/produkte/languages/".$_SESSION['page_language'].".inc.php");
?>
<form id="modForm" action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'];?>" method="post">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	
    	
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
        	<td><?php echo $zip;?>/<?php echo $city;?>*</td><td><input name="plz_req" type="text" value="<?php echo isset($_SESSION['page_user']['plz_req'])?$_SESSION['page_user']['plz_req']:""?>" style="width: 70px;" <?php echo isset($_POST['error_array'])&&in_array("plz_req",$_POST['error_array'])?"class=\"errorInput\"":""?> />
            <input name="ort_req" type="text" value="<?php echo isset($_SESSION['page_user']['ort_req'])?$_SESSION['page_user']['ort_req']:""?>" style="width: 190px;" <?php echo isset($_POST['error_array'])&&in_array("ort_req",$_POST['error_array'])?"class=\"errorInput\"":""?> />
            </td>
        </tr>
        <tr>
        	<td><?php echo $phone;?></td><td><input name="telefon" type="text" value="<?php echo isset($_SESSION['page_user']['telefon'])?$_SESSION['page_user']['telefon']:""?>" /></td>
        </tr>
         
        <tr>
        	<td><?php echo $email;?>*</td><td><input name="email_req" type="text" value="<?php echo isset($_SESSION['page_user']['email_req'])?$_SESSION['page_user']['email_req']:""?>" <?php echo isset($_POST['error_array'])&&in_array("email_req",$_POST['error_array'])?"class=\"errorInput\"":""?> /></td>
        </tr>
        <tr>
        	<td>Passwort*</td><td><input name="password_req" type="text" value="<?php echo isset($_SESSION['page_user']['password_req'])?$_SESSION['page_user']['password_req']:""?>" <?php echo isset($_POST['error_array'])&&in_array("password_req",$_POST['error_array'])?"class=\"errorInput\"":""?> /></td>
        </tr>
         <tr>
        	<td>Passwort wiederholen*</td><td><input name="password_check_req" type="text" value="<?php echo isset($_SESSION['page_user']['password_check_req'])?$_SESSION['page_user']['password_check_req']:""?>" <?php echo isset($_POST['error_array'])&&in_array("password_check_req",$_POST['error_array'])?"class=\"errorInput\"":""?> /></td>
        </tr>
             
    
         
        <tr>
        <td></td><td>
        	
            <button type="submit">ABSENDEN</button> 
            </td>
        </tr>
</table> 	
</form>