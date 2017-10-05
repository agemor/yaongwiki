<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */

define("YAONGWIKI_CORE", __DIR__);

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/manager.user.php";
require_once __DIR__ . "/manager.settings.php";
require_once __DIR__ . "/manager.http-vars.php";
require_once __DIR__ . "/manager.log.php";
require_once __DIR__ . "/manager.recaptcha.php";
require_once __DIR__ . "/manager.email.php";

// 언어팩 로드
$site_language = SettingsManager::get_instance()->get("site_language");
$site_theme = SettingsManager::get_instance()->get("site_theme");
if (empty($site_language)) {
    $site_language = "en_US";
}
if (empty($site_theme)) {
    $site_theme = "default";
}
require_once YAONGWIKI_CORE . "/languages/" . $site_language . ".php";

const PERMISSION_NO_FILTERING = 3;
const PERMISSION_CHANGE_TITLE = 1;
const PERMISSION_DELETE_ARTICLE = 1;

const PERMISSION_TABLE = array(
    0 => "Reader",
    1 => "Editor",
    2 => "Moderator",
    3 => "Admistrator",
    4 => "System Admistrator"
);

function redirect($url) {
    header("Location: " . $url);
}

function get_theme_path() {
    global $site_theme;
    return dirname(YAONGWIKI_CORE) . "/themes/" .$site_theme . "/";
}

function hash_password($password) {
    return hash("sha512", $password . "yw");
}
