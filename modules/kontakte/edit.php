<?php
include_once("../../admin/shared/include/environment.inc.php");

$module_name = getParentDir($_SERVER['SCRIPT_FILENAME']);
if(strstr($module_name,"\\")){
	$module_name = substr($module_name,strrpos($module_name,"\\")+1);
}

if(checkPermission("user",$_SESSION['cms_user']['user_id'],"module",$module_name)==false){
	die("Sie haben keine Berechtigung auf dieses Element zuzugreifen!");
}
if($_SERVER['REQUEST_METHOD']=="POST"){
	include("save.inc.php");
}

//get categories
$query="SELECT DISTINCT main_category FROM _cms_modules_contacts_";
$result_cat=mysqli_query($_SESSION['conn'], $query);

//get data	
if(isset($_GET['entry_id']) && $_GET['entry_id'] != "new"){
	$query="SELECT * FROM _cms_modules_contacts_ WHERE entry_id=".intval($_GET['entry_id'])." LIMIT 1";
	$result=mysqli_query($_SESSION['conn'], $query);
	if(mysqli_num_rows($result)>0){
		$row=mysqli_fetch_assoc($result);
	}
}


//get users
$result_usr = mysqli_query($_SESSION['conn'], "SELECT entry_id AS entry_id, CONCAT(user_name,', ',user_fname) AS user_name FROM _cms_hp_user_ WHERE user_type='intern' ORDER BY user_name ASC, user_fname ASC");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>zeemes : web content management system : modules : <?php echo $module_name;?> : edit</title>
<script type="text/javascript" src="../../admin/shared/javascript/functions.js"></script>
<script type="text/javascript" src="../../admin/pages/javascript/functions.js"></script>

<link rel="stylesheet" href="../../admin/shared/css/styles.css" />
</head>

<body id="content" onload="SHARED_showFieldSetSwitch('form0');">
<h2><?php echo strtoupper($module_name);?>: <?php echo isset($row['title'])?$row['title']:"neuen Eintrag anlegen";?></h2>
<div id="saveTools">
	<button type="button" onclick="SHARED_submitForm(document.form0,'Änderungen übernehmen?')">Änderungen übernehmen</button>
  	<button type="button" onclick="location.href='index.php'">zurück zur Liste</button>
</div>
<?php
if($_SERVER['REQUEST_METHOD']=="POST"){
	echo "<span class=\"showState\">Ihre Änderungen wurden gespeichert</span>";
}
?>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" id="form0" name="form0" enctype="multipart/form-data">
  <input type="hidden" name="entry_id" value="<?php echo $_GET['entry_id'];?>" />
  <table cellpadding="0" cellspacing="0" class="table">
    <thead>
      <tr>
        <th class="betweenHeader" colspan="2">Allgemein</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="width: 220px;">letzte Aktualisierung</td>
        <td><?php echo isset($row['last_change'])?$row['last_change']:"";?><?php echo isset($row['entry_last_usr'])?" von ".getUserName($row['entry_last_usr']):"Datensatz noch nicht angelegt";?></td>
      </tr>
      <?php
      if(isAdmin()){
	  ?>
      <tr>
      	<td>Eigentümer des Datensatzes</td>
        <td>
        	<select name="entry_last_usr">
            	<?php
                if(mysqli_num_rows($result_usr) > 0){
					while($row_usr = mysqli_fetch_assoc($result_usr)){
				?>
                	<option value="<?php echo $row_usr['entry_id'];?>" <?php echo isset($row['entry_last_usr']) && $row_usr['entry_id'] == $row['entry_last_usr']?"selected=\"selected\"":""; ?>><?php echo $row_usr['user_name'];?></option>
                <?php
					}
				}
				?>
            </select>        </td>
      </tr>
      <?php
	  }
	  ?>
      <tr>
         <td colspan="2" class="betweenHeader">Kategorie, ID</td>
       </tr>
       <tr>
        <td>Kategorie</td>
        <td>
        	<select name="main_category" style="width: auto">
            	<option value="">Kategorie wählen</option>
               
                
				<?php 
                if(mysqli_num_rows($result_cat)>0){
					while($row_cat=mysqli_fetch_assoc($result_cat)){
					?>
                    <option value="<?php echo $row_cat['main_category']?>" <?php echo isset($row['main_category'])&&$row['main_category']==$row_cat['main_category']?"selected=\"selected\"":"";?>><?php echo $row_cat['main_category']?></option>
                    <?php
					}
				}
				?>
            </select>&nbsp;oder neu&nbsp;
            <input type="text" name="main_category_new" value="" style="width: 120px;" />        </td>
      </tr>
       <tr>
        <td>ID</td>
        <td>
          <input type="text" name="kundennummer" value="<?php echo isset($row['kundennummer'])?stripslashes($row['kundennummer']):"";?>" style="width: 80px;" title="Kundennummer"  />        </td>
      </tr>
      
       <tr>
         <td colspan="2" class="betweenHeader">Name, Firma, Adresse</td>
       </tr>
       <tr>
        <td>Anrede - Vorname - Nachname</td>
        <td>
        <input type="text" name="anrede" value="<?php echo isset($row['anrede'])?stripslashes($row['anrede']):"";?>" style="width: 70px;" title="Anrede"  />
        <input type="text" name="vorname" value="<?php echo isset($row['vorname'])?stripslashes($row['vorname']):"";?>" style="width: 150px;" title="Vorname"  />
        <input type="text" name="name" value="<?php echo isset($row['name'])?stripslashes($row['name']):"";?>" style="width: 200px;" title="Nachname"  />        </td>
      </tr>
       <tr>
        <td>Firma</td>
        <td>
        <input type="text" name="firma" value="<?php echo isset($row['firma'])?stripslashes($row['firma']):"";?>" title="Firma"  />        </td>
      </tr>
       <tr>
         <td>Abteilung</td>
         <td><input type="text" name="abteilung" value="<?php echo isset($row['abteilung'])?stripslashes($row['abteilung']):"";?>" title="Abteilung"  />        </td>
       </tr>
      <tr>
        <td>Straße</td>
        <td>
        <input type="text" name="strasse" value="<?php echo isset($row['strasse'])?stripslashes($row['strasse']):"";?>" title="Straße"  />        </td>
      </tr>
      <tr>
        <td>PLZ - Ort</td>
        <td>
        <input type="text" name="plz" value="<?php echo isset($row['plz'])?stripslashes($row['plz']):"";?>" style="width: 70px;" title="PLZ"  />
        <input type="text" name="ort" value="<?php echo isset($row['ort'])?stripslashes($row['ort']):"";?>" style="width: 150px;" title="Ort"  />        </td>
      </tr>
       <tr>
         <td colspan="2" class="betweenHeader">Kommunikation</td>
       </tr>
       <tr>
        <td>Telefon</td>
        <td>
        <input type="text" name="telefon" value="<?php echo isset($row['telefon'])?stripslashes($row['telefon']):"";?>" title="Telefon"  />        </td>
      </tr>
      <tr>
        <td>Mobil</td>
        <td>
        <input type="text" name="mobil" value="<?php echo isset($row['mobil'])?stripslashes($row['mobil']):"";?>" title="Mobil"  />        </td>
      </tr>
      <tr>
        <td>Fax</td>
        <td>
        <input type="text" name="fax" value="<?php echo isset($row['fax'])?stripslashes($row['fax']):"";?>" title="Fax"  />        </td>
      </tr>
      <tr>
        <td>Email</td>
        <td>
        <input type="text" name="email" value="<?php echo isset($row['email'])?stripslashes($row['email']):"";?>" title="Email"  />        </td>
      </tr>
      <tr>
        <td>URL</td>
        <td>
        <input type="text" name="url" value="<?php echo isset($row['url'])?stripslashes($row['url']):"";?>" title="URL"  />        </td>
      </tr>
      <tr>
         <td colspan="2" class="betweenHeader">Sonstiges</td>
       </tr>
       <tr>
        <td>Benutzername</td>
        <td>
        <input type="text" name="username" value="<?php echo isset($row['username'])?stripslashes($row['username']):"";?>" title="Benutzername"  />        </td>
      </tr>
      <tr>
        <td>Passwort (nur Neuvergabe)</td>
        <td>
        <input type="text" name="password" value="" title="Passwort"  />        </td>
      </tr>
       <tr>
        <td>Rabatt %</td>
        <td>
        <input type="text" name="rabatt" value="<?php echo isset($row['rabatt'])?stripslashes($row['rabatt']):"";?>" title="Rabatt"  />        </td>
      </tr>
      </tbody>
    <tfoot>
    </tfoot>
  </table>
</form>
</body>
</html>
