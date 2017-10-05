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
require_once YAONGWIKI_CORE . "/languages/" . SettingsManager::get_instance()->get("site_language") . ".php";
require_once dirname(YAONGWIKI_CORE) . "/themes/theme.php";

const PERMISSION_NO_FILTERING = 3;
const PERMISSION_CHANGE_TITLE = 1;
const PERMISSION_DELETE_ARTICLE = 1;

const NAVIGATOR_TABLE = array(
    "main" => "page.main.php",
    "signin" => "page.signin.php",
    "signup" => "page.signup.php",
    "signout" => "page.signout.php",
    "reset" => "page.reset.php",
    "search" => "page.search.php",
    "read" => "page.read.php",
    "write" => "page.write.php",
    "create" => "page.create.php",
    "revision-history" => "page.revision-history.php",
    "revision" => "page.revision.php",
    "dashboard" => "page.dashboard.php",
    "profile" => "page.profile.php",
    "suggest" => "page.suggest.php",
    "recent" => "page.recent.php",
    "out-of-service" => "page.out-of-service.php",
    "page-not-found" => "page.page-not-found.php",
    "phpinfo" => "page.phpinfo.php"
);

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
    return dirname(YAONGWIKI_CORE) . "/themes/" . THEME . "/";
}

function hash_password($password) {
    return hash("sha512", $password . "yw");
}
