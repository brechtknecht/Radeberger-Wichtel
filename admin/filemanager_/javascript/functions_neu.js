function showFileInfo(fileObj){
    var fileList;
    //alle listenpunkte deaktivieren
    if(fileList = document.getElementById("fileList")){
		for(var i = 0; i < fileList.getElementsByTagName("li").length; i++){
			var li = fileList.getElementsByTagName("li")[i];
			li.className = "";
		}
    }
    //angeklickten eintrag auf aktiv setzen
    fileObj.className = "fileActive";
    var outStr = "<h2>Dateieigenschaften</h2>";
    outStr+= "<p>";
    outStr+= "Dateiname: " + imgArray[fileObj.id]['file_real_name'] + "<br />";
    outStr+= "gespeichert unter: <a href=\"../../files/" + imgArray[fileObj.id]['file_save_name'] + "\" target=\"_blank\">" + imgArray[fileObj.id]['file_save_name'] + "</a><br />";
    outStr+= "Dateigröße: " + parseFloat(imgArray[fileObj.id]['file_size']).toFixed(2) + " MByte<br />";
    if(imgArray[fileObj.id]['file_type'] == "image"){
		outStr+= "Abmessungen: " + imgArray[fileObj.id]['file_img_width'] + " x " + imgArray[fileObj.id]['file_img_height'] + " px<br />";
    }
    outStr+= "</p>";
	//description texts
	outStr+= showFileDesc(fileObj);
	//sequence
    outStr+= showImageSequence(fileObj);
	//file actions (delete, change)
    outStr+= showFileActions(fileObj);
	
    
    if(imgArray[fileObj.id]['file_type'] == "image"){
		//outStr+= showImageActions(fileObj);
    }
	
	
    document.getElementById("fileActions").innerHTML = outStr;
}

function showFileActions(fileObj){
    var outStr = "";
    outStr+= "<h2>Dateiaktionen</h2>";
    outStr+= "<ul>";
    /*
	outStr+= "<li><img src=\"images/refresh.png\" alt=\"\" title=\"Datei austauschen\" onclick=\"changeFile('" + fileObj.id + "')\" /></li>";
    outStr+= "<li><img src=\"images/delete.png\" alt=\"\" title=\"Datei löschen\" onclick=\"deleteFile('" + fileObj.id + "')\" /></li>";
	*/
	outStr+= "<li><button type=\"button\" onclick=\"changeFile('" + fileObj.id + "')\">Datei austauschen</button></li>";
    outStr+= "<li><button type=\"button\" onclick=\"deleteFile('" + fileObj.id + "')\">Datei löschen</button></li>";
    if(call == "tinymce"){
		//outStr+= "<li><img src=\"images/accept.png\" alt=\"\" title=\"Übernehmen\" onclick=\"FileBrowserDialogue.mySubmit('" + imgArray[fileObj.id]['file_save_name'] + "', '" + imgArray[fileObj.id]['desc_de']  + "')\" /></li>";
		outStr+= "<li><button type=\"button\" onclick=\"FileBrowserDialogue.mySubmit('" + imgArray[fileObj.id]['file_save_name'] + "', '" + imgArray[fileObj.id]['desc_de']  + "')\">Übernehmen</button></li>";
	}
	outStr+= "</ul>";
    return outStr;
}

function showImageSequence(fileObj){
    var outStr = "";
    outStr+= "<h2>Reihenfolge ändern</h2>";
    outStr+= "<ul>";
    
	outStr+= "<li><select id=\"sequenceMode\">";
	outStr+= "<option value=\"before\">einfügen vor</option>";
	outStr+= "<option value=\"behind\">einfügen nach</option>";
	outStr+= "</select></li>";
    outStr+= "<li><select id=\"sequenceTarget\"";
	outStr+= " onchange=\"changeImageSequence('" + fileObj.id + "', this.value, document.getElementById('sequenceMode').value)\">";
	outStr+= "<option value=\"\">Bild auswählen</option>";
	var fileList;
	if(fileList = document.getElementById("fileList")){
		for(var i = 0; i < fileList.getElementsByTagName("li").length; i++){
			var li = fileList.getElementsByTagName("li")[i];
			if(fileObj.id != li.id){
				outStr+= "<option value=\"" + li.id + "\">" + imgArray[li.id]['file_real_name'] + "</option>";
			}
		}
    }
	outStr+= "</select></li>";
    
	outStr+= "</ul>";
    return outStr;
}

function changeImageSequence(fileId, targetId, mode){
	var moveObj, targetObj, parentObj;
	if(moveObj = document.getElementById(fileId)){
		if(targetObj = document.getElementById(targetId)){
			parentObj = moveObj.parentNode;
			//clonemoveObj
			var clone = moveObj.cloneNode(true);
			parentObj.removeChild(moveObj);
			if(mode == "before"){
				parentObj.insertBefore(clone, targetObj);	
				
			}
			if(mode == "behind"){
				if(targetObj.id != parentObj.getElementsByTagName("li")[parentObj.getElementsByTagName("li").length-1].id){
					parentObj.insertBefore(clone, targetObj.nextSibling);	
				}
				else{
					parentObj.insertBefore(clone, targetObj);	
					parentObj.insertBefore(targetObj, clone);	
					
				}
				
			}
			
			clone.onclick = function(){
				showFileInfo(this);		
			};
			
			//save changes
			var sequence = "";
			for(var i = 0; i < parentObj.getElementsByTagName("li").length;i++){
				var tmp = fileList.getElementsByTagName("li")[i];
				var id = tmp.id.split("_");
				sequence+= id[1] + "|";	
				
			}
			SHARED_makeRequest("updateFileSequence.ajax.php","post","sequence=" + sequence);
			
		}
	}
}


function changeFile(fileId){
    //flash uploader
    insertFlash("flash/upload_neu.swf" + window.location.search + "&rid=" + fileId.replace(/f_/, "") + "&rname=" + imgArray[fileId]['file_save_name']);
    
}

function setFile(fileName){
	mySubmit(fileName);
}

function addFile(){
    //flash uploader
    insertFlash("flash/upload_neu.swf" + window.location.search);
}

function insertFlash(url){
   	var fmRoot = location.href.substr(0, location.href.indexOf("filemanager/"));
	fmRoot = fmRoot + "filemanager/";
	
	var uploader = "";
    uploader+= "<object";
    uploader+= " id=\"uploader\"";
    uploader+= " data=\"" + url + "&uscript=" + fmRoot + "upload.php\"";
    uploader+= " width=\"750\"";
    uploader+= " height=\"508\"";
    uploader+= " type=\"application/x-shockwave-flash\"";
    uploader+= ">";
    uploader+= "<param name=\"movie\" value=\"" + url + "&uscript=" + fmRoot + "upload.php\"></param>";
    uploader+= "</object>";
    document.getElementById("uploadContainer").innerHTML = uploader;
    return false;
}

function deleteFile(fileId){
    if(fileId != ""){
		if(confirm("Möchten Sie die Datei \"" + imgArray[fileId]['file_real_name'] + "\" wirklich löschen?")){
			var success = SHARED_makeRequest("delete.php","post","entry_id="+imgArray[fileId]['id']);
			document.getElementById(fileId).parentNode.removeChild(document.getElementById(fileId));
			//top.opener.location.reload();
			document.getElementById("fileActions").innerHTML = "<h2>Dateieigenschaften</h2><p>Keine Datei ausgewählt.</p>";
	
		}
    }
}

function deleteAllFiles(){
    if(confirm("Möchten Sie wirklich alle angezeigten Dateien löschen?")){
        var success = SHARED_makeRequest("deleteAll.php","post","entry_parent_id=" + entryParentId + "&file_cat1=" + fileCat1 + "&file_cat2=" + fileCat2);
       	 var fileList;
		//alle listenpunkte löschen
		if(fileList = document.getElementById("fileList")){
			fileList.innerHTML = "";	 
		}
		//window.location.reload();
	   
        //top.opener.location.reload();
    }
}

function uploadComplete(){
    //top.opener.location.reload();
    window.location.reload();
}

function cancelUpload(){
    var uploader;
    if(uploader = document.getElementById("uploader")){
		uploader.display = "none";
		uploader.parentNode.removeChild(uploader);
    }
}

function showFileDesc(fileObj){
	var outStr = "";
    if(langArray && langArray.length > 0){
		outStr+= "<h2>Beschreibungstext(e)</h2>"; 	
		for(var i = 0; i < langArray.length; i++){
			outStr+= "<fieldset>";
			outStr+= "<label for=\"desc_" + langArray[i] + "\">" + langArray[i].toUpperCase() + "</label>";
			outStr+= "<input name=\"desc_" + langArray[i] + "\" onblur=\"updateDescription('" + fileObj.id + "', '" + langArray[i] + "', this.value)\" value=\"" + imgArray[fileObj.id]['desc_' + langArray[i]] + "\" />";	
			outStr+= "</fieldset>";
			
		}
	}
	return outStr;
}

function updateDescription(fileId, language, desc){
	imgArray[fileId]['desc_' + language] = desc;
	SHARED_makeRequest("updateFileDesc.ajax.php","post","entry_id="+fileId.replace(/f_/, "")+"&desc="+desc+"&language="+language);
}

function showImageActions(fileObj){
    var outStr = "";
    outStr+= "<h2>Größe ändern</h2>";
    outStr+= "<form>";
    outStr+= "<input type=\"text\" name=\"file_img_width\" id=\"file_img_width\" value=\"" + imgArray[fileObj.id]['file_img_width'] + "\" onkeyup=\"getImagePropSize(this,document.getElementById('" + fileObj.id + "'))\" />";
    outStr+= " x ";
    outStr+= "<input type=\"text\" name=\"file_img_height\" id=\"file_img_height\" value=\"" + imgArray[fileObj.id]['file_img_height'] + "\" onkeyup=\"getImagePropSize(this,document.getElementById('" + fileObj.id + "'))\" />";
    outStr+= "</form>";
    return outStr;
}

function getImagePropSize(src, fileObj){
    if(src){
		if(isNaN(src.value)){
			alert("Bitte geben Sie nur Zahlen ein!");
			document.getElementById("file_img_width").value = imgArray[fileObj.id]['file_img_width'];
			document.getElementById("file_img_height").value = imgArray[fileObj.id]['file_img_height'];
			return;
		}
		if(src.id == "file_img_width"){
			var newHeight = Math.round(parseInt(src.value)*imgArray[fileObj.id]['file_img_height']/imgArray[fileObj.id]['file_img_width']);
			document.getElementById("file_img_height").value = newHeight;
		}
		if(src.id == "file_img_height"){
			var newWidth = Math.round(parseInt(src.value)*imgArray[fileObj.id]['file_img_width']/imgArray[fileObj.id]['file_img_height']);
			document.getElementById("file_img_width").value = newWidth;
		}
    }
}

function showInsertButton(){
	var outStr = "";
    outStr+= "<h2>Übernehmen</h2>";
	outStr+= "<button type=\"button\">Übernehmen</button>"
	return outStr;
}


function makeFileList(imgArray){
	var fileList;
	if(fileList = document.getElementById("fileList")){
		if(imgArray){
			for(var i in imgArray){
				var li = document.createElement("li");
				li.id = i;
				var span = document.createElement("span");
				span.appendChild(document.createTextNode(imgArray[i]['file_real_name']));
				li.appendChild(span);
				if(imgArray[i]['file_type'] == "image"){
					var img = document.createElement("img");
					img.src = "../../files/" + imgArray[i]['file_save_name'];
					img.alt = "";
					li.appendChild(img);
					
				}
				li.onclick = function(){
					showFileInfo(this);
				};
				fileList.appendChild(li);
			}	
		}
	}
}

makeFileList(imgArray);
