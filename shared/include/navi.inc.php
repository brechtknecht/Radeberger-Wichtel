<?php
function getParent($entry_id,$check_id, $table){
	$query="SELECT entry_parent_id FROM ".$table." WHERE entry_id=".intval($entry_id)." LIMIT 1";
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		$row=mysqli_fetch_assoc($result);
		if($row['entry_parent_id']==$check_id){
			return true;		
		}
		else{
			return getParent($row['entry_parent_id'],$check_id, $table);
		}
	}
	else{
		return false;	
	}
}

function breadCrumbNavi($entry_id,$output_array = array()){
	$query = "SELECT";
	$query.= " entry_parent_id AS entry_parent_id";
	$query.= ", entry_id AS entry_id";
	$query.= ", (SELECT entry_navi_name FROM _cms_hp_pages_ t2 WHERE entry_parent_id=t1.entry_id AND entry_lang='".$_SESSION['page_language']."' LIMIT 1) AS entry_name";
	$query.= ", (SELECT entry_id FROM _cms_hp_navigation_ t2 WHERE t2.entry_parent_id=t1.entry_id AND t1.entry_deleted=0 AND t1.entry_active=1 ORDER BY entry_sequence ASC LIMIT 1) AS entry_sub_id";
	$query.= " FROM _cms_hp_navigation_ t1 WHERE entry_id=".intval($entry_id)." LIMIT 1";
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		$row=mysqli_fetch_assoc($result);
		array_push($output_array,"<li><a href=\"index.php?entry_id=".(!empty($row['entry_sub_id'])?$row['entry_sub_id']:$row['entry_id'])."\">/".$row['entry_name']."</a></li>");
		if($row['entry_parent_id']>0){
			return breadCrumbNavi($row['entry_parent_id'],$output_array);	
		}
		else{
			//array_pop($output_array);
			$output_array = array_reverse($output_array);
			$output_str = "<ul id=\"breadCrumbNavi\"><li><a href=\"./\">schlupp-video.de</a></li>".implode(" ",$output_array);
			$output_str.= "<li style=\"float: right;\"><a href=\"impressum,7.php\" >IMPRESSUM</a></li>";
			$output_str.= "</ul>";
			return $output_str;
		}
	}
}

function makeNaviList($entry_parent_id=0,$css_id="",$output_str=""){
	$query="SELECT ";
	$query.=" _cms_hp_navigation_.entry_id AS page_id,";
	$query.=" _cms_hp_navigation_.entry_shortcut AS entry_shortcut,";
	$query.=" _cms_hp_navigation_.entry_parent_id AS page_parent_id,";
	$query.=" _cms_hp_navigation_.entry_deleted AS page_deleted,";
	$query.=" _cms_hp_navigation_.entry_user_role AS entry_user_role,";
	$query.=" _cms_hp_navigation_.entry_direct_link as entry_direct_link,"; 
	$query.=" _cms_hp_navigation_.entry_direct_link_target as entry_direct_link_target,"; 
	$query.=" _cms_hp_navigation_.entry_style AS navi_style,";
	$query.=" _cms_hp_pages_.entry_navi_name AS page_real_name,";
	$query.=" _cms_hp_navigation_.entry_name AS page_intern_name";
	//check for subnavi
	//$query.=" (SELECT COUNT(*) FROM _cms_hp_navigation_ WHERE _cms_hp_navigation_.entry_parent_id=page_id AND _cms_hp_navigation_.entry_deleted=0 AND _cms_hp_navigation_.entry_active=1) AS has_sub,";
	//$query.=" (SELECT entry_id FROM _cms_hp_navigation_ WHERE _cms_hp_navigation_.entry_parent_id=page_id AND _cms_hp_navigation_.entry_deleted=0 AND _cms_hp_navigation_.entry_active=1 ORDER BY _cms_hp_navigation_.entry_sequence ASC LIMIT 1) AS sub_page_id";
	$query.=" FROM _cms_hp_navigation_ INNER JOIN _cms_hp_pages_ ON _cms_hp_navigation_.entry_id=_cms_hp_pages_.entry_parent_id";
	$query.=" WHERE";
	$query.=" _cms_hp_navigation_.entry_parent_id=".$entry_parent_id." AND";
	$query.=" _cms_hp_navigation_.entry_active=1 AND ";
	$query.=" _cms_hp_navigation_.entry_user_role='' AND ";
	$query.=" _cms_hp_pages_.entry_lang='".$_SESSION['page_language']."' AND ";
	$query.=" _cms_hp_pages_.entry_state='public' AND ";
	$query.=" _cms_hp_navigation_.entry_deleted=0";
	$query.=" ORDER BY _cms_hp_navigation_.entry_sequence, _cms_hp_navigation_.entry_id ASC";
	
	$result=mysqli_query($_SESSION['conn'], $query);
	$result_count=mysqli_num_rows($result);
	
	if($result_count>0){
		$output_str.="<ul";
		if(isset($css_id) && !empty($css_id)){
			$output_str.=" id=\"".$css_id."\"";
		}
		
		
		$output_str.=">";
		$i=0;
		while($row=mysqli_fetch_assoc($result)){
			$user_role = str_replace("|","",$row['entry_user_role']);
			$user_role_array = explode("|",$row['entry_user_role']);
			$in_array = 0;
			foreach($user_role_array as $val){
				if(!empty($val) && (isset($_SESSION['ext_user']) && in_array($val,$_SESSION['ext_user']['perm']))){
					$in_array+= 1;	
				}
			}
			
			if(!empty($user_role) && (!isset($_SESSION['ext_user']) || (isset($_SESSION['ext_user']) && $in_array == 0	))){
				//echo "test";
				continue;
			}
			
			$query="SELECT ";
			$query.=" _cms_hp_navigation_.entry_id AS page_id";
			$query.=" FROM _cms_hp_navigation_ INNER JOIN _cms_hp_pages_ ON _cms_hp_navigation_.entry_id=_cms_hp_pages_.entry_parent_id";
			$query.=" WHERE";
			$query.=" _cms_hp_navigation_.entry_parent_id=".$row['page_id']." AND";
			$query.=" _cms_hp_navigation_.entry_active=1 AND ";
			$query.=" _cms_hp_pages_.entry_lang='".$_SESSION['page_language']."' AND ";
			$query.=" _cms_hp_pages_.entry_state='public' AND ";
			$query.=" _cms_hp_navigation_.entry_deleted=0";
			$query.=" ORDER BY _cms_hp_navigation_.entry_sequence, _cms_hp_navigation_.entry_id ASC";
			$result_sub=mysqli_query($_SESSION['conn'], $query);
			
			
			$sub_pages_count=mysqli_num_rows($result_sub);
			if($sub_pages_count>0){
				$row_sub=mysqli_fetch_assoc($result_sub);
			}
			
			$row['has_sub']=mysqli_num_rows($result_sub);
			$rewrite_url="";
			$output_str.="<li class=\"";
			
			if((getParent($_GET['entry_id'], $row['page_id'], "_cms_hp_navigation_") ||  $_GET['entry_id'] == $row['page_id'])){
				$output_str.= "active";
			}
			if(!empty($row['navi_style'])){
				$output_str.= " ".htmlspecialchars($row['navi_style']);
			}
			
			$output_str.="\">";
			
			$page_name=($row['page_real_name']==""?$row['page_intern_name']:$row['page_real_name']);
			//$page_name = nl2br($page_name);
			if(!empty($row['entry_direct_link'])){
				if(!strstr($row['entry_direct_link'],"javascript:") && !strstr($row['entry_direct_link'],"http://")){
					$url="http://".$row['entry_direct_link'];
					$url=$row['entry_direct_link'];
				}
				else{
					$url=$row['entry_direct_link'];
				}
				$output_str.="<a href=\"".$url."\"";
				if($row['entry_direct_link_target']=="_blank"){
						$output_str.=" target=\"".$row['entry_direct_link_target']."\"";
				}
				$output_str.=">".$page_name."</a>";
			}
			else{
				$rewrite_url.=make_html_file($page_name);
				//$rewrite_url.="index.php?entry_id=";
				if(getParent($_GET['entry_id'],$row['page_id'],"_cms_hp_navigation_")==true || $row['page_id']==$_GET['entry_id'] || $row['navi_style']=="onlybreak" || ($row['has_sub']>0)){
					$output_str.="<a href=\"";
					if($row['entry_shortcut']!=0){
						$rewrite_url.=",".$row['entry_shortcut'];
						//$rewrite_url.=$row['entry_shortcut'];	
					}
					else{
						if($sub_pages_count>0){
							//$rewrite_url.=",".$row_sub['page_id'];	
							$rewrite_url.=",".$row['page_id'];
							//$rewrite_url.=$row['page_id'];
						}
						else{
							$rewrite_url.=",".$row['page_id'];	
							//$rewrite_url.=$row['page_id'];	
						}
					}
					
					
					$rewrite_url.=".php";
					$output_str.=$rewrite_url;
					$output_str.="\"";
					
					if(getParent($_GET['entry_id'],$row['page_id'],"_cms_hp_navigation_")==true || $row['page_id']==$_GET['entry_id']){
						/*
						$output_str.=" class=\"";
						$output_str.="active";
						$output_str.="\"";	
						*/
						
						
					}
					if($i == $result_count-1){
						$output_str.=" style=\"margin-right: 0px;\"";		
					}
					if($row['entry_direct_link_target']=="_blank"){
						$output_str.=" target=\"".$row['entry_direct_link_target']."\"";
					}
					$output_str.=">";	
					$output_str.= nl2br(($page_name));	
					$output_str.="</a>";
				}
				else{
					
					$output_str.="<a href=\"";
					if($row['entry_shortcut']!=0){
						$rewrite_url.=",".$row['entry_shortcut'];	
						//$rewrite_url.=$row['entry_shortcut'];	
					}
					else{
						if($sub_pages_count>0){
							//$rewrite_url.=",".$row_sub['entry_id'];	
							$rewrite_url.=",".$row['page_id'];
							//$rewrite_url.=$row['page_id'];
						}
						else{
							$rewrite_url.=",".$row['page_id'];	
							//$rewrite_url.=$row['page_id'];	
						}
					}
					$rewrite_url.=".php";
					
					$output_str.=$rewrite_url;
					$output_str.="\"";
					
					if(getParent($_GET['entry_id'],$row['page_id'],"_cms_hp_navigation_")==true || $row['page_id']==$_GET['entry_id']){
						/*
						$class = "active";
						$output_str.=" class=\"";
						$output_str.="active";
						if(!empty($row['entry_style'])){
						    $output_str.=" ".$row['entry_style'];
						}

						$output_str.="\"";	
						*/
					}
					else{
						$class = "";	
					}
					if($i == $result_count-1){
						$output_str.=" style=\"margin-right: 0px\"";	
					}
					if($row['entry_direct_link_target']=="_blank"){
						$output_str.=" target=\"".$row['entry_direct_link_target']."\"";
					}
					$output_str.=">";	
					$output_str.= nl2br($page_name);
					$output_str.="</a>";
				}
			}
			if($row['has_sub']>0 && (getParent($_GET['entry_id'],$row['page_id'],"_cms_hp_navigation_")==true || $row['page_id']==$_GET['entry_id'])){
			//if($row['has_sub']>0 && $level<1){
				//$output_str.=makeNaviList($row['page_id'],"","",$level+1);
				
			}
			$output_str.=makeNaviList($row['page_id'],"","","");
			
			
			$output_str.="</li>";
			$i++;
		}
		$output_str.="</ul>";
	}
	return $output_str;
}





function make_html_file($word){
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
