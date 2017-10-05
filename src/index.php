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

define("YAONGWIKI_DIR", "");
define("YAONGWIKI_ROOT", __DIR__ . YAONGWIKI_DIR);

require_once YAONGWIKI_ROOT . "/core/common.php";
require_once YAONGWIKI_ROOT . "/navigator.php";

HttpVarsManager::get_instance()->set($_GET, $_POST);

$current_page_url = get_current_page_url();
require_once to_inner_url($current_page_url);