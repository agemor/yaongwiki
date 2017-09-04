<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 03
 */

require_once __DIR__ . "/common.php";
require_once __DIR__ . "/module.form.php";
require_once __DIR__ . "/module.redirect.php";

function process() {

    global $get;
    global $redirect;

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
