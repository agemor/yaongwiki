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

const DINNER_END = 19 * 60;

function main() {

    global $session;
    global $db_connect_info;    

    $db = new YwDatabase($db_connect_info);
        
    if (!$db->connect())
        return array(
            'result'=>'서버와의 연결에 실패했습니다'
        );
    
    date_default_timezone_set('Asia/Seoul');
    
    $now_time = intval(date("H")) * 60 + intval(date("i"));
    
    // 만약 석식 시간이 지났으면 다음날 식단 보여주기
    if ($now_time > DINNER_END)
        $now_date = date('Y-m-d', strtotime('+1 day'));
    else
        $now_date = date("Y-m-d");

    if (!$db->query("SELECT `content` FROM " . ARTICLE_TABLE . " WHERE `title`='" . $now_date . " 국제캠퍼스 학식 정보' LIMIT 1;"))
        return array(
            'result'=>false
        );
    
    $result = $db->get_result();
    return array(
        'result'=>$result['content']
    );
}

$page_response = main();

echo $page_response['result'];
?>