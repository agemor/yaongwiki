<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */

define("YAONGWIKI_THEME", "default");
define("YAONGWIKI_LANGUAGE", "en_US"); 

define("YAONGWIKI_ROOT_DIR", __DIR__);
define("YAONGWIKI_CORE_DIR", __DIR__ . "/core");
define("YAONGWIKI_THEME_DIR", __DIR__ . "/themes/" . YAONGWIKI_THEME);
define("YAONGWIKI_THEME_INCLUDE_DIR", YAONGWIKI_ENGINE_DIRNAME . "/themes/" . YAONGWIKI_THEME);


define("YAONGWIKI_NAVIGATOR_TABLE", array(
    "install" => "page.install.php",
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
));

require_once YAONGWIKI_CORE_DIR . "/languages/" . YAONGWIKI_LANGUAGE . ".php";
require_once YAONGWIKI_CORE_DIR . "/db.account.php";

/**
 * 현재 페이지의 전체 URL 구하기
 */
function get_current_page_url() {

    $current_page_url = "http";

    if (isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) == "on") {
        $current_page_url .= "s";
    }

    $current_page_url .= "://";
    
    if ($_SERVER["SERVER_PORT"] != "80") {
        $current_page_url .= $_SERVER["HTTP_HOST"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $current_page_url .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    }

    return $current_page_url;
}

/**
 * 받은 URL을 내부 주소로 전환하기
 */
function to_inner_url($url) {

    $parsed_url = parse_url($url);
    
    if (empty(DB_HOST)) {

        $target = YAONGWIKI_NAVIGATOR_TABLE["install"];

    } elseif (isset($parsed_url["query"])) {

        $first_param = explode("&", $parsed_url["query"])[0];
        $first_param_key = explode("=", $first_param)[0];

        if (array_key_exists($first_param_key, YAONGWIKI_NAVIGATOR_TABLE)) {
            $target = YAONGWIKI_NAVIGATOR_TABLE[$first_param_key];
        } else {
            $target = YAONGWIKI_NAVIGATOR_TABLE["page-not-found"];
        }

    } else {
        
        $target = YAONGWIKI_NAVIGATOR_TABLE["read"];
    }

    return YAONGWIKI_THEME_DIR . "/" . $target;
}

// 사이트 로드
$current_page_url = get_current_page_url();
require_once to_inner_url($current_page_url);