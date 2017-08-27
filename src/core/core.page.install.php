<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);

function main() {
    
    $http_db_host     = $_POST['db-host'];
    $http_db_name     = $_POST['db-name'];
    $http_db_user     = $_POST['db-user'];
    $http_db_password = $_POST['db-password'];
    
    if (empty($http_db_host) || empty($http_db_name) || empty($http_db_user) || empty($http_db_password))
        return array(
            'result'=>true,
            'message'=>''
        );
    
    $connection = new mysqli($http_db_host, $http_db_user, $http_db_password, $http_db_name);
    
    if ($connection->connect_errno)
        return array(
            'result'=>false,
            'message'=>'서버에 접속할 수 없습니다: <br>' . $connection->connect_error
        );
    
    $query = file_get_contents('assets/db.sql');
    
    if (!$connection->multi_query($query))
        return array(
            'result'=>false,
            'message'=>'테이블 추가에 실패했습니다: <br>' . $connection->error
        );
    
    $config_keywords = array(
        '{DB_HOST}',
        '{DB_USER}',
        '{DB_PASSWORD}',
        '{DB_NAME}'
    );
    $settings        = array(
        $http_db_host,
        $http_db_user,
        $http_db_password,
        $http_db_name
    );
    
    $filecontent = file_get_contents("common.php");
    $filecontent = str_replace($config_keywords, $settings, $filecontent);
    file_put_contents("common.php", $filecontent);
    
    header('Location: /');
}

$page_response = main();
$page_location = "page.install.php";

?>
<!DOCTYPE html>
<html lang="ko">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="./favicon.ico">
    <title>데이터베이스 설정</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="./theme/bootstrap.yeti.css" rel="stylesheet">
  </head>
  <body style="padding-bottom: 70px;">
    <nav class="navbar navbar-inverse">
    </nav>
    <div class="container text-center">
      <h2>데이터베이스에 야옹위키를 설치합니다.</h2>
      <hr/>
      <p>만약 이미 설치되어 있는 상태라면 모든 정보가 초기화되므로,
        <br/> 연결 정보를 수동으로 설정해 주시기 바랍니다.
      </p>
      <p><b>데이터베이스 설정을 위해 아래 정보를 입력해 주세요.</b></p>
      <br/>
    
    <div class="container" style="width: 30%; min-width:350px">
      <?php
        if (!$page_response['result']) {
            echo '<div class="alert alert-danger" role="alert">'.$page_response['message'].'</div>';
         }
         ?>
      <form action="page.install.php" method="post">
        <div style="margin-bottom: 10px" class="input-group">
          <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
          <input type="text" name="db-host" class="form-control" placeholder="DB 호스트" value="localhost" required autofocus>
        </div>
        <div style="margin-bottom: 10px" class="input-group">
          <span class="input-group-addon"><i class="glyphicon glyphicon-hdd"></i></span>
          <input type="text" name="db-name" class="form-control" placeholder="DB 이름" required>
        </div>
        <div style="margin-bottom: 10px" class="input-group">
          <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
          <input type="text" name="db-user" class="form-control" placeholder="DB 계정" required>
        </div>
        <div style="margin-bottom: 30px" class="input-group">
          <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
          <input type="password" name="db-password" class="form-control" placeholder="DB 계정 비밀번호" required>
        </div>
        <button class="btn btn-default btn-block" type="submit">설치하기</button> 
      </form>
      </div>
      <hr/>
    </div>
    
    <footer class="text-center">
      <p>&copy; 2016 야옹위키</p>
    </footer>
  </body>
</html>