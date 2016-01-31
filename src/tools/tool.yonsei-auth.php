<?php
/*
 * 연세포탈 로그인 인증 수행 모듈
 * 
 * @author HyunJun Kim
 * @date 2016. 1. 21
 */

const YONSEI_LOGIN_URL = 'https://infra.yonsei.ac.kr/lauth/YLLOGIN.do';
const YONSEI_WACTION = 'aW50bHBvcnRhbA==';
const YONSEI_SCODE = 'bm9lbmNyeXB0';

function getYonseiAuth($id, $password) {
    
    $data = array(
        'id' => $id,
        'pw' => $password,
        'waction' => YONSEI_WACTION,
        'sCode' => YONSEI_SCODE,
        'returl' => 'verification.php'
    );
    
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context = stream_context_create($options);
    $result  = htmlspecialchars(file_get_contents(YONSEI_LOGIN_URL, false, $context));
    
    if ($result === FALSE || strlen($result) < 20 || empty($result)) {
        return false;
    } else {
        return true;
    }
}
?>