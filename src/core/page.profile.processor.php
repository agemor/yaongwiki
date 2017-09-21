<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 31
 */

require_once __DIR__ . "/common.php";
require_once __DIR__ . "/module.db.php";
require_once __DIR__ . "/module.redirect.php";
require_once __DIR__ . "/module.form.php";
require_once __DIR__ . "/module.user.php";

const MAX_REVISIONS = 20;

function process() {
    
    global $db;
    global $post;
    global $get;
    global $redirect;
    global $user;

    $http_user_name = $get->retrieve("name") == null ? $post->retrieve("user-name") : $get->retrieve("name");
    $http_user_info = strip_tags($post->retrieve("user-info"));
    $http_user_commit_page = $get->retrieve("p") == null ? 0 : intval($get->retrieve("p"));

    if (empty($http_user_name)) {
        return array(
            "result" => false,
            "message" => STRINGS["EPPF0"]
        );
    }
    
    if (strlen($http_user_name) < 2) {
        return array(
            "result" => false,
            "message" => STRINGS["EPPF0"]
        );
    }
    
    $user_data = $db->in(DB_USER_TABLE)
                   ->select("*")
                   ->where("name", "=", $http_user_name)
                   ->go_and_get();

    if (!$user_data) {
        return array(
            "result" => false,
            "message" => STRINGS["EPPF0"]
        );
    }
    
    $contribution_data = $db->in(DB_REVISION_TABLE)
                    ->select("*")
                    ->where("user_name", "=", $http_user_name)
                    ->order_by("`timestamp` DESC")
                    ->limit(($http_user_commit_page * MAX_REVISIONS) . "," . MAX_REVISIONS)
                    ->go_and_get_all();
    
    if (!$contribution_data) {
        return array(
            "result" => false,
            "user" => $user_data,
            "message" => STRINGS["EPPF1"]
        );
    }

    $user_data["contributions"] = $contribution_data;

    // 자기소개 업데이트
    if (!empty($http_user_info)) {
        if ($user->id != $user_data["id"]) {
            return array(
                "result" => false,
                "user" => $user_data,
                "message" => STRINGS["EPPF2"]
            );
        }

        $response = $db->in(DB_USER_TABLE)
                       ->update("info", $http_user_info)
                       ->where("id", "=", $user_data["id"])
                       ->go();

        if (!$response) {
            return array(
                "result" => false,
                "user" => $user_data,
                "message" => STRINGS["EPPF2"]
            );
        }

        $user_data["info"] = $http_user_info;

        $response = $db->in(DB_LOG_TABLE)
                       ->insert("behavior", "update-user-info")
                       ->insert("data", $http_user_info)
                       ->go();

        $redirect->set("./?profile&name=" . $user_data["name"]);
        return array(
            "redirect" => true
        );
    }

    return array(
        "result" => true,
        "user" => $user_data,
        "page" => $http_user_commit_page
    );
}
