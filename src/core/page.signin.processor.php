<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 03
 */

require_once __DIR__ . "/common.php";
require_once __DIR__ . "/module.db.php";
require_once __DIR__ . "/module.form.php";
require_once __DIR__ . "/module.user.php";
require_once __DIR__ . "/module.redirect.php";

function process() {
    
    global $db;
    global $post;
    global $get;
    global $user;
    global $redirect;

    $http_user_name = $post->retrieve("user-name");
    $http_user_password = $post->retrieve("user-password");
    $http_redirect = $get->retrieve("redirect") == null ? "./" : $get->retrieve("redirect");
    
    if ($user->signined()) {
        $redirect->set($http_redirect);
        return array(
            "redirect" => true
        );
    }
    
    if (empty($http_user_name) || empty($http_user_password)) {
        return array(
            "result" => true
        );
    }

    if (!$db->connect()) {
        
        $redirect->set("./?out-of-service");
        
        return array(
            "redirect" => true,
            "result" => false,
            "message" => STRINGS["ESDB0"]
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

        $response = $db->in(DB_LOG_TABLE)
                       ->insert("user_name", $user_data["name"])
                       ->insert("behavior", "signin")
                       ->insert("data", "0")
                       ->go();

        return array(
            "result" => false,
            "message" => STRINGS["EPSI1"]
        );
    }

    // 세션 등록
    $user->signin($user_data["name"], $user_data["id"], intval($user_data["permission"]));

    $response = $db->in(DB_LOG_TABLE)
                   ->insert("user_name", $user_data["name"])
                   ->insert("behavior", "signin")
                   ->insert("data", "1")
                   ->go();
    
    $redirect->set($http_redirect);
   
    return array(
        "redirect" => true
    );
}
