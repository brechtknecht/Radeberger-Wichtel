<?php
if(isset($_FILES['csvdata']) && $_FILES['csvdata']['size'] > 0){
		mysqli_query($_SESSION['conn'], "DELETE FROM _cms_modules_newsletter_users_ WHERE entry_category NOT LIKE '%test_%'");
		$handle = fopen ($_FILES['csvdata']['tmp_name'],"r");              // Datei zum Lesen öffnen
		while ( ($data = fgetcsv ($handle, 10000, ";")) !== FALSE ) { // Daten werden aus der Datei
			$query="INSERT INTO _cms_modules_contacts_ SET";	
			$query.=" main_category='".mysqli_real_escape_string($_SESSION['conn'], $_POST['main_category'])."',";
			$query.=" kundennummer='".mysqli_real_escape_string($_SESSION['conn'], $_POST['kundennummer'])."',";
			$query.=" anrede='".mysqli_real_escape_string($_SESSION['conn'], $_POST['anrede'])."',";
			$query.=" name='".mysqli_real_escape_string($_SESSION['conn'], $_POST['name'])."',";
			$query.=" vorname='".mysqli_real_escape_string($_SESSION['conn'], $_POST['vorname'])."',";
			$query.=" firma='".mysqli_real_escape_string($_SESSION['conn'], $_POST['firma'])."',";
			$query.=" abteilung='".mysqli_real_escape_string($_SESSION['conn'], $_POST['abteilung'])."',";
			$query.=" strasse='".mysqli_real_escape_string($_SESSION['conn'], $_POST['strasse'])."',";
			$query.=" plz='".mysqli_real_escape_string($_SESSION['conn'], $_POST['plz'])."',";
			$query.=" ort='".mysqli_real_escape_string($_SESSION['conn'], $_POST['ort'])."',";
			$query.=" telefon='".mysqli_real_escape_string($_SESSION['conn'], $_POST['telefon'])."',";
			$query.=" mobil='".mysqli_real_escape_string($_SESSION['conn'], $_POST['mobil'])."',";
			$query.=" fax='".mysqli_real_escape_string($_SESSION['conn'], $_POST['fax'])."',";
			$query.=" email='".mysqli_real_escape_string($_SESSION['conn'], $_POST['email'])."',";
			$query.=" url='".mysqli_real_escape_string($_SESSION['conn'], $_POST['url'])."',";
			$query.=" last_change=NOW(),";
			$query.=" entry_last_usr=".$_SESSION['cms_user']['user_id'];
			$result = mysqli_query($_SESSION['conn'], $query);
			echo mysqli_error($_SESSION['conn']);
		}
		fclose ($handle);
	
		
		
		
	}
?>