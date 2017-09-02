<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 12
 */

require_once 'common.php';
require_once 'common.db.php';
require_once 'common.session.php';
require_once 'tools/tool.recaptcha.php';
require_once 'tools/tool.mailer.php';

function main() {

    global $session;
    global $db_connect_info;
    global $http_user_email;
    
    if ($session->started())
        navigateTo(HREF_MAIN);
    
    $http_user_email = trim($_POST['user-email']);
    
    // 입력 값의 유효성을 검증한다.
    if (empty($http_user_email))
        return array(
            'result'=>true,
            'message'=>''
        );
    
    // 이메일 포멧의 유효성을 검증한다.
    if (!filter_var($http_user_email, FILTER_VALIDATE_EMAIL))
        return array(
            'result'=>false,
            'message'=>'이메일 주소가 올바르지 않습니다'
        );
    
    // reCAPTCHA를 검증한다.
    if (!getReCaptcha())
        return array(
            'result'=>false,
            'message'=>'reCAPTCHA가 올바르게 입력되지 않았습니다'
        );
    
    $db = new YwDatabase($db_connect_info);
    
    // 데이터베이스 연결을 체크한다.
    if (!$db->connect())
        return array(
            'result'=>false,
            'message'=>'서버와의 연결에 실패했습니다'
        );
    
    // 아이디와 이메일 유효성을 검증한다.
    if (!$db->query("SELECT `name` FROM " . USER_TABLE . " WHERE `email`='" . $db->purify($http_user_email) . "';"))
        return array(
            'result'=>false,
            'message'=>'이메일 주소를 조회하는데 실패했습니다'
        );
    
    if ($db->total_results() < 1) {
        return array(
            'result'=>false,
            'message'=>'존재하지 않는 이메일 주소입니다'
        );
    }
    
    $result    = $db->get_result();
    $user_name = $result['name'];
    
    // 새로운 비밀번호를 생성한다.
    $generated_password = bin2hex(openssl_random_pseudo_bytes(6));
    
    if (!$db->query("UPDATE " . USER_TABLE . " SET `password`='" . passwordHash($generated_password) . "' WHERE `email`='" . $db->purify($http_user_email) . "';")) {
        return array(
            'result'=>false,
            'message'=>'비밀번호를 업데이트하는데 실패했습니다'
        );
    }
    
    $email_content = "<b>" . $user_name . "</b> 회원님의 새 비밀번호는 <b>" . $generated_password . "</b>입니다.";
    if (!getMailer($http_user_email, "연세위키 비밀번호를 알려드립니다", $email_content))
        return array(
            'result'=>false,
            'message'=>'이메일 발송에 실패했습니다'
        );
    
    $db->log($user_name, LOG_RESET, '1');
    $db->close();
    
    return array(
        'result'=>true,
        'message'=>'이메일로 아이디와 새로운 비밀번호를 전송했습니다'
    );
}