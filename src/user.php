<?php
require 'db.php';
require 'session.php';

$PERMISSION_LEVEL = array(
	array("독자", "info"), 
	array("편집자", "warning"), 
	array("중재자", "primary"), 
	array("관리자", "danger")
);

$target_user_name = stripslashes(trim(!empty($_GET["name"]) ? $_GET["name"] : $_POST["user-name"]));

if (empty($target_user_name)) {
	if ($loggedin) {
		$target_user_name = $user_name;
	} else {
		header("Location: 404.php");
		exit();
	}
}

$error = false;
$error_message = "";

// 데이터베이스 연결
$db = new mysqli($db_server, $db_user, $db_password, $db_name);
if ($db->connect_errno) {
  exit($db->connect_error);
} 

// 유저 정보 읽어오기
$sqlQuery = "SELECT * FROM `$db_users_table` WHERE ";
$sqlQuery .= "`name`='".$db->real_escape_string($target_user_name)."' LIMIT 1;";
$result = $db->query($sqlQuery);

store_log($db, $loggedin ? $user_name : $user_ip, "유저 정보 열람", $target_user_name);

if ($result->num_rows < 1) {
  header('Location: 404.php');
  exit();
}

$row = $result->fetch_assoc();
$target_user_id = $row["id"];
$target_user_email = $row["email"];
$target_user_password = $row["password"];
$target_user_permission = intval($row["permission"]);
$target_user_info = $row["info"];
$target_user_timestamp = $row["timestamp"];

if ($result->num_rows < 1) {
	//$error = true;
	//$error_message = $sqlQuery;
  	header('Location: 404.php');
  	exit();
}

// 정보 업데이트
if (isset($_POST["user-name"]) && $target_user_id == $user_id) {

	$target_user_email = trim($_POST["user-email"]);
	$target_user_info = $_POST["user-info"];

	if(!empty($target_user_email) && filter_var($_POST["user-email"], FILTER_VALIDATE_EMAIL)) {
		if(strcmp($target_user_password, hash("sha256", trim($_POST["user-current-password"]))) == 0) {

			$sqlQuery = "UPDATE `$db_users_table` SET ";

			$new_password = trim($_POST["user-new-password"]);

			// 비밀번호 변경인지.
			if(!empty($new_password)) {
				if(strlen($new_password) < 5) {

					$error = true;
					$error_message = "비밀번호가 너무 짧습니다.";

				} else if(strcmp($new_password, trim($_POST["user-new-password-re"])) == 0) {

					$sqlQuery .= "`password`='".hash("sha256", $new_password)."', ";

				} else {
					$error = true;
					$error_message = "비밀번호와 비밀번호 확인이 일치하지 않습니다.";
				}
			}

			if (!$error) {
				$sqlQuery .= "`email`='".$db->real_escape_string($target_user_email)."', ";
				$sqlQuery .= "`info`='".$db->real_escape_string($target_user_info)."' ";
				$sqlQuery .= "WHERE `id`=".$user_id.";";

				store_log($db, $loggedin ? $user_name : $user_ip, "유저 정보 수정", $sqlQuery);

				if ($db->query($sqlQuery) === TRUE) {
					header('Location: user.php?update=1');
					exit();
				} else {
					$error = true;
					$error_message = "서버 등록에 실패했습니다.";
				}
			} 

		} else {
			$error = true;
			$error_message = "현재 비밀번호가 올바르지 않습니다.";
		}
	} else {
		$error = true;
		$error_message = "이메일이 올바르지 않습니다.";
	}

}

$result->free();
$db->close();

$page_title = "유저 정보 보기 - 야옹위키";
$page_location = "user.php?name=".$target_user_name;
include 'header.php';?>

<div class="container">


<h1><?php
    	echo $target_user_name;
		echo '  <a role="button" class="btn btn-xs btn-'.$PERMISSION_LEVEL[$target_user_permission][1].'">'.$PERMISSION_LEVEL[$target_user_permission][0].'</a>';
    	?>
    	</h1>
    	<hr/>
<?php
if (isset($_GET["update"])) {
  echo "<div class=\"alert alert-success\" role=\"alert\">계정 정보를 업데이트했습니다.</div>";
}

?>
<div class="row">
	
      <div class="col-md-6">
      <blockquote>
        <p>
        <?php
        if (empty($target_user_info)) {
        	echo "<em>자기소개 정보가 없습니다.</em>";
        } else {
        	echo $target_user_info;
    	}
        ?></p>
        <footer>계정 생성일 <cite title="Source Title"><?php echo $target_user_timestamp;?></cite></footer>
      </blockquote>

      <?php

      if ($target_user_id != $user_id) {
      	echo "</div>";
      } else {
      	echo '
      	<form class="form-signin" style="max-width:500px; margin:auto;" action="user.php" method="post">
<div class="panel panel-default" style="max-width:500px; margin:auto;">
		<div class="panel-heading">
			<h3 class="panel-title">소개글 수정</h3>
		</div>
		<div class="panel-body">
			<div  class="input-group">
  	        	<span class="input-group-addon"><i class="glyphicon glyphicon-pencil"></i></span>
				<textarea class="form-control" rows="6" name="user-info" maxlength="1000">'.$target_user_info.'</textarea>
  	        </div>
		</div>
	</div>
	<br/>
    </div>
      <div class="col-md-6">
        
          <div class="panel panel-default" style="max-width:500px; margin:auto;">
            <div class="panel-heading">
              <h3 class="panel-title">계정 정보 수정</h3>
            </div>
			    <div class="panel-body">
			    ';


if ($error) {
  echo "<div class=\"alert alert-danger\" role=\"alert\">".$error_message."</div>";
}
			echo '<input type="hidden" name="user-name" value="'.$target_user_name.'">
  	        <div style="margin-bottom: 10px" class="input-group">
  	          <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
  	          <input type="text" id="user-email" name="user-email" class="form-control" placeholder="이메일" value="'.$target_user_email.'" required>
  	        </div>
  	        <div style="margin-bottom: 10px" class="input-group">
  	          <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
  	          <input type="password" id="user-current-password" name="user-current-password" class="form-control" placeholder="현재 비밀번호" required>
  	        </div>
  	        <label for="user-password">공란으로 둘 경우 비밀번호가 변경되지 않습니다.</label>	
  	        <div style="margin-bottom: -1px" class="input-group">
  	          <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
  	          <input type="password" id="user-password" name="user-new-password" class="form-control" placeholder="새 비밀번호">
  	        </div>
  	        <div style="margin-bottom: 20px" class="input-group">
  	          <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
  	          <input type="password" id="user-password-re" name="user-new-password-re" class="form-control" placeholder="새 비밀번호 재입력">
  	        </div>
  	        <button class="btn btn-default btn-block" type="submit">업데이트</button>
			    </div>
			  </div>
      
    </div></form>';
}
    ?>

  </div>
  <br/><br/>
       
</div>
<?php include 'footer.php';?>