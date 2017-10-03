<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */

require_once __DIR__ . "/common.php";
require_once __DIR__ . "/module.db.php";
require_once __DIR__ . "/module.form.php";
require_once __DIR__ . "/module.user.php";
require_once __DIR__ . "/module.redirect.php";

function process() {
    
    global $db;
    global $post;
    global $user;
    global $redirect;

    $http_user_email = $post->retrieve("user-email");
    $http_user_password = $post->retrieve("user-password");
    $http_user_new_password = $post->retrieve("user-new-password");
    $http_user_new_password_re = $post->retrieve("user-new-password-re");
    $http_user_password_drop = $post->retrieve("user-drop-password");

    $page_focus = 0;
    
    // 로그인 되어 있지 않을 경우
    if (!$user->signined()) {
        $redirect->set(get_theme_path() . HREF_MAIN);
        return array(
            "redirect" => true
        );
    }
    
    $user_data = $db->in(DB_USER_TABLE)
                    ->select("*")
                    ->where("id", "=", $user->id)
                    ->go_and_get();

    if (!$user_data) {
        return array(
            "result" => false,
            "message" => STRINGS["EPDH0"]
        );
    }
    
    // 최근 3일간 로그인 기록 가져오기
    $response = $db->in(DB_LOG_TABLE)
                   ->select("*")
                   ->where("user_name", "=", $user_data["name"], "AND")
                   ->where("behavior", "=", "signin", "AND")
                   ->where("timestamp", ">=", "(CURDATE() - INTERVAL 3 DAY)", "AND", true)
                   ->order_by("`timestamp` DESC")
                   ->limit("30")
                   ->go_and_get_all();

    if (!$response) {
        return array(
            "result" => false,
            "user" => $user_data,
            "message" => STRINGS["EPDH1"] . $db->rq()
        );
    }

    $user_data["login_history"] = $response;
    
    // 이메일 변경
    if (!empty($http_user_email)) {
        $page_focus = 1;
        
        if (!filter_var($http_user_email, FILTER_VALIDATE_EMAIL)) {
            return array(
                "result" => false,
                "user" => $user_data,
                "message"=> STRINGS["EPDH2"]
            );
        }
        
        if (strcmp($user_data["email"], $http_user_email) == 0) {
            return array(
                "result" => false,
                "user" => $user_data,
                "message" => STRINGS["EPDH3"]
            );
        }

        $response = $db->in(DB_USER_TABLE)
                        ->select("*")
                        ->where("email", "=", $http_user_email)
                        ->go_and_get();
        
        if ($response) {
            return array(
                "result" => false,
                "user" => $user_data,
                "message" => STRINGS["EPDH4"]
            );
        }

        $response = $db->in(DB_USER_TABLE)
                       ->update("email", $http_user_email)
                       ->where("id", "=", $user_data["id"])
                       ->go();
        
        if (!$response) {
            return array(
                "result" => false,
                "user" => $user_data,
                "message" => STRINGS["EPDH5"]
            );
        }
        
        $response = $db->in(DB_LOG_TABLE)
                       ->insert("behavior", "change-email")
                       ->insert("data", $user_data["email"] . ":" . $http_user_email)
                       ->go();
        
        $user_data["email"] = $http_user_email;
        
        return array(
            "result" => true,
            "user" => $user_data,
            "message" => STRINGS["EPDH6"]
        );
    }
    
    // 비밀번호 변경
    if (!empty($http_user_new_password)) {
        $page_focus = 2;
        
        if (strcmp($http_user_new_password, $http_user_new_password_re) != 0) {
            return array(
                "result" => false,
                "user" => $user_data,
                "message" => STRINGS["EPDH7"]
            );
        }
        
        if (strlen($http_user_new_password) < 4) {
            return array(
                "result" => false,
                "user" => $user_data,
                "message" => STRINGS["EPDH8"]
            );
        }
        
        $http_user_password = hash_password($http_user_password);
        $http_user_new_password = hash_password($http_user_new_password);

        if (strcmp($user_data["password"], $http_user_password) != 0) {
            return array(
                "result" => false,
                "user" => $user_data,
                "message" => STRINGS["EPDH9"]
            );
        }

        $response = $db->in(DB_USER_TABLE)
                       ->update("password", $http_user_new_password)
                       ->where("id", "=", $user_data["id"])
                       ->go();
        
        if (!$response) {
            return array(
                "result" => false,
                "user" => $user_data,
                "message" => STRINGS["EPDH10"]
            );
        }
        
        $response = $db->in(DB_LOG_TABLE)
                       ->insert("behavior", "password-change")
                       ->insert("data", "*")
                       ->go();
        
        return array(
            "result" => true,
            "user" => $user_data,
            "message" => STRINGS["EPDH11"]
        );
    }
    
    // 계정 삭제
    if (!empty($http_user_password_drop)) {
        $page_focus = 3;
        
        if (strcmp($user_data["password"], hash_password($http_user_password_drop)) != 0) {
            return array(
                "result" => false,
                "user" => $user_data,
                "message" => STRINGS["EPDH9"]
            );
        }

        $response = $db->in(USER_TABLE)
                       ->delete()
                       ->where("id", "=", $user_data["id"])
                       ->go();
        
        if (!$response) {
            return array(
                "result" => false,
                "user" => $user_data,
                "message" => STRINGS["EPDH12"]
            );
        }
        
        $response = $db->in(DB_LOG_TABLE)
                       ->insert("behavior", "account-delete")
                       ->insert("data", $user_data["name"])
                       ->go();
        
        $redirect->set(get_theme_path() . HREF_SIGNOUT);

        return array(
            "redirect" => true
        );
    }

    return array(
        "result" => true,
        "user" => $user_data
    );
}
