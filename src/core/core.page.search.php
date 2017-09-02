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
    global $post;
    global $get;
    global $user;
    global $redirect;

    $http_query = $get->retrieve("q") !== null ? $get->retrieve("q") : $post->retrieve("q");
    $http_page  = intval($get->retrieve("p") !== null ? $get->retrieve("p") : "0");
    
    if (empty($http_query)) {
        return array(
            "result" => false,
            "message" => STRINGS["EPSH0"]
        );
    }
    
    // 검색 모드 판별
    $tag_search_mode = (strlen($http_query) > 1) && (strcmp($http_query{0}, "@") == 0);
    if ($tag_search_mode) {
        $http_query = substr($http_query, 1);
    }
    
    // 쿼리 유효성 검증
    $http_query = preg_replace("/\s+/", " ", $http_query);
    if (strlen($http_query) < 1) {
        return array(
            "result" => false,
            "message" => STRINGS["EPSH0"]
        );
    }
    
    // 검색 쿼리 취득
    $keywords = explode(" ", $http_query);
    $query = $tag_search_mode ? get_tag_search_query($keywords) : get_content_search_query($keywords);
    
    
    // 정확히 제목이 일치하는 항목이 있으면 바로 이동
    if (count($keywords) == 1) {

        $response = $db->in(DB_ARTICLE_TABLE)

        if (!$db->query("SELECT 1 FROM " . ARTICLE_TABLE . " WHERE `title`="" . $keywords[0] . "";")) {
            return array(
                "result" => false,
                "message" => "검색 결과를 가져오는데 실패했습니다".("SELECT 1 FROM " . ARTICLE_TABLE . " WHERE `title`="" . $keywords[0] . "";")
            );
        }
        if ($db->total_results() > 0) {
            navigateTo(HREF_READ . "/" . $keywords[0]);
        }
    }
    
    $start_time = microtime(true);
    
    // 전체 검색 결과를 얻기 위해 먼저 서치
    if (!$db->query($query)) {
        return array(
            "result" => false,
            "message" => "검색 결과를 가져오는데 실패했습니다2"
        );
    }
    
    $elapsed_time   = round(microtime(true) - $start_time, 5);
    $total_articles = $db->total_results();
    
    // 현재 페이지 결과 가져오기
    $query .= " LIMIT " . ($http_page * MAX_ARTICLES) . ", " . MAX_ARTICLES . ";";
    
    if (!$db->query($query)) {
        return array(
            "result" => false,
            "message" => "검색 결과를 가져오는데 실패했습니다3"
        );
    }

    $search_result = array();
    while ($result = $db->get_result()) {
        array_push($search_result, $result);
    }
    
    return array(
        "result" => true,
        "search_result" => $search_result,
        "keywords" => $keywords,
        "total_results" => $total_articles,
        "elapsed_time" => $elapsed_time
    );
}

function get_tag_search_query($keywords, $fulltext = FULL_TEXT_SEARCH)
{
    
    // FullText 검색
    if ($fulltext) {
        $match = "MATCH(`tags`) AGAINST("";
        for ($i = 0; $i < count($keywords); $i++) {
            if (strlen($keywords[$i]) > 0) {
                $match .= ($i > 0 ? " " : "") . $keywords[$i];
            }
        }
        $match .= "" IN BOOLEAN MODE)";
        
        $query = "SELECT *, ";
        $query .= $match . " AS relevance ";
        $query .= "FROM " . ARTICLE_TABLE . " ";
        $query .= "WHERE " . $match . " ";
        $query .= "ORDER BY (relevance * `hits`) DESC";
    }
    
    // 일반 검색
    else {
        $query = "SELECT * FROM " . ARTICLE_TABLE . " WHERE ";
        for ($i = 0; $i < count($keywords); $i++) {
            if (strlen($keywords[$i]) > 0) {
                $query .= ($i > 0 ? " OR " : "");
                $query .= "`tags` LIKE "%" . $keywords[$i] . "%"";
            }
        }
        $query .= " ORDER BY `hits` DESC";
    }
    
    return $query;
}

function get_content_search_query($keywords, $fulltext = FULL_TEXT_SEARCH)
{
    
    // FullText 검색
    if ($fulltext) {
        $against = "AGAINST("";
        for ($i = 0; $i < count($keywords); $i++) {
            if (strlen($keywords[$i]) > 0) {
                $against .= ($i > 0 ? " " : "") . "*" . $keywords[$i] . "*";
            }
        }
        $against .= "" IN BOOLEAN MODE)";
        
        // 쿼리문 생성
        $query = "SELECT * ";
        $query .= "FROM " . ARTICLE_TABLE . " ";
        $query .= "WHERE MATCH(`title`, `content`) " . $against . " ";
        $query .= "ORDER BY `hits` DESC";
    }
    
    // 일반 검색
    else {
        $query = "SELECT * FROM " . ARTICLE_TABLE . " WHERE ";
        for ($i = 0; $i < count($keywords); $i++) {
            if (strlen($keywords[$i]) > 0) {
                $query .= ($i > 0 ? " OR " : "");
                $query .= "`title` LIKE" . ""%" . $keywords[$i] . "%" OR";
                $query .= "`content` LIKE" . ""%" . $keywords[$i] . "%" OR";
                $query .= "`tags` LIKE" . ""%" . $keywords[$i] . "%"";
            }
        }
        $query .= " ORDER BY `hits` DESC";
    }
    
    return $query;
}

function highlight($text, $keywords)
{
    foreach ($keywords as $keyword) {
        $text = preg_replace("|($keyword)|Ui", "<mark>$1</mark>", $text);
    }
    return $text;
}

function truncate($text, $limit, $break = ".", $pad = "...")
{
    if (strlen($text) <= $limit) {
        return $text;
    }
    if (false !== ($breakpoint = strpos($text, $break, $limit))) {
        if ($breakpoint < strlen($text) - 1) {
            $text = substr($text, 0, $breakpoint) . $pad;
        }
    }
    return $text;
}

function parseTags($tags)
{
    $chunks = explode(" ", $tags);
    $tags   = "";
    for ($i = 0; $i < count($chunks); $i++) {
        if (strlen($chunks[$i]) > 0) {
            $tags .= ($i > 0 ? "&nbsp;&nbsp;" : "") . "<a href="" . HREF_SEARCH . "?" . $chunks[$i] . "">#" . $chunks[$i] . "</a>";
        }
    }
    return $tags;
}
