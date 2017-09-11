<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 08
 */

require_once CORE_DIRECTORY . "/page.create.processor.php";

$page = process();

if (isset($page["redirect"]) && $page["redirect"] == true) {
    $redirect->redirect();  
}

$page["title"] = "Create Article";

require_once __DIR__ . "/frame.header.php";
?>

<div class="container">
  <div class="title my-4">
    <h2>
    Create Article
    <h2>
  </div>
  <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>
  <form action="./?create" method="post">
    <div class="row my-4">
      <div class="col-md-6">
        <p>Before you create a new article, please check that:
          <br/>1. Whether there are no similar or identical posts.
          <br/>2. Make sure it's not too narrow topic.
        </p>
        <div class="form-group">
          <label for="titleInput">Title</label>
          <input type="text" name="article-title" class="form-control" id="titleInput" value="<?php echo($post->retrieve('article-title'));?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
      </div>
      <div class="col-md-6">
      </div>
    </div>
  </form>
</div>
<?php
require_once __DIR__ . "/frame.footer.php";
?>