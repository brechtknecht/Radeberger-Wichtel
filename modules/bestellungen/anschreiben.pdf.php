<?php
define('FPDF_FONTPATH','../../shared/include/fpdf/font/');
require('../../shared/include/fpdf/fpdf.php');
require('../../shared/include/connect_db.inc.php');

if(isset($_GET['entry_id'])){
	$query = "SELECT";
	$query.= " CONCAT(vorname,' ',name) AS name";
	$query.= ", strasse AS strasse";
	$query.= ", CONCAT(plz,' ',ort) AS ort";
	$query.= " FROM _cms_modules_orders_";
	$query.= " WHERE entry_id=".intval($_GET['entry_id']);
	$result = mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		$row = mysqli_fetch_assoc($result);
		foreach($row as $key=>$val){
			$row[$key] = utf8_decode($val);
		}
	}
}
else{
	die("Fehler: Es wurde keine Veranstaltung übergeben.");
}

/* test
$row['name'] = "Fritz Mustermann";
$row['strasse'] = "Heideweg 19";
$row['ort'] = "01307 Dresden";
*/

$pdf=new FPDF();
$pdf->Open();
$pdf->SetDisplayMode("fullpage");
$pdf->AddPage();

$pdf->Image('images/logo_waterloo.png',110,10,88,64);
$pdf->Image('images/waterloo_address.png',22,55,57);

$pdf->SetFont('Arial','',12);

$pdf->SetXY(20,60);
$pdf->Cell(40,10,$row['name'],0,1,'L');

$pdf->SetXY(20,65);
$pdf->Cell(40,10,$row['strasse'],0,1,'L');

$pdf->SetFont('Arial','B',12);
$pdf->SetXY(20,75);
$pdf->Cell(40,10,$row['ort'],0,1,'L');

$pdf->SetFont('Arial','',12);
$pdf->SetXY(20,85);
$pdf->Cell(175,10,'Dresden, den '.date("d.m.Y"),0,1,'R');

$pdf->SetXY(20,125);
$pdf->Cell(40,10,'Sehr geehrte/r '.$row['name'].',',0,1,'L');

$pdf->SetXY(20,132);
$pdf->Cell(40,10,'anbei erhalten Sie die von Ihnen bestellten Eintrittskarten.',0,1,'L');

$pdf->SetXY(20,139);
$pdf->Cell(40,10,'Wir wünschen Ihnen viel Vergnügen auf der/den Veranstaltung/en.',0,1,'L');

$pdf->SetXY(20,159);
$pdf->Cell(40,10,'Mit freundlichen Grüßen',0,1,'L');

$pdf->SetXY(20,166);
$pdf->Cell(40,10,'Waterloo Produktion Dresden',0,1,'L');



$pdf->Output();
?>