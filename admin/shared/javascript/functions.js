// JavaScript Document
function SHARED_init()
	{
	if(window.XMLHttpRequest) 
		{
		var xmlHTTP = new XMLHttpRequest();
		} 
	else if(window.ActiveXObject) 
			{
			var xmlHTTP = new ActiveXObject("Microsoft.XMLHTTP");
			}	
	if(xmlHTTP)
		{
		return xmlHTTP;	
		}
	else
		{
		return false;	
		}
	}
	
function SHARED_isIE(){
	if(document.all){
		return true;	
	}	
	else{
		return false;	
	}
}

cat_id=0;
function SHARED_showChilds(entry)
	{
	if(entry.childNodes.length>0)
		{
		for(var i=0;i<entry.childNodes.length;i++)
			{
			if(entry.childNodes[i].nodeName=="UL")
				{
				if(!entry.childNodes[i].style.display || entry.childNodes[i].style.display=="none")
					{
					entry.childNodes[i].style.display="block";	
					}
				else
					{
					entry.childNodes[i].style.display="none"	
					}		
				}
			}
		}
	}
	
function SHARED_setEntryActive(entry,id)
	{
	for(var i=0;i<document.getElementsByTagName("span").length;i++)
		{
		var tmp=document.getElementsByTagName("span")[i];
		if(tmp.className.indexOf("Active")>-1)
			{
			tmp.className=tmp.className.replace(/Active/,"");	
			}
		}
	entry.className=entry.className+"Active";
	cat_id=id;
	if(document.getElementById("entry_tools"))
		{
		document.getElementById("entry_tools").style.display="block";
		}
	}	
	
function SHARED_getBody(w){
    return (w.document.compatMode && w.document.compatMode == "CSS1Compat") ? w.document.documentElement : w.document.body || null;
} 
	
function SHARED_setElementsHeight()
	{
	var avail_height=window.innerHeight?window.innerHeight:SHARED_getBody(window).clientHeight;
	var toolBoxHeight=document.getElementById("toolBox") && document.getElementById("toolBox").offsetHeight?document.getElementById("toolBox").offsetHeight:0;
	var content_height=avail_height-document.getElementsByTagName("h1")[0].offsetHeight-toolBoxHeight;
	if(document.getElementById("content"))
		{
		document.getElementById("content").style.height=content_height+"px";	
		}
	if(document.getElementById("navigation"))
		{
		document.getElementById("navigation").style.height=content_height+"px";	
		}
	}
	
function SHARED_setInnerHTML(src_file,target_id,method,vars,func)
	{
	
	if(xmlHTTP=SHARED_init())
		{
		if(document.getElementById(target_id))
			{
			//document.getElementById(target_id).innerHTML="<img src=\"shared/images/load_content.gif\" alt=\"\" />";
			document.getElementById(target_id).innerHTML="<span style=\"padding: 20px;display:block\">Daten werden übertragen...</span>";
			var postvars=vars;
			xmlHTTP.open(method,src_file,true);
			
			xmlHTTP.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			xmlHTTP.send(postvars);
			xmlHTTP.onreadystatechange=function()
				{
				if(xmlHTTP.readyState==4)
					{
					
					var div=document.createElement("div");
					div.innerHTML=xmlHTTP.responseText;
					document.getElementById(target_id).innerHTML="";
					document.getElementById(target_id).appendChild(div);
					if(func && func!="")
						{
						eval(func);	
						}
					}
				}	
			}
		}	
	}


function SHARED_makeRequest(src_file,method,vars)
	{
	if(xmlHTTP=SHARED_init())
		{
		var postvars=vars;
		xmlHTTP.open(method,src_file,true);
		xmlHTTP.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		xmlHTTP.send(postvars);
		xmlHTTP.onreadystatechange=function()
			{
			if(xmlHTTP.readyState==4)
				{
				//
				//alert(xmlHTTP.responseText.toString());
				//return xmlHTTP.responseText.toString();
				//document.getElementById(target_id).innerHTML=xmlHTTP.responseText;
				}
			}
		}	
	}
	
function SHARED_getFormValues()
	{
	var vars="";
	for(var i=0;i<document.getElementsByTagName("input").length;i++)
		{
		var tmp=element.childNodes[i];
		vars+=tmp.name+"="+tmp.value+"&";
		}
	for(var i=0;i<document.getElementsByTagName("textarea").length;i++)
		{
		var tmp=element.childNodes[i];
		vars+=tmp.name+"="+tmp.value+"&";
		}
	for(var i=0;i<document.getElementsByTagName("select").length;i++)
		{
		var tmp=element.childNodes[i];
		vars+=tmp.name+"="+tmp.value+"&";
		}
	alert(vars);
	}
	
function SHARED_submitForm(form_obj,confirm_msg)
	{
	if(confirm(confirm_msg))
		{
		form_obj.submit();
		}
	else
		{
		return false;
		}
	}
	
function SHARED_getFormVars(parent_obj)
	{
	if(parent_obj && parent_obj.childNodes.length>0)
		{
		for(var i=0;i<parent_obj.childNodes.length;i++)
			{
			var tmp=parent_obj.childNodes[i];
			if(tmp.nodeName=="INPUT" || tmp.nodeName=="TEXTAREA" || tmp.nodeName=="SELECT")
				{
				if(tmp.nodeName=="INPUT" && (tmp.type=="checkbox" || tmp.type=="radio"))
					{
					if(tmp.checked==true)
						{
						var_string+="&"+tmp.name+"="+tmp.value;		
						}
					}
				else
					{
					if(tmp.nodeName=="SELECT" && tmp.multiple)
						{
						var t=0;
						for(var z=0;z<tmp.options.length;z++)
							{
							if(tmp.options[z].selected==true)
								{
								var_string+="&"+tmp.name+t+"="+tmp.options[z].value;	
								t++;
								}
							}
						}
					else
						{
						var_string+="&"+tmp.name+"="+tmp.value;		
						}
					}
				
				
				}
			if(tmp.childNodes.length>0)
				{
				SHARED_getFormVars(tmp);	
				}
			}
		}
	return var_string;
	}

function SHARED_getSelectMultipleValues(select_obj)
	{
	var str="|";
	for(var i=0;i<select_obj.options.length;i++)
		{
		if(select_obj.options[i].selected==true && select_obj.options[i].value!="")
			{
			str+=select_obj.options[i].value+"|";
			}	
		}
	return str;
	}

function SHARED_showFieldSetSwitch(id)
	{
	
	if(document.getElementById(id))
		{
		var ul=document.createElement("ul");
		ul.id="switchFormList";
		var form=document.getElementById(id);
		for(var i=0;i<form.getElementsByTagName("fieldset").length;i++)
			{
			var fs=form.getElementsByTagName("fieldset")[i];
			if(fs.id)
				{
				var li=document.createElement("li");
				var span=document.createElement("span");
				span.id="switch_"+fs.id;
				span.appendChild(document.createTextNode(fs.id));
				if(i>0)
					{
					fs.style.display="none";	
					span.style.borderBottomColor="#FFFFFF";
					span.style.color="#333333";
					span.style.fontWeight="normal";
					}
				span.onclick=new Function("SHARED_switchFieldSet('"+fs.id+"','"+id+"','"+span.id+"')");
				li.appendChild(span);
				ul.appendChild(li);
				}
			
			}	
		form.parentNode.insertBefore(ul,form);
		
		}
	else
		{
		window.setTimeout("SHARED_showFieldSetSwitch('"+id+"')",10);	
		}
	}
	
function SHARED_switchFieldSet(fs_id,form_id,own_id)
	{
	if(document.getElementById(fs_id) && document.getElementById(form_id) && document.getElementById(own_id))
		{
		var form=document.getElementById(form_id);
		for(var i=0;i<form.getElementsByTagName("fieldset").length;i++)	
			{
			var fs=form.getElementsByTagName("fieldset")[i];	
			if(fs.id)
				{
				fs.style.display="none";	
				}
			}
		document.getElementById(fs_id).style.display="block";
		var parent_ul=document.getElementById(own_id).parentNode.parentNode;
		for(var i=0;i<parent_ul.childNodes.length;i++)
			{
			var tmp=parent_ul.childNodes[i].firstChild;
			tmp.style.borderBottomColor="#FFFFFF";
			tmp.style.color="#000000";
			tmp.style.fontWeight="normal";
			}
		document.getElementById(own_id).style.borderBottomColor="#999999";
		document.getElementById(own_id).style.color="";
		document.getElementById(own_id).style.fontWeight="bold";
		}
	}
	
function SHARED_setFocus(id)
	{
	if(document.getElementById(id))
		{
		document.getElementById(id).focus();
		}
	}
	
function SHARED_scrollTBody(table_id,scroll_height,scroll_top){
	var table;
	if(table=document.getElementById(table_id)){
		var newTable=table.cloneNode(true);
		newTable.id="new_"+table_id;
		var tBody=table.getElementsByTagName("tbody")[0];
		var tHead=newTable.getElementsByTagName("thead")[0];
		
		for(var z=0;z<tHead.childNodes.length;z++){
			var tmp=tHead.childNodes[z];
			if(tmp.nodeType==1){
				for(var i=0;i<tmp.childNodes.length;i++){
					if(tmp.childNodes[i].nodeName=="TH"){
						tmp.childNodes[i].innerHTML="";
						tmp.childNodes[i].style.fontSize="1px";
						tmp.childNodes[i].style.paddingTop="0";
						tmp.childNodes[i].style.paddingBottom="0";
						tmp.childNodes[i].style.visibility="hidden";
					}
				}	
			}	
		}
		
		
		
		
		table.removeChild(tBody);
		table.appendChild(document.createElement("tbody"));
		tHead.style.visibility="hidden";
								
		var scrollContainer=document.createElement("div");
		scrollContainer.id="scrollContainer";
		scrollContainer.style.height=scroll_height+"px";
		scrollContainer.style.overflow="auto";
		scrollContainer.appendChild(newTable);
		table.parentNode.appendChild(scrollContainer);
		document.getElementById("new_"+table_id).style.width=document.getElementById("scrollContainer").offsetWidth-20+"px";
		if(scroll_top>0){
			document.getElementById("scrollContainer").scrollTop=scroll_top;
		}
		
	}
}

function SHARED_getNextElement(element){
	if(element.nextSibling.nodeType!=1){
		return SHARED_getNextElement(element.nextSibling);	
	}
	else{
		return element.nextSibling;
	}
}

function SHARED_getPreviousElement(element){
	if(element.previousSibling.nodeType!=1){
		return SHARED_getPreviousElement(element.previousSibling);	
	}
	else{
		return element.previousSibling;
	}
}

function SHARED_getFirstChild(element){
	if(element.nodeType!=1){
		return SHARED_getFirstChild(element.nextSibling);	
	}
	else{
		return element;
	}
}

function SHARED_getLastChild(element){
	if(element.nodeType!=1){
		return SHARED_getLastChild(element.previousSibling);	
	}
	else{
		return element;
	}
}

function SHARED_getAvailHeight(element,ignore){
	if(ignore && document.getElementById(ignore)){
		var theadHeight=document.getElementById(ignore).getElementsByTagName("thead")[0].offsetHeight;	
	}
	else{
		var theadHeight=0;
	}
	if(!ignore){
		var ignore="";	
	}
	
	if(element && element.offsetHeight){
		var avail_height=element.offsetHeight;
		for(var i=0;i<element.childNodes.length;i++){
			var tmp=element.childNodes[i];
			
			if(tmp.nodeType==1 && tmp.offsetHeight){
				if(!tmp.id || (tmp.id && tmp.id!=ignore)){
					avail_height-=tmp.offsetHeight;
				}
			}	
		}
		avail_height-=theadHeight;
		return avail_height;
	}
	else{
		alert("getAvailHeight('"+element+"'): Fehler");
		return 0;
	}
}

/* 'Stylesheet/Regeln hinzufuegen 260907' (c) cybaer@binon.net - http://Coding.binon.net/AddStyle */
/* Lizenz CC <http://creativecommons.org/licenses/by-nc-sa/2.5/> */
function SHARED_addStyle(rules,target) {
 var styleObj=null, styleSheetObj=null, i, j, p, selector, singleSelector, text;
 if(document.createElement && document.getElementsByTagName) {
  if(typeof(target)=="number") {
   if(target<=-1) { target=document.getElementsByTagName("style").length+Math.ceil(target); }
   target=Math.max(0,Math.min(document.getElementsByTagName("style").length-1,Math.floor(target)));
  }
  if(typeof(target)=="undefined" || typeof(target)=="string" || !document.getElementsByTagName("style")[target]) {
   if(document.createStyleSheet) {
    styleSheetObj=document.createStyleSheet();
    styleObj=styleSheetObj.owningElement || styleSheetObj.ownerNode;
   } else {
    styleObj=document.createElement("style");
    document.getElementsByTagName("head")[0].appendChild(styleObj);
   }
   styleObj.setAttribute("type","text/css");
   if(target) { styleObj.setAttribute("media",target); }
  } else if(typeof(target)=="number") {
   styleObj=document.getElementsByTagName("style")[target];
   styleSheetObj=styleObj.sheet || styleObj.styleSheet;
  }
  if(styleObj && rules) {
   /*@cc_on
   @if(@_jscript)
    rule=rules.replace(/\s+/g," ").replace(/\/\*.+?\*\//g,"").split("}");
    for(i=0;i<rule.length;i++) {
     p=rule[i].indexOf("{");
     selector=rule[i].substring(0,p).replace(/^\s+|\s+$/g,"");
     text=rule[i].substring(p+1).replace(/^\s+|\s+$/g,"");
     if(selector) {
      if(selector.indexOf(",")) {
       singleSelector=selector.split(",");
      } else {
       singleSelector=new Array(selector);
      }
      for(j=0;j<singleSelector.length;j++) { styleSheetObj.addRule(singleSelector[j].replace(/^\s+|\s+$/g,""),(text)?text:" "); }
     }
    }
   @else @*/
    if(styleObj.firstChild) { styleObj.firstChild.nodeValue=styleObj.firstChild.nodeValue.replace("<!--",""); }
    if(styleObj.lastChild) { styleObj.lastChild.nodeValue=styleObj.lastChild.nodeValue.replace("-->",""); }
    styleObj.appendChild(document.createTextNode(rules+"\n"));
   /*@end @*/
  }
 }
 return styleObj;
}

