<?php
require_once 'common.php';
require_once 'common.db.php';

function getPopularArticles($db) {
    
    if (!$db->query("SELECT * FROM " . ARTICLE_TABLE . " ORDER BY `today_hits` DESC LIMIT 2;"))
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