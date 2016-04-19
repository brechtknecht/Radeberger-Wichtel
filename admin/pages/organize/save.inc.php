<?php
//no external call
if(defined("internalCall")){
	/////////////////////////////////////navi table////////////////////////////////////////////////
	if(!isset($_POST['entry_linear_navigation'])){
		$_POST['entry_linear_navigation']="0";
	}
	
	if(!isset($_POST['entry_shortcut']) || empty($_POST['entry_shortcut'])){
		$_POST['entry_shortcut']=0;
	}
	
	if(!isset($_POST['multilingual'])){
		$_POST['multilingual']="0";
	}
	
	if(!isset($_POST['show_subnavi'])){
		$_POST['show_subnavi']="0";
	}
	
	if(!isset($_POST['show_in_add_navi'])){
		$_POST['show_in_add_navi']="0";
	}
		
	if($_POST['entry_id']=="new"){
		$query="INSERT _cms_hp_navigation_ SET";
		$query.=" entry_sequence=999999,";
	}
	else{
		$query="UPDATE _cms_hp_navigation_ SET";
	}
		
	$query.=" last_change=NOW(), ";
	$query.=" entry_template='".$_POST['entry_template']."', ";
	$query.=" entry_name='".$_POST['entry_name']."', ";
	
	if(isset($_POST['entry_deeplink'])){
		$url = "/index.php?entry_id=".$_POST['entry_id'];
		if(!empty($_POST['entry_deeplink'])){
			addDeepLink($_POST['entry_deeplink'], $url);		
		}
		else{
			removeDeepLink($url);		
		}
		
		$query.=" entry_deeplink='".$_POST['entry_deeplink']."', ";
	}
	
	if(!isset($_POST['page_perm_modules'])){
		$_POST['page_perm_modules']="";
	}
	$query.=" entry_inc_module='".$_POST['page_perm_modules']."', ";
	$query.=" entry_inc_module_mode='".$_POST['entry_inc_module_mode']."', ";
	$query.=" entry_inc_module_target='".$_POST['entry_inc_module_target']."', ";
	$query.=" entry_inc_gallery='".$_POST['entry_inc_gallery']."', ";
	//$query.=" entry_inc_sound='".$_POST['entry_inc_sound']."', ";
	
	if(isset($_POST['multilingual'])){
		$query.=" multilingual='".$_POST['multilingual']."', ";
	}
	if(isset($_POST['show_subnavi'])){
		//$query.=" show_subnavi='".$_POST['show_subnavi']."', ";
	}
	//$query.=" show_in_add_navi='".$_POST['show_in_add_navi']."', ";
	
	$query.=" entry_shortcut='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_shortcut'])."', ";
	
	
	if(isset($_POST['entry_user_role']) && !empty($_POST['entry_user_role'])){
		if(is_array($_POST['entry_user_role'])){
			$_POST['entry_user_role'] = "|".implode("|",$_POST['entry_user_role'])."|";
		}
		else{
			$_POST['entry_user_role'] = "|".$_POST['entry_user_role']."|";
		}
		
	}
	else{
		$_POST['entry_user_role'] = "";
	}
	$query.=" entry_user_role='".$_POST['entry_user_role']."', ";
			
	if(isset($_POST['entry_start']) && $_POST['entry_start']==1){
		$result_sub=mysqli_query($_SESSION['conn'], "UPDATE _cms_hp_navigation_ SET entry_start=0");
	}
	else{
		$_POST['entry_start']=0;
	}
	if(!isset($_POST['entry_active'])){
		$_POST['entry_active']=0;
	}
	
	$query.=" entry_start=".$_POST['entry_start'].", ";
	
	
	if(isset($_POST['entry_style'])){
		$query.=" entry_style='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_style'])."', ";
	}
	
	if(isset($_POST['entry_custom_css'])){
		$query.=" entry_custom_css='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_custom_css'])."', ";
	}
	
	if(isset($_POST['entry_bg_image'])){
		$query.=" entry_bg_image='".$_POST['entry_bg_image']."', ";
	}
	
	$query.=" entry_direct_link='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_direct_link'])."', ";
	$query.=" entry_direct_link_target='".$_POST['entry_direct_link_target']."', ";
	
	$query.=" entry_active='".$_POST['entry_active']."', ";
	$query.=" entry_last_usr='".$_SESSION['cms_user']['user_id']."'";
	
	if($_POST['entry_id']!="new"){
		$query.=" WHERE entry_id=".$_POST['entry_id'];
	}
	//echo $query;
	
	$result=mysqli_query($_SESSION['conn'], $query);
	echo mysqli_error($_SESSION['conn']);
	if($_POST['entry_id']=="new"){
		$_POST['entry_id']=mysqli_insert_id($_SESSION['conn']);
	}
	
	/////////////////////////////////////pages table////////////////////////////////////////////////
	//check if entry exist
	foreach($_SESSION['lang_array'] as $language){
		$query="";
		$test="SELECT entry_id FROM _cms_hp_pages_ WHERE entry_parent_id=".intval($_POST['entry_id'])." AND entry_lang='".mysqli_real_escape_string($_SESSION['conn'], $language)."' AND entry_state='public' LIMIT 1";
		
		$result=mysqli_query($_SESSION['conn'], $test);
		$success=mysqli_num_rows($result);
		if($success>0){
			
			$test_row=mysqli_fetch_assoc($result);
			$query.="UPDATE _cms_hp_pages_ SET";
		}
		else{
			$query.="INSERT INTO _cms_hp_pages_ SET";
		}
		$query.=" entry_meta_keywords='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_meta_keywords_'.$language])."',";
		$query.=" entry_meta_description='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_meta_description_'.$language])."',";
		$query.=" entry_title='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_title_'.$language])."',";
		$query.=" entry_navi_name='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_navi_name_'.$language])."',";
		$query.=" entry_navi_desc='".mysqli_real_escape_string($_SESSION['conn'], $_POST['entry_navi_desc_'.$language])."',";
		$query.=" last_change=NOW(),";
		
		$query.=" entry_parent_id='".intval($_POST['entry_id'])."',";
		$query.=" entry_lang='".$language."'";
		if($success>0){
			$query.=" WHERE entry_id=".$test_row['entry_id']." AND entry_lang='".$language."'";
		}
		
		$result=mysqli_query($_SESSION['conn'], $query);
		echo mysqli_error($_SESSION['conn']);
	//public version
		if(isset($_POST['entry_state_'.$language])){
			
			$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_hp_pages_ SET entry_state='archived' WHERE entry_parent_id=".intval($_POST['entry_id'])." AND entry_lang='".$language."'");
			$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_hp_pages_ SET entry_state='public' WHERE entry_id=".intval($_POST['entry_state_'.$language])." LIMIT 1");
		}
	}	
	
		
	/////////////////////////////////////permission table////////////////////////////////////////////////
	//del old
	$result=mysqli_query($_SESSION['conn'], "DELETE FROM _cms_perm_ WHERE perm_cat='page' AND user_id=".intval($_POST['entry_id']));
	//set new
	foreach($_POST as $key=>$val){
		if(strstr($key,"page_perm_modules") && !strstr($key,"arg_name") && !strstr($key,"arg_value")){
			$result=mysqli_query($_SESSION['conn'], "INSERT INTO _cms_perm_ SET perm_cat='page',user_id=".$_POST['entry_id'].",perm_name='module',perm_value='".mysqli_real_escape_string($_SESSION['conn'], $val)."',last_change=NOW()");
			for($i=0;$i<50;$i++){
				$tmp_1=$key."_arg_name".$i;
				if(isset($_POST[$tmp_1]) && !empty($_POST[$tmp_1])){
					$tmp_2=$key."_arg_value".$i;
					if(isset($_POST[$tmp_2])){
						foreach($_POST[$tmp_2] as $val2){
							$query="INSERT INTO _cms_perm_ SET";
							$query.=" perm_cat='page',";
							$query.=" user_id=".$_POST['entry_id'].",";
							$query.=" perm_name='module',";
							$query.=" perm_value='".$val."',";
							$query.=" perm_arg_name='".mysqli_real_escape_string($_SESSION['conn'], $_POST[$tmp_1])."',";
							$query.=" perm_arg_value='".mysqli_real_escape_string($_SESSION['conn'], $val2)."',";
							$query.=" last_change=NOW()";
							$result=mysqli_query($_SESSION['conn'], $query);
						}
					}
				}
			}
		}
	}
	//change position/sequence
	if($_POST['new_position']!="" && $_POST['position_mode']!=""){
		//split new_position
		$position_array=explode("|",$_POST['new_position']);
		$new_sequence=$position_array[0];
		$new_parent=$position_array[1];
		$new_id=$position_array[2];
		
		if($_POST['position_mode']=="before"){
			$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_hp_navigation_ SET entry_sequence=entry_sequence+1 WHERE entry_sequence>=".$new_sequence);
			$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_hp_navigation_ SET entry_sequence=".$new_sequence.", entry_parent_id=".$new_parent." WHERE entry_id=".$_POST['entry_id']);
		}
		if($_POST['position_mode']=="behind"){
			$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_hp_navigation_ SET entry_sequence=entry_sequence-1 WHERE entry_sequence<=".$new_sequence);
			$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_hp_navigation_ SET entry_sequence=".$new_sequence.", entry_parent_id=".$new_parent." WHERE entry_id=".$_POST['entry_id']);
		}
		if($_POST['position_mode']=="submenu"){
			$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_hp_navigation_ SET entry_parent_id=".$new_id." WHERE entry_id=".$_POST['entry_id']);
		}
	}
	//neu nummerieren
	$result=mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_hp_navigation_ ORDER BY entry_sequence,entry_id ASC");
	$i=1;
	while($row=mysqli_fetch_assoc($result)){
		$query=mysqli_query($_SESSION['conn'], "UPDATE _cms_hp_navigation_ SET entry_sequence=".$i." WHERE entry_id='".$row['entry_id']."'");
		$i++;
	}	
	$_GET=$_POST;
	
}
else{
	die("Fehler!");
}
?>