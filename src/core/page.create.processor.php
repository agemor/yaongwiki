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

    $http_article_title = $post->retrieve("article-title");

    // 로그인 되어 있지 않을 경우
    if (!$user->signined()) {
        $redirect->set("/?signin&redirect=/?create");
        return array(
            "redirect" => true
        );
    }

    // 제목 길이가 너무 짧을 경우
    if (strlen(preg_replace("/\s+/", "", $http_article_title)) < 2) {
        return array(
            "result" => false,
            "message" => STRINGS["EPCR0"]
        );
    }
    
    // 제목이 숫자로만 구성되어 있을 경우
    if (is_numeric($http_article_title)) {
        return array(
            "result" => false,
            "message"=> STRINGS["EPCR1"]
        );
    }
    
    $response = $db->in(DB_ARTICLE_TABLE)
                   ->select("*")
                   ->where("title", "=", $http_article_title)
                   ->go_and_get();
    
    if (!$response) {
        return array(
            "result" => false,
            "message" => STRINGS["EPCR2"]
        );
    }
    
    // 이미 존재하는 제목일 경우
    if ($db->total_results() > 0) {
        return array(
            "result" => false,
            "title" => $http_article_title,
            "message" => STRINGS["EPCR3"]
        );
    }
    
    $response_1 = $db->in(DB_ARTICLE_TABLE)
                     ->insert("title", $http_article_title)
                     ->go();

    $response = $db->in(DB_ARTICLE_TABLE)
                   ->select("*")
                   ->where("title", "=", $http_article_title)
                   ->go_and_get();

    $response_2 = $db->in(DB_REVISION_TABLE)
                     ->insert("article_id", $response["id"])
                     ->insert("article_title", $http_article_title)
                     ->insert("revision", "0")
                     ->insert("user_name", $user->name)
                     ->insert("snapshot_content", "")
                     ->insert("snapshot_tags", "")
                     ->insert("fluctuation", "0")
                     ->insert("comment", "새로 만들어짐")
                     ->go();

    if (!$response_1 || !$response_2) {
        return array(
            "result" => false,
            "title" => $http_article_title,
            "message" => STRINGS["EPCR4"]
        );
    }

    $response = $db->in(DB_LOG_TABLE)
                   ->insert("behavior", "create")
                   ->insert("data", $http_article_title)
                   ->go();

    $redirect->set("/?write&" . "?t=" . $http_article_title);
    
    return array(
        "redirect" => true
    );
}
