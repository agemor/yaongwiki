<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 05
 */

require_once YAONGWIKI_CORE . "/page.test.processor.php";

$page = process();

require_once __DIR__ . "/frame.header.php";
?>

<div class="container">
  <h1>테스트 페이지 (전달받은 값: <?php echo($page["value"]);?>)</h1>
  <br/>
  <hr/>
  <blockquote>
    <p>메인 텍스트</p>
    <footer>서브 텍스트</footer>
  </blockquote>
</div>
<?php
require_once __DIR__ . "/frame.footer.php";
?>