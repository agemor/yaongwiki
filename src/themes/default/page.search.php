<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 10. 03
 */

require_once YAONGWIKI_CORE_DIR . "/page.search.processor.php";

const MAX_DISPLAY = 5;
const CONTENT_PREVIEW_LENGTH = 100;

$page = process(MAX_DISPLAY);

if (isset($page["redirect"])) {
    redirect($page["redirect"]);
    exit();
}

$page["title"] = "Search Results for " . implode($page["keywords"], " ") . " - " .  SettingsManager::get_instance()->get("site_title");
?>

<?php require_once __DIR__ . "/frame.header.php"; ?>
<div class="container">

  <div class="title my-4">
    <h2>
    Search Results for <em><?php echo(implode($page["keywords"], " "));?></em>
    </h2>
  </div>

  <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>

  <div class="alert alert-light" role="alert">
    Found <?php echo(count($page["search_result"]));?> results. (<?php echo($page["elapsed_time"]);?>sec)
  </div>

  <div class="card-columns">
  <?php foreach ($page['search_result'] as $result) { ?>
    <div class="card mb-3" >
      <div class="card-body">
        <h4 class="card-title"><a href="./?read&t=<?php echo($result["title"]);?>"><?php echo($result["title"]);?></a></h4>
        <p class="card-text"><?php echo(highlight(truncate(strip_tags($result["content"]), CONTENT_PREVIEW_LENGTH), $page['keywords']));?></p>
      </div>
      <div class="card-footer">
        <small class="text-muted"><?php echo(parse_tags($result["tags"]));?></small>
      </div>
    </div>
 <?php }?>
 </div>

  <?php if (($page["page"] + 1) * MAX_DISPLAY <= count($page["search_result"])) { ?>
  <a href="./?search&q=<?php echo(HttpVarsManager::get_instance()->get("q"));?>&p=<?php echo($page["page"] + 1);?>" class="btn btn-link">Load more...</a>
  <?php } ?>

</div>

<?php
function parse_tags($tags) {
  $chunks = explode(',', $tags);
  $tags = "";
  for ($i = 0; $i < count($chunks); $i++) {
      if (mb_strlen($chunks[$i]) > 0) {
          $tags .= ($i > 0 ? '&nbsp;&nbsp;' : '') . '<a href="./?search&q=@' . $chunks[$i] . '">#' . $chunks[$i] . '</a>';
      }
  }
  return $tags;
}
?>
<?php require_once __DIR__ . "/frame.footer.php"; ?>