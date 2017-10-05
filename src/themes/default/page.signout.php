<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 10. 04
 */

require_once YAONGWIKI_CORE_DIR . "/page.signout.processor.php";

$page = process();

if (isset($page["redirect"])) {
    redirect($page["redirect"]);
    exit();
}
