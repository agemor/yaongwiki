<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */

session_start();
session_destroy();

$redirect = !empty($_GET["redirect"]) ? $_GET["redirect"] : "./";

header('Location: '.$redirect);
?>