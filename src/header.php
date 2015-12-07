<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Yaong Engine 1.0">
<meta name="author" content="HyunJun Kim">
<link rel="icon" href="favicon.ico">
<title><?php echo $title;?></title>
<link href="libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-default">
  <div class="container">
    <div class="navbar-header">
      <a href="index.php" class="pull-left"><img style="max-width:150px; margin-top: 4px; margin-left: 4px;" src="logo.png"></a>
    </div>
    <form class="navbar-form navbar-right" role="search" style="margin-bottom: -1px;">
      <div class="input-group">
        <span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
        <input type="text" class="form-control" placeholder="검색어 입력">
        <span class="input-group-btn">
          <a class="btn btn-default" href="search.php" role="button">검색</a>
          <?php
            if ($loggedin) {
              echo '<a class="btn btn-default" href="signout.php" role="button"><span class="glyphicon glyphicon-off" aria-hidden="true"></a>';
            } else {
              echo '<a class="btn btn-default" href="signin.php" role="button"><span class="glyphicon glyphicon-user" aria-hidden="true"></a>';
            }
          ?>
        </span>
      </div>
    </form> 
  </div>
</nav>