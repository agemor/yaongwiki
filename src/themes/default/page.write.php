<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 08
 */

require_once YAONGWIKI_CORE . "/page.write.processor.php";

$page = process();

if (isset($page["redirect"]) && $page["redirect"] == true) {
    $redirect->redirect();  
}

$page["title"] = "Edit Article";

$have_permission = $user->permission >= intval($page["article"]["permission"]);

require_once __DIR__ . "/frame.header.php";
?>
<link href=".<?php echo(YAONGWIKI_DIR);?>/themes/default/css/simplemde.min.css" rel="stylesheet">
<script src=".<?php echo(YAONGWIKI_DIR);?>/themes/default/js/simplemde.min.js"></script>

<div class="container">
  <div class="title my-4">
    <h2>
    Edit Article <em>(<?php echo($page["article"]["title"]);?>)</em>
    <h2>
  </div>
  <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>

  <form action="./?write" method="post" name="form">
    <fieldset<?php echo($have_permission ? "" : " disabled");?>>
      <div class="form-group">
        <label for="titleInput">Title</label>
        <input type="text" name="article-new-title" class="form-control" id="titleInput" value="<?php echo($page["article"]["title"]);?>" required>
      </div>

      <?php if ($have_permission && $user->permission > 0) { ?>
      <div class="form-group">
        <label for="id">Editable Permission</label>
        <select name="article-permission" class="form-control" id="permission">
        <?php
          for ($i = 0; $i < min($user->permission, count(PERMISSION_TABLE)); $i++) {
              if ($i == intval($page["article"]["permission"])) {
                  echo '<option value="'. $i . '" selected>' . PERMISSION_TABLE[$i] . '</option>';
              } else {
                  echo '<option value="' . $i . '">' . PERMISSION_TABLE[$i] . '</option>';
              }
          }
        ?>
        </select>
      </div>
      <?php } ?>
      <div class="form-group">
        <label for="contentInput">Content</label>
        <textarea id="editor" name="article-content" class="form-control" id="contentInput"><?php echo($page["article"]["content"]);?></textarea>
      </div>
      <div class="form-group">
        <label for="tagsInput">Tags</label>
        <input type="text" name="article-tags" class="form-control" id="tagsInput" value="<?php echo($page["article"]["tags"]);?>" required>
        <small id="tagsInputHelper" class="text-muted">Separate it with a commas.</small>
      </div>
      <div class="form-group">
        <label for="commentInput">Comment</label>
        <input type="text" name="article-comment" class="form-control" id="commentInput" required>
        <small id="commentInputHelper" class="text-muted">Reason for why you changed this article.</small>
      </div>
      <input type="hidden" name="article-title" value="<?php echo($page["article"]["title"]);?>">
      <input type="hidden" name="article-id" value="<?php echo($page["article"]["id"]);?>">
    </fieldset>
    <button type="submit" class="btn btn-primary">Save</button>
    <?php if ($have_permission && $user->permission >= PERMISSION_DELETE_ARTICLE) { ?>
    <button type="button" class="btn btn-danger" onclick="deleteAlert(this);">Delete</button>
    <?php } ?>
    <a href="./?read&t=<?php echo($page["article"]["title"]);?>" class="btn btn-default">Cancel</a>
  </form>
</div>
<script>
var editor = new SimpleMDE({ element: document.getElementById("editor") });

function deleteAlert(e) {
    if(!confirm("Are you sure to delete this article?")) {
        return;
    }
    var writeForm = document.forms["form"];
    var input = document.createElement("input");
    input.type = "hidden";
    input.name = "article-delete";
    input.value = "true";
    writeForm.appendChild(input);
    writeForm.submit();
}
</script>
<?php
require_once __DIR__ . "/frame.footer.php";
?>