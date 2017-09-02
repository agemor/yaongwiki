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
require_once 'tools/tool.recaptcha.php';

function main()
{
    global $session;
    global $db_connect_info;
    global $http_user_name;
    global $http_user_email;

    if ($session->started()) {
        navigateTo(HREF_MAIN);
    }

    $http_user_name        = trim($_POST['user-name']);
    $http_user_password    = trim($_POST['user-password']);
    $http_user_password_re = trim($_POST['user-password-re']);
    $http_user_email       = trim($_POST['user-email']);
    
    // 입력 값의 유효성을 검증한다.
    if (empty($http_user_name) || empty($http_user_password) || empty($http_user_email)) {
        return array(
            'result'=>true,
            'message'=>''
        );
    }
    
    if (strlen($http_user_name) < 2) {
        return array(
            'result'=>false,
            'message'=>'아이디는 3자 이상으로 입력해 주세요'
        );
    }
    
    if (strlen($http_user_password) < 5) {
        return array(
            'result'=>false,
            'message'=>'비밀번호는 4자 이상으로 입력해 주세요'
        );
    }
    
    if (strcmp($http_user_password, $http_user_password_re) != 0) {
        return array(
            'result'=>false,
            'message'=>'비밀번호와 비밀번호 확인이 일치하지 않습니다'
        );
    }
    
    // 이메일 포멧의 유효성을 검증한다.
    if (!filter_var($http_user_email, FILTER_VALIDATE_EMAIL)) {
        return array(
            'result'=>false,
            'message'=>'올바르지 않은 이메일 주소입니다'
        );
    }
    
    // reCAPTCHA를 검증한다.
    if (!getReCaptcha()) {
        return array(
            'result'=>false,
            'message'=>'reCAPTCHA가 올바르게 입력되지 않았습니다'
        );
    }
    
    $db = new YwDatabase($db_connect_info);
    
    // 데이터베이스 연결을 체크한다.
    if (!$db->connect()) {
        return array(
            'result'=>false,
            'message'=>'서버와의 연결에 실패했습니다'
        );
    }
    
    // 아이디와 이메일 유효성을 검증한다.
    if (!$db->query("SELECT `name` FROM " . USER_TABLE . " WHERE `name`='" . $db->purify($http_user_name) . "' OR `email`='" . $db->purify($http_user_email) . "';")) {
        return array(
            'result'=>false,
            'message'=>'유저 정보를 불러오는데 실패하였습니다'
        );
    }
    
    if ($db->total_results() > 0) {
        $result = $db->get_result();
        if (strcmp($http_user_name, $result['name']) == 0) {
            return array(
                'result'=>false,
                'message'=>'이미 사용중인 아이디입니다'
            );
        } else {
            return array(
                'result'=>false,
                'message'=>'이미 사용중인 이메일 주소입니다'
            );
        }
    }
    
    // 서버로 데이터를 전송한다.
    if (!$db->query("INSERT INTO " . USER_TABLE . " (`name`, `password`, `email`) VALUES ('" . $db->purify($http_user_name) . "', '" . passwordHash($http_user_password) . "', '" . $db->purify($http_user_email) . "');")) {
        return array(
            'result'=>false,
            'message'=>'계정을 생성하는데 실패했습니다'
        );
    }
    
    $db->log($http_user_name, LOG_SIGNUP, '1');
    $db->close();
    
    navigateTo(HREF_SIGNIN . '?signup=1');
    
    return array(
        'result'=>true,
        'message'=>''
    );
}
