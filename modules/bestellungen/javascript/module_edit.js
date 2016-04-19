// JavaScript Document
function MODULE_deleteEntry(entry_id,elem){
	if(confirm("Möchten Sie diesen Eintrag wirklich löschen?")){
		elem.parentNode.removeChild(elem);
		SHARED_makeRequest("deleteEntry.ajax.php","POST","entry_id="+entry_id);
	}	
}

function MODULE_hideEntry(entry_id,elem){
	if(confirm("Möchten die Überprüfung auf Mehrfachbestellung für diesen Eintrag deaktivieren?")){
		elem.parentNode.removeChild(elem);
		SHARED_makeRequest("hideEntry.ajax.php","POST","entry_id="+entry_id);
		window.setTimeout("location.reload()",250);
	}	
}

function MODULE_showEntry(entry_id,elem){
	if(confirm("Möchten die Überprüfung auf Mehrfachbestellung für diesen Eintrag aktivieren?")){
		//elem.parentNode.removeChild(elem);
		SHARED_makeRequest("showEntry.ajax.php","POST","entry_id="+entry_id);
		window.setTimeout("location.reload()",250);
	}	
}

function MODULE_showDuplicates(target){
	location.href = target;
	/*
	var table = document.getElementById("new_listTable");
	for(var i=0;i<table.getElementsByTagName("tr").length;i++){
		var tmp = table.getElementsByTagName("tr")[i];
		if(tmp.className && tmp.className != "alarm"){
			tmp.style.display = "none";	
		}
	}
	document.getElementById("showDuplicates").innerHTML = "Alle anzeigen";
	document.getElementById("showDuplicates").onclick = function(){
		MODULE_showAll();	
	}
	*/

}

function MODULE_showAll(){
	location.reload();
	/*
	var table = document.getElementById("new_listTable");
	for(var i=0;i<table.getElementsByTagName("tr").length;i++){
		var tmp = table.getElementsByTagName("tr")[i];
		tmp.style.display = "";	
	}
	document.getElementById("showDuplicates").innerHTML = "Mehrfachbestellungen anzeigen";
	document.getElementById("showDuplicates").onclick = function(){
		MODULE_showDuplicates();	
	}
	*/
}

