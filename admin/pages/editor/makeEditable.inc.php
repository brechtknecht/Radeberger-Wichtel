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

//get infoboxes
$query = "SELECT * FROM _cms_modules_boxes_ ORDER BY name ASC";
$result_boxes = mysqli_query($_SESSION['conn'], $query);

?>
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
				tmp.style.minHeight="100px";
			}
		}
	}
}

//info boxes
var boxes_obj = new Object();
<?php
if(isset($result_boxes)){
	$boxes_str = "";
	while($row_boxes = mysqli_fetch_assoc($result_boxes)){
		$boxes_str.= "boxes_obj[".$row_boxes['entry_id']."] = {name:\"".stripslashes($row_boxes['name'])."\", html:\"".rawurlencode($row_boxes['html'])."\"};";
		
	}
	echo $boxes_str;
}
?>

var boxC;
if(boxC = document.getElementById("bContainer")){
	$(boxC).css("border","1px dotted #ff0000");
	$(boxC).css("padding-top","30px");
	$(boxC).css("position","relative");
	saveBoxes();
	addBoxesSelect(boxC);
	addContentBoxControls(boxC);
}	

function addBoxesSelect(parent_element){
	if(parent_element){
		var sel = document.createElement("select");
		sel.style.position = "absolute";
		sel.style.left = "0px";
		sel.style.top = "0px";
		sel.style.width = parent_element.offsetWidth + "px";
		var opt = new Option("Box hinzufügen", "", false, false);	
		sel.options[sel.options.length] = opt;
		if(boxes_obj){
			for(var i in boxes_obj){
				var opt = new Option(boxes_obj[i]['name'], i, false, false);	
				//console.log(opt);
				sel.options[sel.options.length] = opt;	
			}	
		}
		sel.onchange = function(){
			addBox(this);	
		}
		
		if($("#bContainer div").length > 0){
			$(sel).insertBefore($("#bContainer div:first-child"));
		}
		else{
			$("#bContainer").append($(sel));	
		}
	}	
}

function addBox(element){
	if(element && boxes_obj[element.value]){
		if(!checkBox(element.value)){
			var div = document.createElement("div");
			div.id = "box_" + element.value;
			div.className = "boxContent";
			div.innerHTML = "<h3>" + boxes_obj[element.value]['name'] +"</h3>";
			div.innerHTML+= decodeURIComponent(boxes_obj[element.value]['html']);	
			document.getElementById("bContainer").appendChild(div);
			addContentBoxControlsElements(div);
		}
		else{
			alert("Die Box wurde bereits hinzugefügt!");	
		}
	}
	element.selectedIndex = 0;
	saveBoxes();
}

function checkBox(id){
	
	if($("#box_" + id).length > 0){
		return true;	
	}
	return false;	
}

function addContentBoxControls(parent_element){
	if(parent_element){
		
		for(var i = 0; i < parent_element.childNodes.length; i++){
			var box = parent_element.childNodes[i];
			if(box.className == "boxContent"){
				addContentBoxControlsElements(box);			
				
			}	
		}	
	}
}

function addContentBoxControlsElements(element){
	if(element){
		var moveUp = document.createElement("img");
		moveUp.src = "modules/infoboxen/images/arrow_up.png";
		moveUp.style.float = "right";
		moveUp.style.position = "absolute";
		moveUp.style.right = "30px";
		moveUp.style.top = "5px";
		moveUp.style.cursor = "pointer";
		moveUp.title = "nach oben verschieben";
		moveUp.onclick = function(){
			moveBox(this.parentNode.parentNode, "up");	
		};
		var moveDown = document.createElement("img");
		moveDown.src = "modules/infoboxen/images/arrow_down.png";
		moveDown.style.float = "right";
		moveDown.style.position = "absolute";
		moveDown.style.right = "20px";
		moveDown.style.top = "5px";
		moveDown.style.cursor = "pointer";
		moveDown.title = "nach unten verschieben";
		moveDown.onclick = function(){
			moveBox(this.parentNode.parentNode, "down");	
		};
		var remBox = document.createElement("img");
		remBox.src = "modules/infoboxen/images/page_delete.png";
		remBox.style.float = "right";
		remBox.style.position = "absolute";
		remBox.style.right = "5px";
		remBox.style.top = "5px";
		remBox.style.cursor = "pointer";
		remBox.title = "Box entfernen";
		remBox.onclick = function(){
			removeBox(this.parentNode.parentNode);
		};
		
		$("#" + element.id + " h3:first-child").css("position", "relative");
						
		$("#" + element.id + " h3:first-child").append(moveUp);
		$("#" + element.id + " h3:first-child").append(moveDown);
		$("#" + element.id + " h3:first-child").append(remBox);
	}	
}

function removeBox(element){
	if(element){
		$(element).remove();
		//element.parentNode.removeChild(element);
	}
	saveBoxes();
}

function moveBox(element, dir){
	if(element){
		if(dir == "down"){
			if($(element).attr("id") != $("#bContainer").find("div").last().attr("id")){
				$(element).insertAfter($(element).next());		
			}
		}	
		if(dir == "up"){
			//console.log($(element));		
			if($(element).attr("id") != $("#bContainer").find("div").first().attr("id")){
				$(element).insertBefore($(element).prev());	
			}
		}	
	}
	saveBoxes();
}

function saveBoxes(){
	if($("#zeemes_boxes").length == 1){
		$("#zeemes_boxes").remove();	
	}
	var zeemes_boxes = document.createElement("input");	
	zeemes_boxes.type = "hidden";
	zeemes_boxes.id = "zeemes_boxes";
	zeemes_boxes.value = "";
	
	$("#bContainer > div").each(function(index, element) {
        zeemes_boxes.value+= element.id.split("_")[1] + "|";
    });
	
	document.body.appendChild(zeemes_boxes);
} 

function myFileBrowser(field_name, url, type, win){
	var fileMngrURL="<?php echo $_SESSION['global_vars']['path_to_root']."admin/filemanager/index.php?".$_SERVER['QUERY_STRING'];?>&mode="+type+"&file_cat1=page&call=tinymce&max_width=1000";
	
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
		plugins: "table, paste, googlemaps, advimage",
		theme : "advanced",
		popup_css : "",
		auto_resize : true,
		theme_advanced_buttons1 : "mysave,|,bold,italic,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,link,unlink,anchor,|,image,|,formatselect,styleselect,|,code",
		theme_advanced_buttons2 : "tablecontrols,|,hr,|,pastetext",
		theme_advanced_buttons3 : "",
		theme_advanced_blockformats : "p,h4, h5",
		theme_advanced_toolbar_location : "top",
		paste_text_use_dialog : true,
		paste_create_paragraphs : false,
		paste_create_linebreaks : false,
		paste_auto_cleanup_on_paste : true,
		paste_convert_middot_lists : true,
		paste_unindented_list_class : "unindentedList",
		paste_convert_headers_to_strong : false,
		language : "de",
		file_browser_callback : "myFileBrowser",
		content_css : "<?php echo $_SESSION['global_vars']['path_to_root']."admin/shared/css/cms.css";?>",
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