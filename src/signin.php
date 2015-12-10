<?php
require 'db.php';
require 'session.php';

if($loggedin) {
  header('Location: index.php');
}

$user_name = stripslashes($_POST['user-id']);
$user_password = stripslashes($_POST['user-password']);
$redirect = !empty($_GET['redirect']) ? $_GET['redirect'] : (!empty($_POST['redirect']) ? $_POST['redirect'] : 'index.php');

// 로그인 체크
if (!empty($user_name) && !empty($user_password)) {
  $error = false;
  $error_message = "";

  // 데이터베이스 연결
  $db = new mysqli($db_server, $db_user, $db_password, $db_name);

  if ($db->connect_errno) {
    exit($db->connect_error);
  } 

  $sqlQuery = "SELECT `id`, `password`, `email`, `permission` FROM `$db_users_table` WHERE `name`=? LIMIT 1";
  $statement = $db->prepare($sqlQuery);
  $statement->bind_param('s', $user_name);
  $statement->execute();
  $statement->store_result();
  $statement->bind_result($user_db_id, $user_db_password, $user_db_email, $user_permission);
  $statement->fetch();

  // 로그인 성공
  if ($statement->num_rows == 1 && hash("sha256", $user_password) == $user_db_password) {
    $_SESSION['name'] = $user_name;
    $_SESSION['id'] = $user_db_id;
    $_SESSION['email'] = $user_db_email;
    $_SESSION['permission'] = intval($user_permission);
    $_SESSION['loggedin'] = TRUE;
    $statement->close();

    header("Location: ".$redirect);
  }

  // 로그인 실패
  else {
    $error_message = "등록되지 않은 아이디거나, 비밀번호를 잘못 입력하셨습니다.";
    $error = true;
  }
  $db->close();
}

$title = "야옹위키 로그인";
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
            echo "<div class=\"alert alert-danger\" role=\"alert\">".$error_message."</div>";
          } else if ($_GET["signup"] == 1) {
            echo "<div class=\"alert alert-success\" role=\"alert\">계정을 생성하였습니다.</div>";
          }
          ?>
          <div style="margin-bottom: 10px" class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input type="text" name="user-id" class="form-control" placeholder="아이디" value="<?php echo $user_name;?>" required <?php if(!$error) { echo "autofocus"; }?>>
          </div>
          <div style="margin-bottom: 10px" class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
            <input type="password" name="user-password" class="form-control" placeholder="비밀번호" required <?php if($error) { echo "autofocus"; }?>>
          </div>
          <div class="checkbox text-right">
            <label><input type="checkbox" value="remember-me">정보 기억하기</label>
          </div>
          <input type="hidden" name="redirect" value="<?php echo $redirect;?>">
          <button class="btn  btn-default btn-block" type="submit">로그인</button>
          <a class="btn btn-primary btn-block" href="signup.php" role="button">계정 생성</a>
        </div>
      </form>
    </div>
  </div>
  <br/><br/>
</div>

<?php include 'footer.php';?>