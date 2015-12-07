<?php
require 'db.php';
require 'session.php';

$article_title = stripslashes($_GET['t']);

if(empty($article_title)) {
  header('Location: 404.php');
}

$error = false;

$conn = new mysqli($db_server, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
  $message = "서버 연결에 실패하였습니다.";
  $error = true;
} else {

  $sql = "SELECT * FROM $db_articles_table WHERE title='$article_title'";
  $result = $conn->query($sql);

  if ($result->num_rows < 1) {
    $conn->close();
    $result->free();
    header('Location: 404.php');
  }

  $row = $result->fetch_row();
  $article_summary = $row[2];
  $article_content = $row[3];
  $article_tags = $row[4];
  $article_hits = $row[5];

  $result->free();
}

$conn->close();

?>


<?php
$title = "야옹위키";
require 'session.php';
include 'header.php';?>

    <div class="container">

      <h2><?php echo $article_title;?> <span class="badge"><abbr title="이 지식의 조회수"><?php echo "+".$article_hits;?></abbr></span></h2>
      <br/>
      <div class=" text-right">
      <div class="btn-group" role="group" aria-label="...">
        <a type="button" class="btn btn-default" role="button">역대 집필자 보기</a>
        <a type="button" href="edit.php?t=<?php echo $article_title;?>" class="btn btn-default" role="button">지식 업데이트하기</a></div>
      </div>
      <br/>

      <blockquote>
        <?php echo $article_summary; ?>
      </blockquote>

      <?php

      echo $article_content;

      ?>
      <br/>
      <div class="well well-sm"><?php echo $article_tags; ?></div>

       
    </div>
<?php include 'footer.php';?>