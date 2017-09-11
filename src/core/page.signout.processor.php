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
require_once __DIR__ . "/module.user.php";
require_once __DIR__ . "/module.redirect.php";

function process() {

    global $get;
    global $user;
    global $redirect;

    $http_redirect = $get->retrieve("redirect") == null ? "./" : $get->retrieve("redirect");

    $user->signout();

    $redirect->set($http_redirect);

    return array(
        "redirect" => true
    );
}