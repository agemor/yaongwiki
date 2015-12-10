<?php
require 'db.php';
require 'session.php';
include 'finediff.php';

$TITLE_CHANGE_PERMISSION = 1;
$ARTICLE_DELETE_PERMISSION = 1;

$article_title = empty($_GET['t']) ? $_POST['article-title'] : $_GET['t'];

if(empty($article_title)) {
  header('Location: 404.php');
  exit();
}

if(!$loggedin) {
  header('Location: signin.php?redirect=edit.php?t='.$article_title);
  exit();
}

$error = false;
$error_message = "";

// 데이터베이스 연결
$db = new mysqli($db_server, $db_user, $db_password, $db_name);
if ($db->connect_errno) {
  exit($db->connect_error);
} 

// 읽어오기
$sqlQuery = "SELECT * FROM `$db_articles_table` ";
$sqlQuery .= "WHERE `title`='".$db->real_escape_string($article_title)."' LIMIT 1;";

$result = $db->query($sqlQuery);

// 읽어올 결과값이 없는 경우 404로 리다이렉트
if ($result->num_rows < 1) {
  header('Location: 404.php');
  exit();
}

$row = $result->fetch_assoc();
$article_id = $row["id"];
$article_content = $row["content"];
$article_tags = $row["tags"];
$article_hits = $row["hits"];

$result->free();

// 저장하기
if (isset($_POST['article-content'])) {

  $article_content_previous = $article_content;
  $article_tags_previous = $article_tags;

  $article_new_title = $_POST['article-new-title'];
  $article_content = $_POST['article-content'];
  $article_tags = $_POST['article-tags'];
  $article_delete = isset($_POST['article-delete']);

  if (empty($article_title) || empty($article_id)) {
    exit();
  }

  // 삭제 명령
  if($article_delete && $user_permission >= $ARTICLE_DELETE_PERMISSION) {
    $sqlQuery = "DELETE FROM `$db_articles_table` WHERE `id`='$article_id';";
    $sqlQuery .= "DELETE FROM `$db_revisions_table` WHERE `article_id`='$article_id';"; 
    if ($db->multi_query($sqlQuery) === TRUE) {
      header('Location: 404.php');
    }

    exit();
  }

  // 변경된 내용이 없으면 그냥 리다이렉트
  if (strlen($article_content) - strlen($article_content_previous) == 0) {
    if (strcmp($article_content, $article_content_previous) == 0 && strcmp($article_tags, $article_tags_previous) == 0 && strcmp($article_title, $article_new_title) == 0) {
      header('Location: read.php?t='.$article_title."&update=2");
      exit();
    }
  }

  $sqlQuery = "UPDATE `$db_articles_table` SET ";
  $sqlQuery .= "`content`='".$db->real_escape_string($article_content)."', ";
  $sqlQuery .= "`tags`='".$db->real_escape_string($article_tags)."' ";
  if (strlen($article_new_title) > 1  && $user_permission >= $TITLE_CHANGE_PERMISSION) {
    $titleChanged = true;
    $sqlQuery .= ",`title`='".$db->real_escape_string($article_new_title)."' ";
  }
  $sqlQuery .= "WHERE `id`='".$db->real_escape_string($article_id)."';";

  // 글 업데이트
  if ($db->query($sqlQuery) === TRUE) {
  
    $opcodes = FineDiff::getDiffOpcodes($article_content_previous, $article_content);

    $sqlQuery = "INSERT INTO `$db_revisions_table` (`article_id`, `user_name`, `content`, `opcodes`, `tags`, `fluctuation`) ";
    $sqlQuery .= "VALUES (";
    $sqlQuery .= "'".$article_id."', ";
    $sqlQuery .= "'".$user_name."', ";
    $sqlQuery .= "'".$db->real_escape_string($article_content_previous)."', ";
    $sqlQuery .= "'".$db->real_escape_string($opcodes)."', ";
    $sqlQuery .= "'".$db->real_escape_string($article_tags)."', ";
    $sqlQuery .= (strlen($article_content) - strlen($article_content_previous)).");";

    if ($db->query($sqlQuery) === TRUE) {
      if ($titleChanged) {
        header('Location: read.php?t='.$article_new_title."&update=1");
      } else {
        header('Location: read.php?t='.$article_title."&update=1");
      }
    } else {
      $error_message = $db->error;
      $error = true;
    }
    
  } else {
    $error_message = $db->error;
    $error = true;
  }
}
$db->close();

$title = $article_title." - 편집하기";
include 'header.php';
?>

<link href="libs/bootstrap-markdown/css/bootstrap-markdown.min.css" rel="stylesheet">

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="libs/bootstrap-markdown/js/markdown.min.js"></script>
<script src="libs/bootstrap-markdown/js/bootstrap-markdown.js"></script>
<script src="libs/bootstrap-markdown/locale/bootstrap-markdown.kr.js"></script>

<div class="container">

  <h1>
  <?php
  echo $article_title." ";
  if ($user_permission >= $TITLE_CHANGE_PERMISSION) { 
    echo " <a role=\"button\" class=\"btn btn-default btn-xs\" data-toggle=\"collapse\" data-target=\"#edit-title\">제목 수정</a>";
  }?>
  </h1>

  <form action="edit.php" method="post">

    <?php
    if ($user_permission >= $TITLE_CHANGE_PERMISSION) { 
      echo '<div class="collapse" id="edit-title">';
      echo '<input type="text" class="form-control input-lg" name="article-new-title" placeholder="이 지식의 제목" value="'.$article_title.'" aria-describedby="helpBlock">';
      echo '<span id="helpBlock" class="help-block"><em>지식 제목의 잦은 변경은 다른 사용자들에게 혼란을 줄 수 있습니다.</em></span></div>';   
    }
    ?>
    <hr>
    <div class="well well-sm">
      자세한 편집 방법은 <a href="read.php?t=편집 방법">편집 방법</a> 문서를 참조하세요.<br/>
      <em>지식에 대해 고의적인 훼손을 가하거나 악의적인 내용을 작성할 경우 차단될 수 있습니다.</em>
    </div>
    <?php
    if ($error) {
      echo "<div class=\"alert alert-danger\" role=\"alert\">".$error_message."</div>";
    }?>
  
    <div class="form-group">
      <label for="content">내용</label>
      <textarea type="text" name="article-content" class="form-control" id="content" data-provide="markdown" rows="23" style="padding: 10px"><?php echo $article_content;?></textarea>
    </div>
    <div class="form-group">
      <label for="tags">태그 <small>(공백으로 구분해 주세요)</small></label>
      <input type="text" name="article-tags" class="form-control"  id="tags" value="<?php echo $article_tags;?>">
    </div>
    <input type="hidden" name="article-title" value="<?php echo $article_title;?>">
    <div class="text-center">
      <?php
      if ($user_permission >= $ARTICLE_DELETE_PERMISSION) { 
        echo '<div class="checkbox"><label><input type="checkbox" name="article-delete" value="1" onclick="return deleteAlert(this);">이 지식 삭제하기</label></div>';
      }?>
      <button type="submit" class="btn btn-default" onclick="window.onbeforeunload=null;">업데이트</button>
      <a href="read.php?t=<?php echo $article_title;?>" class="btn btn-danger" role="button">취소</a>
    </div>
  </form>
</div>
<script type="text/javascript">
  $("#content").markdown({language:'kr'});

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
<?php include 'footer.php';?>