<?php
header('Content-Type: text/html; charset=utf-8');
ini_set('arg_separator.output', '&amp;');
error_reporting(E_ALL);
//verbindungsdaten
$username="dbo601025058";
$password="5r5zn1oi42TMHTyv";
$host="db601025058.db.1and1.com";
$database="db601025058";

//verbinden
$_SESSION['conn'] = mysqli_connect($host, $username, $password);
mysqli_query($_SESSION['conn'], "SET CHARACTER SET 'utf8'");
//select db
mysqli_select_db($_SESSION['conn'], $database);
?>
