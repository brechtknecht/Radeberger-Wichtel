<?php
if(isset($_POST['module']) && !empty($_POST['module'])){
	
	if(isset($_POST['entry_id'])){
		$_GET['entry_id']=$_POST['entry_id'];
	}
	if(!defined("internalCall")){
		include("../../shared/include/environment.inc.php");
	}
	$dir="../../../modules/";
	if($_POST['module']!="" && is_dir($dir.$_POST['module'])){
		
		if(file_exists($dir.$_POST['module']."/ini.inc.php")){
			
			include($dir.$_POST['module']."/ini.inc.php");
			
			if(isset($mod_param_array)){
				$z=0;
				foreach($mod_param_array as $key=>$val){
					?>
					<input type="hidden" name="page_perm_modules_arg_name<?php echo $z;?>" value="<?php echo $key;?>" />
		            <select name="page_perm_modules_arg_value<?php echo $z;?>[]" multiple="multiple" size="20" style="width: auto;margin-right: 20px;vertical-align: top;">
					<option value="">Alle</option>
					<?php
					foreach($val as $value){
						if(is_array($value)){
							$o_value = $value[0];
							$o_text = $value[1];
						}
						else{
							$o_value = $value;
							$o_text = $value;
						}
						?>
						<option value="<?php echo $o_value;?>"
						<?php echo (checkPermission("page",$_GET['entry_id'],"module",$_POST['module'],$key,$o_value)==true)?"selected=\"selected\"":"";?>><?php echo $o_text;?></option>
						<?php
					}
					?>
					</select>
					<?php
					$z++;
				}
			}
		}
	}
}
?>