// JavaScript Document
function modAccountData(element){
	$("#bankeinzug").hide();
	$("#kreditkarte").hide();
	if(element.value == "bankeinzug"){
		$("#bankeinzug").show(250);	
	}
	if(element.value == "kreditkarte"){
		$("#kreditkarte").show(250);	
	}
}

function modSubmitOrder(){
	
	if($("#widerruf").prop("checked")){
//		location.href = "bestellung_abgeschlossen,18.php?send";	
		document.forms['order'].submit();
	}
	else{
		alert("Bitte bestätigen Sie, dass Sie unsere Widerrufsbelehrung zur Kenntnis genommen haben.");	
	}	
}
