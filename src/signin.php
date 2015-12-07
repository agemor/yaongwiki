<?php
require 'db.php';
require 'session.php';

if($loggedin) {
  header('Location: index.php');
}

$user_id = stripslashes($_POST['userId']);
$user_password = stripslashes($_POST['userPassword']);

$error = false;

if (!empty($user_id) && !empty($user_password)) {
$conn = new mysqli($db_server, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
  $message = "서버 연결에 실패하였습니다.";
  $error = true;
} else {

$query = "SELECT name FROM ".$db_users_table." WHERE name=? AND password=?";

if ($statement = $conn->prepare($query)){
  $statement->bind_param('ss', $user_id, hash("sha256", $user_password));
  $statement->execute();
  $statement->store_result();

  if ($statement->num_rows > 0) {
    $statement->bind_result($name);
    $statement->fetch();

    $_SESSION['loggedin'] = TRUE;
    $_SESSION['name'] = $name;
    $_SESSION['id'] = $user_id;
    if (!empty($_POST['redirect'])){
      header('Location: '.$_POST['redirect']);
    } else {
      header('Location: index.php');
    }
  } else {
    $message = "로그인에 실패하였습니다. 입력한 정보를 다시 확인해 주세요.";
    $error = true;
  }
  $statement->close();
} else {
  $message = "서버 오류로 로그인에 실패하였습니다.";
  $error = true;
}

$conn->close();
}}
?>

<?php
$title = "로그인 - 야옹위키";
include 'header.php';?>

    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <h2>계정 로그인</h2>
          <br/><br/>
          <blockquote>
            <p>이 기능 사용을 위해서는 사용자 신원 확인이 필요합니다.</p>
            <footer>야옹위키를 찾는 <cite title="Source Title">모든 이용자</cite></footer>
          </blockquote>
        </div>
        <div class="col-md-6">
          <form style="max-width:500px; margin:auto;" action="signin.php" method="post">
            <div class="well">
              <?php
              if ($error) {
                echo "<div class=\"alert alert-danger\" role=\"alert\">".$message."</div>";
              } else if ($_GET["signup"] == 1) {
                echo "<div class=\"alert alert-success\" role=\"alert\">회원 가입에 성공하셨습니다.</div>";
              }
              ?>
              <div style="margin-bottom: 10px" class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                <input type="text" id="user-id" name="userId" class="form-control" placeholder="아이디" value="<?php echo $user_id;?>" required <?php if(!$error) { echo "autofocus"; }?>>
              </div>
              <div style="margin-bottom: 10px" class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                <input type="password" id="user-password" name="userPassword" class="form-control" placeholder="비밀번호" required <?php if($error) { echo "autofocus"; }?>>
              </div>
              <div class="checkbox text-right">
                <label><input type="checkbox" value="remember-me">정보 기억하기</label>
              </div>
              <input type="hidden" name="redirect" value="<?php echo (!empty($_GET['redirect']) ? $_GET['redirect'] : $_POST['redirect']); ?>">
              <button class="btn  btn-default btn-block" type="submit">로그인</button>
              <a class="btn btn-primary btn-block" href="signup.php" role="button">계정 생성</a>
            </div>
          </form>
        </div>
      </div>
      <br/><br/>
    </div>

<?php include 'footer.php';?>