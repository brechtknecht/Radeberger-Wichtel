// JavaScript Document
//global vars
var activeArea="";
function EDITOR_setHeight(){
	document.getElementById("editorFrame").style.height=SHARED_getAvailHeight(document.body)+"px";
}

function EDITOR_mkEditable(parent_element){
	for(var i=0;i<parent_element.childNodes.length;i++){
		var tmp=parent_element.childNodes[i];
		if(tmp.nodeType==1){
			if(tmp.childNodes.length>0){
				EDITOR_mkEditable(tmp);
			}
			if(tmp.id && tmp.id.search(/contentarea/)!=-1){
				if(SHARED_isIE()==true){
					tmp.contentEditable=true;	
					tmp.onclick=new Function("activeArea='"+tmp.id+"';");
				}
				else{
					tmp.addEventListener("click", new Function("activeArea='"+tmp.id+"';"),false);
					
				}
				tmp.style.border="1px dotted #ff0000";
			}
		}
	}
}

function EDITOR_getIframeDoc(){
	var parent_element=false;
	var container=document.getElementsByTagName("iframe")[0];
	if(container.contentWindow){
		parent_element=container.contentWindow.document;
	}
	if(container.contentDocument){
		parent_element=container.contentDocument;
	}
	if(!container.contentWindow && container.document) {
        parent_element=container.document;
    }
	return parent_element;
}

function EDITOR_saveContent(publish){
	var parent_element=EDITOR_getIframeDoc();
	
	window.frames['editorFrame'].removeControl();	
	
	if(confirm("Änderungen übernehmen?")){
		var content_str="";
		
		
		if(parent_element!=false){
			//parent_element=parent_element.body;
			for(var i=0;i<20;i++){
				if(parent_element.getElementById("contentarea"+i)){
					var tmp=parent_element.getElementById("contentarea"+i);
					/*
					content_str+=tmp.id+"#";
					content_str+=tmp.innerHTML;
					content_str+="||";
					*/
					var input = document.createElement("input");
					input.type = "hidden";
					input.name = tmp.id;
					input.value = tmp.innerHTML;
					document.saveForm.appendChild(input);
					
				}
			}		
			document.saveForm.content.value=content_str;
			document.saveForm.publish.value=publish;
			
			//boxes
			//alert(document.saveForm.contentarea1.value);
			document.saveForm.boxes.value = parent_element.getElementById("zeemes_boxes")?parent_element.getElementById("zeemes_boxes").value:"";
			
			document.saveForm.submit();
		}
		else{
			alert("Fehler: EDITOR_saveContent");	
		}
	}
}

function EDITOR_publishPage(entry_id){
	if(entry_id){
		alert(entry_id);	
	}	
}

function EDITOR_loadPageVersion(entry_id,page_version_id){
	if(entry_id && page_version_id){
		location.href="index.php?entry_id="+entry_id+"&page_version_id="+page_version_id;
	}	
}

function EDITOR_getActiveSource(){
	var parent_element=EDITOR_getIframeDoc();
	if(parent_element!=false){
		if(parent_element.getElementById(activeArea)){
			return parent_element.getElementById(activeArea);	
		}	
	}
}

function ORGANIZE_setFieldValue(form,fieldName,newValue,empty){
	if(form){
		for(var i=0;i<form.elements.length;i++){
			if(form.elements[i].name && form.elements[i].name.indexOf(fieldName)!=-1){
				if(empty==true){
					if(form.elements[i].value==""){
						form.elements[i].value=newValue;	
					}	
				}
				else{
					form.elements[i].value=newValue;	
				}
				
			}	
		}	
	}
}

function ORGANIZE_deletePage(entry_id,elem){
	if(confirm("Möchten Sie diese Seite inklusive aller Unterseiten wirklich löschen?")){
		elem.parentNode.removeChild(elem);
		SHARED_makeRequest("organize/deletePage.ajax.php","POST","entry_id="+entry_id);
	}	
}

