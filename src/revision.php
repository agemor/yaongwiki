<?php
require 'db.php';
require 'session.php';
include 'finediff.php';
include 'parsedown.php';

function splitTags($text) {
  $words = explode(' ', $text);
  $text = "";
  for($i = 0; $i < count($words); $i++) {
    if (strlen($words[$i]) > 0 ) {
      $text .= ($i > 0 ? "&nbsp;&nbsp;" : "")."<a href=\"search.php?q=".$words[$i]."\">#".$words[$i]."</a>";
    }
  }
  return $text;
}

$revision_id = stripslashes($_GET['i']);
$comparison_mode = !empty(stripslashes($_GET['compare']));


if(empty($revision_id)) {
  header('Location: 404.php');
}

$error = false;
$error_message = "";

// 데이터베이스 연결
$db = new mysqli($db_server, $db_user, $db_password, $db_name);
if ($db->connect_errno) {
  exit($db->connect_error);
} 

$sqlQuery = "SELECT * FROM `$db_revisions_table` WHERE `id`='$revision_id' LIMIT 1;";
$result = $db->query($sqlQuery);

if ($result->num_rows < 1) {
  header('Location: 404.php');
}

$parsedown = new Parsedown();

$row = $result->fetch_assoc();
$article_id = $row["article_id"];
$article_title = $row["article_title"];
$article_revision = $row["revision"];
$article_user_name = $row["user_name"];
$article_content = $row["content"];
$article_opcodes = $row["opcodes"];
$article_tags = splitTags($row["tags"]);
$article_fluctuation = intval($row["fluctuation"]);
$article_timestamp = $row["timestamp"];

$result->free();

// Rollback 롤백
if (isset($_GET["rollback"])) {
	// 퍼미션 체크
	 $sqlQuery = "SELECT * FROM `$db_articles_table` WHERE `id`='$article_id' LIMIT 1;";
	 $result = $db->query($sqlQuery);
	 $row = $result->fetch_assoc();

	 if($user_permission < intval($row["permission"])){
	 	$error = true;
	 	$error_message = "되돌리기 위한 권한이 부족합니다.";
	 } else {

	 	$fulltext = FineDiff::renderToTextFromOpcodes($article_content, $article_opcodes);
	 	// 원본 업데이트
	 	$sqlQuery = "UPDATE `$db_articles_table` SET ";
		$sqlQuery .= "`content`='".$db->real_escape_string($fulltext)."', ";
		$sqlQuery .= "`title`='".$db->real_escape_string($article_title)."', ";
		$sqlQuery .= "`tags`='".$db->real_escape_string($article_tags)."' ";
		$sqlQuery .= "WHERE `id`='".$db->real_escape_string($article_id)."';";

		$db->query($sqlQuery);
    
	    // 가장 최근의 revision 넘버 가져오기
	    $sqlQuery = "SELECT * FROM `$db_revisions_table` WHERE `article_id`='$article_id' ORDER BY `timestamp` DESC LIMIT 1;";
	    if ($result = $db->query($sqlQuery)){
	      $row = $result->fetch_assoc();
	      $article_recent_revision_number = intval($row["revision"]);
	    } else {
	      $article_recent_revision_number = 0;
	    }

	    $opcodes = FineDiff::getDiffOpcodes($fulltext, $fulltext);

	    $sqlQuery = "INSERT INTO `$db_revisions_table` (`article_id`, `article_title`, `revision`, `user_name`, `content`, `tags`, `opcodes`, `fluctuation`, `comment`) ";
	    $sqlQuery .= "VALUES (";
	    $sqlQuery .= "'".$article_id."', ";
	    $sqlQuery .= "'".$article_title."', ";
	    $sqlQuery .= "'".($article_recent_revision_number + 1)."', ";
	    $sqlQuery .= "'".$user_name."', ";
	    $sqlQuery .= "'".$db->real_escape_string($fulltext)."', ";
	    $sqlQuery .= "'".$db->real_escape_string($article_tags)."', ";
	    $sqlQuery .= "'".$db->real_escape_string($opcodes)."', ";
	    $sqlQuery .= "0, ";
	    $sqlQuery .= "'".$article_revision."에서 복구함');";
		
		$db->query($sqlQuery);

		store_log($db, $loggedin ? $user_name : $user_ip, "항목 롤백", $sqlQuery);

		header('Location: read.php?i='.$article_id);
		exit();

	 }

}

// 원본 퍼미션 체크

// 원본에 덮어쓰기

if (!$comparison_mode) {
    $article_construct = FineDiff::renderToTextFromOpcodes($article_content, $article_opcodes);
    $article_content = $parsedown->text($article_construct);
} else {
  $article_content = FineDiff::renderDiffToHTMLFromOpcodes($article_content, $article_opcodes);
  //$article_content = $parsedown->text($article_construct);
}

$db->close();

$page_title = $article_title." (버전 ".$article_revision.") - 야옹위키";
$page_location = "revision.php?i=".$revision_id;

include 'header.php';?>

<div class="container">
  <h1><?php echo '<a style="text-decoration: none;" href="read.php?i='.$article_id.'">'.$article_title.'</a><small> (버전 '.$article_revision.' / <em>'.$article_timestamp.'</em>)</small>';?></h1>
   <div class=" text-right">
    <div class="btn-group" role="group">
    	<a type="button" href="revisions.php?i=<?php echo $article_id;?>" class="btn btn-default" role="button">다른 버전 보기</a>
      	<a type="button" href="revision.php?i=<?php echo $revision_id;?>&rollback=1" class="btn btn-default" role="button">이 버전으로 되돌리기</a>
    </div></div>
  <hr>

<?php

if ($error) {
      echo "<div class=\"alert alert-danger\" role=\"alert\">".$error_message."</div>";
}
echo $article_content;?>
  <br/><br/>
  <div class="well well-sm"><?php echo !empty($article_tags) ? $article_tags : "<em>이 지식에는 아직 태그가 없습니다. 태그를 추가해 주세요!</em>";?></div>
</div>

<?php
include 'footer.php';
?>