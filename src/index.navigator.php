<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */

require_once "./core/core.php";
require_once "./themes/theme.php";

/**
 * Get full URL of current page 
 */
function get_current_page_url() {

    $current_page_url = "http";

    if ($_SERVER["HTTPS"] == "on") {
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
 * Translate URL to inner path
 */
function to_inner_url($url) {

    $parsed_url = parse_url($url);
    $first_param = explode("&", $parsed_url["query"])[0];
    $first_param_chunks = explode("=", $first_param);
    $first_param_key = $first_param_chunks[0];
    $first_param_value = count($first_param_chunks) > 1 ? $first_param_chunks[1] : null;

    if (DB_HOST == '{DB_HOST}') {
        $target = "page.install.php";
    } else if (!array_key_exists($first_param_key, NAVIGATOR_TABLE)) {
        $target = "index.php";
    } else {
        $target = NAVIGATOR_TABLE[$first_param_key]
        . ($first_param_value != null ? "?value=" . $first_param_value : "");
    }

    return "./themes/" . THEME . "/" . $target;
}

?>