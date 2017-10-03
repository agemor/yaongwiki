<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 04
 */

require_once __DIR__ . "/common.php";

function process() {

    $http_vars = HttpVarsManager::get_instance();

    $http_value = $http_vars->get("value");

    return array(
        "result" => true,
        "value" => $http_value
    );
}
