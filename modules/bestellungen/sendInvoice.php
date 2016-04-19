<?php
if(isset($_GET['sid'])){
	include_once('../../shared/include/phpmailer/class.phpmailer.php');
	include_once('../../shared/include/functions.inc.php');
	$invoice = outputFile("../../rechnung.php");
	
	//echo $_GET['email'];
	
	$mail = new PHPMailer();
	
	$body = $invoice;
	$mail->IsSendmail(); // telling the class to use SendMail transport
	$mail->From = "vorverkauf@waterloo-produktion.de";
	$mail->FromName = "vorverkauf@waterloo-produktion.de";
	$mail->Subject = "Waterloo Produktion Vorverkauf: Ihre Bestellung";
	//$mail->AltBody = "Diese EMail ist im HTML-Format verfasst. Wenn Ihr EMail-Programm diese Mail nicht korrekt anzeigt, können Sie Ihre Rechnung unter http://www.pfund.info/projekte/vvk/rechnung.php?sid=".$_GET['sid']." anzeigen lassen."; // optional, comment out and test
	$mail->MsgHTML($body);
	$mail->AddAddress($_GET['email']);
	//$mail->AddBCC("vorverkauf@waterloo-produktion.de");
	//$mail->AddBCC("susi@waterloo-produktion.de");
	if($mail->Send()){
		echo "Rechnung wurde erneut an ".$_GET['email']." gesendet.";
	}
	
}

?>