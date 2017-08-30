<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */

require_once "core/core.session.php";
require_once "index.navigator.php";

$post->set($_POST);
$get->set($_GET);

$current_page_url = get_current_page_url();

echo (to_inner_url($current_page_url));

?>