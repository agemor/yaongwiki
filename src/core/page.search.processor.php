<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 03
 */

require_once __DIR__ . "/common.php";
require_once __DIR__ . "/module.db.php";
require_once __DIR__ . "/module.form.php";
require_once __DIR__ . "/module.user.php";
require_once __DIR__ . "/module.redirect.php";

function process() {
    
    global $db;
    global $get;
    global $post;
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
    $query .= " LIMIT " . ($http_page * MAX_ARTICLES) . ", " . MAX_ARTICLES . ";";
    
    // 정확히 제목이 일치하는 항목이 있으면 바로 이동
    if (count($keywords) == 1) {

        $response = $db->in(DB_ARTICLE_TABLE)
                       ->select("*")
                       ->where("title", "=", $keywords[0])
                       ->go_and_get();

        if ($response) {
            $redirect->set(get_theme_path() . HREF_READ . "?t=" . $response["title"]);
            return array(
                "redirect" => true
            );
        }
    }
    
    $start_time = microtime(true);
    
    // 검색 수행
    $search_result_data = $db->custom($query);
    if (!$search_result_data) {
        return array(
            "result" => false,
            "message" => STRINGS["EPSH1"]
        );
    }
    
    $elapsed_time = round(microtime(true) - $start_time, 5);

    return array(
        "result" => true,
        "search_result" => $search_result_data,
        "keywords" => $keywords,
        "elapsed_time" => $elapsed_time
    );
}

function get_tag_search_query($keywords, $fulltext = FULL_TEXT_SEARCH) {
    
    // FullText 검색
    if ($fulltext) {
        $match = "MATCH(`tags`) AGAINST('";
        for ($i = 0; $i < count($keywords); $i++) {
            if (strlen($keywords[$i]) > 0) {
                $match .= ($i > 0 ? " " : "") . $keywords[$i];
            }
        }
        $match .= "' IN BOOLEAN MODE)";
        
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

function get_content_search_query($keywords, $fulltext = FULL_TEXT_SEARCH) {
    
    // FullText 검색
    if ($fulltext) {
        $against = "AGAINST('";
        for ($i = 0; $i < count($keywords); $i++) {
            if (strlen($keywords[$i]) > 0) {
                $against .= ($i > 0 ? " " : "") . "*" . $keywords[$i] . "*";
            }
        }
        $against .= "' IN BOOLEAN MODE)";
        
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