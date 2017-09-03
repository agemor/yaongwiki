<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 03
 */

require_once "core.php";
require_once "core.session.php";

function process() {

    global $get;

    $http_title = $get->retrieve("t");

    if ($http_title == null) {
        $redirect->set(get_theme_path() . HREF_404);
        return array(
            "redirect" => true
        );
    }

    return array(
        "result" => true,
        "title" => $http_title
    );
}
