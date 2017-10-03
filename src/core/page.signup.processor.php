<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 03
 */

require_once __DIR__ . "/common.php";

const ENABLE_RECAPTCHA = false;

function process() {
    
    $db = Database::get_instance();
    $user = UserManager::get_instance();
    $log = LogManager::get_instance();
    $http_vars = HttpVarsManager::get_instance();
    $recaptcha = ReCaptchaManager::get_instance();

    $http_user_name = $http_vars->get("user-name");
    $http_user_password = $http_vars->get("user-password");
    $http_user_password_re = $http_vars->get("user-password-re");
    $http_user_email = $http_vars->get("user-email");
    $http_recaptch_response = $http_vars->get("g-recaptcha-response");
    
    if ($user->authorized()) {
        return array(
            "result" => false,
            "redirect" => "./"
        );
    }

    if (empty($http_user_name) || empty($http_user_password) || empty($http_user_email)) {
        return array(
            "result" => true
        );
    }
    
    if (strlen($http_user_name) < 2) {
        return array(
            "result" => false,
            "message" => STRINGS["EPSU0"]
        );
    }
    
    if (strlen($http_user_password) < 4) {
        return array(
            "result" => false,
            "message" => STRINGS["EPSU1"]
        );
    }
    
    if (strcmp($http_user_password, $http_user_password_re) != 0) {
        return array(
            "result" => false,
            "message" => STRINGS["EPSU2"]
        );
    }
    
    if (!filter_var($http_user_email, FILTER_VALIDATE_EMAIL)) {
        return array(
            "result" => false,
            "message" => STRINGS["EPSU3"]
        );
    }

    $recaptcha_enable = (strtolower($settings->get("recaptcha_enable")) == "true");
    if ($recaptcha_enable && !$recaptcha->verify($settings->get("recaptcha_private_key"), $http_recaptch_response)) {
        return array(
            "result" => false,
            "message" => STRINGS["EPSU4"]
        );
    }
    
    $response = $db->in(DB_USER_TABLE)
                   ->select("*")
                   ->where("name", "=", $http_user_name, "OR")
                   ->where("email", "=", $http_user_email)
                   ->go_and_get_all();
    
    if ($response) {
        for ($i = 0; $i < count($response); $i++) {
            if (strcmp($http_user_name, $response[i]["name"]) == 0) {
                return array(
                    "result" => false,
                    "message" => STRINGS["EPSU5"]
                );
            } else {
                return array(
                    "result" => false,
                    "message" => STRINGS["EPSU6"]
                );
            }
        }
    }

    $response = $db->in(DB_USER_TABLE)
                   ->insert("name", $http_user_name)
                   ->insert("password", hash_password($http_user_password))
                   ->insert("email", $http_user_email)
                   ->go();
    
    if (!$response) {
        return array(
            "result" => false,
            "message" => STRINGS["EPSU7"]
        );
    }
    
    $log->create($http_user_name, "signup", "");
    
    return array(
        "result" => true,
        "message" => "success"
    );
}
