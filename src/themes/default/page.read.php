<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 14
 */

require_once YAONGWIKI_CORE . "/page.read.processor.php";

$page = process();

if (isset($page["redirect"]) && $page["redirect"] == true) {
    $redirect->redirect();  
}

$page["title"] = $page["article"]["title"];

$have_permission = $user->permission >= intval($page["article"]["permission"]);

require_once __DIR__ . "/frame.header.php";
?>
<div class="container">
  <div class="title my-4">
    <h2>
    <?php echo($page["article"]["title"]);?>
    <small class="text-muted"> (Views: <?php echo($page["article"]["hits"]);?>)</small>
    <h2>
    </div>
    <div class="text-right mb-3">
      <div class="btn-group" role="group">
        <a class="btn btn-default" href="./?write&i=<?php echo($page['article']['id']);?>" >Edit article</a>
        <a class="btn btn-default" href="./?revision-history&i=<?php echo($page['article']['id']);?>" >Revision history</a>
      </div>
    </div>
  
  <?php if ($get->retrieve("from") !== null) { ?>
  <div class="alert alert-info" role="alert">
    Redirected from <a href="./?read&t=<?php echo($get->retrieve('from'));?>&no-redirect=1"><?php echo($get->retrieve("from"));?></a>.
  </div>
  <?php } ?>
  <ol class="breadcrumb">
  <?php 
  if (count($page["article"]["tags"]) == 0) { ?>
    <li class="breadcrumb-item">No tags</li>
  <?php }
  for ($i = 0; $i < count($page["article"]["tags"]); $i++) { ?>
    <li class="breadcrumb-item"><a href="./?search&q=@<?php echo($page["article"]["tags"][$i]);?>"><?php echo($page["article"]["tags"][$i]);?></a></li>
  <?php } ?>
  </ol>
  <div class="text-content my-4">
  <?php echo($page["article"]["content"]);?><br/>
  </div>
</div>
<?php
require_once __DIR__ . "/frame.footer.php";
?>