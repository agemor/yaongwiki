<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */

require_once 'common.php';

$page_title = "존재하지 않는 페이지";
$page_location = HREF_404;

include 'frame.header.php';?>

<div class="container">
  <h1><a style="text-decoration: none;" href="#">존재하지 않는 페이지</a></h1>
  <br/>
  <hr/>
  <blockquote>
    <p>이 페이지는 존재하지 않습니다. 다른 곳으로 이동했거나 삭제됐을 수 있습니다.</p>
    <footer>술먹고 상경대 반가를 부르는 <cite> - 어떤 학생</cite></footer>
  </blockquote>
</div>

<?php include 'frame.footer.php';?>