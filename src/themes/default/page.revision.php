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

$page["title"] = $page["revision"]["article_title"] . " (Revision " .$page["revision"]["revision"]. ")";

require_once __DIR__ . "/frame.header.php";
?>
<div class="container">
  <div class="title my-4">
    <h2>
    <?php echo($page["revision"]["article_title"]);?>
    <small class="text-muted"> (<em>Revision <?php echo($page["revision"]["revision"]);?>)</em></small>
    <h2>
  </div>  
  <div class="text-right mb-3">
      <div class="btn-group" role="group">
        <a class="btn btn-default" href="./?read&i=<?php echo($page['revision']['article_id']);?>" >Read article</a>
        <a class="btn btn-default" href="./?revision-history&i=<?php echo($page['revision']['article_id']);?>">Rollback to this version</a>
        <a class="btn btn-default" href="./?revision-history&i=<?php echo($page['revision']['article_id']);?>">Revision History</a>
      </div>
    </div>
  <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>

  <ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link active" href="#">Content view</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">Markdown view</a>
  </li>
</ul>

<div>
<?php echo($page["revision"]["snapshot_content"]);?>

</div>


</div>

<?php 
require_once __DIR__ . "/frame.footer.php";
?>