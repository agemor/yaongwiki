<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 08
 */

if (isset($page["redirect"]) && $page["redirect"] == true) {
    $redirect->redirect();  
}

$page["title"] = "Page Not Found";

require_once __DIR__ . "/frame.header.php";
?>

<div class="container">
  <div class="title my-4">
    <h2>
    Page Not Found
    <h2>
  </div>
  <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>
    <div class="row my-4">
      <div class="col-md-6">
        <p>
          Sorry, this page<em><?php echo(" ". HttpVarsManager::get_instance()->get("t"));?></em> does not exist. It may be removed or renamed. You can try another search keyword.
        </p>
        <a href="./" class="btn btn-default">Go Main Page</a>
      </div>
    </div>
</div>
<?php
require_once __DIR__ . "/frame.footer.php";
?>