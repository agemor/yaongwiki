<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 09
 */

$page["title"] = "YaongWiki";
 
require_once __DIR__ . "/frame.header.php";
?>
<div class="container">
  <div class="title my-4">
    <h2>
    Main Page
    <h2>
  </div>
  <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>
  <div class="row my-4">
    <div class="col-md-6">
		<a href="/?recent">View recently edited articles</a><br/>
		<a href="/?create">Create new article</a>
	  </div>
		<div class="col-md-6">
	  </div>
	</div>
</div>
<?php
require_once __DIR__ . "/frame.footer.php";
?>