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

function main() {
    
    global $session;
    global $db_connect_info;

    $http_file = $_FILES['userfile'];

    if (!isset($http_file))
        return array(
            'result'=>''
        );
    
    if (!$session->started())
        return array(
            'result'=>'로그인한 사용자만 업로드가 가능합니다'
        );
    
    $file_size = $http_file['size'];
    if ($file_size > FILE_MAXIMUM_SIZE)
        return array(
            'result'=>'파일 최대 업로드 용량(' . ((FILE_MAXIMUM_SIZE / 1024) / 1024) . 'MB)을 초과하였습니다'
        );
    
    $file_extension = strtolower(pathinfo($http_file['name'], PATHINFO_EXTENSION));
    if (array_search($file_extension, FILE_AVALIABLE_EXTENSIONS, false) === false)
        return array(
            'result'=>'업로드 가능한 포맷이 아닙니다'
        );
    
    $db = new YwDatabase($db_connect_info);
    
    if (!$db->connect())
        return array(
            'result'=>'서버와의 연결에 실패했습니다'
        );
    
    if (!$db->query("SHOW TABLE STATUS LIKE '" . 'yw_file' . "';"))
        return array(
            'result'=>'서버와의 연결에 실패했습니다'
        );
    
    $result = $db->get_result();
    
    // 파일 이름 (36진수)
    $file_name = base_convert(intval($result['Auto_increment']), 10, 36);
    $file_path = FILE_DIRECTORY . '/' . $file_name . "." . $file_extension;

    if (!move_uploaded_file($http_file['tmp_name'], $file_path))
        return array(
            'result'=>'업로드에 실패했습니다'
        );
    
    if (!$db->query("INSERT INTO " . FILE_TABLE . " (`name`, `type`, `size`, `uploader`) VALUES ('" . $file_name . "." . $file_extension . "', '" . $file_extension . "', " . $file_size . ", '" . $session->name . "')"))
        return array(
            'result'=>'파일 등록에 실패했습니다'
        );
    
    return array(
        'result'=>'/' . $file_path
    );
}

$page_response = main();

echo $page_response['result'];
?>