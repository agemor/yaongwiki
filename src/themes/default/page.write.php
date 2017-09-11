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

$page["title"] = "Write Article";

require_once __DIR__ . "/frame.header.php";
?>
<link href=".<?php echo(YAONGWIKI_DIR);?>/themes/default/css/simplemde.min.css" rel="stylesheet">
<script src=".<?php echo(YAONGWIKI_DIR);?>/themes/default/js/simplemde.min.js"></script>



<div class="container">
  <div class="title my-4">
    <h2>
    <?php echo($page["article"]["title"]);?>
    <h2>
  </div>
  <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>
  <textarea id="editor" name="article-content"></textarea>



</div>

<script>
var editor = new SimpleMDE({ element: document.getElementById("editor") });
</script>

<div class="container">

  <h1>
  <?php
  echo '<a style="text-decoration: none;" href="'.HREF_READ.'/'.$page_response['article']['title'].'">'.$page_response['article']['title'].'</a> ';
  if ($session->permission >= PERMISSION_CHANGE_TITLE) { 
      echo ' <a role="button" class="btn btn-default btn-xs" data-toggle="collapse" data-target="#edit-title">제목 수정하기</a>';
  }?>
  </h1><br/>

  <form action="<?php echo $page_location;?>" method="post">

    <?php
    if ($session->permission >= PERMISSION_CHANGE_TITLE) { 
        echo '<div class="collapse" id="edit-title">';
        echo '<input type="text" class="form-control input-lg" name="article-new-title" placeholder="이 지식의 제목" value="'
             .$page_response['article']['title'].'" aria-describedby="helpBlock">';
        echo '<span id="helpBlock" class="help-block"><em>지식 제목의 잦은 변경은 다른 사용자들에게 혼란을 줄 수 있습니다.</em></span></div>';   
    }
    echo "<hr>";

    if (!$page_response['result']) {
        echo "<div class=\"alert alert-dismissible alert-danger\" role=\"alert\">".$page_response['message'];
        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        echo '</div>';
    }
    ?>
    <div class="well well-sm">
      자세한 편집 방법은 <a href="<?php echo HREF_READ;?>/편집 방법">편집 방법</a> 문서를 참조하세요.<br/>
      <em>지식에 대해 고의적인 훼손을 가하거나 악의적인 내용을 작성할 경우 차단될 수 있습니다.</em>
    </div>
  
    <div class="form-group">
      <label for="content">내용</label>
      <textarea id="editorc" name="article-content" required><?php echo $page_response['article']['content'];?></textarea>
      <script src="./js/editor.js"></script>
    </div>

    <div class="form-group">
      <label for="tags">분류 <small>(공백으로 구분해 주세요)</small></label>
      <input type="text" name="article-tags" class="form-control" id="tags" value="<?php echo $page_response['article']['tags'];?>">
    </div>
    <input type="hidden" name="article-title" value="<?php echo $page_response['article']['title'];?>">
      <?php
      if ($session->permission > 0) {
          echo '<div class="form-group">
                <label for="id">편집 가능한 권한</label>
                <select name="article-permission" class="form-control" id="permission">';
          
          for ($i = 0; $i <= $session->permission; $i++) {
              $info = permissionInfo($i);
              if ($i == intval($page_response['article']['permission']))
                  echo '<option value="'.$i.'" selected>'.$info['description'].'</option>';
              else 
                  echo '<option value="'.$i.'">'.$info['description'].'</option>';
          }
          echo '</select></div>'; 
      }?>
    <div class="form-group">
      <label for="comment">수정한 내용 요약 <small>(구체적으로 적어주시면 도움이 됩니다.)</small></label>
      <input type="text" name="article-comment" class="form-control"  id="comment" value="<?php echo $http_article_comment;?>">
    </div>

    <div class="text-center">
      <?php
      if ($session->permission >= PERMISSION_DELETE_ARTICLE && $session->permission >= intval($page_response['article']['permission'])) { 
          echo '<div class="checkbox"><label><input type="checkbox" name="article-delete" value="1" onclick="return deleteAlert(this);">이 지식 삭제하기</label></div>';
      }?>
      <button type="submit" class="btn btn-default <?php echo (intval($page_response['article']['permission']) > $session->permission) ? "disabled" : "";?>" onclick="window.onbeforeunload=null;">업데이트</button>
      <a href="<?php echo HREF_READ.'/'.$page_response['article']['title'];?>" class="btn btn-danger" role="button">취소</a>
    </div>
  </form>
</div>
<script type="text/javascript">
  function deleteAlert(e) {
    if (e.checked) {
      alert('이 옵션을 누르면 지식이 영구적으로 삭제됩니다.');
    }
    return true;
  }
  var confirmOnPageExit = function (e) {
    e = e || window.event;
    var message = '업데이트되지 않은 내용은 삭제됩니다.';
    if (e) {
        e.returnValue = message;
    }
    return message;
  };
  window.onbeforeunload = confirmOnPageExit;
</script>

<?php
require_once __DIR__ . "/frame.footer.php";
?>