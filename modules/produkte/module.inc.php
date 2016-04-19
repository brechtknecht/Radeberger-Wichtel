<?php
function showModule(){
	
	//get cat by page perm if not set
	if(!isset($_GET['c'])){
		$query = "SELECT perm_arg_value FROM _cms_perm_";
		$query.= " WHERE perm_cat='page'";
		$query.= " AND user_id='".mysqli_real_escape_string($_SESSION['conn'], $_GET['entry_id'])."'";
		$query.= " AND perm_value='produkte'";
		$query.= " AND perm_arg_name='category'";
		$query.= " LIMIT 1";
		if($result = mysqli_query($_SESSION['conn'], $query)){
			if(mysqli_num_rows($result) == 1){
				$row_cat = mysqli_fetch_assoc($result);	
				$_GET['c'] = $row_cat['perm_arg_value'];
			}	
		}
	}
	
	
	//delete item from cart
	if(isset($_GET['del'])){
		modDeleteItemFromCart(intval($_GET['del']));
	}
	//add item to cart
	if(isset($_GET['add'])){
		modAddToCart(intval($_GET['add']));
	}
	
	//show cart
	if(isset($_GET['cart'])){
		return modShowCart("edit");	
	}	
	
	//show form
	if(isset($_GET['data'])){
		return modShowContactForm("edit");	
	}	
	
	//show summary
	if(isset($_GET['sum'])){
		//save user data
		if(modCheckForm()){
			return modShowSummary("edit");		
		}
		return modShowContactForm("edit");	 
		
	}	
	
	//send order
	if(isset($_GET['send'], $_SESSION['modCart'], $_SESSION['page_user'])){
		return modSendOrder();
		
	}	
		
	//list products by category
	if(isset($_GET['c']) || (isset($_GET['s']) && !empty($_GET['s']))){
		return modListEntries(intval($_GET['c']));	
	}
	//list categories
	return modListCategories("full");
	
}

function modShowSearchField(){
	$output_str = "";
	$output_str.= "<form id=\"shopSearch\" action=\"suche,21.php\" method=\"get\">";
	$output_str.= "<input type=\"text\" name=\"s\" value=\"".(isset($_GET['s'])?htmlspecialchars($_GET['s']):"<::produktsuche::>")."\" onfocus=\"this.value = '';\">";
	$output_str.= "</form>";
	return $output_str;	
}

function modListCategories($mode){
	$output_str = "";
	$query = "SELECT *";
	$query.= ", (SELECT file_save_name FROM _cms_hp_files_ t2 WHERE t2.entry_parent_id=t1.entry_id AND t2.file_cat2='produktkategorien' LIMIT 1) AS category_image";
	$query.= " FROM _cms_modules_produkte_kategorien_ t1 ORDER BY entry_sequence ASC";
	if($result = mysqli_query($_SESSION['conn'], $query)){
		$output_str.= modShowSearchField();
		$output_str.= "<ul id=\"modProdCategoriesList\" class=\"modProdList ".$mode."\">";
		
		while($row = mysqli_fetch_assoc($result)){
			$row['entry_name'] = json_decode($row['entry_name'], true);
			$row['entry_desc'] = json_decode($row['entry_desc'], true);
			
			//get 1st product image
			if(empty($row['category_image'])){
				$query = "SELECT file_save_name";
				$query.= " FROM _cms_hp_files_";
				$query.= " WHERE file_cat1='module'";
				$query.= " AND file_cat2='produkte'";
				$query.= " AND entry_parent_id=(SELECT entry_id FROM _cms_modules_produkte_ t2 WHERE t2.entry_kategorie LIKE '%|".mysqli_real_escape_string($_SESSION['conn'], $row['entry_id'])."|%' LIMIT 1)";
				$query.= " LIMIT 1";	
				
				if($result_img = mysqli_query($_SESSION['conn'], $query)){
					if($row_img = mysqli_fetch_assoc($result_img)){
						if(!empty($row_img['file_save_name'])){
							$row['category_image'] = $row_img['file_save_name'];	
						}	
					}	
				}
			}
			
			$url = (make_url_name($row['entry_url']))."?c=".$row['entry_id'];
			
			$output_str.= "<li title=\"".strtoupper(htmlspecialchars($row['entry_name'][$_SESSION['page_language']]))."\">";
			$output_str.= "<a href=\"".$url."\" title=\"".htmlspecialchars(strtoupper($row['entry_name'][$_SESSION['page_language']]))."\"";
			//$output_str.= " style=\"background-image: url(files/".htmlspecialchars($row['category_image']).")\"";
			$output_str.= ">";
			$output_str.= "<img src=\"files/".htmlspecialchars($row['category_image'])."\" alt=\"".htmlspecialchars(strtoupper($row['entry_name'][$_SESSION['page_language']]))."\">";
			$output_str.= "<span></span>";
			$output_str.= "</a>";
			
			$output_str.= "<div>";
			$output_str.= "<div class=\"modArticleText\">";
			$output_str.= "<h4>".htmlspecialchars($row['entry_name'][$_SESSION['page_language']])."</h4>";
			//description
			$output_str.= "<p>".nl2br(htmlspecialchars($row['entry_desc'][$_SESSION['page_language']]))."</p>";
			//link
			$to_shop = $_SESSION['page_language'] == "en"?"GO TO SHOP":"ZUM SHOP";
			$output_str.= "<p><a href=\"".$url."\">".$to_shop."</a>";
			$output_str.= "</div>";
			$output_str.= "</div>";
			$output_str.= "</li>";
			
			
		}	
		$output_str.= "</ul>";
	}
	return $output_str;
}

function modListEntries($category){
	
	$total_count = 0;
	$entries_per_page = 16;
	$_SESSION['c'] = $category;
	if(!isset($_SESSION['s'])){
		$_SESSION['s'] = 0;	
	}
	
	$output_str = "";
	//categories navigation
	//$output_str.= modListCategories("small");
	
	//cat 
	$query = "SELECT * FROM _cms_modules_produkte_kategorien_ WHERE entry_id='".mysqli_real_escape_string($_SESSION['conn'], $category)."' LIMIT 1";
	if($result = mysqli_query($_SESSION['conn'], $query)){
		$row_cat = mysqli_fetch_assoc($result);
		
	}
	
	
	//list products
	$query = "SELECT entry_id AS entry_id";
	$query.= ", entry_name AS entry_name";
	$query.= ", entry_desc AS entry_desc";
	$query.= ", entry_preis AS entry_preis";
	$query.= ", entry_video AS entry_video";
	
	$query.= ", (SELECT file_save_name FROM _cms_hp_files_ t2 WHERE t2.entry_parent_id=t1.entry_id AND file_cat1='module' AND file_cat2='produkte' LIMIT 1) AS entry_img";
	$query.= ", (SELECT COUNT(entry_id) FROM _cms_modules_produkte_ WHERE entry_kategorie LIKE '%|".$_SESSION['c']."|%') AS total_count";
	$query.= " FROM _cms_modules_produkte_ t1";
	$query.= " WHERE";
	//by search term
	if(isset($_GET['s']) && !empty($_GET['s'])){
		$query.= " entry_name LIKE '%".mysqli_real_escape_string($_SESSION['conn'], $_GET['s'])."%'";
	}
	//by category
	else{
		$query.= " entry_kategorie LIKE '%|".$_SESSION['c']."|%'";
	}
	$query.= " ORDER BY entry_sequence ASC, entry_name ASC";
	//$query.= " LIMIT ".$_SESSION['s'].",".$entries_per_page;
	//echo $query;
	if($result = mysqli_query($_SESSION['conn'], $query)){
		if(isset($_GET['s']) && mysqli_num_rows($result) == 0){
			$output_str.= "<p>Leider wurden zu Ihrem Suchbegriff keine Artikel gefunden.</p>";	
		}
	}
	
	if(!isset($_GET['s'])){
		//$output_str.= "<h4 class=\"w700Text\">".htmlspecialchars(strtoupper($row_cat['entry_name']))."</h4>";
		//$output_str.= "<p class=\"w700Text\"><i>".htmlspecialchars($row_cat['entry_desc'])."</i></p>";
		$output_str.= "<hr>";
	}
	$output_str.= modShowSearchField();
	$output_str.= "<ul id=\"modProdList\" class=\"modProdList\">";
	if(mysqli_num_rows($result) > 0){
		while($row = mysqli_fetch_assoc($result)){
			$url = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],"?"));
			$url.= "?prodid=".$row['entry_id'];
			
			$output_str.= "<li>";
			if(empty($row['entry_video'])){
				$output_str.= "<a href=\"files/".htmlspecialchars($row['entry_img'])."\" title=\"".htmlspecialchars($row['entry_name'])."\" target=\"_blank\" class=\"colorbox\">";
			}
			else{
				$output_str.= "<a href=\"#\" onclick=\"$.colorbox({inline: true, href: $('#video_".$row['entry_id']."')}); document.getElementById('video_".$row['entry_id']."').currentTime = 0; document.getElementById('video_".$row['entry_id']."').play(); return false;\">";	
			}
			$output_str.= "<img src=\"files/".htmlspecialchars($row['entry_img'])."\" alt=\"".htmlspecialchars(strtoupper($row['entry_name']))."\">";
			$output_str.= "<span></span>";
			//add video icon
			if(!empty($row['entry_video'])){
				$output_str.= "<img class=\"videoIcon\" src=\"shared/images/icons/monitor.png\" title=\"Produktvideo abspielen\" alt=\"Produktvideo vorhanden\">";
			}
			$output_str.= "</a>";
			
			$output_str.= "<div>";
			$output_str.= "<div class=\"modArticleText\">";
			$output_str.= "<h4>".htmlspecialchars($row['entry_name'])."</h4>";
			$output_str.= "<p>".nl2br(htmlspecialchars($row['entry_desc']))."</p>";
			$output_str.= "</div>";
			$output_str.= "<h4 class=\"modPrice\">".number_format($row['entry_preis'], 2, "," ,".")." €</h4>";
			//price & cart
			//$output_str.= "<a href=\"shop,".$_GET['entry_id'].".php?c=".$_SESSION['c']."&amp;add=".intval($row['entry_id'])."\"><img src=\"shared/images/icons/shoppingcart.png\" alt=\"".htmlspecialchars($row['entry_name'])." in den Warenkorb legen\" title=\"".htmlspecialchars($row['entry_name'])." in den Warenkorb legen\"></a>";
			$output_str.= "<a href=\"shop,".$_GET['entry_id'].".php?c=".$_SESSION['c']."&amp;add=".intval($row['entry_id'])."\">In den Warenkorb</a>";
			$output_str.= "</div>";
			
			if(!empty($row['entry_video'])){
				$output_str.= "<video id=\"video_".$row['entry_id']."\" controls loop style=\"background-image: url(video/".$row['entry_video'].".jpg);\" width=\"960\" height=\"540\"  onclick=\"if(/Android/.test(navigator.userAgent))this.play();\">";
				$output_str.= "<source src=\"video/".$row['entry_video'].".mp4\" type=\"video/mp4\" />";
				$output_str.= "<source src=\"video/".$row['entry_video'].".webm\" type=\"video/webm\" />";
				$output_str.= "<source src=\"video/".$row['entry_video'].".ogv\" type=\"video/ogg\" />";
				
				$output_str.= "</video>";
			}
			
			$output_str.= "</li>";
			
			/*
			$output_str.= "<li>";
			
			
			$output_str.= "<figure>";
			$output_str.= "<a href=\"files/".htmlspecialchars($row['entry_img'])."\" title=\"".htmlspecialchars($row['entry_name'])."\" target=\"_blank\" class=\"colorbox\">";
			$output_str.= "<img src=\"files/".htmlspecialchars($row['entry_img'])."\" alt=\"".htmlspecialchars($row['entry_name'])."\">";
			$output_str.= "</a>";
			$output_str.= "</figure>";
			
			$output_str.= "<div>";
			$output_str.= "<h4>".htmlspecialchars($row['entry_name'])."</h4>";
			//description
			$output_str.= "<p>".nl2br(htmlspecialchars($row['entry_desc']))."</p>";
			$output_str.= "<h4>".number_format($row['entry_preis'], 2, "," ,".")." €</h4>";
			//price & cart
			$output_str.= "<a href=\"shop,".$_GET['entry_id'].".php?c=".$_SESSION['c']."&amp;add=".intval($row['entry_id'])."\"><img src=\"shared/images/icons/shoppingcart.png\" alt=\"".htmlspecialchars($row['entry_name'])." in den Warenkorb legen\" title=\"".htmlspecialchars($row['entry_name'])." in den Warenkorb legen\"></a>";
			//$output_str.= "<a href=\"shop,".$_GET['entry_id'].".php?c=".$_SESSION['c']."&amp;add=".intval($row['entry_id'])."\">In den Warenkorb</a>";
			
			$output_str.= "</div>";
						
			$output_str.= "</li>";
			*/
			if($total_count == 0){
				$total_count = $row['total_count'];
			}
		}
		
		$output_str.= "</ul>";
	}
	
	return $output_str;
}

function modEditCart($mode){
	$output_str = "<div id=\"modContent\">";
	$output_str.=modShowCart($mode);
	$output_str.= "</div>";
	return $output_str;
}

function modShowCart($mode = "show"){
	
	//get module settings
	$module_settings = getXMLNodeContent("modules/produkte/module_settings.xml");
	$region_shipping = json_decode($module_settings['shipping'], true);
		
	$_SESSION['modOrderStep'] = "checkCart";
	$output_str = "<div id=\"modShoppingCart\">";
	
	
	
	if(isset($_SESSION['modCart']) && sizeof($_SESSION['modCart']) > 0){
		//var_dump($_SESSION['modCart']);
		
		$totalprice = 0;
		$output_str.= "<form action=\"daten,15.php?data\" method=\"post\">";
						
		$output_str.= "<table cellpadding=\"0\" cellspacing=\"0\">";
		$output_str.= "<tr>";
		$output_str.= "<th style=\"width: 60px\"><::menge::></th>";
		$output_str.= "<th><::produkt::></th>";
		$output_str.= "<th style=\"text-align: right\"><::preis::> €</th>";
		$output_str.= "</tr>";
		foreach($_SESSION['modCart'] as $key => $val){
			
			$totalprice+= floatval($val['entry_preis']*$val['entry_count']);
			$output_str.= "<tr>";
			$output_str.= "<td>";
			$output_str.= "<input type=\"hidden\" name=\"item_id[".$key."]\" value=\"".$key."\" />";
			if($mode == "edit"){
				$output_str.= "<input class=\"modEntryCount\" type=\"text\" name=\"item_count[".$key."]\" value=\"".htmlspecialchars($val['entry_count'])."\" />";
			
			}
			else{
				$output_str.= htmlspecialchars($val['entry_count']);
			}
			
			$output_str.= "</td>";
			$output_str.= "<td>";
			//$output_str.= "<img src=\"files/".$val['entry_img']."\" alt=\"".$val['entry_name']."\" />";
			$output_str.= htmlspecialchars($val['entry_name']);
			if($mode == "edit"){
				$output_str.= "<a title=\"Artikel aus dem Warenkorb entfernen\" class=\"modDelItem\" href=\"shop,".$_GET['entry_id'].".php?cart&amp;del=".$key."\"><::aus Warenkorb entfernen::></a>";
			}
			$output_str.= "</td>";
			$output_str.= "<td style=\"text-align: right; white-space: nowrap\">";
			if($val['entry_preis_select'] == 0 || $mode != "edit"){
				$output_str.= number_format($val['entry_preis']*$val['entry_count'],2,",",".");
			}
			else{
				//gutschein ()
				$output_str.= "<select name=\"price_select[".$key."]\">";	
				for($p = 5; $p < 155; $p+= 5){
					$p = number_format($p,2,".",",");
					$output_str.= "<option value=\"".$p."\" ".(number_format($val['entry_preis'],2,".",",") == $p?"selected=\"selected\"":"").">".number_format($p,2,",",".")."</option>";		
				}
				$output_str.= "</select>";
			}
			$output_str.= "</td>";
			$output_str.= "</tr>";
		}
		
		//shipping
		if(isset($_SESSION['page_user']['versand_region'])){
			$totalprice+= $region_shipping[0][$_SESSION['page_user']['versand_region']];
			$output_str.= "<tr>";
			$output_str.= "<td colspan=\"2\">Versand und Verpackung (<::".$_SESSION['page_user']['versand_region']."::>)</td>";
			$output_str.= "<td style=\"text-align: right; white-space: nowrap; \">".number_format($region_shipping[0][$_SESSION['page_user']['versand_region']],2,",",".")."</td>";
			$output_str.= "</tr>";
		}	
		
		
		$mwst = $totalprice*19/119;
		$output_str.= "<tr>";
		$output_str.= "<td colspan=\"2\"><::enthaltene mwst::> 19%</td>";
		$output_str.= "<td style=\"text-align: right; white-space: nowrap\">".number_format($mwst,2,",",".")."</td>";
		$output_str.= "</tr>";
		$output_str.= "<tr class=\"modSummary\">";
		$output_str.= "<td colspan=\"2\"><::summe::></td>";
		$output_str.= "<td style=\"text-align: right; white-space: nowrap; \">".number_format($totalprice,2,",",".")."</td>";
		$output_str.= "</tr>";
		
		
		
		$output_str.= "</table>";
		//checkout
		if($mode == "edit"){
			$output_str.= "<p style=\"text-align: center\"><button type=\"submit\"><::weiter::></button></p>";
		}
		$output_str.= "</form>";
	}
	else{
		$output_str.= "<p>Ihr Warenkorb enthält keine Artikel.</p>";
	}
	
	$output_str.= "</div>";
	return $output_str;
}

function modShowContactForm(){
	//update cart
	if(isset($_POST['item_id'])){
		foreach($_POST['item_id'] as $key=>$val){
			modAddToCart($key,intval($_POST['item_count'][$key]), "update");
		}
	}
	
	$output_str = "";
	//show form
	$output_str.= "<div id=\"modContactForm\">";
	$output_str.= "<h3>Kontaktdaten und Bezahlung</h3>";
	$output_str.= outputFile("modules/produkte/forms/userdata.form.php");
	$output_str.= "</div>";
	//show cart
	$output_str.= "<div id=\"modPreviewCart\" style=\"width: 380px; \">";
	//$output_str.= "<h3>Bestellübersicht</h3>";
	//$output_str.= modPreviewCart(false);
	$output_str.= "</div>";
	$_SESSION['modOrderStep'] = "sendOrder";
	return $output_str;
	
}

function modCheckForm(){
	if(!isset($_SESSION['page_user'])){
		$_SESSION['page_user'] = array();
	}
	if(isset($_SESSION['page_user']['error_array'])){
		unset($_SESSION['page_user']['error_array']);
	}
	$_SESSION['page_user']['error_array']=array();
	
	foreach($_POST['page_user'] as $key => $val){
		$val = preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "",$val);
		
		$_SESSION['page_user'][$key] = $val;
		if(strstr($key,"_req") && (empty($val) || $val == "Pflichtfeld!")){
			array_push($_SESSION['page_user']['error_array'],$key);
			$_SESSION['page_user'][$key] = "";
		}
		
	}
	
	if($_POST['page_user']['bezahlung'] == "bankeinzug"){
		if(empty($_POST['page_user']['kontoinhaber']) || $_POST['page_user']['kontoinhaber'] == "Pflichtfeld"){
			array_push($_SESSION['page_user']['error_array'],"kontoinhaber");	
			$_SESSION['page_user']['kontoinhaber'] = "";
		}
		if(empty($_POST['page_user']['iban']) || $_POST['page_user']['iban'] == "Pflichtfeld"){
			array_push($_SESSION['page_user']['error_array'],"iban");	
			$_SESSION['page_user']['iban'] = "";
		}
		
		if(!isset($_POST['page_user']['einzug_bestaetigt']) || $_POST['page_user']['einzug_bestaetigt'] != "ja"){
			array_push($_SESSION['page_user']['error_array'],"einzug_bestaetigt");	
			
		}
	}
	else{
		//array_push($nosend_array, "kontoinhaber", "iban", "bic");
	}
	
	if(sizeof($_SESSION['page_user']['error_array']) == 0){
		return true;
	}
	
	return false;
}

function modShowSummary(){
	
	
	$output_str = "<div id=\"modContent\">";
	//shopping cart
	$output_str.= modShowCart();
	//user data
	
	$output_str.= "<form name='order' method='POST' action='bestellung_abgeschlossen,18.php?send'>";
	$output_str.= "<table cellpadding=\"0\" cellspacing=\"0\" class=\"modDataTable\">";
	$output_str.= "<tr><th colspan=\"2\"><::ihre daten::></th></tr>";
	$output_str.= "<tr>";
	$output_str.= "<td><::ihr name::></td>";
	$output_str.= "<td>".$_SESSION['page_user']['name_req']."</td>";
	$output_str.= "</tr>";
	if(!empty($_SESSION['page_user']['firma'])){
		$output_str.= "<tr>";
		$output_str.= "<td>Firma</td>";
		$output_str.= "<td>".$_SESSION['page_user']['firma']."</td>";
		$output_str.= "</tr>";
	}
	$output_str.= "<tr>";
	$output_str.= "<td><::straße::></td>";
	$output_str.= "<td>".$_SESSION['page_user']['strasse_req']."</td>";
	$output_str.= "</tr>";
	$output_str.= "<tr>";
	$output_str.= "<td><::plz::> <::ort::></td>";
	$output_str.= "<td>".$_SESSION['page_user']['plz_req']." ".$_SESSION['page_user']['ort_req']."</td>";
	$output_str.= "</tr>";
	if(!empty($_SESSION['page_user']['telefon'])){
		$output_str.= "<tr>";
		$output_str.= "<td>Telefon</td>";
		$output_str.= "<td>".$_SESSION['page_user']['telefon']."</td>";
		$output_str.= "</tr>";
	}
	$output_str.= "<tr>";
	$output_str.= "<td><::email::></td>";
	$output_str.= "<td>".$_SESSION['page_user']['email_req']."</td>";
	$output_str.= "</tr>";
	if(!empty($_SESSION['page_user']['anmerkungen'])){
		$output_str.= "<tr>";
		$output_str.= "<td><::anmerkungen::></td>";
		$output_str.= "<td>".$_SESSION['page_user']['anmerkungen']."</td>";
		$output_str.= "</tr>";
	}
	
	$output_str.= "<tr>";
	$output_str.= "<td><::versandoptionen::></td>";
	$output_str.= "<td><::".strtolower($_SESSION['page_user']['versand'])."::></td>";
	$output_str.= "</tr>";
	
	
	$output_str.= "<tr>";
	$output_str.= "<td><::bezahloptionen::></td>";
	$output_str.= "<td>";
	$output_str.= "<::".strtolower($_SESSION['page_user']['bezahlung'])."::>";
	if($_SESSION['page_user']['bezahlung'] == "bankeinzug"){
		$output_str.= "<br>(Einzug von Konto: ".htmlspecialchars($_SESSION['page_user']['iban']);
		if(isset($_SESSION['page_user']['bic']) && !empty($_SESSION['page_user']['bic'])){
			$output_str.= "<br>BIC: ".htmlspecialchars($_SESSION['page_user']['bic']);
		}
		$output_str.= ")";
	}
	if($_SESSION['page_user']['bezahlung'] == "kreditkarte"){
	$output_str.= "<br/>";
	$output_str.= "<table><tr><td>Karteninhaber</td><td><input type='text' name='ccname' size='40'/></td></tr>";
	$output_str.= "<tr><td>Kartennummer</td><td><input type='text' name='ccnumber' size='24'/></td></tr>";
	$output_str.= "<tr><td>CCV (Sicherheitsnummer)</td><td><input type='text' size='5' name='ccccv'/></td></tr>";
	$output_str.= "<tr><td>g&uuml;ltig bis</td><td>";
	$month_options = '';
	$year_options = '';
	for( $i = 1; $i <= 12; $i++ ) {
		$month_num = str_pad( $i, 2, 0, STR_PAD_LEFT );
		$year_options .= '<option value="' . ($i + date("Y") - 1)  . '">' . ($i + date("Y") - 1) . '</option>';
		$month_options .= '<option value="' . $month_num  . '">' . $month_num . '</option>';
	}
	$output_str.= '<select name="ccvalidtomm">' . $month_options . '</select>';
	$output_str.= '<select name="ccvalidtoyy">' . $year_options . '</select>';
	$output_str.= "</td></tr></table>";
#		$output_str.= ")";
	}
	$output_str.= "</td>";
	$output_str.= "</tr>";
	
	$output_str.= "<tr><th colspan=\"2\"><::widerrufsbelehrung::></th></tr>";
	$output_str.= "<tr>";
	$output_str.= "<td colspan=\"2\">";
	$output_str.= "<label class=\"checkboxLabel\"><input type=\"checkbox\" id=\"widerruf\" name=\"widerruf\"> <::widerrufsbelehrung_text::></label>";
	$output_str.= "</td>";
	$output_str.= "</tr>";
	
	$output_str.= "<tr>";
	$output_str.= "<td colspan=\"2\" class=\"modActionRow\">";
	$output_str.= "<button type=\"button\" onclick=\"modSubmitOrder()\"><::kostenpflichtig bestellen::></button>";
	$output_str.= "<a href=\"warenkorb,12.php?cart\" class=\"buttonLink\"><::bestellung bearbeiten::></a>";
	$output_str.= "</td>";
	$output_str.= "</tr>";
	
	$output_str.= "</table>";
	$output_str.= "</form>";
	
	
	//send order
	
	
	
	$output_str.= "</div>";
	
	return $output_str;
}

function modCCPayment(){
	$output_str = "";
	$module_settings = getXMLNodeContent("modules/produkte/module_settings.xml");
	$region_shipping = json_decode($module_settings['shipping'], true);
	$_POST['module_settings'] = $module_settings;
	
	if(isset($_SESSION['modCart'], $_SESSION['page_user'])){
		$output_str.= "<div id=\"modSummary\">";
		$output_str.= "<form name='ccdata'>";
		$output_str.= "<table><tr><td>Karteninhaber</td><td><input type='text' size='40'>Karteninhaber</input></td></tr>";
		$output_str.= "<tr><td>Kartennummer</td><td><input type='text' size='24'>Kreditkartennummer</input></td></tr>";
		$output_str.= "<tr><td>g&uuml;ltig bis</td><td><input type='text' size='5'>MM/JJ</input></td></tr>";
		$output_str.= "<tr><td>Sicherheitsziffern</td><td><input type='text' size='5'>000</input></td></tr>";
	//	$output_str.= "";
	//	$output_str.= "";
		$output_str.= "</div>";
	}
}

function modSendOrder(){
	$output_str = "";
	$ccAccountNo = "";
	$ccTransAuthCode = "";
	$ccTransId = "";
	//get module settings
	$module_settings = getXMLNodeContent("modules/produkte/module_settings.xml");
	$region_shipping = json_decode($module_settings['shipping'], true);
	$_POST['module_settings'] = $module_settings;

	if(isset($_SESSION['page_user'], $_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung']=="kreditkarte"){
		include_once("pymnts/PaymentTransactions/charge-credit-card.php");
		$totalprice = 0;
		foreach($_SESSION['modCart'] as $val){
			$totalprice+= floatval($val['entry_preis']*$val['entry_count']);
		}
		$isCCCharged = modChargeCC($totalprice);
		if(isset($isCCCharged['text'], $isCCCharged['ResponseCode']) && $isCCCharged['ResponseCode']==3){
			$output_str.=$isCCharged['text'];
			return $output_str;
		}else{
			$transactionResponse = $isCCCharged['detail'];
			$tr = $transactionResponse->getTransactionResponse();
		}

		if ($transactionResponse->getMessages()->getResultCode() == "Ok"){
			$arrMessages = $tr->getMessages();
			$output_str .= "Kreditkartenzahlung erfolgreich.<br />\n";
			$output_str.= "Antwort:" . $arrMessages[0]->getCode() . "  " .$arrMessages[0]->getDescription() . "<br />\n";
			$output_str .= "Transaction Code:" . $tr->getAuthCode() . "<br />\n";
			$output_str.= "TransaktionsID:" . $tr->getTransId() . "<br />\n";
			$ccAccountNo = $tr->getAccountNumber();
			$ccTransId = $tr->getTransId();
			$ccTransAuthCode = $tr->getAuthCode();
		} else {
			$output_str.="Fehler :  Invalid response<br />\n";
			$output_str.="Antwort : " . $transactionResponse->getMessages()->getMessage()[0]->getCode() . "  " .$transactionResponse->getMessages()->getMessage()[0]->getText() . "<br />\n";
			if(is_array($tr->getErrors())){
				$arrErrors = $tr->getErrors();
				$output_str.="Detail : Fehlercode [" . $arrErrors[0]->getErrorCode() . "]  " .$arrErrors[0]->getErrorText() . "<br />\n";
			}
//var_dump($tr);
     			return $output_str; 
		}	
	}
	
	//write in table
	//order array exists?

	if(isset($_SESSION['modCart'], $_SESSION['page_user'])){
		//write in orders table
		$query = "INSERT INTO _cms_modules_orders_ SET";
		$query.= " name='".mysqli_real_escape_string($_SESSION['conn'], $_SESSION['page_user']['name_req'])."'";
		//$query.= ", vorname='".mysqli_real_escape_string($_SESSION['conn'], $_SESSION['page_user']['vorname_req'])."'";
		$query.= ", strasse='".mysqli_real_escape_string($_SESSION['conn'], $_SESSION['page_user']['strasse_req'])."'";
		$query.= ", plz='".mysqli_real_escape_string($_SESSION['conn'], $_SESSION['page_user']['plz_req'])."'";
		$query.= ", ort='".mysqli_real_escape_string($_SESSION['conn'], $_SESSION['page_user']['ort_req'])."'";
		$query.= ", email='".mysqli_real_escape_string($_SESSION['conn'], $_SESSION['page_user']['email_req'])."'";
		$query.= ", shipping=".mysqli_real_escape_string($_SESSION['conn'], $region_shipping[0][$_SESSION['page_user']['versand_region']]);
		$query.= ", bezahlung='".mysqli_real_escape_string($_SESSION['conn'], $_SESSION['page_user']['bezahlung'])."'";
		if(isset($_SESSION['page_user']['rabatt'])){
			$query.= ", rabatt='".intval($_SESSION['page_user']['rabatt'])."'";
		}
		if(isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung'] == "bankeinzug"){
			$query.= ", iban='".mysqli_real_escape_string($_SESSION['conn'], $_SESSION['page_user']['iban'])."'";
			$query.= ", kontoinhaber='".mysqli_real_escape_string($_SESSION['conn'], $_SESSION['page_user']['kontoinhaber'])."'";
			$query.= ", bic='".mysqli_real_escape_string($_SESSION['conn'], $_SESSION['page_user']['bic'])."'";
		}
		$query.= ", sid='".mysqli_real_escape_string($_SESSION['conn'], session_id())."'";
		$query.= ", ip='".mysqli_real_escape_string($_SESSION['conn'], $_SERVER['REMOTE_ADDR'])."'";
		$query.= ", last_change=NOW()";
		//echo $query;
		$result = mysqli_query($_SESSION['conn'], $query);
		$id = mysqli_insert_id($_SESSION['conn']);
						
		foreach($_SESSION['modCart'] as $key=>$val){
			$query = "INSERT INTO _cms_modules_orders_products_ SET";
			$query.= " pid=".$id ;
			$query.= ", product_id=".$key;
			$query.= ", anzahl=".mysqli_real_escape_string($_SESSION['conn'], $val['entry_count']);
			$query.= ", preis=".mysqli_real_escape_string($_SESSION['conn'], str_replace(",",".",$val['entry_preis']));
			$query.= ", last_change=NOW()";
			$result = mysqli_query($_SESSION['conn'], $query);
		}
		
		$order_id = $id.date("Ym");
		$result = mysqli_query($_SESSION['conn'], "UPDATE _cms_modules_orders_ SET order_id='".mysqli_real_escape_string($_SESSION['conn'], $order_id)."' WHERE entry_id=".$id);
		
		$msg = "";
		$msg.= $module_settings['mailtext'];
		
		$msg.= "\nKONTAKTDATEN\n";
		$msg.= "---------------------------------------\n";
		
		$msg.= "Name: ";
		$msg.= $_SESSION['page_user']['name_req'];
		if(!empty($_SESSION['page_user']['firma'])){
			$msg.= "\nFirma: ";
			$msg.= $_SESSION['page_user']['firma'];
		}
		$msg.= "\nStraße: ";
		$msg.= $_SESSION['page_user']['strasse_req'];
		$msg.= "\nPLZ Ort: ";
		$msg.= $_SESSION['page_user']['plz_req']." ".$_SESSION['page_user']['ort_req'];
		if(!empty($_SESSION['page_user']['telefon'])){
			$msg.= "\nTelefon: ";
			$msg.= $_SESSION['page_user']['telefon'];
		}
		$msg.= "\nEMail-Adresse: ";
		$msg.= $_SESSION['page_user']['email_req'];
		
		if(!empty($_SESSION['page_user']['rabatt'])){
			$msg.= "\nRabatt: ";
			$msg.= $_SESSION['page_user']['rabatt']."%";
		}
		if(!empty($_SESSION['page_user']['anmerkungen'])){
			$msg.= "\n\nAnmerkungen: ";
			$msg.= $_SESSION['page_user']['anmerkungen'];
		}
		
		$msg.= "\n\nBESTELLDATEN\n";
		$msg.= "---------------------------------------\n";
		
		$totalprice = 0;
		foreach($_SESSION['modCart'] as $val){
			$totalprice+= floatval($val['entry_preis']*$val['entry_count']);
				
			$msg.= $val['entry_count']." x ";
			$msg.= $val['entry_name'].": ";
			$msg.= number_format($val['entry_preis']*$val['entry_count'],2,",",".")." €";
			$msg.= "\n";
		}
		
		$msg.= "\n";
		$msg.= "Summe: ".number_format($totalprice,2,",",".")." €\n";
		
		//rabatt?
		if(isset($_SESSION['page_user']['rabatt']) && $_SESSION['page_user']['rabatt'] > 0){
			$rabatt = $totalprice*$_SESSION['page_user']['rabatt']/100*-1;
			$totalprice+= $rabatt;
			$msg.= "abzgl. ".$_SESSION['page_user']['rabatt']."% Rabatt: ";
			$msg.= number_format($rabatt,2,",",".")." €\n";
			$msg.= "Zwischensumme: ";
			$msg.= number_format($totalprice,2,",",".")." €\n";
			
		}
		
		//shipping
		$totalprice+= $region_shipping[0][$_SESSION['page_user']['versand_region']];
		if($region_shipping[0][$_SESSION['page_user']['versand_region']] > 0){
			$msg.= "Versand und Verpackung: ";
			$msg.= number_format($region_shipping[0][$_SESSION['page_user']['versand_region']],2,",",".")." €\n";
		}
		$msg.= "Endsumme: ";
		$msg.= number_format($totalprice,2,",",".")." €";
		
		if(isset($_SESSION['page_user']['versand'])){
			//shipping
			$msg.= "\n\nVERSAND\n";
			$msg.= "---------------------------------------\n";
			$msg.= ucfirst($_SESSION['page_user']['versand']);
		}
		
		//payment
		$msg.= "\n\nBEZAHLUNG\n";
		$msg.= "---------------------------------------\n";
		
		//order text
		if(isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung'] == "vorkasse"){
			$msg.= (str_replace("{vwdz}",$order_id,$module_settings['mailtext_vorkasse']))."\n\n";
		}
		if(isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung'] == "bankeinzug"){
			$msg.= ($module_settings['mailtext_lastschrift'])."\n";
		}
		if(isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung'] == "kreditkarte"){
			$msg.= ($module_settings['mailtext_kreditkarte'])."\n";
		}
		if(isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung'] == "paypal"){
			$msg.= ($module_settings['mailtext_paypal'])."\n\n";
		}
		
		$msg_seller = $msg;
		if($_SESSION['page_user']['bezahlung'] == "bankeinzug"){
			$msg_seller.= "Kontoinhaber: ".$_SESSION['page_user']['kontoinhaber']."\n";
			$msg_seller.= "IBAN: ".$_SESSION['page_user']['iban']."\n";
			if(isset($_SESSION['page_user']['bic']) && !empty($_SESSION['page_user']['bic'])){
				$msg_seller.= "BIC: ".$_SESSION['page_user']['bic']."\n";
			}
		}
		if($_SESSION['page_user']['bezahlung'] == "kreditkarte"){
			$msg_seller.= "Karteninhaber: ".$_POST['ccname']."\n";
			$msg_seller.= "Kartennummer: ".$ccAccountNo."\n";
			$msg_seller.= "Transaction Code:" . $ccTransAuthCode . "<br />\n";
                        $msg_seller.= "TransaktionsID:" . $ccTransId . "<br />\n";
		}
		
		
		$mailto = $module_settings['mailto'];
		$mailfrom = $module_settings['mailfrom'];
		
		//$mailto = "kontakt@pfunds.net";
				
		$output_str.= "<div id=\"modSummary\">";
		//$mailto = "kontakt@pfunds.net";

		if($mail = mail($mailto,"Onlineshop Radeberger Wichtel: Bestellung",($msg_seller."\n".$module_settings['mailtext_ende']),"FROM:".$_SESSION['page_user']['email_req']."\r\nContent-Type: text/plain; Charset=utf-8")){
			mail($_SESSION['page_user']['email_req'],$module_settings['subject'],($msg."\n".$module_settings['mailtext_ende']),"FROM:".$module_settings['mailfrom']."\r\nContent-Type: text/plain; Charset=utf-8");
			$output_str.= "<h3>Vielen Dank für Ihre Bestellung</h3>";	
			
			
			if(isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung'] == "vorkasse"){
				$output_str.= "<p>Sie haben \"Vorkasse\" als Bezahlvariante gewählt.<br />Eine EMail mit den Bestelldaten und Zahlungsinformationen wurde an die von Ihnen angegebene EMailadresse gesendet.</p>";
			}
			if(isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung'] == "lastschrift"){
				$output_str.= "<p>Sie haben \"Lastschrift\" als Bezahlvariante gewählt.<br />Der Kaufpreis wird von Ihrem angegebenen Konto eingezogen.</p>";
			}
			if(isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung'] == "kreditkarte"){
				$output_str.= "<p>Sie haben \"Kreditkarte\" als Bezahlvariante gewählt.</p>";
			}
			if(isset($_SESSION['page_user']['bezahlung']) && $_SESSION['page_user']['bezahlung'] == "paypal"){
				$output_str.= "<p>Sie haben \"PayPal\" als Bezahlvariante gewählt.<br />Eine EMail mit den Bestelldaten wurde an die von Ihnen angegebene EMailadresse gesendet.</p>";
				$output_str.= "<p>Bitte nehmen Sie die Zahlung über den untenstehenden PayPal-Button vor.</p>";
				$output_str.= outputFile("modules/produkte/paypalbutton.inc.php");
				//$mail_text_client.= ($module_settings['mailtext_paypal'])."\n\n";
				//$output_str.= "\n".($module_settings['mailtext_paypal']);
				//
			}
			unset($_SESSION['modCart']);
		}
		
		$output_str.= "</div>";
	}
	else{
		$output_str.= "<div id=\"modSummary\">";
		$output_str.= "<h3>Es ist ein Fehler aufgetreten. </h3>";
		$output_str.= "<p>Wenn Sie Ihre Bestellung bereits versendet haben, wurde Ihr Warenkorb gelöscht. </p>";
		$output_str.= "</div>";
	}
	
	return $output_str;
}

//delete item from cart

function modDeleteItemFromCart($item_id = 0){
	if($item_id != 0){
		if(isset($_SESSION['modCart'][$item_id])){
			unset($_SESSION['modCart'][$item_id]);
		}
	}
}

//add item to shopping cart
function modAddToCart($item_id = 0, $item_count = 1, $mode = "insert"){
	if($item_id != 0){
		//get product data
		$query = "SELECT entry_id AS entry_id";
		$query.= ", entry_name AS entry_name";
		$query.= ", entry_desc AS entry_desc";
		$query.= ", entry_nummer AS entry_nummer";
		$query.= ", entry_preis AS entry_preis";
		$query.= ", entry_preis_select AS entry_preis_select";
		$query.= ", (SELECT file_save_name FROM _cms_hp_files_ t2 WHERE t2.entry_parent_id=t1.entry_id AND file_cat1='module' AND file_cat2='produkte' LIMIT 1) AS entry_img";
		$query.= " FROM _cms_modules_produkte_ t1";
		$query.= " WHERE entry_id='".mysqli_real_escape_string($_SESSION['conn'], $item_id)."'";
		$query.= " LIMIT 1";
		$result = mysqli_query($_SESSION['conn'], $query);
		if(mysqli_num_rows($result) > 0){
			$row = mysqli_fetch_assoc($result);
			if(!isset($_SESSION['modCart'])){
				$_SESSION['modCart'] = array();
			}
			if($mode == "insert"){
				$_SESSION['modCart'][$item_id] = array();
				$_SESSION['modCart'][$item_id]['entry_name'] = $row['entry_name'];
				$_SESSION['modCart'][$item_id]['entry_nummer'] = $row['entry_nummer'];
				$_SESSION['modCart'][$item_id]['entry_preis'] = $row['entry_preis'];
				$_SESSION['modCart'][$item_id]['entry_preis_select'] = $row['entry_preis_select'];
				$_SESSION['modCart'][$item_id]['entry_img'] = $row['entry_img'];
			}
			$_SESSION['modCart'][$item_id]['entry_count'] = $item_count;
		}
		if($item_count == 0){
			unset($_SESSION['modCart'][$item_id]);
		}
	}
}

function make_url_name($word){
	$word = str_replace("ä","ae",$word);
	$word = str_replace("ö","oe",$word);
	$word = str_replace("ü","ue",$word);
	$word = str_replace("Ä","ae",$word);
	$word = str_replace("Ö","oe",$word);
	$word = str_replace("Ü","ue",$word);
	$word = str_replace(" ","-",$word);
	$word = str_replace("ß","ss",$word);
	$word = str_replace("/","_",$word);
	$word = strtolower($word);
	return $word;
}

?>
