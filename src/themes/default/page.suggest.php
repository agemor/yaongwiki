<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */

require_once 'common.php';

if (empty($_GET["t"])) {
    navigateTo(HREF_404);
}

$title = $_GET["t"];

$page_title    = $title;
$page_location = HREF_SUGGEST . '?t=' . $title;

include 'frame.header.php';
?>

<div class="container">
  <h1><?php echo '<a style="text-decoration: none;" href="'.$page_location.'">'.$title.'</a>';?></h1>
  <br/>
  <hr/>
  <blockquote>
        <p>이 항목은 지금 존재하지 않습니다. 여기에 당신의 <a href="<?php echo HREF_CREATE.'?t='.$title; ?>">지식을 공유해</a> 주세요.</p>
        <footer>항상 수고해 주셔서 고맙습니다. <cite> - 엘리베이터에서 청소 아주머니를 만난 한 여학생</cite></footer>
  </blockquote>
</div>
<?php include 'frame.footer.php';?>