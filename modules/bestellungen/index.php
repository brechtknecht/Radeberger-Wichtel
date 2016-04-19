<?php
include_once("../../admin/shared/include/environment.inc.php");
if(!isset($_SESSION['cms_user']) || checkPermission("user",$_SESSION['cms_user']['user_id'],"module","bestellungen")==false){
	die("Sie haben keine Berechtigung auf dieses Element zuzugreifen!");
}
//filter entries
if(isset($_GET['filter_orders'])){
	if(!empty($_GET['filter_orders'])){
		$_SESSION['filter_orders']=$_GET['filter_orders'];
	}
	else{
		unset($_SESSION['filter_orders']);
	}
}


//get entries
$class="bg1";
function getEntries($entry_parent_id,$indent=25){
	
	$evt_array = array();
	global $class;
	$query = "SELECT";
	$query.= " entry_id AS this_id";
	$query.= ", order_id AS order_id";
	$query.= ", name AS this_name";
	$query.= ", vorname AS this_vorname";
	$query.= ", ort AS this_ort";
	$query.= ", plz AS this_plz";
	$query.= ", strasse AS this_strasse";
	$query.= ", email AS this_email";
	$query.= ", bezahlung AS bezahlung";
	$query.= ", status AS status";
	$query.= ", last_change AS last_change";
	$query.= " FROM _cms_modules_orders_ WHERE ";
	if(isset($_SESSION['filter_orders'])){
		if($_SESSION['filter_orders'] == "teilweise"){
			$query.= " status='teilweise bezahlt' AND";
		}
		if($_SESSION['filter_orders'] == "bezahlt"){
			$query.= " status='vollständig bezahlt' AND";
		}
		if($_SESSION['filter_orders'] == "abgeschlossen"){
			$query.= " status='abgeschlossen' AND";
		}
		if($_SESSION['filter_orders'] == "all"){
			//$query.= " status='abgeschlossen' AND";
		}
	}
	else{
		$query.= " status='offen' AND";
	}
	if(isset($_GET['search_name']) && $_GET['search_name'] != "Namen suchen"){
		$query.= " (name LIKE '%".mysqli_real_escape_string($_SESSION['conn'], $_GET['search_name'])."%' OR vorname LIKE '%".mysqli_real_escape_string($_SESSION['conn'], $_GET['search_name'])."%') AND";
	}
	
	$query.= " status!='del' ORDER BY last_change ASC";
	//echo $query;
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		while($row=mysqli_fetch_assoc($result)){
			$output_str = "";
			$query_product = "SELECT";
			$query_product.= " product_id AS product_id";
			$query_product.= ", anzahl AS anzahl";
			$query_product.= ", (SELECT entry_name FROM _cms_modules_produkte_ WHERE entry_id=product_id) AS name";
			//$query_product.= ", rechnungsnummer_prefix AS rechnungsnummer_prefix";
  			//$query_product.= ", rechnungsnummer_jahr AS rechnungsnummer_jahr";
  			//$query_product.= ", rechnungsnummer AS rechnungsnummer";
			$query_product.= " FROM _cms_modules_orders_products_ WHERE pid=".$row['this_id'];
		  	$result_product = mysqli_query($_SESSION['conn'], $query_product);
			echo mysqli_error($_SESSION['conn']);
			
			if($class=="bg1"){
				$class="bg2";
			}
			else{
				$class="bg1";
			}
			
			$output_str.= "<tr class=\"".(isset($row['multi_order']) && $row['multi_order']>0 && $row['check_multi_order']==1?"alarm":$class)."\" onmouseover=\"myColor=this.style.backgroundColor;this.style.backgroundColor='#FFFFFF'\" onmouseout=\"this.style.backgroundColor=myColor\">";
					
			$output_str.= "<td>".($row['this_name'].", ".$row['this_vorname']);
			$output_str.= "<br /><span style=\"font-size: 10px;\">".$row['this_plz']." ".$row['this_ort'].", ".$row['this_strasse']."</span>";
			$output_str.= "</td>";
			$output_str.= "<td>".($row['last_change'])."</td>";
			$output_str.= "<td>".($row['order_id'])."</td>";
			$output_str.= "<td>";
			while($row_product = mysqli_fetch_assoc($result_product)){
				$output_str.= stripslashes($row_product['name']).": ".$row_product['anzahl']." Stück<br />";
			}
			$output_str.= "</td>";
			$output_str.= "<td>".$row['bezahlung']."</td>";
			$output_str.= "<td>".$row['status']."</td>";
			$output_str.= "<td nowrap=\"nowrap\">";
			$output_str.= "<a href=\"edit.php?entry_id=".$row['this_id']."\"><img src=\"../../admin/shared/images/editable.gif\" alt=\"\" title=\"Eintrag bearbeiten\" /></a>";  
			$output_str.= "<img src=\"../../admin/shared/images/delete.gif\" alt=\"\" title=\"Eintrag löschen\" onclick=\"MODULE_deleteEntry(".$row['this_id'].",this.parentNode.parentNode);\" />";
			$output_str.= "</td>";
			$output_str.= "</tr>";			
			
			echo $output_str;
						
		}
		
	}
	
	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>zeemes : web content management system : modules : bestellungen</title>
<script type="text/javascript" src="../../admin/shared/javascript/functions.js">
</script>
<script type="text/javascript" src="javascript/module_edit.js">
</script>
<link rel="stylesheet" href="../../admin/shared/css/styles.css" />
<link rel="stylesheet" href="css/module_edit_styles.css" />
</head>

<body id="content" onload="SHARED_scrollTBody('listTable',SHARED_getAvailHeight(document.body,'listTable'));">
<h2>MODUL: Bestellungen</h2>
<table cellpadding="0" cellspacing="0" id="listTable" class="table">
	<thead>
    <tr class="filterRow" >
    	<th colspan="7" style="background-color: #FFFFFF;">
        	<form action="index.php" method="get">
                <select name="filter_orders" style="width: auto">
                    <option value="">offene Bestellungen anzeigen</option>
                    <option value="bezahlt" <?php echo (isset($_SESSION['filter_orders']) && $_SESSION['filter_orders'] == "bezahlt"?"selected=\"selected\"":"")?>>bezahlte Bestellungen anzeigen</option>
                    <option value="abgeschlossen" <?php echo (isset($_SESSION['filter_orders']) && $_SESSION['filter_orders'] == "abgeschlossen"?"selected=\"selected\"":"")?>>abgeschlossene Bestellungen anzeigen</option>
                    <option value="all" <?php echo (isset($_SESSION['filter_orders']) && $_SESSION['filter_orders'] == "all"?"selected=\"selected\"":"")?>>alle Bestellungen anzeigen</option>
                 </select>
                 
             <input type="text" name="search_name" value="Namen suchen" style="width: auto" />
            <button style="width: auto" type="submit">=&gt;</button>
            </form>
            
           
        </th>
   	</tr>
    <tr>
    	<th style="width: 250px">Name</th>
        <th style="width: 150px">Bestelldatum</th>
        <th style="width: 100px">Bestellnummer</th>
         <th style="width: auto">Artikel</th>
         <th style="width: 100px">Bezahlung</th>
         <th style="width: 80px">Status</th>
        <th style="width: 80px">Aktionen</th>
    </tr>
    </thead>
    <tbody>
		<tr>
        	<td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
        </tr>
	<?php
	getEntries(0);
	?>
    </tbody>
    <tfoot>
    </tfoot>
</table>
</body>
</html>
