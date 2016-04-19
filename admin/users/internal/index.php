<?php
$req_role="Administrator1";
include("../../shared/include/environment.inc.php");
isLoggedIn();
$result=mysqli_query($_SESSION['conn'], "SELECT * FROM _cms_hp_user_ WHERE user_type='intern' ORDER BY user_name ASC");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>zeemes : web content management system : user</title>
<script type="text/javascript" src="../../shared/javascript/functions.js"></script>
<script type="text/javascript" src="../javascript/functions.js"></script>
<link rel="stylesheet" href="../../shared/css/styles.css" />
</head>

<body id="content">
<h2>CMS Benutzer bearbeiten</h2>
<div id="saveTools">
	<button type="button" onclick="location.href='edit.php?user_type=intern&amp;entry_id=new'" style="margin: 0">Neuer Eintrag</button>
</div>
<table cellpadding="0" cellspacing="0" class="table">
	<thead>
    <tr>
    	<th  style="width: 60px">Aktionen</th>
        <th style="width: 200px">Name</th>
    	<th style="width: 200px">Gruppe</th>
    	<th style="width: auto">letzes Login</th>
        
    </tr>
    </thead>
    <tbody>
		
	<?php
	if(mysqli_num_rows($result)>0){
		$bgclass="col1";
		while($row=mysqli_fetch_assoc($result)){
			if($bgclass=="col2"){
				$bgclass="col1";
			}
			else{
				$bgclass="col2";
			}
			?>
			<tr class="<?php echo $bgclass;?>" onmouseover="this.style.backgroundColor='#FFFFFF'" onmouseout="this.style.backgroundColor=''">
				<td>
					<a href="edit.php?user_type=intern&amp;entry_id=<?php echo $row['entry_id'];?>"><img src="../../shared/images/editable.gif" alt="" title="bearbeiten" /></a>
					<img src="../../shared/images/delete.gif" alt="" title="User löschen" onclick="USERS_deleteUser(<?php echo $row['entry_id'];?>,this.parentNode.parentNode)" />
                </td>
                <td><?php echo $row['user_name'].", ".$row['user_fname'];?></td>
				<td><?php echo $row['user_role'] == "Chefredakteur"?"eingeschränkter Nutzer":$row['user_role'];?></td>
				<td><?php echo formatDate2Local($row['user_last_login'],"dd.mm.YYYY",true,false,false);?></td>
				
			</tr>
			<?php
		}	
	}
	?>
    </tbody>
   
</table>
</body>
</html>
