// JavaScript Document
var activeFile=0;
function FILEMANAGER_loadFileInfo(entry_id,src,file_real_name,file_size,last_change,file_save_name){
	
	
	if(entry_id!=""){
		document.getElementById("entry_id").value=entry_id;
		document.getElementById("file_save_name").value=file_save_name;
		document.getElementById("src").value=src;
		document.getElementById("file_save_name").innerHTML=src;
		document.getElementById("file_real_name").innerHTML=file_real_name;	
		document.getElementById("file_size").innerHTML=file_size;	
		document.getElementById("last_change").innerHTML=last_change;
		//descriptions
		for(var i=0;i<languageArray.length;i++){
			if(document.getElementById("file_description_"+languageArray[i])){
				document.getElementById("file_description_"+languageArray[i]).value=descArray[entry_id][languageArray[i]];	
			}	
		}
		if(document.getElementById("selectFile")){
			document.getElementById("selectFile").style.display="";		
		}
		document.getElementById("deleteFile").style.display="";	
		document.getElementById("file_description_row").style.display="";
		if(document.getElementById("file_sequence_row")){
			document.getElementById("file_sequence_row").style.display="";
		}
		
	}
	else{
		if(document.getElementById("selectFile")){
			document.getElementById("selectFile").style.display="none";		
		}
		document.getElementById("deleteFile").style.display="";	
		document.getElementById("file_description_row").style.display="none";
		if(document.getElementById("file_sequence_row")){
			document.getElementById("file_sequence_row").style.display="none";
		}
	}
	FILEMANAGER_makeSequenceList();
	if(activeFile!=0 && document.getElementById(activeFile)){
		document.getElementById(activeFile).style.backgroundColor="";		
	}
	if(entry_id!="" && document.getElementById(entry_id)){
		document.getElementById(entry_id).style.backgroundColor="#990000";
		activeFile=entry_id;	
	}
}

function FILEMANAGER_deleteFile(file_id){
	if(file_id && confirm("Möchten Sie diese Datei wirklich löschen?")){
		var success=SHARED_makeRequest("delete.php","post","entry_id="+file_id);
		document.getElementById(file_id).parentNode.removeChild(document.getElementById(file_id));	
		activeFile=0;
		FILEMANAGER_loadFileInfo("","","","","","");
	}	
}

function FILEMANAGER_makeSequenceList(){
	if(document.getElementById("file_sequence_row") ){
		var fileList=document.getElementById("fileList");
		var sequenceList=document.getElementById("file_sequence");
		sequenceList.options.length=0;
			
		var newOpt=new Option("bitte Bezug wählen","");
		document.getElementById("file_sequence").options[document.getElementById("file_sequence").options.length]=newOpt;
		for(var i=0;i<fileList.childNodes.length;i++){
			var tmp=fileList.childNodes[i];
			if(tmp.nodeType==1 && tmp.id && tmp.id!=document.getElementById("entry_id").value){
				for(var z=0;z<tmp.childNodes.length;z++){
					if(tmp.childNodes[z].className=="fileName"){
						var fileName=tmp.childNodes[z].innerHTML;	
					}	
				}
				newOpt=new Option(fileName,tmp.id);
				sequenceList.options[sequenceList.options.length]=newOpt;
			}	
		}		
	}
	
}

function FILEMANAGER_changeSequence(entry,target_entry,mode){
	if(!mode || mode==""){
		alert("Bitte wählen Sie eine Einfügeoption aus!");	
	}
	else{
		var fileList=document.getElementById("fileList");
		var moveEntry=document.getElementById(entry).cloneNode(true);
		fileList.removeChild(document.getElementById(entry));
		//get target entry
		for(var i=0;i<fileList.childNodes.length;i++){
			var tmp=fileList.childNodes[i];
			if(tmp.nodeType==1 && tmp.id && tmp.id==target_entry){
				var targetNode=i;	
				if(mode=="before"){
					if(targetNode==0){
						targetNode=fileList.childNodes.length-1;
					}
					fileList.insertBefore(moveEntry,fileList.childNodes[targetNode]);
				}
				if(mode=="behind"){
					if(targetNode==fileList.childNodes.length-1){
						targetNode=0;	
					}
					fileList.insertBefore(moveEntry,fileList.childNodes[targetNode+1]);
				}
			}
		}
		//make ajax request
		var sequence="";
		for(var i=0;i<fileList.childNodes.length;i++){
			var tmp=fileList.childNodes[i];
			if(tmp.nodeType==1 && tmp.id){
				sequence+=tmp.id+"|";	
			}
		}
		SHARED_makeRequest("updateFileSequence.ajax.php","post","sequence="+sequence);
	}
}

function FILEMANAGER_updateDescription(entry_id,desc,language){
	descArray[entry_id][language]=desc;
	SHARED_makeRequest("updateFileDesc.ajax.php","post","entry_id="+entry_id+"&desc="+desc+"&language="+language);
}