<?php
//connect db
include("../shared/include/connect_db.inc.php");
//get entry
$result=mysqli_query($_SESSION['conn'], "SELECT file_real_name,file_save_name FROM _cms_hp_files_ WHERE file_save_name='".mysqli_real_escape_string($_SESSION['conn'], $_GET['file_name'])."' LIMIT 1");
$row=mysqli_fetch_assoc($result);
//downloadcounter um 1 erhöhen
mysqli_query($_SESSION['conn'], "UPDATE _cms_hp_files_ SET file_dl_count=file_dl_count+1 where file_save_name='".mysqli_real_escape_string($_SESSION['conn'], $_GET['file_name'])."'");
// set file name
$save_as_name = $row['file_real_name'];

$size = filesize($row['file_save_name']);
header("Content-type: application/octet-stream");
header("Content-disposition: attachment; filename=\"".$save_as_name."\"");
header("Content-Length: ".$size);
header("Pragma: no-cache");
header("Expires: 0");
readfile($row['file_save_name']);
exit;
?>