<?php
require 'db.php';
require 'session.php';

if(!$loggedin) {
  header('Location: signin.php?redirect=create.php');
}

$article_title = stripslashes($_POST['articleTitle']);
$error = false;

if (!empty($article_title)) {

$conn = new mysqli($db_server, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
  $message = "서버 연결에 실패하였습니다.";
  $error = true;
} else {

// 계정 중복 등록 체크
$sql = "SELECT 1 FROM $db_articles_table WHERE title='$article_title'";
if ($conn->query($sql)->num_rows > 0) {
  $message = "이미 등록된 항목입니다.";
  $error = true;
} else { 

// 새 데이터 row 만들기
$sql = "INSERT INTO $db_articles_table (title)
VALUES ('{$article_title}')";

if ($conn->query($sql) === TRUE) {
  header('Location: edit.php?t='.$article_title);
} else {
  $message = "서버 오류가 발생하였습니다.";
  //$message = mysqli_error($conn);
  $error = true;
}}
$conn->close();

}}
?>


<?php
$title = "새 항목 만들기 - 야옹위키";
require 'session.php';
include 'header.php';?>

    <div class="container">

    
    <div class="col-md-6">
          <h2>새 항목 만들기</h2>
          <br/><br/>
          <blockquote>
            <p>새 항목을 만들기 전에 반드시 아래의 원칙을 확인해 주세요!</p><br/>
            <h5>1. 중복되는 항복이 없는지 </h5>
            <h5>2. 자신의 주관이 담기지 않았는지 </h5>
            <h5>3. 공동체에 도움이 될 만한 내용인지 </h5><br/>
            <footer>야옹위키를 찾는 <cite title="Source Title">모든 이용자</cite></footer>
          </blockquote>
        </div>
        <div class="col-md-6">
          <form class="form-signin" style="max-width:500px; margin:auto;" action="create.php" method="post">
            <div class="well">
              <?php
              if ($error) {
                echo "<div class=\"alert alert-danger\" role=\"alert\">".$message."</div>";
              }
              ?>
              <div style="margin-bottom: 10px" class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-pencil"></i></span>
                <input type="text" id="article-title" name="articleTitle" class="form-control" placeholder="지식 제목" value="" required autofocus>
              </div>
             
              <button class="btn btn-default btn-block" type="submit">만들기</button>
            </div>
          </form>
        </div>
      </div>





       
    </div>
<?php include 'footer.php';?>
