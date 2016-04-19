<?php
error_reporting(0);
date_default_timezone_set('Europe/Berlin');
//start session
session_start();
//connect database
include("shared/include/connect_db.inc.php");
//include functions
include("shared/include/functions.inc.php");
include("shared/include/lang.inc.php");

if(isset($_REQUEST['logout'])){
	unset($_SESSION['ext_user']);
}

//first call?
if(!isset($_GET['entry_id'])){
	//get start page
	$result=mysqli_query($_SESSION['conn'], "SELECT entry_id FROM _cms_hp_navigation_ WHERE entry_start=1");
	echo mysqli_error($_SESSION['conn']);
	
	if(mysqli_num_rows($result)>0){
		$row=mysqli_fetch_assoc($result);
		$_GET['entry_id'] = $row['entry_id'];
		}
	else 
		{
		$_GET['entry_id'] = 1;
		}
	}
$_GET['entry_id'] = intval($_GET['entry_id']);


//set language
if(!isset($_SESSION['page_language']) || empty($_SESSION['page_language'])){
	//get default language
	$result=mysqli_query($_SESSION['conn'], "SELECT setting_value FROM _cms_settings_ WHERE setting_key='languages' LIMIT 1");
	$row=mysqli_fetch_assoc($result);
	
	$language=substr($row['setting_value'],strpos($row['setting_value'],"!"));
	$language=str_replace("!","",$language);
	
	$_SESSION['page_language']=$language;
}


if(isset($_GET['entry_lang']) || isset($_POST['entry_lang'])){
	$_SESSION['page_language']=$_GET['entry_lang'];
}
	
if(isset($_GET['font-size'])){
	$_SESSION['font-size']=$_GET['font-size'];
}


//check permissions
if(isset($_GET['page_mode']) && $_GET['page_mode']=="edit"){
	if(checkPermission("user",$_SESSION['cms_user']['user_id'],"page",$_GET['entry_id'])==false){
		die("Sie haben keine Berechtigung auf dieses Element zuzugreifen!");
	}
}


//get content	
$query = "SELECT *";
$query.= ", (SELECT entry_html FROM _cms_hp_pages_ t3 WHERE t3.entry_parent_id='".mysqli_real_escape_string($_SESSION['conn'], $_GET['entry_id'])."' AND t3.entry_lang='de' AND t3.entry_state='public') AS entry_html_de";
$query.= " FROM _cms_hp_navigation_ INNER JOIN _cms_hp_pages_";
$query.= " ON _cms_hp_navigation_.entry_id=_cms_hp_pages_.entry_parent_id";
$query.= " WHERE _cms_hp_navigation_.entry_id='".mysqli_real_escape_string($_SESSION['conn'], $_GET['entry_id'])."'";
if(isset($_GET['page_version_id'])){
	$query.=" AND _cms_hp_pages_.entry_id=".intval($_GET['page_version_id'])." LIMIT 1";
}
else{
	$query.=" AND _cms_hp_pages_.entry_lang='".mysqli_real_escape_string($_SESSION['conn'], $_SESSION['page_language'])."'";
	$query.= " AND _cms_hp_pages_.entry_state='public'";
	$query.= " ORDER BY _cms_hp_pages_.last_change DESC LIMIT 1";
}

$result = mysqli_query($_SESSION['conn'], $query);
if(mysqli_num_rows($result)>0){
	$row=mysqli_fetch_assoc($result);
	$_GET['navi_name'] = str_replace("-\r\n","",$row['entry_navi_name']);
}
else{
	header("Location: ./");
	exit();
}


//include navi
include("shared/include/navi.inc.php");
include("shared/include/switch_language.inc.php");
include("shared/include/slideshow.inc.php");

//replace placeholders
$search_array=array();
$replace_array=array();

//check for module
if($row['entry_inc_module']!="")
	{
	include("modules/".$row['entry_inc_module']."/module.inc.php");
	array_push($search_array,"/<::modulestyles::>/");
	if(file_exists("modules/".$row['entry_inc_module']."/css/module_styles.css"))
		{
		array_push($replace_array,"<link href=\"modules/".$row['entry_inc_module']."/css/module_styles.css\" rel=\"stylesheet\" type=\"text/css\" />");
		}
	else
		{
		array_push($replace_array,"");
		}
	array_push($search_array,"/<::modulejs::>/");
	if(file_exists("modules/".$row['entry_inc_module']."/javascript/modulejs.js"))
		{
		array_push($replace_array,"<script src=\"modules/".$row['entry_inc_module']."/javascript/modulejs.js\" type=\"text/javascript\"></script>");
		}
	else
		{
		array_push($replace_array,"");
		}
	
	}
else
	{
	array_push($search_array,"/<::modulestyles::>/");
	array_push($replace_array,"");
	array_push($search_array,"/<::modulejs::>/");
	array_push($replace_array,"");
	}


array_push($search_array,"/<::customstyles::>/");
if(!empty($row['entry_custom_css'])){
	array_push($replace_array,"<link rel=\"stylesheet\" type=\"text/css\" href=\"shared/css/custom_styles/".$row['entry_custom_css'].".css\" />");	
}
else{
	array_push($replace_array,"");
}




$output="";
$template=$row['entry_template'];
if(file_exists("shared/templates/".$template)){
	$tmpl="shared/templates/".$template;
	$_GET['tmpl']=$tmpl;
	$output.=implode(" ",file($tmpl));
}

array_push($search_array,"/<::entry_title::>/");
array_push($replace_array,stripslashes($row['entry_name']." | ".$row['entry_title']));

array_push($search_array,"/<::entry_meta_keywords::>/");
array_push($replace_array,stripslashes($row['entry_meta_keywords']));

array_push($search_array,"/<::entry_meta_description::>/");
array_push($replace_array,stripslashes($row['entry_meta_description']));

array_push($search_array,"/<::entry_id::>/");
array_push($replace_array,$_GET['entry_id']);

array_push($search_array,"/<::sub_headline::>/");
if($_SESSION['page_language'] == "en"){
	array_push($replace_array,"One cannot help but love these Gnomes");
}
else{
	array_push($replace_array,"Diese Zwerge muss man lieben");
}

array_push($search_array,"/<::entry_name::>/");
array_push($replace_array,stripslashes($row['entry_navi_name']));

array_push($search_array,"/<::time::>/");
array_push($replace_array,time());

array_push($search_array,"/<::year::>/");
array_push($replace_array,date("Y"));

array_push($search_array,"/<::navi_main::>/");
array_push($replace_array,makeNaviList(0,"mainNaviList"));

array_push($search_array,"/<::navi_desc::>/");
array_push($replace_array,nl2br(str_replace(" ", "&nbsp;", $row['entry_navi_desc'])));

array_push($search_array,"/<::navi_contact::>/");
array_push($replace_array,makeNaviList(3,"contactNaviList"));

array_push($search_array,"/<::switch_language::>/");
array_push($replace_array,showLanguageSwitch());
/*
if(!isset($_REQUEST['page_mode'])){
	array_push($replace_array,makeNaviList(0,"mainNavi"));
}
else{
	array_push($replace_array,"");
}
*/

array_push($search_array,"/<::breadcrumb_navi::>/");
array_push($replace_array,breadCrumbNavi($_GET['entry_id']));

//getSliderImages
array_push($search_array,"/<::slideImages::>/");
array_push($replace_array,slideShow($row['entry_inc_gallery'], "module", "galleries", "", 950));

array_push($search_array,"/<::static_navi::>/");
if(!isset($_REQUEST['page_mode'])){
	array_push($replace_array,makeNaviList(52,"staticNaviList"));
}
else{
	array_push($replace_array,"");
}


for($ca_i=1;$ca_i<=10;$ca_i++){
	array_push($search_array,"/<::content".$ca_i."::>/");
	array_push($replace_array,outputContent("contentarea".$ca_i));
}

array_push($search_array,"/<::box_content::>/");
array_push($replace_array,outputBoxContent());

array_push($search_array,"/<::cart_count::>/");
array_push($replace_array,isset($_SESSION['modCart'])?sizeof($_SESSION['modCart']):"0"); 

// language replacements
foreach($lang as $key=>$val){
	array_push($search_array,"/<::".$key."::>/");	
	array_push($replace_array,isset($val[$_SESSION['page_language']])?$val[$_SESSION['page_language']]:$key); 
}

//page background
array_push($search_array,"/<::page_styles::>/");
if(!empty($row['entry_bg_image'])){
	$css = "<style type=\"text/css\">";
	$css.= "content	{background-image: url(files/".$row['entry_bg_image'].");}";
	$css.= "</style>";
	array_push($replace_array,$css);	
}
else{
	array_push($replace_array,"");	
}

//set page mode -> cms
if(isset($_GET['page_mode']) && $_GET['page_mode']=="edit")
	{
	$output=str_replace("<body>","<body class=\"editable\">",$output);	
	$output=str_replace("</body>","</body>".outputFile("admin/pages/editor/makeEditable.inc.php"),$output);	
	//array_push($replace_array,makeNaviList(1,"</body>".outputFile("admin/pages/editor/makeEditable.inc.php")));
}

//replace elements
$output=preg_replace($search_array,$replace_array,$output);

echo $output;
?>
