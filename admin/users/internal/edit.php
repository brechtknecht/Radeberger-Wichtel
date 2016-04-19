<?php
if(!isset($_REQUEST['entry_id'], $_GET['user_type'])){
	header("Location: index.php");
	exit();	
}

$req_role="Administrator";
include("../../shared/include/environment.inc.php");
isLoggedIn();
if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['save'])){
	include("save.inc.php");
}
//get user data
if($_GET['entry_id']!="new"){
	$result=mysqli_query($_SESSION['conn'], "SELECT * FROM _cms_hp_user_ WHERE user_type='".mysqli_real_escape_string($_SESSION['conn'], $_GET['user_type'])."' AND entry_id='".mysqli_real_escape_string($_SESSION['conn'], $_GET['entry_id'])."' LIMIT 1");
	if(mysqli_num_rows($result) == 1){
		$var_array=mysqli_fetch_assoc($result);
	}
}
//get supervisors
$result=mysqli_query($_SESSION['conn'], "SELECT * FROM _cms_hp_user_ WHERE (user_role='Chefredakteur' OR user_role='Administrator') ORDER BY user_name ASC");

//get pages
function getEntries($entry_parent_id,$indent=""){
	global $indent;
	$query="SELECT entry_id,entry_parent_id,entry_name";
	$query.=" FROM _cms_hp_navigation_ WHERE entry_parent_id=".$entry_parent_id;
	
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		while($row=mysqli_fetch_assoc($result)){
			$result_sub=mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_hp_navigation_ WHERE entry_parent_id='".mysqli_real_escape_string($_SESSION['conn'], $row['entry_id'])."' AND entry_deleted=0 ORDER BY entry_sequence");
			
			$row['entry_name']=str_replace("-||"," ",$row['entry_name']);
			$row['entry_name']=str_replace("||"," ",$row['entry_name']);
		
			echo "<option ".(checkPermission("user",$_GET['entry_id'],"page",$row['entry_id'])==true?"selected=\"selected\"":"")." value=\"".$row['entry_id']."\">".$indent.$row['entry_name']."</option>";
						
			if(mysqli_num_rows($result_sub)>0){
				$indent.="-----";
				getEntries($row['entry_id'],$indent);
				$indent=substr($indent,0,strlen($indent)-5);
			}
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>zeemes : web content management system : modules : user : edit</title>
<script type="text/javascript" src="../../shared/javascript/functions.js"></script>
<script type="text/javascript" src="../javascript/functions.js"></script>
<link rel="stylesheet" href="../../shared/css/styles.css" />
</head>

<body onload="SHARED_showFieldSetSwitch('form0');" id="content">
<h2>CMS Benutzer bearbeiten: <?php echo $_GET['entry_id']=="new"?"neu":$var_array['user_fname']." ".$var_array['user_name'];?></h2>
<div id="saveTools">
 <button type="button" onclick="USERS_submitForm(document.form0,'Änderungen übernehmen?');">Änderungen übernehmen</button> 
  <button type="button" onclick="location.href='index.php'">zur&uuml;ck zur Liste</button>
</div>
<?php
if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['save']))
	{
	echo "<span class=\"showState\">Ihre Änderungen wurden gespeichert</span>";
	}
?>
<form action="#" method="post" id="form0" name="form0">
  <?php
    if(isset($_SESSION['fid'])){
		?>
         <input type="hidden" name="fid" value="<?php echo htmlspecialchars($_SESSION['fid'])?>" />
		<?php	
	}
	?>
  <input type="hidden" name="save" value="1" />
  <input type="hidden" name="user_type" value="<?php echo $_GET['user_type'];?>" />
  <input type="hidden" name="entry_id" value="<?php echo htmlspecialchars($_GET['entry_id']);?>" />
  <fieldset id="Benutzerdaten">
  <table cellpadding="0" cellspacing="0" class="table">
    <thead>
      <tr>
        <th style="width: 100px;">&nbsp;</th>
        <th style="width: auto;">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Name*</td>
        <td><input type="text" name="user_name" value="<?php echo isset($var_array['user_name'])?$var_array['user_name']:"";?>" /></td>
      </tr>
      <tr>
        <td>Vorname*</td>
        <td><input type="text" name="user_fname" value="<?php echo isset($var_array['user_fname'])?$var_array['user_fname']:"";?>" /></td>
      </tr>
      <tr>
        <td>eMail*</td>
        <td><input type="text" name="user_email" value="<?php echo isset($var_array['user_email'])?$var_array['user_email']:"";?>" /></td>
      </tr>
      <tr>
        <td>Login*</td>
        <td><input type="text" name="user_login" value="<?php echo isset($var_array['user_login'])?$var_array['user_login']:"";?>" style="width: 120px" /></td>
      </tr>
      <tr>
        <td>Passwort*</td>
        <td><input type="text" name="user_password" value="" style="width: 120px" />
          </td>
      </tr>
      <tr>
        <td>Passwort wiederholen*</td>
        <td><input type="text" name="user_password_verify" value="" style="width: 120px" /></td>
      </tr>
    </tbody>
    <tfoot>
    </tfoot>
  </table>
  </fieldset>
  <fieldset id="Gruppe">
  <table cellpadding="0" cellspacing="0" class="table">
    <thead>
      <tr>
        <th style="width: 100px">&nbsp;</th>
        <th style="width: auto">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Benutzergruppe</td>
        <td>
        <select name="user_role" onchange="USERS_checkRole(this,'userSupervisor')">
           
             <?php
            /*
			?>
            <option value="Redakteur" <?php echo isset($var_array)&&$var_array['user_role']=="Redakteur"?"selected=\"selected\"":"";?>>Redakteur</option>
           <?php
            */
			?>
		    <option value="Chefredakteur" <?php echo isset($var_array)&&$var_array['user_role']=="Chefredakteur"?"selected=\"selected\"":"";?>>eingeschränkter Nutzer</option>
            <option value="Administrator" <?php echo isset($var_array)&&$var_array['user_role']=="Administrator"?"selected=\"selected\"":"";?>>Administrator</option>
			
          </select>
        </td>
      </tr>
      <tr id="userSupervisor" <?php echo isset($var_array)&&$var_array['user_role']=="Redakteur"?"":"style=\"display: none\"";?>>
        <td>zuständiger Chefredakteur</td>
        <td><select name="user_supervisor">
            <?php
						if(mysqli_num_rows($result)>0)
							{
							while($row=mysqli_fetch_assoc($result))
								{
								?>
            <option value="<?php echo $row['entry_id'];?>" <?php echo isset($var_array['user_supervisor'])&&$var_array['user_supervisor']==$row['entry_id']?"selected=\"selected\"":"";?>><?php echo $row['user_name'].", ".$row['user_fname'];?></option>
            <?php
								}
							}
						?>
          	<option value="9999">eigenverantwortlich</option>
          </select>
        </td>
      </tr>
    </tbody>
    <tfoot>
    </tfoot>
  </table>
  </fieldset>
  
  <fieldset id="Inhalte/Seiten">
  <table cellpadding="0" cellspacing="0" class="table">
    <thead>
      <tr>
        <th style="width: 100px">&nbsp;</th>
        <th style="width: auto">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td>Seiten anlegen</td>
      <td><input type="checkbox" name="user_perm_pages_new" style="width: 30px; border: none; background: 0" />
      </td>
    </tr>
    <tr>
      <td>Seiten löschen</td>
      <td><input type="checkbox" name="user_perm_pages_del" style="width: 30px; border: none; background: 0" />
      </td>
    </tr>
    
    <tr>
      <td>Seiten bearbeiten <br /><br /><span class="smallDescription">(untergeordnete Seiten werden automatisch mit freigegeben)</span></td>
      <td><select size="10" multiple="multiple" name="user_perm_pages_edit[]">
          <option value="">keine</option>
           <option value="all" <?php echo (checkPermission("user",$_GET['entry_id'],"page","all")==true?"selected=\"selected\"":"");?>>alle Seiten</option>
		  <?php
			  getEntries(0);
			  ?>
        </select>
      </td>
      </tr>
    </tbody>
    
    <tfoot>
    </tfoot>
  </table>
  </fieldset>
  
  <fieldset id="Module">
  <table cellpadding="0" cellspacing="0" class="table">
    <thead>
      <tr>
        <th style="width: 100px">&nbsp;</th>
        
        <th style="width: 30px">neu</th>
        <th style="width: 30px">löschen</th>
        <th style="width: auto">Filter</th>
      </tr>
    </thead>
    <tbody>
    <?php
	$dir="../../../modules/";
	$fd=opendir($dir);
	$i=0;
	while($mods=readdir($fd))
		{
		if($mods!="."&&$mods!="..")
			{
			if(file_exists($dir.$mods."/index.php"))
				{
				//check for ini file => filter permission
				if(file_exists($dir.$mods."/perm_filter.inc.php")){
					include($dir.$mods."/perm_filter.inc.php");
				}
				?>
			   <tr>
					<td><?php echo ucfirst($mods);?></td>
					
                    <td>
					<input type="checkbox" name="user_perm_modules_edit<?php echo $i;?>[new]" value="<?php echo $mods;?>" style="width: 30px; border: none; background: 0" <?php echo (checkPermission("user",$_GET['entry_id'],"module",$mods,"function","new"))?"checked=\"checked\"":"";?> /></td>
                    <td>
					<input type="checkbox" name="user_perm_modules_edit<?php echo $i;?>[del]" value="<?php echo $mods;?>" style="width: 30px; border: none; background: 0" <?php echo (checkPermission("user",$_GET['entry_id'],"module",$mods,"function","del"))?"checked=\"checked\"":"";?> /></td>
                    <td>
                    <?php 
					if(isset($mod_param_array)){
						if(isset($mod_param_array['name'])){
							$filter_name = $mod_param_array['name'];
							unset($mod_param_array['name']);
						}
						if(isset($mod_param_array['multiple']) && $mod_param_array['multiple'] == true){
							$filter_multiple = $mod_param_array['multiple'];
							unset($mod_param_array['multiple']);
						}
						?>
                        <select name="user_perm_modules_filter[<?php echo $mods;?>][]" <?php echo $filter_multiple?"multiple=\"multiple\"":""?> size="10">
                        	<option value="">keine Beschränkung</option>
                            <?php
							$z=0;
							
							foreach($mod_param_array as $key=>$val){
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
									<option value="<?php echo $key."|".$o_value;?>" <?php echo (checkPermission("user",$_GET['entry_id'],"module",$mods,$key,$o_value)==true)?"selected=\"selected\"":"";?>>									<?php echo $o_text;?>
                                    </option>
									<?php
								}
								?>
								</select>
								<?php
								$z++;
							}
								?>
                       <?php
					}
					?>
                    </td>
                    
					
				</tr>
				<?php
				if(isset($mod_param_array))
					{
					unset($mod_param_array, $filter_multiple, $filter_name);
					}
				$i++;
				}
			}
		}
	
	?>
    </tbody>
    <tfoot>
    </tfoot>
  </table>
  </fieldset>
 
</form>
</body>
</html>
