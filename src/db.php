<?php
$db_server = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "yaongwiki";
$db_articles_table = "yw_articles";
$db_logs_table = "yw_logs";
$db_revisions_table = "yw_revisions";
$db_users_table = "yw_users";

$TITLE_CHANGE_PERMISSION = 1;
$ARTICLE_DELETE_PERMISSION = 2;
$ADMIN_PERMISSION = 3;

function store_log($db, $name, $behavior, $data) {
	$sqlQuery = "INSERT INTO `yw_logs` (`user_name`, `behavior`, `data`) VALUES (";
	$sqlQuery .= "'".$db->real_escape_string($name)."', ";	
	$sqlQuery .= "'".$db->real_escape_string($behavior)."', ";
	$sqlQuery .= "'".$db->real_escape_string($data)."');";
	return $db->query($sqlQuery);
}
?>