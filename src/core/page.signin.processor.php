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
    
    $db = Database::get_instance();
    $user = UserManager::get_instance();
    $log = LogManager::get_instance();
    $http_vars = HttpVarsManager::get_instance();

    $http_user_name = $http_vars->get("user-name");
    $http_user_password = $http_vars->get("user-password");
    $http_redirect = $http_vars->get("redirect") != null ? $http_vars->get("redirect") : "./";
    
    if ($user->authorized()) {
        return array(
            "result" => false,
            "redirect" => $http_redirect
        );
    }
    
    if (empty($http_user_name) || empty($http_user_password)) {
        return array(
            "result" => true
        );
    }

    if (!$db->connect()) {
        return array(
            "result" => false,
            "message" => STRINGS["ESDB0"],
            "redirect" => "./?out-of-service"
        );
    }

    $user_data = $db->in(DB_USER_TABLE)
                    ->select("*")
                    ->where("name", "=", $http_user_name)
                    ->go_and_get();

    if (!$user_data) {
        return array(
            "result" => false,
            "message" => STRINGS["EPSI0"]
        );
    }
    
    if (strcmp(hash_password($http_user_password), $user_data["password"]) != 0) {

        $log->create($user_data["name"], "signin", "0");

        return array(
            "result" => false,
            "message" => STRINGS["EPSI1"]
        );
    }

    // 세션 등록
    $user->signin($user_data["name"], $user_data["id"], intval($user_data["permission"]));
    $log->create($user_data["name"], "signin", "1");
   
    return array(
        "result" => true,
        "redirect" => $http_redirect
    );
}
