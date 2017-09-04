<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 02
 */

require_once __DIR__ . "/common.php";
require_once __DIR__ . "/module.db.php";
require_once __DIR__ . "/module.form.php";
require_once __DIR__ . "/module.user.php";
require_once __DIR__ . "/module.redirect.php";

function process() {
    
    global $db;
    global $get;
    global $user;
    global $redirect;

    $http_article_title = $get->retrieve("t");
    $http_article_id = $get->retrieve("i");
    $http_revisions_page = intval($get->retrieve("p") !== null ? $get->retrieve("p") : "0");
    
    $read_by_id = !empty($http_article_id);
    
    if (empty($http_article_title) && empty($http_article_id)) {
        $redirect->set(get_theme_path() . HREF_MAIN);
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

    $article_data = $db->limit("0")->go_and_get();
    
    if (!$article_data) {
        return array(
            "result" => false,
            "message" => STRINGS["EPRV1"]
        );
    }
    
    $revisions_data = $db->in(DB_REVISION_TABLE)
                         ->select("*")
                         ->where("article_id", "=", $article_data["id"])
                         ->order_by("`timestamp` DESC")
                         ->limit(($http_revisions_page * MAX_REVISIONS) . "," . MAX_REVISIONS)
                         ->go_and_get_all();

    if (!$revisions_data) {
        return array(
            "result" => false,
            "message" => STRINGS["EPRV0"]
        );
    }

    return array(
        "result" => true,
        "page" => $http_revisions_page,
        "article" => $article_data,
        "revisions" => $revisions_data,
    );
}
