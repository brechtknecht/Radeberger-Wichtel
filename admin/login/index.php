<?php
include("../shared/include/environment.inc.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>zeemes : web content management system : login</title>
<link rel="stylesheet" href="../shared/css/styles.css" />
</head>
<body onload="document.getElementById('username').focus();">
<form style="position:absolute;left:0px;top:20%;width:100%;text-align:center;display:block" action="../login.php" method="post" target="_parent">
	<?php
    if(isset($_SESSION['fid'])){
		?>
         <input type="hidden" name="fid" value="<?php echo htmlspecialchars($_SESSION['fid'])?>" />
		<?php	
	}
	?>
    <label for="username">Benutzername</label>
    <input type="text" name="username" value="" id="username" style="margin-bottom: 10px;text-align:center" />
    <label for="password">Passwort</label>
    <input type="password" name="password" value="" style="text-align:center" /><br /><br />
    <button type="submit">Anmelden</button>
   
</form>
</body>
</html>
