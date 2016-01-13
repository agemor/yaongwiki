<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Yaong Engine 1.0">
<meta name="author" content="HyunJun Kim">
<link rel="icon" href="favicon.ico">
<title>연세포탈 연동 테스트</title>
<link href="libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<style type="text/css">
	body {
  background-color: #eee;
}

.vertical-center {
  min-height: 100%;
  min-height: 100vh;
  display: -webkit-box;
  display: -moz-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex; 
    -webkit-box-align : center;
  -webkit-align-items : center;
       -moz-box-align : center;
       -ms-flex-align : center;
          align-items : center;
  width: 100%;
         -webkit-box-pack : center;
            -moz-box-pack : center;
            -ms-flex-pack : center;
  -webkit-justify-content : center;
          justify-content : center;
}

.form-signin {
  min-width: 260px;
  max-width: 300px;
  padding-bottom: 145px;
  margin: 0 auto;
}
.form-signin .form-signin-heading,
.form-signin .checkbox {
  margin-bottom: 10px;
}
.form-signin .checkbox {
  font-weight: normal;
}
.form-signin .form-control {
  position: relative;
  height: auto;
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
  padding: 10px;
  font-size: 16px;
}
.form-signin .form-control:focus {
  z-index: 2;
}

</style>
</head>
<body>

<?php

$success = false;

if(!empty($_POST["id"]) && !empty($_POST["password"])) {

	$user_id = $_POST["id"];
	$user_password = $_POST["password"];

	$url = 'https://infra.yonsei.ac.kr/lauth/YLLOGIN.do';
	$data = array('id' => $user_id, 'pw' => $user_password, 'waction' => 'aW50bHBvcnRhbA==', 'sCode' => 'bm9lbmNyeXB0', 'returl' => 'yonseilogin.php');

	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => http_build_query($data),
	    ),
	);
	$context  = stream_context_create($options);
	$result = htmlspecialchars(file_get_contents($url, false, $context));
	if ($result === FALSE || strlen($result) < 20 || empty($result)) {
		$success = false;
	} else {
		$studentData = explode("|", urldecode(base64_decode(explode("'", explode("gubun5' value='", $result)[1])[0])));
		$success = true;
	}

}

if($success){

echo '

<div class="container">
<div class="vertical-center">
<p><b>이름:</b> '.$studentData[1].'('.$studentData[2].')    /   </p>

<p><b>전화번호:</b> '.$studentData[3].'</p>
</div>
</div>
';

}else{
	echo '
	<div class="container">
	      <div class="vertical-center">
	        <form class="form-signin" action="yonseilogin.php" method="post">
	          <h4 class="form-signin-heading text-center">연세포탈 로그인</h4>
	          <div class="well">
	            <div style="margin-bottom: 10px" class="input-group">
	              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
	              <input type="text" id="student-id" name="id" class="form-control" placeholder="학번" value="" required autofocus>
	            </div>
	            <div style="margin-bottom: 10px" class="input-group">
	              <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
	              <input type="password" id="student-password" name="password" class="form-control" placeholder="비밀번호" required>
	            </div>
	            <div class="checkbox">
	              <label><input type="checkbox" value="remember-me">정보 기억하기</label>
	            </div>
	                <button class="btn btn-lg btn-danger btn-block" type="submit">접속</button>
	          </div>
	        </form>
	      </div>
	    </div>';
}
?>



<script src="libs/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
