<?php
include("shared/include/environment.inc.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>zeemes : web content management system : black edition</title>
</head>

<frameset rows="40,*" frameborder="no" border="0" framespacing="0">
  <frame src="header/index.php" name="headFrame" scrolling="no" noresize="noresize" id="headFrame" title="headFrame" />
  	<?php
	//check permission
	if(isset($_SESSION['cms_user'])){
	?>
    <frameset cols="250,*" frameborder="no" border="0" framespacing="0">
      <frame src="navigation/index.php" name="navigationFrame" scrolling="no" noresize="noresize" id="navigationFrame" title="navigationFrame" />
      <frame src="overview/index.php" name="contentFrame" id="contentFrame" scrolling="auto" />
	</frameset>
    <?php
	}
	else{
	//show login
	?>
    <frame src="login/index.php" name="contentFrame" id="contentFrame" />
    <?php	
	}
?>
</frameset>
<noframes>
<body>
</body>
</noframes>
</html>