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
    
    $http_article_title = $http_vars->get("article-title");

    // 로그인 되어 있지 않을 경우
    if (!$user->authorized()) {
        return array(
            "result" => false,
            "redirect" => "./?signin&redirect=./?create",
            "message" => STRINGS["ESDB1"]
        );
    }

    if (empty($http_article_title)) {
        return array(
            "result" => true
        );
    }

    // 제목 길이가 너무 짧을 경우
    if (strlen(preg_replace("/\s+/", "", $http_article_title)) < 2) {
        return array(
            "result" => false,
            "message" => STRINGS["EPCR0"]
        );
    }

    if (!$db->connect()) {
        return array(
            "result" => false,
            "message" => STRINGS["ESDB0"],
            "redirect" => "./?out-of-service"
        );
    }
    
    $response = $db->in(DB_ARTICLE_TABLE)
                   ->select("*")
                   ->where("title", "=", $http_article_title)
                   ->go_and_get();
    
    if ($response) {
        return array(
            "result" => false,
            "message" => STRINGS["EPCR3"],
            "title" => $http_article_title            
        );
    }

    $response = $db->in(DB_USER_TABLE)
                   ->update("total_contributions", "`total_contributions` + 1", true)
                   ->where("id", "=", $user->get("id"))
                   ->go();
    
    $response_1 = $db->in(DB_REVISION_TABLE)
                     ->insert("article_id", $response["id"])
                     ->insert("article_title", $http_article_title)
                     ->insert("revision", "0")
                     ->insert("user_name", $user->get("name"))
                     ->insert("snapshot_content", "")
                     ->insert("snapshot_tags", "")
                     ->insert("fluctuation", "0")
                     ->insert("comment", STRINGS["SPCR0"])
                     ->go();
    
    $initial_revision_id = $db->last_insert_id();
                     
    $response_2 = $db->in(DB_ARTICLE_TABLE)
                     ->insert("title", $http_article_title)
                     ->insert("latest_revision_id", $initial_revision_id)
                     ->go();
    
    $article_id = $db->last_insert_id();

    $response = $db->in(DB_REVISION_TABLE)
                   ->update("article_id", $article_id)
                   ->where("id", "=", $initial_revision_id)
                   ->go();

    if (!$response_1 || !$response_2) {
        return array(
            "result" => false,            
            "message" => STRINGS["EPCR4"],
            "title" => $http_article_title
        );
    }

    $log->create($user->get("name"), "create", $http_article_title);
    
    return array(
        "result" => true,
        "redirect" => "./?write&" . "t=" . $http_article_title
    );
}
