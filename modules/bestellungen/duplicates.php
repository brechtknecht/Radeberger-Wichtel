<?php
include_once("../../admin/shared/include/environment.inc.php");
if(!isset($_SESSION['cms_user']) || checkPermission("user",$_SESSION['cms_user']['user_id'],"module","bestellungen")==false){
	die("Sie haben keine Berechtigung auf dieses Element zuzugreifen!");
}
//order entries
if(!isset($_GET['order_orders'])){
	$_GET['order_orders'] = "name";
}
if(isset($_GET['order_orders'])){
	if(!empty($_GET['order_orders'])){
		$_SESSION['order_orders']=$_GET['order_orders'];
	}
	else{
		unset($_SESSION['order_orders']);
	}
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
	$query.= ", name AS this_name";
	$query.= ", vorname AS this_vorname";
	$query.= ", ort AS this_ort";
	$query.= ", plz AS this_plz";
	$query.= ", strasse AS this_strasse";
	$query.= ", email AS this_email";
	$query.= ", status AS status";
	$query.= ", check_multi_order AS check_multi_order";
	$query.= ", last_change AS last_change";
	$query.= ", (SELECT COUNT(entry_id) FROM _cms_modules_orders_ WHERE entry_id!=this_id AND check_multi_order=1 AND (email LIKE this_email OR name LIKE this_name OR strasse LIKE this_strasse) AND status!='del') AS multi_order";
	$query.= " FROM _cms_modules_orders_";
	
	if(isset($_SESSION['filter_orders'])){
		$query.= " WHERE";
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
			$query.= " status!='del' AND";
		}
	}
	else{
		$query.= " WHERE status='offen' AND";
	}
	$query.= " check_multi_order=1";
	if(isset($_SESSION['order_orders'])){
		if($_SESSION['order_orders'] == "name"){
			$query.= " ORDER BY name ASC, vorname ASC";
		}
		if($_SESSION['order_orders'] == "name_adresse"){
			$query.= " ORDER BY name ASC, plz ASC, ort ASC, strasse ASC";
		}
		if($_SESSION['order_orders'] == "email"){
			$query.= " ORDER BY email ASC";
		}
		if($_SESSION['order_orders'] == "adresse"){
			$query.= " ORDER BY plz ASC, ort ASC, strasse ASC";
		}
	}
	
	//echo $query;
	$result=mysqli_query($_SESSION['conn'], $query);
	//echo mysqli_error($_SESSION['conn']);
	if(mysqli_num_rows($result)>0){
		while($row=mysqli_fetch_assoc($result)){
			//echo $row['multi_order'];
			if($row['multi_order']>0){
				$output_str = "";
				$query_evt = "SELECT";
				$query_evt.= " evt_id AS evt_id";
				$query_evt.= ", anzahl AS anzahl";
				$query_evt.= ", (SELECT name FROM _cms_modules_veranstaltungen_ WHERE entry_id=evt_id) AS name";
				$query_evt.= ", (SELECT location FROM _cms_modules_veranstaltungen_ WHERE entry_id=evt_id) AS location";
				$query_evt.= ", (SELECT entry_parent_id FROM _cms_modules_veranstaltungen_ WHERE entry_id=evt_id) AS entry_parent_id";
				$query_evt.= ", (SELECT name FROM _cms_modules_locations_ WHERE entry_id=location) AS location_name";
				$query_evt.= ", rechnungsnummer_prefix AS rechnungsnummer_prefix";
				$query_evt.= ", rechnungsnummer_jahr AS rechnungsnummer_jahr";
				$query_evt.= ", rechnungsnummer AS rechnungsnummer";
				$query_evt.= " FROM _cms_modules_orders_events_ WHERE pid=".$row['this_id'];
				$result_evt = mysqli_query($_SESSION['conn'], $query_evt);
				
				if($class=="bg1"){
					$class="bg2";
				}
				else{
					$class="bg1";
				}
				
				$output_str.= "<tr class=\"".$class."\" onmouseover=\"myColor=this.style.backgroundColor;this.style.backgroundColor='#FFFFFF'\" onmouseout=\"this.style.backgroundColor=myColor\">";
						
				$output_str.= "<td ".(isset($_SESSION['order_orders']) && ($_SESSION['order_orders']=="name" || $_SESSION['order_orders']=="name_adresse")?"class=\"alarm\"":"").">".($row['this_name'].", ".$row['this_vorname'])." (".$row['this_email'].")";
				//$output_str.= "<br /><span style=\"font-size: 10px;\">".$row['this_plz']." ".$row['this_ort'].", ".$row['this_strasse']."</span>";
				$output_str.= "</td>";
				$output_str.= "<td ".(isset($_SESSION['order_orders']) && $_SESSION['order_orders']=="email"?"class=\"alarm\"":"").">".($row['this_email'])."</td>";
				$output_str.= "<td ".(isset($_SESSION['order_orders']) && ($_SESSION['order_orders']=="adresse" || $_SESSION['order_orders']=="name_adresse")?"class=\"alarm\"":"").">".$row['this_plz']." ".$row['this_ort'].", ".$row['this_strasse']."</td>";
				$output_str.= "<td>";
				while($row_evt = mysqli_fetch_assoc($result_evt)){
					$rechnungsnummer = $row_evt['rechnungsnummer_prefix']." ".$row_evt['rechnungsnummer_jahr']."/".fillNumber($row_evt['rechnungsnummer']);
					$output_str.= "<strong style=\"display: block; float: left; width: 80px; \">".$rechnungsnummer."</strong>: ".$row_evt['name'].": ".$row_evt['anzahl']." Karte".($row_evt['anzahl']>1?"n":"")."<br />";
					if(isset($_SESSION['search_evt']) && ($row_evt['evt_id'] == $_SESSION['search_evt'] || $row_evt['entry_parent_id'] == $_SESSION['search_evt'])){
						array_push($evt_array,$row['this_id']);
					}
				}
				$output_str.= "</td>";
				
				$output_str.= "<td nowrap=\"nowrap\">";
				$output_str.= "<img src=\"../../admin/shared/images/forbidden.gif\" alt=\"\" title=\"Überprüfung auf Mehrfachbestellung deaktivieren\" onclick=\"MODULE_hideEntry(".$row['this_id'].",this.parentNode.parentNode);\" />";
				$output_str.= "<a href=\"edit.php?entry_id=".$row['this_id']."\"><img src=\"../../admin/shared/images/editable.gif\" alt=\"\" title=\"Eintrag bearbeiten\" /></a>";  
				$output_str.= "<img src=\"../../admin/shared/images/delete.gif\" alt=\"\" title=\"Eintrag löschen\" onclick=\"MODULE_deleteEntry(".$row['this_id'].",this.parentNode.parentNode);\" />";
				$output_str.= "</td>";
				$output_str.= "</tr>";			
				
				if(isset($_SESSION['search_evt']) && !empty($_SESSION['search_evt'])){
					if(in_array($row['this_id'],$evt_array)){
						echo $output_str;		
					}
				}
				else{
					echo $output_str;
				}
			}
			
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
<h2>MODUL: Bestellungen: Mehrfachbestellungen</h2>
<table cellpadding="0" cellspacing="0" id="listTable" class="table">
	<thead>
    <tr class="filterRow" >
    	<th colspan="5" style="background-color: #FFFFFF;">
        	<form action="duplicates.php" method="get">
                <select name="filter_orders" style="width: auto">
                    <option value="">offene Bestellungen anzeigen</option>
                    <option value="teilweise bezahlt" <?php echo (isset($_SESSION['filter_orders']) && $_SESSION['filter_orders'] == "teilweise bezahlt"?"selected=\"selected\"":"")?>>teilweise bezahlte Bestellungen anzeigen</option>
                    <option value="bezahlt" <?php echo (isset($_SESSION['filter_orders']) && $_SESSION['filter_orders'] == "bezahlt"?"selected=\"selected\"":"")?>>bezahlte Bestellungen anzeigen</option>
                    <option value="abgeschlossen" <?php echo (isset($_SESSION['filter_orders']) && $_SESSION['filter_orders'] == "abgeschlossen"?"selected=\"selected\"":"")?>>abgeschlossene Bestellungen anzeigen</option>
                    <option value="all" <?php echo (isset($_SESSION['filter_orders']) && $_SESSION['filter_orders'] == "all"?"selected=\"selected\"":"")?>>alle Bestellungen anzeigen</option>
                 </select>
                <select name="order_orders" style="width: auto">
                    <option value="name" <?php echo isset($_SESSION['order_orders']) && $_SESSION['order_orders']=="name"?"selected=\"selected\"":"";?>>nach Namen sortieren</option>
                    <option value="name_adresse" <?php echo isset($_SESSION['order_orders']) && $_SESSION['order_orders']=="name_adresse"?"selected=\"selected\"":"";?>>nach Namen und Adresse sortieren</option>
                     <option value="email" <?php echo isset($_SESSION['order_orders']) && $_SESSION['order_orders']=="email"?"selected=\"selected\"":"";?>>nach eMail sortieren</option>
                     <option value="adresse" <?php echo isset($_SESSION['order_orders']) && $_SESSION['order_orders']=="adresse"?"selected=\"selected\"":"";?>>nach Adresse sortieren</option>
                 </select>
             <button style="width: auto" type="submit">=&gt;</button> <button type="button" id="showDuplicates" style="width: auto; background-color: #FF0000" onclick="MODULE_showDuplicates('index.php')">Alle anzeigen</button>
            </form>
            
           
        </th>
   	</tr>
    <tr>
    	<th style="width: 250px">Name</th>
        <th style="width: 250px">eMail</th>
         <th style="width: 250px">Adresse</th>
         <th style="width: auto">Veranstaltung(en)</th>
        <th style="width: 100px">Aktionen</th>
    </tr>
    </thead>
    <tbody>
		<tr>
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
