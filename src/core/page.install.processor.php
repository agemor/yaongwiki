<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */

require_once __DIR__ . "/common.php";
require_once __DIR__ . "/module.form.php";
require_once __DIR__ . "/module.user.php";
require_once __DIR__ . "/module.redirect.php";

function process() {
    
    global $post;
    global $user;
    global $redirect;

    $http_db_host = $post->retrieve("db-host");
    $http_db_name = $post->retrieve("db-name");
    $http_db_user = $post->retrieve("db-user");
    $http_db_password = $post->retrieve("db-password");
    $http_db_prefix = $post->retrieve("db-prefix");
    
    if (empty($http_db_prefix)) {
        $http_db_prefix = "";
    }

    if (empty($http_db_host) || empty($http_db_name) || empty($http_db_user) || empty($http_db_password)) {
        return array(
            "result" => true
        );
    }
    
    $connection = new mysqli($http_db_host, $http_db_user, $http_db_password, $http_db_name);
    
    if ($connection->connect_errno) {
        return array(
            "result" => false,
            "message" => STRINGS["EPIN0"] . " (" . $connection->connect_error . ")"
        );
    }
    
    $query = file_get_contents(__DIR__ . "/module.db.schema.sql");
    $query = str_replace("[PREFIX]", $http_db_prefix, $query);
    
    if (!$connection->multi_query($query)) {
        /*return array(
            "result" => false,
            "message" => STRINGS["EPIN1"]
        );*/
    }
    
    $config_keywords = array(
        "/DB_HOST = \".*\"/",
        "/DB_USER_NAME = \".*\"/",
        "/DB_USER_PASSWORD = \".*\"/",
        "/DB_NAME = \".*\"/",
        "/DB_TABLE_PREFIX = \".*\"/"
    );
    
    $settings = array(
        "DB_HOST = \"" . $http_db_host . "\"",
        "DB_USER_NAME = \"" . $http_db_user . "\"",
        "DB_USER_PASSWORD = \"" . $http_db_password . "\"",
        "DB_NAME = \"" . $http_db_name . "\"",
        "DB_TABLE_PREFIX = \"" . $http_db_prefix . "\""
    );
    
    $filecontent = file_get_contents(__DIR__ . "/module.db.account.php");
    $filecontent = preg_replace($config_keywords, $settings, $filecontent);
    file_put_contents(__DIR__ . "/module.db.account.php", $filecontent);
    var_dump($filecontent);
    $redirect->set(get_theme_path() . HREF_MAIN);
    return array(
        "redirect" => true
    );
}
