<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 02
 */

require_once "core.php";
require_once "core.db.php";
require_once "core.session.php";
 
function process() {

    global $db;
    global $get;
    global $user;
    global $redirect;
    
    $http_revision_id = $get->retrieve("i");
    $http_revision_target_id = $get->retrieve("j");
    $http_rollback = !empty($get->retrieve("rollback"));
    
    $compare_to_original = intval($http_revision_target_id) == 0;
    
    if (empty($http_revision_id) || !isset($http_revision_target_id)) {
        $redirect->set(get_theme_path() . HREF_MAIN);
        return array(
            "redirect" => true
        );
    }
    
    $revision_data = $db->in(DB_REVISION_TABLE)
                        ->select("*")
                        ->where("id", "=", $http_revision_id)
                        ->go_and_get();
    
    if (!$revision_data) {
        return array(
            "result" => false,
            "message" => STRINGS["EPRV0"]
        );
    }
    
    $article_data = $db->in(DB_ARTICLE_TABLE)
                       ->select("*")
                       ->where("id", "=", $revision_data["article_id"])
                       ->go_and_get();
    
    if (!$article_data) {
        return array(
            "result" => false,
            "message" => STRINGS["EPRV1"]
        );
    }
    
    // 특정 버전으로 글 되돌리기
    if ($http_rollback) {
        if (!$user->signined()) {
            $redirect->set(get_theme_path() . HREF_MAIN);
            return array(
                "redirect" => true
            );
        }
        
        if ($user->permission < intval($article_data["permission"])) {
            return array(
                "result" => false,
                "message" => STRINGS["EPRV2"]
            );
        }

        $response = $db->in(DB_REVISION_TABLE)
                       ->select("revision")
                       ->where("article_id", "=", $article_data["id"])
                       ->order_by("`timestamp` DESC")
                       ->limit("1")
                       ->go_and_get();

        $response_1 = $db->in(DB_ARTICLE_TABLE)
                         ->update("content", $revision_data["snapshot_content"])
                         ->update("tags", $revision_data["snapshot_tags"])
                         ->update("title", $revision_data["article_title"])
                         ->where("id", $article_data["id"])
                         ->go();

        $response_2 = $db->in(DB_REVISION_TABLE)
                         ->insert("article_id", $article_data["id"])
                         ->insert("article_title", $article_data["title"])
                         ->insert("revision", intval($response["revision"]) + 1)
                         ->insert("user_name", $user->name)
                         ->insert("snapshot_content", $article_data["content"])
                         ->insert("snapshot_tags", $article_data["tags"])
                         ->insert("fluctuation", (strlen($revision_data["snapshot_content"]) - strlen($article_data["content"])))
                         ->insert("comment", $revision_data["revision"] . "으로부터 복구함")
                         ->go();

        if (!$response_1 || !$response_2) {
            return array(
                "result" => false,
                "message" => STRINGS["EPRV3"]
            );
        }
        
        $redirect->set(get_theme_path() . HREF_READ . "?i=" . $article_data["id"]);
        return array(
            "redirect" => true
        );
    }
    
    // 비교 대상 지정
    if ($compare_to_original) {
        $comparison_data = array(
            "id" => "0",
            "article_title" => $article_data["title"],
            "revision" => "Now",
            "snapshot_content" => $article_data["content"],
            "snapshot_tags" => $article_data["tags"],
            "timestamp" => $article_data["timestamp"],
        );
    } else {
        $comparison_data = $db->in(DB_REVISION_TABLE)->select("*")->where("id", "=", $http_revision_target_id);
        if (!$comparison_data) {
            return array(
                "result" => false,
                "message" => STRINGS["EPRV4"]
            );
        }
    }
    
    return array(
        "result" => true,
        "comparison_target" => $comparison_data,
        "revision" => $revision_data,
    );
}
