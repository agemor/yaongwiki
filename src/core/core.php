<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */

require_once "./languages/language.php";
require_once "./themes/theme.php";

const FILE_MAXIMUM_SIZE = 10 * 1024 * 1024; // 10MB
const FILE_DIRECTORY = "uploads";
const FILE_AVALIABLE_EXTENSIONS = array("jpg", "jpeg", "png", "gif", "svg", "tiff", "bmp");

const PERMISSION_NO_FILTERING = 3;
const PERMISSION_CHANGE_TITLE = 1;
const PERMISSION_DELETE_ARTICLE = 1;

const TITLE_AFFIX = " - YaongWiki";

const HREF_MAIN = "";
const HREF_SIGNIN = "page.signin.php";
const HREF_SIGNUP = "page.signup.php";
const HREF_SIGNOUT = "page.signout.php";
const HREF_RESET = "page.reset.php";
const HREF_SEARCH = "page.search.php";
const HREF_READ = "page.read.php";
const HREF_WRITE = "page.write.php";
const HREF_CREATE = "page.create.php";
const HREF_REVISIONS = "page.revisions.php";
const HREF_REVISION = "page.revision.php";
const HREF_DASHBOARD = "page.dashboard.php";
const HREF_PROFILE = "page.profile.php";
const HREF_404 = "page.404.php";
const HREF_SUGGEST = "page.suggest.php";
const HREF_RECENT = "page.recent.php";

const NAVIGATOR_TABLE = array(
    "main" => HREF_MAIN,
    "signin" => HREF_SIGNIN,
    "signup" => HREF_SIGNUP,
    "signout" => HREF_SIGNOUT,
    "reset" => HREF_RESET,
    "search" => HREF_SEARCH,
    "read" => HREF_READ,
    "write" => HREF_WRITE,
    "create" => HREF_CREATE,
    "revisions" => HREF_REVISIONS,
    "revision" => HREF_REVISION,
    "dashboard" => HREF_DASHBOARD,
    "profile" => HREF_PROFILE,
    "404" => HREF_404,
    "suggest" => HREF_SUGGEST,
    "recent" => HREF_RECENT
);

const LOG_DELETE_ACCOUNT = "delete-account";
const LOG_STUDENT_AUTH = "auth";
const LOG_CHANGE_EMAIL = "change-email";
const LOG_CHANGE_PASSWORD = "change-password";
const LOG_WRITE = "write";
const LOG_CREATE = "create";
const LOG_UPDATE_USER_INFO = "update-user-info";
const LOG_RESET = "reset";
const LOG_SIGNIN = "signin";
const LOG_SIGNUP = "signup";

function permissionInfo($permission)
{
    switch ($permission) {
        case 0:
            return array(
                "description" => "Reader",
                "color" => "info"
            );
        case 1:
            return array(
                "description" => "Editor",
                "color" => "warning"
            );
        case 2:
            return array(
                "description" => "Moderator",
                "color" => "success"
            );
        case 3:
            return array(
                "description" => "Administrator",
                "color" => "danger"
            );
        default:
            return array(
                "description" => "System Administrator",
                "color" => "primary"
            );
            
    }
}

function get_theme_path() {
    return "./themes/" . THEME . "/";
}

function hash_password($password) {
    return hash("sha512", $password . "yw");
}
