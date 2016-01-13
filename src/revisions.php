<?php
require 'db.php';
require 'session.php';
include 'parsedown.php';
include 'finediff.php';

$MAX_REVISIONS = 25;

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
}
$row = $result->fetch_assoc();
$article_title = $row["title"];
$article_id = $row["id"];
$article_permission = intval($row["permission"]);
$article_hits = $row["hits"];

$result->free();

// 기여 목록 가져오기
if (isset($_GET["p"])) {
  $revisions_page = $_GET["p"];
} else {
  $revisions_page = 0;
}

$sqlQuery = "SELECT * FROM `$db_revisions_table` WHERE `article_id`='$article_id' ";
$sqlQuery .= "ORDER BY `timestamp` DESC ";
$sqlQuery .= "LIMIT ".($revisions_page * $MAX_REVISIONS).",".$MAX_REVISIONS.";";

$result = $db->query($sqlQuery);
$parsedown = new Parsedown();

$page_title = $article_title." - 역대 집필자";
$page_location = "revisions.php?t=".$article_title;

store_log($db, $loggedin ? $user_name : $user_ip, "지식 역사 열람", $article_title);

include 'header.php';?>

<div class="container">
  <h1><?php echo '<a style="text-decoration: none;" href="read.php?t='.$article_title.'">'.$article_title.'</a>';?> <span class="badge"><abbr title="이 지식의 조회수"><?php echo "+".$article_hits;?></abbr></span></h1>
  <div class=" text-right">
    <div class="btn-group" role="group">
      <a type="button" href="edit.php?t=<?php echo $article_title;?>" class="btn btn-default" role="button">지식 업데이트하기</a>
    </div>
    <hr>
  </div>

<table class="table">
  <thead>
      <tr >
        <th class="text-center" style="width: 2%">#</th>
        <th class="text-center" style="width: 8%">버전</th>
        <th class="text-center" style="width: 25%">편집자</th>
        <th style="width: 10%">변동</th>
        <th class="text-center" style="width: 15%">비교</th>
        <th class="text-center" style="width: 25%">편집 시간</th>
      </tr>
  </thead>
  <tbody>
  <?php

  if ($result = $db->query($sqlQuery)) {

    $result_count = $result->num_rows;

    while ($row = $result->fetch_assoc()) {
      echo '<tr>';
      echo '<td class="text-center"><a href="revision.php?i='.$row["id"].'">'.$row["id"].'</a></td>';
      echo '<td class="text-center"><a href="revision.php?i='.$row["id"].'">'.$row["revision"].'</a></td>';
      
      if (strlen($row["comment"]) > 0) {
        echo '<td class="text-center"><a href="profile.php?name='.$row["user_name"].'"">'.$row["user_name"].'</a> ('.$row["comment"].')</td>';
      } else {
        echo '<td class="text-center"><a href="profile.php?name='.$row["user_name"].'"">'.$row["user_name"].'</a></td>';
      }
      
      $fluctuation = intval($row["fluctuation"]);
      if($fluctuation > 0) {
        echo '<td><span style="color:green"><span class="glyphicon glyphicon-arrow-up" aria-hidden="true" ></span> '.$fluctuation.'</span>';
      } else if ($fluctuation == 0) {
        echo '<td><span style="color:grey"><span class="glyphicon glyphicon-minus" aria-hidden="true" ></span> 0</span>';
      } else {
        echo '<td><span style="color:red"><span class="glyphicon glyphicon-arrow-down" aria-hidden="true" ></span> '.abs($fluctuation).'</span>';
      }

      echo '</td>';
      echo '<td class="text-center">
      <a href="revision.php?i='.$row["id"].'&compare=1" class="btn btn-xs btn-default" type="button">비교</a>';

      if($user_permission >= $article_permission){
        echo ' <a href="revision.php?i='.$row["id"].'&rollback=1" class="btn btn-xs btn-default" type="button">되돌리기</a>';
      }
      echo '</td>';
      
      echo '<td class="text-center">'.$row["timestamp"].'</td>';
      echo '</tr>';
        }

        $result->free();
    }

  $db->close();?>
  </tbody>
</table>

<nav>
  <ul class="pager">

<?php
if($revisions_page > 0) {
  echo '<li class="previous"><a href="'.$page_location.'&p='.($revisions_page - 1).'"><span aria-hidden="true">&larr;</span> 새 기록 보기</a></li>';
}
if ($result_count >= $MAX_REVISIONS) {
  echo '<li class="next"><a href="'.$page_location.'&p='.($revisions_page + 1).'">이전 기록 보기 <span aria-hidden="true">&rarr;</span></a></li>';
}
?>
  </ul>
</nav>
</div>


<?php

include 'footer.php';

?>