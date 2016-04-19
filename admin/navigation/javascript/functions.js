// JavaScript Document
var activeElem="";
/*
function NAVIGATION_setActive(elem){
	//alert("hn");
	if(activeElem!=""){
		activeElem.style.backgroundColor="";
		activeElem.style.color="";
	}
	if(elem){
		var naviLink=elem.getElementsByTagName("A")[0];
		naviLink.style.backgroundColor="#990000";
		naviLink.style.color="#FFFFFF";
		activeElem=naviLink;
	}
}

function addFunction(list){
	for(var i=0;i<document.getElementsByTagName("LI").length;i++){
		var tmp=document.getElementsByTagName("LI")[i];
		if(tmp.getElementsByTagName("UL").length==0){
			tmp.onmouseup=function(){
				NAVIGATION_setActive(this);	
			}
		}	
	}	
}

var naviState="visible";
function showHideNavi(){
	var switchNavi=document.getElementById("switchNavi");
	if(naviState=="visible"){
		switchNavi.src="images/open.gif";
		switchNavi.title="Navigation einblenden";
		parent.document.getElementsByTagName("frameset")[1].cols="30,*";
		document.getElementById("mainNaviList").style.display="none";
		naviState="hidden";
	}	
	else{
		switchNavi.src="images/close.gif";
		switchNavi.title="Navigation ausblenden";
		parent.document.getElementsByTagName("frameset")[1].cols="250,*";	
		document.getElementById("mainNaviList").style.display="";
		naviState="visible";
	}
}
*/
// JavaScript Document
var activeElem="";
function NAVIGATION_setActive(elem){
	if(activeElem!=""){
		activeElem.style.backgroundColor="";
		activeElem.style.color="";
	}
	if(elem){
		//var naviLink=elem.getElementsByTagName("A")[0];
		var naviLink=elem;
		naviLink.style.backgroundColor="#990000";
		naviLink.style.color="#FFFFFF";
		activeElem=naviLink;
	}
}

function addFunction(list){
	for(var i=0;i<document.getElementsByTagName("a").length;i++){
		var tmp=document.getElementsByTagName("a")[i];
		//if(tmp.getElementsByTagName("UL").length==0)
		{
			tmp.onmouseup=function(){
				NAVIGATION_setActive(this);	
			}
		}	
	}	
}

var naviState="visible";
function showHideNavi(){
	var switchNavi=document.getElementById("switchNavi");
	if(naviState=="visible"){
		switchNavi.src="images/open.gif";
		switchNavi.title="Navigation einblenden";
		parent.document.getElementsByTagName("frameset")[1].cols="30,*";
		document.getElementById("mainNaviList").style.display="none";
		naviState="hidden";
	}	
	else{
		switchNavi.src="images/close.gif";
		switchNavi.title="Navigation ausblenden";
		parent.document.getElementsByTagName("frameset")[1].cols="250,*";	
		document.getElementById("mainNaviList").style.display="";
		naviState="visible";
	}
}
