<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 31
 */

require_once __DIR__ . "/common.php";
require_once __DIR__ . "/libs/parsedown.php";

const REDIRECT_KEYWORD = "#redirect";

function process() {

    $db = Database::get_instance();
    $user = UserManager::get_instance();
    $http_vars = HttpVarsManager::get_instance();

    $parsedown = new Parsedown();
    
    $http_article_title = $http_vars->get("t");
    $http_article_id = $http_vars->get("i");
    $http_no_redirect = $http_vars->get("no-redirect") != null;
    
    $read_by_id = !empty($http_article_id);
    
    if (empty($http_article_title) && empty($http_article_id)) {
        return array(
            "result" => false,
            "redirect" => "./?page-not-found"
        );
    }

    if (!$db->connect()) {
        return array(
            "result" => false,
            "message" => STRINGS["ESDB0"],
            "redirect" => "./?out-of-service"
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
        } else {
            $redirect->set("./?page-not-found");
        }
        return array(
            "redirect" => true
        );
    }
    
    // 리다이렉트 문서인지 체크
    $stripped_content = trim(strip_tags($article_data["content"]));

    if (!$http_no_redirect && starts_with($stripped_content, REDIRECT_KEYWORD)) {
        return array(
            "result" => true,
            "redirect" => "./?read&t=" . $trim(explode(" ", $stripped_content)[1]) . "?from=" . $article_data["title"]
        );
    }

    // 조회수 증가
    if ($user->visit(intval($article_data["id"]))) {
        
        $response = $db->in(DB_ARTICLE_TABLE)
                       ->update("hits", "`hits`+1", true)
                       ->update("today_hits", "`today_hits`+1", true)
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