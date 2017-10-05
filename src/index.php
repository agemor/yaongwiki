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

define("YAONGWIKI_ENGINE_DIRNAME", "");
define("YAONGWIKI_ENGINE_DIR", __DIR__ . YAONGWIKI_ENGINE_DIRNAME);

require_once YAONGWIKI_ENGINE_DIR . "/navigator.php";