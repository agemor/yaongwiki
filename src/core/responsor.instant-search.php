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

    $http_keyword = trim($_GET['keyword']);
    
    if (empty($http_keyword))
        return array(
            'result'=>false,
        );
    
    $db = new YwDatabase($db_connect_info);
    
    if (!$db->connect())
        return array(
            'result'=>false,
        );
    
    if (!$db->query("SELECT `title` FROM " . ARTICLE_TABLE . " WHERE `title` LIKE '%" . $db->purify($http_keyword) . "%' ORDER BY `hits` DESC LIMIT 10;"))
        return array(
            'result'=>false,
        );
    
    if ($db->total_results() < 1)
        return array(
            'result'=>false,
        );
    
    $result_array = array();
    
    while ($result = $db->get_result())
        array_push($result_array, $result["title"]);
    
    return array(
        'result'=>json_encode($result_array)
    );
}

$page_response = main();
echo $page_response['result'];
?>