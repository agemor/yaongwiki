<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */

require_once "common.php";
require_once "common.db.php";
require_once "common.session.php";

function main() {
    
    global $session;
    global $db_connect_info;

    $http_article_title = trim(strip_tags(empty($_POST["article-title"]) ? "" : $_POST["article-title"]));

    if (!$session->started())
        navigateTo(HREF_SIGNIN . "?redirect=" . HREF_CREATE . "?t=" . $http_article_title);

    if (strlen(preg_replace("/\s+/", "", $http_article_title)) < 2)
        return array(
            "result"=>true,
            "message"=>""
        );
    
    if (is_numeric($http_article_title))
        return array(
            "result"=>false,
            "message"=>"지식 제목으로 숫자를 사용할 수 없습니다"
        );
    
    $db = new YwDatabase($db_connect_info);
    
    if (!$db->connect())
        return array(
            "result"=>false,
            "title"=>$http_article_title,
            "message"=>"서버와의 연결에 실패했습니다"
        );
    
    if (!$db->query("SELECT 1 FROM " . ARTICLE_TABLE . " WHERE `title`=\"" . $http_article_title . "\";"))
        return array(
            "result"=>false,
            "title"=>$http_article_title,
            "message"=>"지식 정보를 조회하는데 실패했습니다"
        );
    
    if ($db->total_results() > 0)
        return array(
            "result"=>false,
            "title"=>$http_article_title,
            "message"=>"이미 존재하는 지식입니다"
        );
    
    // 지식 등록
    if (!$db->query("INSERT INTO " . ARTICLE_TABLE . " (`title`) VALUES ("" . $db->purify($http_article_title) . "");"))
        return array(
            "result"=>false,
            "title"=>$http_article_title,
            "message"=>"지식을 추가하는 중 서버 오류가 발생했습니다"
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