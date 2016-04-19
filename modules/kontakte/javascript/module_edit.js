// JavaScript Document
function MODULE_deleteEntry(entry_id,elem){
	if(confirm("Möchten Sie diesen Eintrag wirklich löschen?")){
		elem.parentNode.removeChild(elem);
		SHARED_makeRequest("deleteEntry.ajax.php","POST","entry_id="+entry_id);
	}	
}

