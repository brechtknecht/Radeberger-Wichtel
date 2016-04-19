<?php
if(is_dir("modules/formulare/forms/")){
	$dir="modules/formulare/forms/";
}
else{
	$dir="../../../modules/formulare/forms/";	
}

$fp=opendir($dir);

$mod_param_array['forms']=array();
while($form=readdir($fp)){
	if(strstr($form,".php")){
		$mod_param_array['forms'][]=$form;
	}
}
?>