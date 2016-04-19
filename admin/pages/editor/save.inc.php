<?php
//no external call
if(defined("internalCall")){
	if(isset($_POST['entry_id']) && isset($_POST['page_version_id'])){
		$insert=false;
		//get page data
		$query="SELECT";
		$query.=" entry_navi_name,entry_meta_keywords,entry_meta_description,entry_title,entry_direct_link,entry_direct_link_target,entry_lang,entry_state";
		$query.=" FROM _cms_hp_pages_ WHERE entry_id=".intval($_POST['page_version_id'])." LIMIT 1";
		$result=mysqli_query($_SESSION['conn'], $query);
		if(mysqli_num_rows($result)>0){
			$row=mysqli_fetch_assoc($result);
			$entry_lang=$row['entry_lang'];
			if($row['entry_state']=="entwurf"){
				$insert=false;
			}
			else{
				$insert=true;
			}
		}
		//build query
		if($insert==false){
			$query="UPDATE _cms_hp_pages_ SET";
		}
		else{
			//check for exisiting draft
			$result=mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_hp_pages_ WHERE entry_parent_id=".intval($_POST['entry_id'])." AND entry_lang='".$entry_lang."' AND entry_state='entwurf' LIMIT 1");
			if(mysqli_num_rows($result)>0){
				$row_draft=mysqli_fetch_assoc($result);
				$_POST['page_version_id']=$row_draft['entry_id'];
				$query="UPDATE _cms_hp_pages_ SET";
				$insert=false;
			}
			else{
				$insert=true;
				$query="INSERT INTO _cms_hp_pages_ SET";
			}
		}
		//prepare content	
		$_POST['content'] = "";
		foreach($_POST as $key=>$val){
			if(strstr($key,"contentarea")){
				$_POST['content'].= $key."#";	
				$_POST['content'].= ($val);	
				$_POST['content'].= "||";
			}
		}
		
		$query.=" entry_parent_id=".intval($_POST['entry_id']);
		$query.=", entry_navi_name='".$row['entry_navi_name']."'";
		$query.=", entry_meta_keywords='".$row['entry_meta_keywords']."'";
		$query.=", entry_meta_description='".$row['entry_meta_description']."'";
		$query.=", entry_title='".$row['entry_title']."'";
		$query.=", entry_direct_link='".$row['entry_direct_link']."'";
		$query.=", entry_direct_link_target='".$row['entry_direct_link_target']."'";
		$query.=", entry_lang='".$entry_lang."'";
		$query.=", entry_html='".mysqli_real_escape_string($_SESSION['conn'], $_POST['content'])."'";
		$query.=", entry_boxes='".mysqli_real_escape_string($_SESSION['conn'], $_POST['boxes'])."'";
		$query.=", last_change=NOW()";
		$query.=", changed_by=".$_SESSION['cms_user']['user_id'];
		
		if(isset($_POST['publish']) && $_POST['publish']=="0"){
			$query.=", entry_state='entwurf'";
		}
		else{
			$query.=", entry_state='public'";
		}
		
		if($insert==false){
			$query.=" WHERE entry_id=".intval($_POST['page_version_id'])." LIMIT 1";
		}
		//echo $query;
		$result=mysqli_query($_SESSION['conn'], $query);
		//die( mysqli_error($_SESSION['conn']));
		
		if($insert==true){
			$_POST['page_version_id']=mysqli_insert_id($_SESSION['conn']);
		}
		
		if(isset($_POST['publish']) && $_POST['publish']=="1"){
			$result=mysqli_query($_SESSION['conn'], "UPDATE _cms_hp_pages_ SET entry_state='archiv' WHERE entry_parent_id=".intval($_POST['entry_id'])." AND entry_lang='".$entry_lang."' AND entry_state='public' AND entry_id!=".intval($_POST['page_version_id'])." LIMIT 1");
		}
		
		
		
		//delete 
	$_GET=$_POST;
	}
}
else{
	die("Fehler!");
}
?>