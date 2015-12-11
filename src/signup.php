<?php
require 'db.php';
require 'session.php';

if($loggedin) {
  header('Location: index.php');
}

$user_id = stripslashes($_POST['userId']);
$user_email = stripslashes($_POST['userEmail']);
$user_password = stripslashes($_POST['userPassword']);
$user_password_re = stripslashes($_POST['userPasswordRe']);

$error = false;
if (!empty($user_id) && !empty($user_email) && !empty($user_password)) {

if($user_password != $user_password_re) {
  $message = "비밀번호와 비밀번호 확인이 일치하지 않습니다.";
  $error = true;
} else {

if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
  $message = "이메일 형식이 올바르지 않습니다.";
  $error = true;

} else {

$conn = new mysqli($db_server, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
  $message = "서버 연결에 실패하였습니다.";
  $error = true;
} else {

// 계정 중복 등록 체크
$sql = "SELECT 1 FROM $db_users_table WHERE name='$user_id'";
if ($conn->query($sql)->num_rows > 0) {
  $message = "이미 등록된 계정입니다.";
  $error = true;
} else { 

$user_password = hash("sha256", $user_password);


// 새 데이터 row 만들기
$sql = "INSERT INTO `$db_users_table` (`name`, `email`, `password`) VALUES (";
$sql .= "'".$db->real_escape_string($user_id)."', ";  
$sql .= "'".$db->real_escape_string($user_email)."', ";
$sql .= "'".$db->real_escape_string($user_password)."');";

store_log($conn, $loggedin ? $user_name : $user_ip, "회원가입 시도", $sql);

if ($conn->query($sql) === TRUE) {
  header('Location: signin.php?signup=1');
} else {
  $message = "서버 오류가 발생하였습니다.";
  //$message = mysqli_error($conn);
  $error = true;
}}
$conn->close();

}}}}
?>

<?php
$page_title = "계정 만들기 - 야옹위키";
$page_location = "signup.php";

include 'header.php';?>

<div class="container">
<h1>계정 만들기</h1>
      <hr/>
  <div class="row">
    <div class="col-md-6">

      
      <blockquote>
        <p>이 기능 사용을 위해서는 사용자 신원 확인이 필요합니다.</p>
        <footer>야옹위키를 찾는 <cite title="Source Title">모든 이용자</cite></footer>
      </blockquote>

      <div class="panel panel-default" style="max-width:500px; margin:auto;">
			  <div class="panel-heading">
			    <h3 class="panel-title">#1 인증하기</h3>
			  </div>
			  <div class="panel-body">
			    recapcha 등등
			  </div>
			</div>
			<br/>

    </div>
      <div class="col-md-6">
        <form class="form-signin" style="max-width:500px; margin:auto;" action="signup.php" method="post">
          <div class="panel panel-default" style="max-width:500px; margin:auto;">
            <div class="panel-heading">
              <h3 class="panel-title">#2 정보 입력</h3>
            </div>
			    <div class="panel-body">
            <?php
              if ($error) {
                echo "<div class=\"alert alert-danger\" role=\"alert\">".$message."</div>";
              }
            ?>
  				  <div style="margin-bottom: 10px" class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input type="text" id="user-id" name="userId" class="form-control" placeholder="아이디" value="<?php echo $user_id;?>" required autofocus>
            </div>
  	        <div style="margin-bottom: 10px" class="input-group">
  	          <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
  	          <input type="text" id="user-email" name="userEmail" class="form-control" placeholder="이메일" value="<?php echo $user_email;?>" required>
  	        </div>
  	        <div style="margin-bottom: -1px" class="input-group">
  	          <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
  	          <input type="password" id="user-password" name="userPassword" class="form-control" placeholder="비밀번호" required>
  	        </div>
  	        <div style="margin-bottom: 10px" class="input-group">
  	          <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
  	          <input type="password" id="user-password-re" name="userPasswordRe" class="form-control" placeholder="비밀번호 재입력" required>
  	        </div>
  	        <div class="alert alert-warning" role="alert">상기 개인정보는 로그인 시 사용자 식별을 위해서만 사용되며, 프로그램 종료 후 전부 안전하게 파기됩니다.</div>
  	        <button class="btn btn-default btn-block" type="submit">계정 등록</button>
			    </div>
			  </div>
      </form>
    </div>
  </div>
  <br/><br/>
</div>

<?php include 'footer.php';?>