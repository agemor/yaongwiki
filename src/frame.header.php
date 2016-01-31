<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */

require_once 'common.php';
require_once 'common.session.php';
?>
<!DOCTYPE html>
<html lang="ko">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/favicon.ico">
    <title><?php echo $page_title.TITLE_AFFIX;?></title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="/theme/bootstrap.yeti.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="/js/typeahead.js"></script>
    <script src="/js/analytics.js"></script>
  </head>
  <body style="padding-bottom: 70px;">
    <nav class="navbar navbar-inverse">
      <div class="container">
        <div class="navbar-header">
          <a href="<?php echo HREF_MAIN;?>" class="pull-left"><img style="max-width:110px; margin-top: 8px; margin-left: 14px;" src="/assets/yonsei-wiki-logo.png"></a>
        </div>
        <div class="navbar-form navbar-right" role="search" style="margin-bottom: -1px;">
          <div class="input-group">
            <input type="text" class="form-control typeahead" id="search-keyword" data-provide="typeahead" placeholder="검색어 입력" style="height: 31px;">
            <span class="input-group-btn">
              <button class="btn btn-default" id="search-button" style="height: 31px;">검색</button>
              <script src="/js/search.js"></script>
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle=dropdown aria-haspopup=true aria-expanded=false style="height: 31px;">
              <?php
                if ($session->started()) {
                    echo '<span class="glyphicon glyphicon-cog" aria-hidden="true">';
                } else {
                    echo '<span class="glyphicon glyphicon-user" aria-hidden="true">';
                }
                ?>
              </button>
              <ul class="dropdown-menu dropdown-menu-right">
                <?php
                  if ($session->started()) {
                      echo '<li><a href="'.HREF_PROFILE.'/'.$session->name.'">내 프로필</a></li>';
                      echo '<li><a href="'.HREF_DASHBOARD.'">계정 관리</a></li>';
                      echo '<li><a href="'.HREF_SIGNOUT.'?redirect='.$page_location.'">로그아웃</a></li>';
                  } else {
                      echo '<li><a href="'.HREF_SIGNIN.'?redirect='.$page_location.'">로그인</a></li>';
                      echo '<li><a href="'.HREF_SIGNUP.'">계정 만들기</a></li>';
                  }
                  ?>
              </ul>
            </span>
          </div>
        </div>
      </div>
    </nav>