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
    global $user;
    global $redirect;

    $http_redirect = $get->retrieve("redirect") == null ? HREF_MAIN : $get->retrieve("redirect");

    $user->signout();

    $redirect->set(get_theme_path() . $http_redirect);

    return array(
        "redirect" => true
    );
}