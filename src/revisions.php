<?php
require 'db.php';
require 'session.php';
include 'parsedown.php';
include 'finediff.php';

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

$article_title = stripslashes($_GET['t']);

if(empty($article_title)) {
  header('Location: 404.php');
}

$error = false;

$error = false;
$error_message = "";

// 데이터베이스 연결
$db = new mysqli($db_server, $db_user, $db_password, $db_name);
if ($db->connect_errno) {
  exit($db->connect_error);
} 

// 글 읽어오기
$sqlQuery = "SELECT * FROM `$db_articles_table` WHERE `title`='$article_title' LIMIT 1;";
$result = $db->query($sqlQuery);

if ($result->num_rows < 1) {
  header('Location: 404.php');
}
$row = $result->fetch_assoc();
$article_id = $row["id"];
$article_hits = $row["hits"];

$result->free();

$sqlQuery = "SELECT * FROM `$db_revisions_table` WHERE `article_id`='$article_id' ORDER BY  `timestamp` DESC";
$result = $db->query($sqlQuery);
$parsedown = new Parsedown();

$page_title = $article_title." - 역대 집필자";
$page_location = "revisions.php?t=".$article_title;

store_log($db, $loggedin ? $user_name : $user_ip, "지식 역사 열람", $article_title);

include 'header.php';?>

<div class="container">
  <h1><?php echo $article_title;?> <span class="badge"><abbr title="이 지식의 조회수"><?php echo "+".$article_hits;?></abbr></span></h1>
  <div class=" text-right">
    <div class="btn-group" role="group">
      <a type="button" href="read.php?t=<?php echo $article_title;?> "class="btn btn-default" role="button">지식 읽기</a>
      <a type="button" href="edit.php?t=<?php echo $article_title;?>" class="btn btn-default" role="button">지식 업데이트하기</a>
    </div>
    <hr>
  </div>
<?php

while ($row = $result->fetch_assoc()) {
  $article_user_name = $row["user_name"];
  $article_content = $row["content"];
  $article_opcodes = $row["opcodes"];
  $article_tags = $row["tags"];
  $article_fluctuation = intval($row["fluctuation"]);
  $article_timestamp = $row["timestamp"];
  
  $article_revision = FineDiff::renderDiffToHTMLFromOpcodes($article_content, $article_opcodes);

  if($article_fluctuation >= 0) {
    echo "<div class=\"panel panel-info\">";
  } else {
    echo "<div class=\"panel panel-danger\">";
  }

  echo "<div class=\"panel-heading\"><code>".$article_timestamp."</code>  <a href=\"user.php?name=".$article_user_name."\">".$article_user_name."</a>님이 ".abs($article_fluctuation)."글자를 ";
  if($article_fluctuation >= 0) {
    echo "추가했습니다.";
  } else {
    echo "삭제했습니다.";
  }
  echo "</div>";
  echo "<div class=\"panel-body\">";
  //echo $parsedown->text($article_revision);
  echo $article_revision;

  if (!empty(trim($article_tags))) {
    echo "<br/><br/>".splitTags($article_tags);
  }
  echo "</div></div>";
}

?>
</div>

<?php 
$result->free();
$db->close();

include 'footer.php';

?>