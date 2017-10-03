<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 31
 */

require_once __DIR__ . "/common.php";

function process($max_displayed_in_one_page = 10) {
    
    $db = Database::get_instance();
    $user = UserManager::get_instance();
    $log = LogManager::get_instance();
    $http_vars = HttpVarsManager::get_instance();

    $http_user_name = $http_vars->get("user-name");
    $http_user_info = $http_vars->get("user-info");
    $http_user_commit_page = $http_vars->get("p");
    
    if (empty($http_user_name) || strlen($http_user_name) < 2) {
        return array(
            "result" => false,
            "message" => STRINGS["EPPF0"],
            "redirect" => "./?page-not-found",
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
            "message" => STRINGS["EPPF0"],
            "redirect" => "./?page-not-found",
        );
    }
    
    $contribution_data = $db->in(DB_REVISION_TABLE)
                    ->select("*")
                    ->where("user_name", "=", $http_user_name)
                    ->order_by("`timestamp` DESC")
                    ->limit(($http_user_commit_page * $max_displayed_in_one_page) . "," . $max_displayed_in_one_page)
                    ->go_and_get_all();
    
    if (!$contribution_data) {
        return array(
            "result" => false,
            "message" => STRINGS["EPPF1"],
            "user" => $user_data
        );
    }

    $user_data["contributions"] = $contribution_data;

    // 자기소개 업데이트
    if (!empty($http_user_info)) {
        if ($user->get("id") != $user_data["id"]) {
            return array(
                "result" => false,
                "message" => STRINGS["EPPF2"],
                "user" => $user_data
            );
        }

        $response = $db->in(DB_USER_TABLE)
                       ->update("info", strip_tags($http_user_info))
                       ->where("id", "=", $user_data["id"])
                       ->go();

        if (!$response) {
            return array(
                "result" => false,
                "message" => STRINGS["EPPF2"],
                "user" => $user_data,
            );
        }

        $log->create($user->get("name"), "update-user-info", strip_tags($http_user_info));

        return array(
            "result" => true,
            "redirect" => "./?profile&user-name=" . $user_data["name"]
        );
    }

    return array(
        "result" => true,
        "user" => $user_data,
        "page" => $http_user_commit_page
    );
}
