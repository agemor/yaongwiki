<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 12
 */

require_once __DIR__ . "/common.php";
require_once __DIR__ . "/module.db.php";
require_once __DIR__ . "/module.redirect.php";
require_once __DIR__ . "/module.user.php";

const MAX_RECENT_CHANGED = 30;

function process() {
    
    global $db;
    global $redirect;

    if (!$db->connect()) {
        
        $redirect->set("./?out-of-service");
        
        return array(
            "redirect" => true,
            "result" => false,
            "message" => STRINGS["ESDB0"]
        );
    }

    $response = $db->custom("SELECT * FROM `" . DB_REVISION_TABLE . "` WHERE `id` IN (SELECT MAX(`id`) FROM `" . DB_REVISION_TABLE . "` GROUP BY `article_id`) ORDER BY `id` DESC LIMIT " . MAX_RECENT_CHANGED . ";");
    
    if (!$response) {
        return array(
            "result" => false,
            "message" => STRINGS["EPRC0"]
        );
    }
    
    return array(
        "result" => true,
        "recent" => $response
    );
}
