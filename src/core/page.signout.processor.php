<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 03
 */

require_once __DIR__ . "/common.php";

function process() {

    $user = UserManager::get_instance();
    $http_vars = HttpVarsManager::get_instance();

    $http_redirect = $http_vars->get("redirect") != null ? $http_vars->get("redirect") : "./";

    $user->signout();
    
    return array(
        "result" => true,
        "redirect" => $http_redirect
    );
}