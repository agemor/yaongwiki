<?php
require 'db.php';
require 'session.php';

$article_title = stripslashes(!empty($_GET['t']) ? $_GET['t'] : $_POST['title']);
$saveMode = !empty($_POST['save']);

if(empty($article_title)) {
  header('Location: index.php');
}

if(!$loggedin) {
  header('Location: signin.php?redirect=edit.php?title='.$article_title);
}

$error = false;

$conn = new mysqli($db_server, $db_user, $db_password, $db_name);

$article_content = $_POST['content'];
$article_summary = $_POST['summary'];
$article_tags = $_POST['tags'];
$article_hits = 330;

if ($conn->connect_error) {
  $message = "서버 연결에 실패하였습니다.";
  $error = true;
} else {

if(!$saveMode) {

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
} else {

  $sql = "UPDATE $db_articles_table SET summary = '$article_summary', content = '$article_content', tags = '$article_tags' WHERE title = '$article_title'";

  if ($conn->query($sql) === TRUE) {
    header('Location: read.php?t='.$article_title);
  } else {
    $message = "서버 오류가 발생하였습니다.";
    //$message = mysqli_error($conn);
    $error = true;
  }

}
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
      <div class="well well-sm">
      자세한 편집 방법은 <a href="#">편집 방법</a> 문서를 참조하세요.<br/>
      <em>지식에 대해 고의적인 훼손을 가하거나 악의적인 내용을 작성하실 경우 차단될 수 있습니다.</em>
      <?php
              if ($error) {
                echo "<div class=\"alert alert-danger\" role=\"alert\">".$message."</div>";
              }
              ?>
      </div>
      <form action="edit.php" method="post">
      <div class="form-group">
        <label for="summery">요약</label>
        <textarea class="form-control" rows="3" id="summery" name="summary"><?php echo $article_summary;?></textarea>
      </div>

      <div class="form-group">
        <label for="content">내용</label>
        <textarea class="form-control" rows="20" id="content" name="content"><?php echo $article_content;?></textarea>
      </div>

      <div class="form-group">
        <label for="tags">태그 <small>(쉼표로 구분해 주세요)</small></label>
        <textarea class="form-control" rows="1" id="tags" name="tags"><?php echo $article_tags;?></textarea>
      </div>
      <input type="hidden" name="title" value="<?php echo $article_title;?>">
      <input type="hidden" name="save" value="1">
      <button class="btn btn-primary btn-block" type="submit">업데이트</button>
       <button class="btn btn-danger btn-block">취소</button>
      </form>
    </div>

<?php include 'footer.php';?>