<?php
include_once("../../admin/shared/include/environment.inc.php");
if(!isset($_SESSION['cms_user']) || checkPermission("user",$_SESSION['cms_user']['user_id'],"module","bestellungen")==false){
	die("Sie haben keine Berechtigung auf dieses Element zuzugreifen!");
}
if($_SERVER['REQUEST_METHOD']=="POST"){
	include("save.inc.php");
}

//get data	
if(isset($_GET['entry_id']) && $_GET['entry_id']!="new"){
	$query="SELECT * FROM _cms_modules_orders_ WHERE entry_id=".$_GET['entry_id'];
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		$row=mysqli_fetch_assoc($result);
		foreach($row as $key=>$val){
			$row[$key] = htmlspecialchars(stripslashes($val));
		}
	
	$query_products = "SELECT";
	$query_products.= " entry_id AS entry_id";
	$query_products.= ", product_id AS product_id";
	$query_products.= ", anzahl AS anzahl";
	$query_products.= ", preis AS preis";
	$query_products.= ", (SELECT entry_name FROM _cms_modules_produkte_ WHERE entry_id=product_id) AS name";
	//$query_products.= ", rechnungsnummer_prefix AS rechnungsnummer_prefix";
  	//$query_products.= ", rechnungsnummer_jahr AS rechnungsnummer_jahr";
  	//$query_products.= ", rechnungsnummer AS rechnungsnummer";
	$query_products.= " FROM _cms_modules_orders_products_ WHERE pid=".$_GET['entry_id'];
	$result_products = mysqli_query($_SESSION['conn'], $query_products);
	
	$products_count = mysqli_num_rows($result_products);
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>zeemes : web content management system : modules : bestellungen : edit</title>
<script type="text/javascript" src="../../admin/shared/javascript/functions.js">
</script>
<script type="text/javascript" src="../../admin/pages/javascript/functions.js">
</script>
<link rel="stylesheet" href="../../admin/shared/css/styles.css" />
</head>

<body id="content" onload="SHARED_showFieldSetSwitch('form0');">
<h2>MODUL: Bestellungen: <?php echo isset($row['name'])?"Eintrag bearbeiten":"neuen Eintrag anlegen";?></h2>
<?php
if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['save'])){
	echo "<span class=\"showState\">Ihre Änderungen wurden gespeichert</span>";
}
?>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" id="form0" name="form0" enctype="multipart/form-data" onSubmit="return SHARED_submitForm(this,'Änderungen übernehmen?')">
  <input type="hidden" name="entry_id" value="<?php echo $_GET['entry_id'];?>" />
 
  <table cellpadding="0" cellspacing="0" class="table">
    <thead>
      <tr>
        <th style="width: 100px;">&nbsp;</th>
        <th colspan="4" style="width: auto;">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
       <tr>
        <td>Bestelldatum</td>
        <td colspan="4"><?php echo isset($row['last_change'])?$row['last_change']:"";?><?php echo isset($row['entry_last_usr'])?" von ".getUserName($row['entry_last_usr']):"";?></td>
      </tr>
      <tr>
        <td>Bestellnummer</td>
        <td colspan="4"><?php echo isset($row['order_id'])?$row['order_id']:"";?></td>
      </tr>
        <tr>
        <td colspan="5" class="betweenHeader">bestellte Artikel</td>        
      </tr> 
      <?php
	   	$total = 0;
	   	$shipping = $row['shipping'];
	 
	  while($row_products = mysqli_fetch_assoc($result_products)){
	 		
			//$rechnungsnummer = $row_evt['rechnungsnummer_prefix']." ".$row_evt['rechnungsnummer_jahr']."/".fillNumber($row_evt['rechnungsnummer']);
			$total+=$row_products['preis']*$row_products['anzahl']; 
	  ?>
	<tr>
    	<td style="background-color: #EAEAEA;">&nbsp;
        </td>
        <td  style="width: 250px;background-color: #EAEAEA;">
        <?php echo stripslashes($row_products['name']);?></td>
        <td style="width: 80px;background-color: #EAEAEA;"><input type="text" name="anzahl_<?php echo $row_products['entry_id'];?>" style="width: 30px" value="<?php echo $row_products['anzahl'];?>" /> Stück</td>
        <td style="width: 200px;background-color: #EAEAEA;"><?php echo number_format($row_products['preis'],2,",",".");?> € (<?php echo number_format($row_products['preis']*$row_products['anzahl'],2,",",".");?> € gesamt)</td>
        <td style="background-color: #EAEAEA;">&nbsp;</td>
	</tr>
     
     <?php 
       }
	  ?>
      
       <tr>
        <td><strong>Zusammenfassung</strong></td>
        <td colspan="4">
        Summe: <?php echo number_format($total,2,",",".");?> €, Rabatt: <?php echo $row['rabatt'];?>%, Versandkosten: <?php echo number_format($shipping,2,",",".");?> €, <strong>Gesamt: <?php echo number_format($shipping+($total-($total*$row['rabatt']/100)),2,",",".");?></strong>
        </td>
      </tr>
       <tr>
        <td colspan="5" class="betweenHeader">Bestellstatus</td>        
      </tr> 
       <tr>
        <td>Status</td>      
        <td colspan="4" >
        	<select name="status">
            	<option value="offen" <?php echo $row['status']=="offen"?"selected=\"selected\"":"";?>>offen</option>
                <option value="teilweise bezahlt" <?php echo $row['status']=="teilweise bezahlt"?"selected=\"selected\"":"";?>>teilweise bezahlt</option>
                <option value="vollständig bezahlt" <?php echo $row['status']=="vollständig bezahlt"?"selected=\"selected\"":"";?>>vollständig bezahlt</option>
                <option value="abgeschlossen" <?php echo $row['status']=="abgeschlossen"?"selected=\"selected\"":"";?>>Artikel versendet - Bestellung abgeschlossen</option>
            </select>
        </td> 
      </tr> 
       <tr>
        <td colspan="5" class="betweenHeader">Personendaten</td>        
      </tr>
      
      <tr>
        <td>Vorname Name</td>
        <td colspan="4"><input type="text" name="vorname" value="<?php echo isset($row['vorname'])?$row['vorname']:"";?>" style="width: 193px;" />&nbsp;<input type="text" name="name" value="<?php echo isset($row['name'])?$row['name']:"";?>" style="width: 195px;" /></td>
      </tr>
      <tr>
        <td>Straße</td>
        <td colspan="4"><input type="text" name="strasse" value="<?php echo isset($row['strasse'])?$row['strasse']:"";?>" /></td>
      </tr>
      <tr>
        <td>PLZ Ort</td>
        <td colspan="4"><input type="text" name="plz" value="<?php echo isset($row['plz'])?$row['plz']:"";?>" style="width: 43px;" />&nbsp;<input type="text" name="ort" value="<?php echo isset($row['ort'])?$row['ort']:"";?>" style="width: 345px;" /></td>
      </tr>
       <tr>
        <td>eMail</td>
        <td colspan="4"><input type="text" name="email" value="<?php echo isset($row['email'])?$row['email']:"";?>" /></td>
      </tr>
      <tr>
        <td>Kontoinhaber</td>
        <td colspan="4"><input type="text" name="kontoinhaber" value="<?php echo isset($row['kontoinhaber'])?$row['kontoinhaber']:"";?>" /></td>
      </tr>
      <tr>
        <td>Kontonummer</td>
        <td colspan="4"><input type="text" name="kontonummer" value="<?php echo isset($row['kontonummer'])?$row['kontonummer']:"";?>" /></td>
      </tr>
      <tr>
        <td>Bankleitzahl</td>
        <td colspan="4"><input type="text" name="bankleitzahl" value="<?php echo isset($row['bankleitzahl'])?$row['bankleitzahl']:"";?>" /></td>
      </tr>
     
       
       <tr>
        <td colspan="5" class="betweenHeader">Aktionen</td>        
      </tr> 
      <?php /*
      <tr>
         <td>&nbsp;</td>
         <td colspan="4">
         <button type="button" style="background-color: #660000" onclick="window.open('anschreiben.pdf.php?entry_id=<?php echo $_GET['entry_id'];?>','Anschreiben','width=800,height=500,scrollbars=yes')">Anschreiben drucken</button>
         <button type="button" style="background-color: #660000" onclick="if(confirm('Rechnung erneut senden?')){window.open('sendInvoice.php?sid=<?php echo $row['sid'];?>&email=<?php echo $row['email'];?>','Anschreiben','width=800,height=500,scrollbars=yes')}">Rechnung erneut senden</button>
         <button type="button" style="background-color: #660000" onclick="if(confirm('Rechnung ansehen?')){window.open('../../rechnung.php?sid=<?php echo $row['sid'];?>','Anschreiben','width=800,height=500,scrollbars=yes,menubar=yes')}">Rechnung ansehen</button>
         </td>        
      </tr>
	  */
	  ?>
      </tbody>
    <tfoot>
    </tfoot>
  </table>

  
  
  <button type="submit">Änderungen übernehmen</button>
  <button type="button" onclick="location.href='index.php'">zurück zur Liste</button>
    
</form>
</body>
</html>
