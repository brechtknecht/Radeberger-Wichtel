<?php
include("../shared/include/environment.inc.php");
isLoggedIn();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>zeemes : web content management system : overview</title>
<link rel="stylesheet" href="../shared/css/styles.css" />
</head>

<body id="content">
<h2>Übersicht</h2>
<table cellpadding="0" cellspacing="0" class="table">
    <thead>
      <tr>
        <th style="width: 100px;">&nbsp;</th>
        <th style="width: auto;">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Angemeldet als</td>
        <td><?php echo isset($_SESSION['cms_user'])?$_SESSION['cms_user']['user_name']:"&nbsp;";?></td>
      </tr>
      <tr>
        <td>Letztes Login</td>
        <td><?php echo isset($_SESSION['cms_user'])?$_SESSION['cms_user']['user_last_login']:"&nbsp;";?></td>
      </tr>
      <tr>
        <td>Nachrichten</td>
        <td>Keine neuen Nachrichten</td>
      </tr>
      
      
    </tbody>
    <tfoot>
    </tfoot>
</table>
</body>
</html>
