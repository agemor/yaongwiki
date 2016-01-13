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

if (!$comparison_mode) {
    $article_construct = FineDiff::renderToTextFromOpcodes($article_content, $article_opcodes);
    $article_content = $parsedown->text($article_construct);
} else {
  $article_content = FineDiff::renderDiffToHTMLFromOpcodes($article_content, $article_opcodes);
  //$article_content = $parsedown->text($article_construct);
}

$result->free();
$db->close();

$page_title = $article_title." (버전 ".$article_revision.") - 야옹위키";
$page_location = "revision.php?i=".$revision_id;

include 'header.php';?>

<div class="container">
  <h1><?php echo '<a style="text-decoration: none;" href="read.php?i='.$article_id.'">'.$article_title.'</a><small> (버전 '.$article_revision.' / <em>'.$article_timestamp.'</em>)</small>';?></h1>
   <div class=" text-right">
    <div class="btn-group" role="group">
    	<a type="button" href="revisions.php?i=<?php echo $article_id;?>" class="btn btn-default" role="button">다른 버전 보기</a>
      	<a type="button" href="edit.php?t=<?php echo $article_id;?>" class="btn btn-default" role="button">이 버전으로 복구하기</a>
    </div></div>
  <hr>
<?php echo $article_content;?>
  <br/>
  <div class="well well-sm"><?php echo !empty($article_tags) ? $article_tags : "<em>이 지식에는 아직 태그가 없습니다. 태그를 추가해 주세요!</em>";?></div>
</div>

<?php
include 'footer.php';
?>