<?php
function showModule(){
	if(isset($_GET['art_id'])){
		return modShowArticle(intval($_GET['art_id']));
	}
	else{
		return modListArticles();
	}
}

function modListArticles(){
	if(!isset($viewmode))
		$viewmode="";
	$output_str="<div id=\"modContent\" class=\"".htmlspecialchars($viewmode)."\">";
	
	//products
			
			$query = "SELECT *";
			$query.= ", (SELECT file_save_name FROM _cms_hp_files_ t2 WHERE t2.entry_parent_id=t1.entry_id AND t2.file_cat2='produktkategorien' LIMIT 1) AS category_image";
			$query.= " FROM _cms_modules_produkte_kategorien_ t1 ORDER BY entry_sequence ASC";
			if($result = mysqli_query($_SESSION['conn'], $query)){
				if(!isset($mode))
					$mode="";
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
					
					$url = "shop,20.php?c=".$row['entry_id'];
					
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
	
	//get entries
	$query="SELECT *";
	$query.= ", (SELECT file_save_name FROM _cms_hp_files_ t2 WHERE t2.entry_parent_id=t1.entry_id AND t2.file_cat1='module' AND t2.file_cat2='news' AND t2.file_cat3='fotos' ORDER BY file_sequence ASC LIMIT 1) AS entry_img";
	$query.= ", (SELECT file_save_name FROM _cms_hp_files_ t2 WHERE t2.entry_parent_id=t1.entry_id AND t2.file_cat1='module' AND t2.file_cat2='news' AND t2.file_cat3='foto_teaser' ORDER BY file_sequence ASC LIMIT 1) AS entry_teaser_img";
	$query.= " FROM _cms_modules_news_ t1";
	$query.= " WHERE";
	$query.= " news_show_on_startpage=1";	
	$query.= " AND entry_deleted=0";
	$query.= " ORDER BY news_sticky DESC, news_date DESC";	

	
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		$media_array = array();
		
		$output_str.= "<div id=\"modArticles\" class=\"newsSlider\">";
		while($row=mysqli_fetch_assoc($result)){
			
			$row['news_headline'] = json_decode($row['news_headline'], true);
			$row['news_teaser'] = json_decode($row['news_teaser'], true);
			$row['news_text'] = json_decode($row['news_text'], true);
			
			$output_str.= "<article";
			$output_str.= !empty($row['news_css_class']) && $category !="0"?" class=\"".htmlspecialchars($row['news_css_class'])."\"":"";
			$output_str.= ">";
			/*
			if(!empty($row['videos'])){
				$output_str.= "<video autoplay loop style=\"background-image: url(video/Wichtel_in_Produktion.jpg);\" width=\"100%\" height=\"540\"  onclick=\"if(/Android/.test(navigator.userAgent))this.play();\">";
				$output_str.= "<source src=\"video/Wichtel_in_Produktion.mp4\" type=\"video/mp4\" />";
				$output_str.= "<source src=\"video/Wichtel_in_Produktion.webm\" type=\"video/webm\" />";
				$output_str.= "<source src=\"video/Wichtel_in_Produktion.ogv\" type=\"video/ogg\" />";
				
				$output_str.= "</video>";
				$output_str.= "<script src=\"video/html5ext.js\" type=\"text/javascript\"></script>";
				
				
			}
			*/						
			if(!empty($row['news_teaser'])){
				$img = $row['entry_teaser_img'];
				if(empty($img)){
					$img = $row['entry_img'];
				}
				
				$output_str.= "<div class=\"articleTeaser\">";
				$output_str.= "<figure>";
				$output_str.= "<div></div>";
				$output_str.= "<img src=\"files/".$row['entry_img']."\" alt=\"\">";	
				$output_str.= "</figure>";
				$output_str.= "<div>";
				$output_str.= "<div class=\"modArticleText\">";
				//$output_str.= "<h4>".stripslashes($row['news_headline'])."</h4>";
				$output_str.= $row['news_teaser'][$_SESSION['page_language']];
				$output_str.= "<a class=\"readmore\" href=\"aktuell,".$row['entry_parent_id'].".php\">weiterlesen</a>";
				$output_str.= "</div>";
				$output_str.= "</div>";
				$output_str.="</div>";
				
				
			}
			
			
			
			
			$output_str.= "</article>";
			
			
		}
		$output_str.="</div>";
				
	}
	else{
		//$output_str.="<p>Momentan sind noch keine Inhalte hinterlegt.</p>";
	}
	$output_str.="</div>";
	return $output_str;
}

?>
