// JavaScript Document
function MODULE_deleteEntry(entry_id,elem){
	if(confirm("Möchten Sie diese Galerie und alle in ihr enthaltenen Bilder wirklich löschen?")){
		elem.parentNode.removeChild(elem);
		SHARED_makeRequest("deleteEntry.ajax.php","POST","entry_id="+entry_id);
	}	
}

