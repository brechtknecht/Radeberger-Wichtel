<?php
//no external call
if(defined("internalCall")){
	if(isset($_POST['entry_id'])){
		//prepare dates
				
		if($_POST['entry_id']=="new"){
			$query="INSERT INTO _cms_modules_contacts_ SET";	
		}
		else{
			$query="UPDATE _cms_modules_contacts_ SET";	
		}
		if(isset($_POST['main_category_new']) && !empty($_POST['main_category_new'])){
			$_POST['main_category']=$_POST['main_category_new'];
		}
				
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
		$query.=" username='".mysqli_real_escape_string($_SESSION['conn'], $_POST['username'])."',";
		if(!empty($_POST['password'])){
			$query.=" password='".md5($_POST['password'])."',";
		}
		$query.=" url='".mysqli_real_escape_string($_SESSION['conn'], $_POST['url'])."',";
		$query.=" rabatt='".mysqli_real_escape_string($_SESSION['conn'], $_POST['rabatt'])."',";
		
		$query.=" entry_last_usr=".$_SESSION['cms_user']['user_id'].",";
		$query.=" last_change=NOW()";
		if($_POST['entry_id']!="new"){
			$query.=" WHERE entry_id=".intval($_POST['entry_id'])." LIMIT 1";
		}
		$result=mysqli_query($_SESSION['conn'], $query);
		echo mysqli_error($_SESSION['conn']);
		if($_POST['entry_id']=="new"){
			$_POST['entry_id']=mysqli_insert_id($_SESSION['conn']);
		}
		$_GET=$_POST;
	}
}
else{
	die("Fehler!");
}
?>