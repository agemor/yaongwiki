<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/session.php";
require_once __DIR__ . "/manager.user.php";
require_once __DIR__ . "/manager.settings.php";
require_once __DIR__ . "/manager.http-vars.php";
require_once __DIR__ . "/manager.log.php";
require_once __DIR__ . "/manager.recaptcha.php";
require_once __DIR__ . "/manager.email.php";

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

function hash_password($password) {
    return hash("sha512", $password . "yw");
}
