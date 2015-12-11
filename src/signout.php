<?php
session_start();
session_destroy();

$redirect = !empty($_GET["redirect"]) ? $_GET["redirect"] : "index.php";

header('Location: '.$redirect);
?>