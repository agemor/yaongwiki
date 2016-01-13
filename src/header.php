<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Yaong Engine 1.0">
<meta name="author" content="HyunJun Kim">
<link rel="icon" href="favicon.ico">
<title><?php echo $page_title;?></title>
<link href="libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="padding-bottom: 70px;">
<nav class="navbar navbar-default">
  <div class="container">
    <div class="navbar-header">
      <a href="index.php" class="pull-left"><img style="max-width:150px; margin-top: 4px; margin-left: 4px;" src="logo.png"></a>
    </div>
    <form class="navbar-form navbar-right" action="search.php" method="get" role="search" style="margin-bottom: -1px;">
      <div class="input-group">
        <input type="text" class="form-control" name="q" value="<?php echo $query;?>" placeholder="검색어 입력">
        <span class="input-group-btn">
          <button class="btn btn-default" type="submit">검색</button>
          <button type="button" class="btn btn-default dropdown-toggle" data-toggle=dropdown aria-haspopup=true aria-expanded=false>
          <?php
          if ($loggedin) {
            echo '<span class="glyphicon glyphicon-cog" aria-hidden="true">';
          } else {
            echo '<span class="glyphicon glyphicon-user" aria-hidden="true">';
          }
          ?>   
          </button>
          <ul class="dropdown-menu dropdown-menu-right">
            <?php
            if ($loggedin) {
              echo '<li><a href="profile.php?name='.$user_name.'">내 프로필</a></li>';
              echo '<li><a href="myinfo.php">계정 관리</a></li>';
              echo '<li><a href="signout.php?redirect='.$page_location.'">로그아웃</a></li>';
            } else {
              echo '<li><a href="signin.php?redirect='.$page_location.'">로그인</a></li>';
              echo '<li><a href="signup.php">계정 만들기</a></li>';
            }
            ?>
          </ul>
        </span>
      </div>
    </form> 
  </div>
</nav>