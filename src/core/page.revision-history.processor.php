<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 02
 */

require_once __DIR__ . "/common.php";

function process($max_displayed_in_one_page = 10) {
    
    $db = Database::get_instance();
    $user = UserManager::get_instance();
    $log = LogManager::get_instance();
    $http_vars = HttpVarsManager::get_instance();

    $http_article_title = $http_vars->get("t");
    $http_article_id = $http_vars->get("i");
    $http_revisions_page = intval($http_vars->get("p") !== null ? $http_vars->get("p") : "0");
    
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

    $article_data = $db->limit("0")->go_and_get();
    
    if (!$article_data) {
        return array(
            "result" => false,
            "message" => STRINGS["EPRV1"],
            "redirect" => "./?page-not-found"
        );
    }
    
    $revisions_data = $db->in(DB_REVISION_TABLE)
                         ->select("*")
                         ->where("article_id", "=", $article_data["id"])
                         ->order_by("`timestamp` DESC")
                         ->limit(($http_revisions_page * $max_displayed_in_one_page) . "," . $max_displayed_in_one_page)
                         ->go_and_get_all();

    if (!$revisions_data) {
        return array(
            "result" => false,
            "message" => STRINGS["EPRV0"],
            "redirect" => "./?page-not-found"
        );
    }

    return array(
        "result" => true,
        "page" => $http_revisions_page,
        "article" => $article_data,
        "history" => $revisions_data,
    );
}
