<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
session_start(); 

$user_name = $_SESSION['name'];
$user_id = $_SESSION['id'];
$user_permission = $_SESSION['permission'];
$loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'];
$user_ip = $_SERVER['REMOTE_ADDR'];

if(!isset($_SESSION['pageview'])) {
	$_SESSION['pageview'] = array();
}

?>