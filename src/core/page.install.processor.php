<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */

require_once __DIR__ . "/common.php";

function process() {
    
    $http_vars = HttpVarsManager::get_instance();

    $http_db_host = $http_vars->get("db-host");
    $http_db_name = $http_vars->get("db-name");
    $http_db_user = $http_vars->get("db-user");
    $http_db_password = $http_vars->get("db-password");
    $http_db_prefix = $http_vars->get("db-prefix");
    
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
    
    $query = file_get_contents(__DIR__ . "/db.schema.sql");
    $query = str_replace("[PREFIX]", $http_db_prefix, $query);
    
    if (!$connection->multi_query($query)) {
        return array(
            "result" => false,
            "message" => STRINGS["EPIN1"]
        );
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
    
    $filecontent = file_get_contents(__DIR__ . "/db.account.php");
    $filecontent = preg_replace($config_keywords, $settings, $filecontent);
    file_put_contents(__DIR__ . "/db.account.php", $filecontent);
    
    return array(
        "result" => true,
        "message" => "success"
    );
}
