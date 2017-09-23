<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 23
 */

require_once YAONGWIKI_CORE . "/page.revision.processor.php";

$page = process();

if (isset($page["redirect"]) && $page["redirect"] == true) {
    $redirect->redirect();  
}

$page["title"] = "Revision(" .$page["revision"]["revision"]. ") of " . $page["revision"]["article_title"];

require_once __DIR__ . "/frame.header.php";
?>
<div class="container">
  <h1><?php echo '<a style="text-decoration: none;" href="'.HREF_REVISIONS.'/'.$page_response['original']['article_id'].'">'.$page_response['original']['article_title'].'</a><small> (버전 '.$page_response['original']['revision'].' / <em>'.$page_response['original']['timestamp'].'</em>)</small>';?></h1>
  <?php
    if (!$page_response['result']) {
        echo '<div class="alert alert-dismissible alert-danger" role="alert">'.$page_response['message'];
        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        echo '</div>';
    }?>
  <div class=" text-right">
    <div class="btn-group" role="group">
      <a type="button" href="<?php echo HREF_REVISIONS.'/'.$page_response['original']['article_id'];?>" class="btn btn-default" role="button">다른 버전 보기</a>
      <?php if (!isset($_GET['pure'])) {?>
      <a type="button" href="<?php echo $page_location.'&pure=1';?>" class="btn btn-default" role="button">원본 보기</a>
      <?php }?>

      <?php
        if ($session->started())
            echo '<a type="button" href="'.$page_location.'&rollback=1" class="btn btn-default" role="button">이 버전으로 되돌리기</a>';
      ?>
    </div>
    <hr/>
  </div>
  <?php if (!isset($_GET['pure'])) {?>
  <div class="well well-sm">
    <?php
      if ($page_response['revision']['revision'] == 0) {
          $link = HREF_READ . '/' . $page_response['original']['article_id'];
      } else {
          $link = HREF_REVISION.'?i='.$page_response['revision']['id'].'&j=0';
      }
      echo '<a href="'.$link.'"><em>'.$page_response['revision']['article_title'].' (버전 '.$page_response['revision']['revision']. ')</em></a>와 비교한 결과입니다.';
      ?>
  </div>
  <div id='content'></div>
  <br/>
  <div id='tags' class="well well-sm"></div>
  <script src="./js/diff.min.js"></script>
  <script>
    var original = <?php echo $page_response['original_json'];?>;
    var revision = <?php echo $page_response['revision_json'];?>;
  </script>
  <script src="./js/revision.js"></script>
  <?php } else { 
    echo $page_response['original']['snapshot_content'].'<br/><br/><div id="tags" class="well well-sm">';
    echo !empty($page_response['original']['snapshot_tags']) ? parseTags($page_response['original']['snapshot_tags']) : "<em>이 지식에는 아직 분류가 없습니다. 분류를 추가해 주세요!</em>";
    echo '</div>';
    }?>
</div>

<?php include 'frame.footer.php';?>