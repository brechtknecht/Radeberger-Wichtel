<?php
function showModule(){
	if(isset($_GET['register'])){
		if($_SERVER['REQUEST_METHOD'] == "POST"){
			return modCheckForm();
		}
		else{
			return modRegisterUser();
		}
		
	}
	else{
		return modLoginUser();
	}
}

function modCheckForm(){
	if(!isset($_SESSION['page_user'])){
		$_SESSION['page_user'] = array();
	}
	if(isset($_POST['error_array'])){
		unset($_POST['error_array']);
	}
	$_POST['error_array']=array();
	
	//check required fields
	foreach($_POST as $key => $val){
		$val = preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "",$val);
		$_SESSION['page_user'][$key] = $val;
		if(strstr($key,"_req") && (empty($val) || $val == "Pflichtfeld!")){
			array_push($_POST['error_array'],$key);
			$_SESSION['page_user'][$key] = "Pflichtfeld!";
		}
	}
	
	//check passwords
	if(!in_array("password_req",$_POST['error_array']) && !in_array("password_check_req", $_POST['error_array'])){
		if($_POST['password_req'] != $_POST['password_check_req']){
			array_push($_POST['error_array'],"password_check_req");
			$_SESSION['page_user']['password_check_req'] = "Passwörter stimmen nicht überein!";
		}
	}
	
	if(sizeof($_POST['error_array'])==0){
		//add user to db
		$query="INSERT INTO _cms_modules_contacts_ SET";		
		$query.=" main_category='".mysqli_real_escape_string($_SESSION['conn'], "Onlineshop")."',";
		$query.=" name='".mysqli_real_escape_string($_SESSION['conn'], $_POST['name_req'])."',";
		$query.=" vorname='".mysqli_real_escape_string($_SESSION['conn'], $_POST['vorname_req'])."',";
		$query.=" firma='".mysqli_real_escape_string($_SESSION['conn'], $_POST['firma'])."',";
		$query.=" strasse='".mysqli_real_escape_string($_SESSION['conn'], $_POST['strasse_req'])."',";
		$query.=" plz='".mysqli_real_escape_string($_SESSION['conn'], $_POST['plz_req'])."',";
		$query.=" ort='".mysqli_real_escape_string($_SESSION['conn'], $_POST['ort_req'])."',";
		$query.=" telefon='".mysqli_real_escape_string($_SESSION['conn'], $_POST['telefon'])."',";
		$query.=" email='".mysqli_real_escape_string($_SESSION['conn'], $_POST['email_req'])."',";
		$query.=" password='".md5($_POST['password_req'])."',";
		$query.=" last_change=NOW()";
		$result = mysqli_query($_SESSION['conn'], $query);
		$error = mysqli_error($_SESSION['conn']);
		if(!empty($error) && strstr($error,"Duplicate entry")){
			$output_str = "<div id=\"modContent\">";
			$output_str.= "<h3>Fehler bei der Anmeldung!</h3>";
			$output_str.= "<p>Die von Ihnen angegebene EMail-Adresse ist bereits vorhanden.<br />Bitte geben Sie eine andere EMail-Adresse an.</p>";
			$output_str.= modShowRegisterForm();
			$output_str.= "</div>";
		}
		else{
			$output_str = "<div id=\"modContent\">";
			$output_str.= "<h3>Vielen Dank für Ihre Anmeldung!</h3>";
			$output_str.= "<p>Sie können sich jetzt mit Ihrer EMail-Adresse und Ihrem Passwort anmelden.</p>";
			$output_str.= modShowLoginForm("register");
			$output_str.= "</div>";
		}
			
		return $output_str;
	}
	else{
		$output_str = "<div id=\"modContent\">";
		$output_str.= "<h3>Bitte geben Sie Ihre Daten ein</h3>";
		$output_str.= modShowRegisterForm();
		$output_str.= "</div>";
		return $output_str;
	}
}


function modShowRegisterForm(){
	$output_str = outputFile("modules/login/forms/contact.form.php");
	return $output_str;
}

function modRegisterUser(){
	$output_str = "<div id=\"modContent\">";
	$output_str.= "<h3>Bitte geben Sie Ihre Daten ein</h3>";
	$output_str.= modShowRegisterForm();
	$output_str.= "</div>";
	return $output_str;
}

function modLoginUser(){
	$output_str = "<div id=\"modContent\">";
	//destroy old data
	if(isset($_SESSION['page_user'])){
		unset($_SESSION['page_user']);
	}
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		//check if valid user
		$query = "SELECT * FROM _cms_modules_contacts_ WHERE email='".mysqli_real_escape_string($_SESSION['conn'], $_POST['username'])."' AND password='".md5($_POST['password'])."'";
		$result = mysqli_query($_SESSION['conn'], $query);
		if(mysqli_num_rows($result) == 1){
			$row = mysqli_fetch_assoc($result);
			//create array
			if(!isset($_SESSION['page_user'])){
				$_SESSION['page_user'] = array();
			}
			$_SESSION['page_user']['user_id'] = $row['entry_id'];
			$_SESSION['page_user']['name_req'] = $row['name'];
			$_SESSION['page_user']['vorname_req'] = $row['vorname'];
			$_SESSION['page_user']['firma'] = $row['firma'];
			$_SESSION['page_user']['strasse_req'] = $row['strasse'];
			$_SESSION['page_user']['plz_req'] = $row['plz'];
			$_SESSION['page_user']['ort_req'] = $row['ort'];
			$_SESSION['page_user']['telefon'] = $row['telefon'];
			$_SESSION['page_user']['email_req'] = $row['email'];
			$_SESSION['page_user']['rabatt'] = $row['rabatt'];
			
			$output_str.= "<p>Vielen Dank für Ihre Anmeldung. <br />Ihre Daten sind jetzt im <a href=\"weihnachten,5.php?prodcat=13\">Onlineshop</a> hinterlegt.</p>";
		}
		else{
			$output_str.= "<h3>Login fehlgeschlagen</h3>";
			$output_str.= modShowLoginForm();
		}
	}
	else{
		$output_str.= "<h3>Geben Sie Ihre Zugangsdaten ein</h3>";
		$output_str.= modShowLoginForm();
	}
	$output_str.= "</div>";
	return $output_str;
}

function modShowLoginForm($comefrom = ""){
	$output_str = "";
	$output_str.= "<form action=\"index.php?entry_id=".$_GET['entry_id']."\" method=\"POST\">";
	if($comefrom != "register"){
		$output_str.= "<p>Bitte geben Sie Ihre Zugangsdaten ein:</p>";
	}
	$output_str.= "<fieldset>";
	$output_str.= "<input type=\"text\" name=\"username\" value=\"eMail-Adresse\" title=\"eMail-Adresse\" />";
	$output_str.= "<input type=\"password\" name=\"password\" value=\"Passwort\" title=\"Passwort\" />";
	$output_str.= "</fieldset>";
	$output_str.= "<button type=\"submit\">LOGIN</button>";
	$output_str.= "</form>";
	if($comefrom != "register"){
		$output_str.= "<p style=\"clear: both;\">oder registrieren Sie sich als Neukunde.</p>";
		$output_str.= "<a href=\"index.php?entry_id=".$_GET['entry_id']."&amp;register\" class=\"buttonLink\">REGISTRIEREN</a>";
	}
	return $output_str;
}
?>