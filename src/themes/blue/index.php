<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */

require_once 'common.php';

if (DB_HOST == '{DB_HOST}')
	include 'page.install.php';
else include 'page.main.php';
?>