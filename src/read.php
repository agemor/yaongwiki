<?php
require 'db.php';
require 'session.php';
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
$sqlQuery = "SELECT * FROM `$db_articles_table` WHERE `title`='$article_title'";
$result = $db->query($sqlQuery);

if ($result->num_rows < 1) {
  header('Location: 404.php');
}
$parsedown = new Parsedown();

$row = $result->fetch_assoc();
$article_id = $row["id"];
$article_content = $parsedown->text($row["content"]);
$article_tags = splitTags($row["tags"]);
$article_hits = $row["hits"];

$result->free();
$db->close();

$title = $article_title;
include 'header.php';?>

<div class="container">
  <?php
  if ($_GET["update"] == 1) {
    echo "<div class=\"alert alert-success\" role=\"alert\">지식을 새로 업데이트했습니다.</div>";
  } 
  ?>
  <h1><?php echo $article_title;?> <span class="badge"><abbr title="이 지식의 조회수"><?php echo "+".$article_hits;?></abbr></span></h1>
  <div class=" text-right">
    <div class="btn-group" role="group">
      <a type="button" class="btn btn-default" role="button">역대 집필자 보기</a>
      <a type="button" href="edit.php?t=<?php echo $article_title;?>" class="btn btn-default" role="button">지식 업데이트하기</a>
    </div>
    <hr>
  </div>
  <?php echo $article_content;?>
  <br/>
  <div class="well well-sm"><?php echo !empty($article_tags) ? $article_tags : "<em>이 지식에는 아직 태그가 없습니다. 태그를 추가해 주세요!</em>";?></div>
</div>

<?php include 'footer.php';?>