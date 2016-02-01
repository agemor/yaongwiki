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

function getRecentUpdates($db, $count=1) {
    
    if (!$db->query("SELECT `article_title`, `fluctuation` FROM " . REVISION_TABLE . " WHERE `id` IN (SELECT MAX(`id`) FROM " . REVISION_TABLE . " GROUP BY `article_id`) ORDER BY `id` DESC LIMIT " . $count . ";"))
        return array();
    
    if ($db->total_results() < 1)
        return array();
    
    $result_array = array();
    
    while ($result = $db->get_result()) {
        array_push($result_array, array(
            'id'=>$result["article_id"],
        	'title'=>$result["article_title"],
        	'fluctuation'=>intval($result["fluctuation"])));
    }
    
    return $result_array;
}

?>