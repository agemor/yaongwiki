<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 02. 01
 */

require_once 'common.php';
require_once 'common.db.php';

function getPopularArticles($db, $count=2) {
    
    if (!$db->query("SELECT * FROM " . ARTICLE_TABLE . " ORDER BY `today_hits` DESC LIMIT ".$count.";"))
        return array();
    
    if ($db->total_results() < 1)
        return array();
    
    $result_array = array();
    
    while ($result = $db->get_result()) {
        array_push($result_array, $result);
    }
    
    return $result_array;
}

?>