<?php
include("../shared/include/environment.inc.php");
isLoggedIn();
function NAVI_moduleNavigation($dir = NULL, $output_str = NULL){
	if($dir != NULL && is_dir($dir)){
		if($output_str == NULL){
			$output_str = "";
		}
		$output_str.= "<ul>";
		$fp=opendir($dir);
		while($module=readdir($fp)){
			if($_SESSION['cms_user']['user_role']=="Administrator" || in_array($module,$_SESSION['cms_user']['modules'])){
				if(is_dir($dir.$module) && $module!=".." && $module!="." && file_exists($dir.$module."/index.php")){
					$output_str.= "<li>";
					$output_str.= "<a href=\"".$dir.$module."/index.php"."\" target=\"contentFrame\">".strtoupper($module)."</a>";
					//check for submodules
					if(is_dir($dir.$module."/submodules")){
						$output_str.= NAVI_moduleNavigation($dir.$module."/submodules/");
					}
					$output_str.= "</li>";
				}
			}
		}
		$output_str.= "</ul>";
		return $output_str;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>zeemes : web content management system : navigation</title>
<script type="text/javascript" src="../shared/javascript/functions.js"></script>
<script type="text/javascript" src="javascript/functions.js"></script>
<link rel="stylesheet" href="../shared/css/styles.css" />
<style type="text/css">
body	{
	border-right: 5px solid #FFFFFF;
	background-color: #000000;
}

#switchNavi	{
	margin: 3px;
	margin-bottom: 4px;
	cursor: pointer;
}
</style>
</head>

<body onload="addFunction(document.getElementById('mainNaviList'));">
	<img id="switchNavi" src="images/close.gif" alt="" title="Navigation ausblenden" onclick="showHideNavi()" />
    <ul id="mainNaviList" style="border-top: 2px solid #FFFFFF;">
    	<li><a href="../overview/" target="contentFrame">Übersicht</a></li>
        <?php
		if(isset($_SESSION['cms_user']['user_role']) && $_SESSION['cms_user']['user_role']=="Administrator"){
		?>
        <li><span onclick="SHARED_showChilds(this.parentNode);">Einstellungen/Vorgaben</span>
        	<ul>
            	<li><a href="../settings/languages/" target="contentFrame">Sprachen auswählen</a></li>
                <li><a href="../settings/metatags/" target="contentFrame">Seitenvorgaben (Metatags)</a></li>
                <li><a href="../settings/user_roles/" target="contentFrame">Benutzergruppen</a></li>
            </ul>
        </li>
        <?php
		}
		if((isset($_SESSION['cms_user']['pages']) && is_array($_SESSION['cms_user']['pages']) && sizeof($_SESSION['cms_user']['pages'])>0) || isset($_SESSION['cms_user']['user_role']) && $_SESSION['cms_user']['user_role']=="Administrator"){
		?>
        <li><a href="../pages/" target="contentFrame">Seiten/Inhalte bearbeiten</a></li>
        <?php
        }
		if((isset($_SESSION['cms_user']['modules']) && is_array($_SESSION['cms_user']['modules']) && sizeof($_SESSION['cms_user']['modules'])>0) || isset($_SESSION['cms_user']['user_role']) && $_SESSION['cms_user']['user_role']=="Administrator"){
		?>
        <li><span onclick="SHARED_showChilds(this.parentNode);">Module</span>
        	<?php
				echo NAVI_moduleNavigation("../../modules/");
			?>
            
        </li>
        <?php
		}
		if(isset($_SESSION['cms_user']['user_role']) && $_SESSION['cms_user']['user_role']=="Administrator"){
		?>
        <li><span onclick="SHARED_showChilds(this.parentNode);">Benutzer bearbeiten</span>
        	<ul>
            	<li><a href="../users/internal/" target="contentFrame">CMS-Benutzer</a></li>
                <li><a href="../users/external/" target="contentFrame">externe Benutzer</a></li>
                
            </ul>
        </li>
        <?php
		}
		?>
        <li style="margin-top: 30px;"><a href="../login.php" target="_parent">Logout</a></li>
    </ul>
</body>
</html>
