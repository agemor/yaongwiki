<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 04
 */

require_once __DIR__ . "/common.php";
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/module.form.php";
require_once __DIR__ . "/module.user.php";
require_once __DIR__ . "/module.redirect.php";

function process() {

    global $get;

    $http_value = $get->retrieve("value");

    return array(
        "result" => true,
        "value" => $http_value
    );
}
