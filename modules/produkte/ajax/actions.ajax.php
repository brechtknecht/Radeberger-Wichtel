<?php
session_start();
include_once "../../../shared/include/connect_db.inc.php";
include_once "../module.inc.php";
//add article to cart
if(isset($_GET['add']) && !empty($_GET['add'])){
	modAddToCart(intval($_GET['add']));	
	echo sizeof($_SESSION['modCart']);
}

//delete item from cart
if(isset($_GET['del']) && !empty($_GET['del'])){
	modDeleteItemFromCart(intval($_GET['del']));
	echo sizeof($_SESSION['modCart']);	
}


?>