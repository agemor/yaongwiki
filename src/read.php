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
$article_id = stripslashes($_GET['i']);

if(empty($article_title) && empty($article_id)) {
  header('Location: 404.php');
}

$error = false;
$error_message = "";

// 데이터베이스 연결
$db = new mysqli($db_server, $db_user, $db_password, $db_name);
if ($db->connect_errno) {
  exit($db->connect_error);
}

// 글 읽어오기
if (!empty($article_id)) {
  $sqlQuery = "SELECT * FROM `$db_articles_table` WHERE `id`='$article_id' LIMIT 1;";
} else {
  $sqlQuery = "SELECT * FROM `$db_articles_table` WHERE `title`='$article_title' LIMIT 1;";
}

$result = $db->query($sqlQuery);

if ($result->num_rows < 1) {
  header('Location: 404.php');
  exit();
}
$parsedown = new Parsedown();

$row = $result->fetch_assoc();
$article_id = $row["id"];
$article_title = $row["title"];
$article_content = $parsedown->text($row["content"]);
$article_tags = splitTags($row["tags"]);
$article_hits = $row["hits"];
$result->free();

// 조회수 증가

if(!in_array($article_id, $_SESSION['pageview'])) {
  array_push($_SESSION['pageview'], $article_id);
  $sqlQuery = "UPDATE `$db_articles_table` SET `hits`=`hits`+1 WHERE `id`='$article_id';";
  $db->query($sqlQuery);
  store_log($db, $loggedin ? $user_name : $user_ip, "항목 읽기", $article_title);
}

$db->close();

$page_title = $article_title;
$page_location = "read.php?t=".$article_title;

include 'header.php';?>

<div class="container">
  <?php
  if ($_GET["update"] == 1) {
    echo "<div class=\"alert alert-success\" role=\"alert\">지식을 새로 업데이트했습니다.</div>";
  } else if ($_GET["update"] == 2) {
    echo "<div class=\"alert alert-warning\" role=\"alert\">변경된 내용이 없습니다.</div>";
  }
  ?>
  <h1><?php echo '<a style="text-decoration: none;" href="'.$page_location.'">'.$article_title.'</a>'?> <span class="badge"><abbr title="이 지식의 조회수"><?php echo "+".$article_hits;?></abbr></span></h1>
  <div class=" text-right">
    <div class="btn-group" role="group">
      <a type="button" href="revisions.php?t=<?php echo $article_title;?>" class="btn btn-default" role="button">역대 집필자 보기</a>
      <a type="button" href="edit.php?t=<?php echo $article_title;?>" class="btn btn-default" role="button">지식 업데이트하기</a>
    </div>
    <hr>
  </div>
  <?php echo $article_content;?>
  <br/>
  <div class="well well-sm"><?php echo !empty($article_tags) ? $article_tags : "<em>이 지식에는 아직 태그가 없습니다. 태그를 추가해 주세요!</em>";?></div>
</div>

<?php include 'footer.php';?>