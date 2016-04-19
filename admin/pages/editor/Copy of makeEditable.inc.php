<?php
function checkInArray($array,$value){
	foreach($array as $val){
		if(strstr($value,$val)){
			return true;
		}
	}
}
//get contentarea css styles
$css_file="shared/css/styles.css";
$css_file=file($css_file);
$css_array=array();
$open=0;
//allowed properties
$prop_array=array("display","position","top","left","padding","margin","width","height","min-height","padding-top","margin-left");
foreach($css_file as $val){
	if(strstr($val,"#contentarea")){
		$open=1;
		$id=str_replace("{","",$val);
		$id=str_replace("#","",$id);
		$id=trim($id);
		if(strstr($id," ")){
			$open=0;
		}
		else{
			$css_array[$id]="";
		}
		
	}
	if(strstr($val,"}")){
		$open=0;
	}
	if($open==1){
		if($val!="" && !strstr($val,"#contentarea") && checkInArray($prop_array,$val)){
			$rule=str_replace("{","",$val);
			$rule=trim($rule);
			if(!strstr($rule,";")){
				$rule=$rule.";";
			}
			$css_array[$id].=$rule;
		}
	}
}

//inline styles
$css_file=$_GET['tmpl'];
$css_file=file($css_file);
$open=0;
//allowed properties
$prop_array=array("position","top","left","padding","margin","width","height","z-index");
foreach($css_file as $val){
	if(strstr($val,"#contentarea")){
		$open=1;
		$id=str_replace("{","",$val);
		$id=str_replace("#","",$id);
		$id=trim($id);
		if(strstr($id," ")){
			$open=0;
		}
		else{
			$css_array[$id]="";
		}
		
	}
	if(strstr($val,"}")){
		$open=0;
	}
	if($open==1){
		if($val!="" && !strstr($val,"#contentarea") && checkInArray($prop_array,$val)){
			$rule=str_replace("{","",$val);
			$rule=trim($rule);
			if(!strstr($rule,";")){
				$rule=$rule.";";
			}
			$css_array[$id].=$rule;
		}
	}
}

?>
<style type="text/css">
.mceExternalToolbar	{
	top: -28px;
	
}
#centerContent	{
	}

#contentContainer	{
	margin-top: 0px;
}
</style>
<script type="text/javascript" src="admin/shared/javascript/functions.js"></script>
<script type="text/javascript" src="admin/pages/javascript/functions.js"></script>
<script type="text/javascript" src="admin/shared/javascript/tiny_mce/tiny_mce_gzip.js"></script>
<script type="text/javascript">
tinyMCE_GZ.init({
	themes : 'advanced',
	compress: true,
	languages : 'de',
	disk_cache : false,
	debug : false
});
</script>
<script type="text/javascript">
//make array for editor styles
var cssArray=new Array();
<?php
foreach($css_array as $key=>$val){
	echo "cssArray['".$key."']='".$val."';\n";
}
?>
function waitForBody(){
	if(document.body){
		mkEditable(document.body);
	}
	else{
		window.setTimeout("waitForBody()",20);
	}
}

function mkEditable(parent_element){
	for(var i=0;i<parent_element.childNodes.length;i++){
		var tmp=parent_element.childNodes[i];
		if(tmp.nodeType==1){
			if(tmp.childNodes.length>0){
				mkEditable(tmp);
			}
			if(tmp.id && tmp.id.search(/contentarea/)!=-1){
				var str="#"+tmp.id+"_parent {"+cssArray[tmp.id]+"}";
				//SHARED_addStyle(str);
					
				if(SHARED_isIE()==true){
					tmp.onmousedown=new Function("if(typeof(activeId)!='undefined' && activeId!=''){removeControl(activeId);};activeId='"+tmp.id+"';tinyMCE.execCommand('mceAddControl', false, '"+tmp.id+"');");
				}
				else{
					tmp.addEventListener("mousedown", new Function("if(typeof(activeId)!='undefined' && activeId!=''){removeControl(activeId);};activeId='"+tmp.id+"';tinyMCE.execCommand('mceAddControl', false, '"+tmp.id+"');"),false);
				}
				tmp.style.border="1px dotted #ff0000";
			}
		}
	}
}

function myFileBrowser(field_name, url, type, win){
	var fileMngrURL="<?php echo $_SESSION['global_vars']['path_to_root']."admin/filemanager/index.php?".$_SERVER['QUERY_STRING'];?>&mode="+type+"&file_cat1=page&call=tinymce";
	
	tinyMCE.activeEditor.windowManager.open({
        file : fileMngrURL,
        title : 'My File Browser',
        width : 750,  // Your dimensions may differ - toy around with them!
        height : 550,
        resizable : "yes",
        inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
        close_previous : "no"
    }, {
        window : win,
        input : field_name
    });
    return false;
}

function removeControl(){
	if(typeof activeId != "undefined" && activeId!=""){
		tinyMCE.execCommand("mceRemoveControl", false, activeId);
		activeId="";
	}
}

function removeWordFormat(){
	if(typeof activeId != "undefined" && activeId!=""){
		var parent_element=EDITOR_getIframeDoc();
		var text=parent_element.body.innerText || parent_element.body.textContent;
		parent_element.body.innerText = text;
		parent_element.body.innerHTML="Bitte warten..."
		text=text.replace(/\n/gi," ");
		text=text.replace(/\r/gi," ");
		text=escape(text);
		//alert(text);
		var vars="html_text="+text;
		if(text!=""){
			if(xmlHTTP=SHARED_init()){
			var postvars=vars;
			xmlHTTP.open("POST","<?php echo $_SESSION['global_vars']['path_to_root'];?>admin/pages/editor/removeWordFormats.ajax.php",true);
			xmlHTTP.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			xmlHTTP.send(postvars);
			xmlHTTP.onreadystatechange=function()
				{
				if(xmlHTTP.readyState==4)
					{
					//
					var textinhalt = parent_element.body.textContent || parent_element.body.innerText;
					textinhalt=unescape(xmlHTTP.responseText.toString());
					tinyMCE.execCommand('mceCleanup');
					//alert(xmlHTTP.responseText.toString());
					//document.getElementById(target_id).innerHTML=xmlHTTP.responseText;
					}
				}
			}	
		}
	}
}

tinyMCE.init({
		doctype : '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
		mode : "none",
		plugins: "table,paste",
		theme : "advanced",
		popup_css : "",
		auto_resize : true,
		theme_advanced_buttons1 : "mysave,|,bold,italic,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,link,unlink,anchor,|,image,|,formatselect,|,code",
		theme_advanced_buttons2 : "tablecontrols,|,hr,|,pastetext",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "external",
		paste_create_paragraphs : false,
		paste_create_linebreaks : false,
		paste_use_dialog : true,
		paste_auto_cleanup_on_paste : true,
		paste_convert_middot_lists : true,
		paste_unindented_list_class : "unindentedList",
		paste_convert_headers_to_strong : false,
		//paste_insert_word_content_callback : "convertWord",
		language : "de",
		file_browser_callback : "myFileBrowser",
		content_css : "<?php echo $_SESSION['global_vars']['path_to_root']."shared/css/cms.css";?>",
		external_link_list_url : "<?php echo $_SESSION['global_vars']['path_to_root']."admin/shared/javascript/internal_link_list.js.php";?>",
		setup : function(ed) {
        // savebutton
        
		ed.addButton('mysave', {
            title : 'Inhalt übernehmen',
            image : '<?php echo $_SESSION['global_vars']['path_to_root']."admin/shared/images/ready.gif";?>',
            onclick : function() {
                removeControl(activeId);
            }
		});
		ed.addButton('removeWordFormat', {
            title : 'MS Word-Formatierungen entfernen',
            image : '<?php echo $_SESSION['global_vars']['path_to_root']."admin/shared/images/removeWordFormat.gif";?>',
            onclick : function() {
                removeWordFormat();
            }
        });
		
    }
});

waitForBody();
</script>