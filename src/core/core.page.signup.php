<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */

require_once "core.php";
require_once "core.db.php";
require_once "core.session.php";
require_once "core.recaptcha.php";

function process() {
    
    global $db;
    global $post;
    global $user;
    global $redirect;
    global $recaptcha;

    if ($user->signined()) {
        $redirect->set(get_theme_path() . HREF_MAIN);
        return array(
            "redirect" => true
        );
    }

    $http_user_name = $post->retrieve("user-name");
    $http_user_password = $post->retrieve("user-password");
    $http_user_password_re = $post->retrieve("user-password-res");
    $http_user_email = $post->retrieve("user-email");
    
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
    
    if (strlen($http_user_password) < 5) {
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
    
    if (!getReCaptcha()) {
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
    
    $response = $db->in(DB_LOG_TABLE)
                   ->insert("behavior", "signup")
                   ->insert("data", $http_user_name)
                   ->go();
    
    $redirect->set(get_theme_path() . HREF_SIGNIN);
    return array(
        "redirect" => true
    );
}
