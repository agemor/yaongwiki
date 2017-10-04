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
    
    $db = Database::get_instance();
    $user = UserManager::get_instance();
    $log = LogManager::get_instance();
    $http_vars = HttpVarsManager::get_instance();

    $http_user_email = $http_vars->get("user-email");
    $http_user_password = $http_vars->get("user-password");
    $http_user_new_password = $http_vars->get("user-new-password");
    $http_user_new_password_re = $http_vars->get("user-new-password-re");
    $http_user_password_drop = $http_vars->get("user-drop-password");

    // 로그인되어 있지 않을 경우
    if (!$user->authorized()) {
        return array(
            "result" => false,
            "redirect" => "./?signin&redirect=./?dashboard"
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
                    ->where("id", "=", $user->get("id"))
                    ->go_and_get();    

    // 최근 3일간 로그인 기록 가져오기
    $response = $db->in(DB_LOG_TABLE)
                   ->select("*")
                   ->where("user_name", "=", $user->get("name"), "AND")
                   ->where("timestamp", ">=", "(CURDATE() - INTERVAL 3 DAY)", "AND", true)
                   ->order_by("`timestamp` DESC")
                   ->limit("30")
                   ->go_and_get_all();

    if (!$response) {
        return array(
            "result" => false,
            "message" => STRINGS["EPDH1"],
            "user" => $user_data
        );
    }
   
    $user_data["logs"] = $response;
    
    // 이메일 변경
    if (!empty($http_user_email)) {
        
        if (!filter_var($http_user_email, FILTER_VALIDATE_EMAIL)) {
            return array(
                "result" => false,
                "message" => STRINGS["EPDH2"],
                "user" => $user_data
            );
        }
        
        if (strcmp($user_data["email"], $http_user_email) == 0) {
            return array(
                "result" => false,
                "message" => STRINGS["EPDH3"],
                "user" => $user_data
            );
        }

        $response = $db->in(DB_USER_TABLE)
                        ->select("*")
                        ->where("email", "=", $http_user_email)
                        ->go_and_get();
        
        if ($response) {
            return array(
                "result" => false,
                "message" => STRINGS["EPDH4"],
                "user" => $user_data
            );
        }

        $response = $db->in(DB_USER_TABLE)
                       ->update("email", $http_user_email)
                       ->where("id", "=", $user_data["id"])
                       ->go();
        
        if (!$response) {
            return array(
                "result" => false,
                "message" => STRINGS["EPDH5"],
                "user" => $user_data
            );
        }

        $log->create($user->get("name"), "change-email", $user_data["email"] . ":" . $http_user_email);
        
        $user_data["email"] = $http_user_email;
        
        return array(
            "result" => true,
            "redirect" => "./?signout",            
        );
    }
    
    // 비밀번호 변경
    if (!empty($http_user_new_password)) {
        $page_focus = 2;
        
        if (strcmp($http_user_new_password, $http_user_new_password_re) != 0) {
            return array(
                "result" => false,
                "message" => STRINGS["EPDH7"],
                "user" => $user_data
            );
        }
        
        if (strlen($http_user_new_password) < 4) {
            return array(
                "result" => false,
                "message" => STRINGS["EPDH8"],
                "user" => $user_data,
            );
        }
        
        $http_user_new_password = hash_password($http_user_new_password);

        $response = $db->in(DB_USER_TABLE)
                       ->update("password", $http_user_new_password)
                       ->where("id", "=", $user->get("id"))
                       ->go();
        
        if (!$response) {
            return array(
                "result" => false,
                "message" => STRINGS["EPDH10"],
                "user" => $user_data
            );
        }
        
        $log->create($user->get("name"), "change-password", "");

        return array(
            "result" => true,
            "redirect" => "./?signout",
        );
    }
    
    // 계정 삭제
    if (!empty($http_user_password_drop)) {
        
        if (strcmp($user_data["password"], hash_password($http_user_password_drop)) != 0) {
            return array(
                "result" => false,
                "message" => STRINGS["EPDH9"],
                "user" => $user_data
            );
        }

        $response = $db->in(DB_USER_TABLE)
                       ->delete()
                       ->where("id", "=", $user->get("id"))
                       ->go();
        
        if (!$response) {
            return array(
                "result" => false,
                "message" => STRINGS["EPDH12"],
                "user" => $user_data
            );
        }

        $log->create($user->get("name"), "delete-account", "");
        
        return array(
            "result" => true,
            "redirect" => "./?signout"
        );
    }

    return array(
        "result" => true,
        "user" => $user_data
    );
}
