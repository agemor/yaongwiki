<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */

require_once 'common.php';
require_once 'common.db.php';
require_once 'common.session.php';
require_once 'tools/tool.html-purifier.php';

const DELETE_REVISIONS = false;

function main()
{
    global $session;
    global $db_connect_info;

    $http_article_title             = trim(!empty($_POST['article-title']) ? $_POST['article-title'] : $_GET['t']);
    $http_article_id                = trim(!empty($_POST['article-id']) ? $_POST['article-id'] : $_GET['i']);
    $http_article_new_title         = strip_tags(trim($_POST['article-new-title']));
    $http_article_content           = $_POST['article-content'];
    $http_article_tags              = preg_replace('!\s+!', ' ', strip_tags($_POST['article-tags']));
    $http_article_delete            = isset($_POST['article-delete']);
    $http_article_change_permission = isset($_POST['article-permission']);
    $http_article_permission        = abs(intval($_POST['article-permission']));
    $http_article_comment           = strip_tags($_POST['article-comment']);
    
    $read_by_id = !empty($http_article_id);
    
    // 파라미터가 충분하지 않음
    if (empty($http_article_title) && empty($http_article_id)) {
        navigateTo(HREF_MAIN);
    }
    
    // 로그인 되어있지 않을 경우 로그인 유도
    if (!$session->started()) {
        navigateTo(HREF_SIGNIN . '?redirect=' . HREF_WRITE . '/' . ($read_by_id ? $http_article_id : $http_article_title));
    }
    
    $db = new YwDatabase($db_connect_info);
    
    if (!$db->connect()) {
        return array(
            'result'=>false,
            'message'=>'서버와의 연결에 실패했습니다'
        );
    }
    
    if ($read_by_id) {
        $query = "SELECT * FROM " . ARTICLE_TABLE . " WHERE `id`='$http_article_id' LIMIT 1;";
    } else {
        $query = "SELECT * FROM " . ARTICLE_TABLE . " WHERE `title`='$http_article_title' LIMIT 1;";
    }
    
    if (!$db->query($query)) {
        return array(
            'result'=>false,
            'message'=>'글을 읽어오던 중 서버 에러가 발생했습니다'
        );
    }
    
    if ($db->total_results() < 1) {
        if (!$read_by_id) {
            navigateTo(HREF_SUGGEST . '?t=' . $http_article_title);
        }
        return array(
            'result'=>false,
            'message'=>'존재하지 않는 지식입니다'
        );
    }
    
    $article = $db->get_result();
    $article_old = $article;
    
    // 편집 권한 검사
    if (intval($article['permission']) > $session->permission) {
        return array(
            'result'=>false,
            'article'=>$article,
            'message'=>'이 지식을 편집하기 위한 권한이 부족합니다'
        );
    }
    
    // 별다른 편집 문자열이 들어오지 않았으면 편집 모드로 들어간다.
    if (empty($http_article_content)) {
        return array(
            'result'=>true,
            'article'=>$article,
            'message'=>''
        );
    }
    
    // 게시글 삭제 명령
    if ($http_article_delete) {
        if (!$db->query("DELETE FROM " . ARTICLE_TABLE . " WHERE `id`='" . $article['id'] . "';")) {
            return array(
                'result'=>false,
                'message'=>'게시글 삭제에 실패했습니다'
            );
        }
        
        if (DELETE_REVISIONS) {
            $db->query("DELETE FROM " . REVISION_TABLE . " WHERE `article_id`='" . $article['id'] . "';");
        }
        
        navigateTo(HREF_READ . '/' . $article['title']);
    }
    
    // 글 내용 필터링
    if ($session->permission < PERMISSION_NO_FILTERING) {
        $http_article_content = getHtmlPurifier($http_article_content);
    }
    
    $article['content'] = $http_article_content;
    $article['tags'] = $http_article_tags;

    $query = "UPDATE " . ARTICLE_TABLE . " SET ";
    
    // 타이틀 유효성 검사
    if (!empty($http_article_new_title) && strcmp($http_article_new_title, $article['title']) != 0) {
        if (strlen(preg_replace('/\s+/', '', $http_article_new_title)) < 2) {
            return array(
                'result'=>false,
                'article'=>$article,
                'message'=>'제목은 최소 두 글자 이상이어야 합니다'
            );
        }
        
        if (is_numeric($http_article_new_title)) {
            return array(
                'result'=>false,
                'article'=>$article,
                'message'=>'지식 제목으로 숫자를 사용할 수 없습니다'
            );
        }
        
        if (!$db->query("SELECT 1 FROM " . ARTICLE_TABLE . " WHERE `title`='" . $db->purify($http_article_new_title) . "';")) {
            return array(
                'result'=>false,
                'article'=>$article,
                'message'=>'지식 제목을 검증하는데 서버 오류가 발생했습니다'
            );
        }
        
        if ($db->total_results() > 0) {
            return array(
                'result'=>false,
                'article'=>$article,
                'message'=>'이미 존재하는 지식 제목입니다'
            );
        }
        
        $article['title'] = $http_article_new_title;
        $query .= "`title`='" . $db->purify($http_article_new_title) . "', ";
    }
    
    // 퍼미션 유효성 검사
    if ($http_article_change_permission) {
        if ($http_article_permission > $session->permission) {
            return array(
                'result'=>false,
                'article'=>$article,
                'message'=>'자신의 권한보다 지식 수정 권한을 크게 설정할 수 없습니다'
            );
        }
        
        $article['permission'] = $http_article_permission;
        $query .= "`permission`='" . $http_article_permission . "', ";
    }
    
    // 태그. 중간 공백을 하나로 설정한다.
    $query .= "`tags`='" . $db->purify($http_article_tags) . "', ";
    $query .= "`content`='" . $db->purify($http_article_content) . "' ";
    $query .= "WHERE `id`='" . $article['id'] . "';";
    
    if (!$db->query($query)) {
        return array(
            'result'=>false,
            'article'=>$article,
            'message'=>'지식 업데이트 중 서버 오류가 발생했습니다'
        );
    }
    
    // 가장 최근의 revision 레코드 넘버를 가져온다.
    if (!$db->query("SELECT `revision` FROM " . REVISION_TABLE . " WHERE `article_id`='" . $article['id'] . "' ORDER BY `timestamp` DESC LIMIT 1;")) {
        return array(
            'result'=>false,
            'article'=>$article,
            'message'=>'수정 기록을 불러오던 중 서버 오류가 발생했습니다'
        );
    }
    
    $result = $db->get_result();
    
    if ($db->total_results() < 1) {
        $article_recent_revision_number = 0;
    } else {
        $article_recent_revision_number = intval($result['revision']);
    }
    
    
    if (!$db->query("INSERT INTO " . REVISION_TABLE . " (`article_id`, `article_title`, `revision`,"
        . " `user_name`, `snapshot_content`, `snapshot_tags`, `fluctuation`, `comment`) VALUES ('"
        . $article_old['id'] . "', '" . $db->purify($article_old['title']) . "', " . ($article_recent_revision_number + 1) . ", '"
        . $session->name . "', '" . $db->purify($article_old['content']) . "', '" . $db->purify($article_old['tags']) . "', "
        . (strlen($http_article_content) - strlen($article_old['content'])) . ", '" . $db->purify($http_article_comment) . "');")) {
        return array(
            'result'=>false,
            'message'=>"수정 기록을 추가하던 중 서버 오류가 발생했습니다"
        );
    }

    $db->log($session->name, LOG_WRITE, $article['id'] . '/' . ($article_recent_revision_number + 1));
    $db->close();
    
    navigateTo(HREF_READ . '/' . $article['title'] . '?update=1');
    
    return array(
        'result'=>true,
        'article'=>$article,
        'message'=>''
    );
}
