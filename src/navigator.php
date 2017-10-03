<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */

require_once YAONGWIKI_ROOT . "/core/common.php";
require_once YAONGWIKI_ROOT . "/core/module.db.account.php";

// 현재 페이지의 전체 URL 구하기
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

// 받은 URL을 내부 주소로 전환하기
function to_inner_url($url) {

    $parsed_url = parse_url($url);
    
    if (empty(DB_HOST)) {
        $target = "page.install.php";
    }

    elseif (isset($parsed_url["query"])) {

        $first_param = explode("&", $parsed_url["query"])[0];
        $first_param_key = explode("=", $first_param)[0];

        if (array_key_exists($first_param_key, NAVIGATOR_TABLE)) {
            $target = NAVIGATOR_TABLE[$first_param_key];
        } 
    } 

    else {
        $target = "page.main.php";
    }

    return get_theme_path() . $target;
}