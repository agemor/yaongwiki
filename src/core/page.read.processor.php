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
require_once __DIR__ . "/module.form.php";
require_once __DIR__ . "/module.user.php";
require_once __DIR__ . "/module.redirect.php";
require_once __DIR__ . "/libs/parsedown.php";

const REDIRECT_KEYWORD = "#redirect";

function process() {

    global $db;
    global $get;
    global $user;
    global $redirect;

    $parsedown = new Parsedown();
    
    $http_article_title = $get->retrieve("t");
    $http_article_id = $get->retrieve("i");
    $http_no_redirect = $get->retrieve("no-redirect") != null;
    
    $read_by_id = !empty($http_article_id);
    
    if (empty($http_article_title) && empty($http_article_id)) {
        $redirect->set("./");
        return array(
            "redirect" => true
        );
    }
    
    $db->in(DB_ARTICLE_TABLE)->select("*");

    if ($read_by_id) {
        $db->where("id", "=", $http_article_id);
    } else {
        $db->where("title", "=", $http_article_title);
    }

    $article_data = $db->go_and_get();
    
    if (!$article_data) {
        if (!$read_by_id) {
            $redirect->set("./?page-not-found&t=" . $http_article_title);
            return array(
                "redirect" => true
            );
        }
        return array(
            "result" => false,
            "message" => STRINGS["EPRD0"]
        );
    }
    
    // 리다이렉트 문서인지 체크
    $stripped_content = trim(strip_tags($article_data["content"]));

    if (!$http_no_redirect && starts_with($stripped_content, REDIRECT_KEYWORD)) {
        $redirect->set("./?read&t=" . $trim(explode(" ", $stripped_content)[1]) . "?from=" . $article_data["title"]);
        return array(
            "redirect" => true
        );
    }
    
    // 조회수 증가
    if ($user->visit(intval($article_data["id"]))) {
        $response = $db->in(DB_ARTICLE_TABLE)
                       ->update("hits", "`hits`+1")
                       ->update("today_hits", "`today_hits`+1")
                       ->where("id", "=", $article_data["id"])
                       ->go();
    }
    
    $article_data["content"] = $parsedown->text($article_data["content"]);
    $article_data["tags"] = parse_tags($article_data["tags"]);

    return array(
        "result" => true,
        "article" => $article_data
    );
}

function starts_with($haystack, $needle) {
    return $needle === "" || strrpos($haystack, $needle, - strlen($haystack)) !== false;
}

function parse_tags($tags_string) {
    $tags = explode(",", $tags_string);
    $new_tags = array();
    for ($i = 0; $i < count($tags); $i++) {
        $tag = trim($tags[$i]);
        if ($tag != "") {
            array_push($new_tags, $tag);
        }
    }
    return $new_tags;
}