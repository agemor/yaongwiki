<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);

require_once __DIR__ . "/core/module.form.php";
require_once __DIR__ . "/index.navigator.php";

$post->set($_POST);
$get->set($_GET);

$current_page_url = get_current_page_url();

require_once to_inner_url($current_page_url);

?>