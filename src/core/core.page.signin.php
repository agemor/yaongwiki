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

function main()
{
    global $session;
    global $db_connect_info;

    $http_user_name     = trim(strip_tags($_POST['user-name']));
    $http_user_password = trim($_POST['user-password']);
    $http_redirect      = empty($_POST['redirect']) ? HREF_MAIN : $_POST['redirect'];
    
    if ($session->started()) {
        navigateTo(HREF_MAIN);
    }
    
    if (empty($http_user_name) || empty($http_user_password)) {
        return array(
            'result'=>true,
            'message'=>''
        );
    }

    $db = new YwDatabase($db_connect_info);
    
    if (!$db->connect()) {
        return array(
            'result'=>false,
            'message'=>'서버와의 연결에 실패했습니다'
        );
    }
    
    // 아이디가 유효한지 확인합니다.
    if (!$db->query("SELECT * FROM " . USER_TABLE . " WHERE `name`='" . $db->purify($http_user_name) . "';")) {
        return array(
            'result'=>false,
            'message'=>'유저 정보를 불러오는데 실패했습니다'
        );
    }
    
    if ($db->total_results() < 1) {
        return array(
            'result'=>false,
            'message'=>'존재하지 않는 아이디입니다'
        );
    }
    
    $result = $db->get_result();
    
    // 비밀번호가 일치하는지 확인합니다.
    if (strcmp(passwordHash($http_user_password), $result['password']) != 0) {
        $db->log($session->ip, LOG_SIGNIN, '0');
        return array(
            'result'=>false,
            'message'=>'비밀번호가 올바르지 않습니다'
        );
    }

    // 세션 등록
    $session->start($result['name'], $result['id'], intval($result['permission']));
    $db->log($session->name, LOG_SIGNIN, '1');
    $db->close();
    
    navigateTo($http_redirect);
    
    return array(
        'result'=>true,
        'message'=>''
    );
}
