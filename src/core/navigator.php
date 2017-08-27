<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */

require_once "./common.php";

function get_current_page_url() {

    $current_page_url = "http";

    if ($_SERVER["HTTPS"] == "on") {
        $current_page_url .= "s";
    }

    $current_page_url .= "://";
    
    if ($_SERVER["SERVER_PORT"] != "80") {
        $current_page_url .= $_SERVER["HTTP_HOST"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    }
    
    else {
        $current_page_url .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    }

    return $current_page_url;
}

function analyze_url($url) {

    $parsed_url = parse_url($url);
    $first_param = explode("&", $parsed_url["query"])[0];

}
 
?>