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

function main() {
    
    global $session;
    global $db_connect_info;

    $http_revision_id    = trim($_GET['i']);
    $http_revision_target_id    = trim($_GET['j']);

    $orginal_target = intval($http_revision_target_id) == 0;
    
    if (empty($http_revision_id) || empty($http_revision_target_id))
        return array(
            'result'=>false
        );
    
    $db = new YwDatabase($db_connect_info);
    
    // 데이터베이스 연결을 체크합니다.
    if (!$db->connect())
        return array(
            'result'=>'서버와의 연결에 실패했습니다'
        );
    
    if (!$db->query("SELECT * FROM ".REVISION_TABLE." WHERE `id`='".$http_revision_id."' LIMIT 1;"))
        return array(
            'result'=>'글을 읽어오던 중 서버 에러가 발생했습니다'
        );
    
    if ($db->total_results() < 1)
        return array(
            'result'=>'존재하지 않는 지식입니다'
        );
    
    $original = $db->get_result();

    // j값이 0일 경우 원본을 비교 대상으로 지정한다.
    if ($orginal_target)
        $query = "SELECT * FROM ".ARTICLE_TABLE." WHERE `id`='".$revision['article_id']."' LIMIT 1;";
    else
        $query = "SELECT * FROM ".REVISION_TABLE." WHERE `id`='".$http_revision_target_id."' LIMIT 1;";

    if (!$db->query($query))
        return array(
            'result'=>'글을 읽어오던 중 서버 에러가 발생했습니다'
        );

    if ($db->total_results() < 1)
        return array(
            'result'=>'존재하지 않는 지식입니다'
        );

    if ($orginal_target) {

        $result = $db->get_result();
        $revision['id'] = 0;
        $revision['article_title'] = $result['title'];
        $revision['snapshot_content'] = $result['content'];
        $revision['snapshot_tags'] = $result['tags'];
        $revision['timestamp'] = $result['timestamp'];

    } else {
        $revision = $db->get_result();
    }

    $db->close();
    return array(
        'original'=>$original,
        'revision'=>$revision
    );
}

$page_response = main();

echo json_encode($page_response);

?>