<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */

require_once "core.php";
require_once "core.db.php";
require_once "core.session.php";

function process() {
    
    global $db;
    global $post;
    global $user;
    global $redirect;

    $http_article_title = $post->retrieve("article-title");

    if (!$user->signined()) {

        $redirect->set(get_theme_path() . HREF_CREATE);

        return array(
            "redirect" => true
        ); 
    }

    if (strlen(preg_replace("/\s+/", "", $http_article_title)) < 2)
        return array(
            "result" => false,
            "message" => STRINGS["EPCR0"]
        ); 
    
    if (is_numeric($http_article_title))
        return array(
            "result" => false,
            "message"=> STRINGS["EPCR1"]
        );
    
    $response = $db->in(DB_ARTICLE_TABLE)
                   ->select("*")
                   ->where("title", "=", $http_article_title)
                   ->go_and_get();
    
    if (!$response)
        return array(
            "result" => false,
            "message" => STRINGS["EPCR2"]
        );
    
    if ($db->total_results() > 0)
        return array(
            "result" => false,
            "title" => $http_article_title,
            "message" => STRINGS["EPCR3"]
        );
    
    $response = $db->in(DB_ARTICLE_TABLE)
                   ->insert("title", $http_article_title)
                   ->go();

    if (!$response)
        return array(
            "result" => false,
            "title" => $http_article_title,
            "message" => STRINGS["EPCR4"]
        );
    
    $db->log($session->name, LOG_CREATE, $http_article_title);
    $db->close();
    
    navigateTo(HREF_WRITE . "/" . $http_article_title);
    
    return array(
        "result"=>true,
        "title"=>$http_article_title,
        ""
    );
}

?>