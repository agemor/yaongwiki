<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */

require_once "./core/navigator.php";

$current_page_url = get_current_page_url();

echo (analyze_url($current_page_url));

?>