<?php
function showModule(){
	//form csrf protection
	if(!isset($_SESSION['fid'])){
		$_SESSION['fid'] = md5(time());
	}
	
	if(isset($_REQUEST['fid'])){
		if($_REQUEST['fid'] === $_SESSION['fid']){
			$_SESSION['fid'] = md5(time());	
		}
		else{
			return showForm();
		}
	}
	
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		return submitForm();
	}
	else{
		return showForm();
	}
}

function showForm(){
	$module = "formulare";
	$dir = "modules/";
	$output_str = "";
	if(file_exists($dir.$module."/ini.inc.php")){
		include($dir.$module."/ini.inc.php");
		if(isset($mod_param_array)){
			foreach($mod_param_array as $key=>$val){
				foreach($val as $value){
					if(checkPermission("page",$_GET['entry_id'],"module",$module,$key,$value)==true){
						$form = $value;
					}
				}
			}
		}
	}
	if(isset($_POST['error_array'])){
		if(sizeof($_POST['error_array'])>0){
			$_POST['error_msg'] = "Bitte füllen Sie die mit * gekennzeichneten Pflichtfelder aus. Vielen Dank!";
		}
	}
	$output_str.=outputFile("modules/formulare/forms/".$form);
	return $output_str;
}


function submitForm(){
	
	$mailto = "kontakt@media-studio-pfund.de";
	$msg = "";
	if(isset($_POST['error_array'])){
		unset($_POST['error_array']);
	}
	$_POST['error_array']=array();
	
	foreach($_POST as $key=>$val){
		$val = preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "",$val);
		if(strstr($key,"_req") && $val == ""){
			array_push($_POST['error_array'],$key);
		}
		if($val != "" && $key!="error_array" && $key != "pid"){
			if(is_array($_POST[$key])){
				foreach($_POST[$key] as $key2=>$val2){
					$msg.= strtoupper(str_replace("_"," ",str_replace("_req","",$key))).": ".$val2."\n";	
				}		
			}
			else{
				$msg.= strtoupper(str_replace("_"," ",str_replace("_req","",$key))).": ".$val."\n";	
			}
		}
	}
	
	
	if(sizeof($_POST['error_array']) == 0){
		if(!isset($mail)){
			$mail=mail($mailto,utf8_decode($_SESSION['subject']),utf8_decode($msg),"FROM:".$_POST['email_req']);
			//header("Location: danke,18.php");
			exit();
			
		}
	}
	else{
		return showForm();
	}
}
?>