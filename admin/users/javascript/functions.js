function USERS_checkRole(src_elem,target_elem){
	if(src_elem && document.getElementById(target_elem)){
		if(src_elem.value=="Redakteur"){
			document.getElementById(target_elem).style.display="";	
		}
		else{
			if(src_elem.value=="Administrator"){
				alert("Für diese Benutzergruppe sind alle Aktionen erlaubt. Eine weitere Auswahl der untenstehenden Optionen ist nicht nötig.");	
			}
			document.getElementById(target_elem).style.display="none";		
		}
	}
	else{
		alert("Es ist ein Fehler aufgetreten");	
	}
}

function USERS_deleteUser(entry_id,elem){
	if(confirm("Möchten Sie diesen Benutzer wirklich löschen?")){
		elem.parentNode.removeChild(elem);
		SHARED_makeRequest("../deleteUser.ajax.php","POST","entry_id="+entry_id);
	}	
}

function USERS_submitForm(form_obj,confirm_msg){
	if(document.form0.user_password.value != document.form0.user_password_verify.value){
		alert("Die eingegebenen Passwörter stimmen nicht überein!");	
	}
	else{
		if(confirm(confirm_msg)){
			form_obj.submit();
		}
		else{
			return false;
		}	
	}
	
}

