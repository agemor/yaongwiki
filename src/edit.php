<?php
require 'db.php';
require 'session.php';

function diff($old, $new){
    $matrix = array();
    $maxlen = 0;
    foreach($old as $oindex => $ovalue){
        $nkeys = array_keys($new, $ovalue);
        foreach($nkeys as $nindex){
            $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
                $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
            if($matrix[$oindex][$nindex] > $maxlen){
                $maxlen = $matrix[$oindex][$nindex];
                $omax = $oindex + 1 - $maxlen;
                $nmax = $nindex + 1 - $maxlen;
            }
        }   
    }
    if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
    return array_merge(
        diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
        array_slice($new, $nmax, $maxlen),
        diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
}

function htmlDiff($old, $new){
    $ret = '';
    $diff = diff(preg_split("/[\s]+/", $old), preg_split("/[\s]+/", $new));
    foreach($diff as $k){
        if(is_array($k))
            $ret .= (!empty($k['d'])?"<del>".implode(' ',$k['d'])."</del> ":'').
                (!empty($k['i'])?"<ins>".implode(' ',$k['i'])."</ins> ":'');
        else $ret .= $k . ' ';
    }
    return $ret;
}

$article_title = empty($_GET['t']) ? $_POST['article-title'] : $_GET['t'];

if(empty($article_title)) {
  header('Location: 404.php');
}

if(!$loggedin) {
  header('Location: signin.php?redirect=edit.php?t='.$article_title);
}

$error = false;
$error_message = "";

// 데이터베이스 연결
$db = new mysqli($db_server, $db_user, $db_password, $db_name);
if ($db->connect_errno) {
  exit($db->connect_error);
} 

// 읽어오기 - 이전에 넘겨진 데이터가 없는 경우
if (empty($_POST['article-id'])) {

  $sqlQuery = "SELECT * FROM `$db_articles_table` ";
  $sqlQuery .= "WHERE `title`='".$db->real_escape_string($article_title)."';";

  $result = $db->query($sqlQuery);

  // 읽어올 결과값이 없는 경우 404로 리다이렉트
  if ($result->num_rows < 1) {
    header('Location: 404.php');
  }

  $row = $result->fetch_assoc();
  $article_id = $row["id"];
  $article_content = $row["content"];
  $article_tags = $row["tags"];
  $article_hits = $row["hits"];

  $result->free();
} 

// 저장하기
else {

  $article_title = $db->real_escape_string($_POST['article-title']);
  $article_id = $db->real_escape_string($_POST["article-id"]);
  $article_content = $db->real_escape_string($_POST['article-content']);
  $article_content_previous = $db->real_escape_string($_POST['article-content-previous']);
  $article_tags = $db->real_escape_string($_POST['article-tags']);
  $article_tags_previous = $db->real_escape_string($_POST['article-tags-previous']);
  if (empty($article_title) || empty($article_id)) {
    exit();
  }

  $sqlQuery = "UPDATE `$db_articles_table` SET ";
  $sqlQuery .= "`content`='$article_content', ";
  $sqlQuery .= "`tags`='$article_tags' ";
  $sqlQuery .= "WHERE `id`='$article_id'";

  // 글 업데이트
  if ($db->query($sqlQuery) === TRUE) {

    $content_diff = $db->real_escape_string(htmlDiff($article_content_previous, $article_content));
    $tags_diff = $db->real_escape_string(htmlDiff($article_tags_previous, $article_tags));

    $sqlQuery = "INSERT INTO `$db_revisions_table` (`article_id`, `user_id`, `content`, `tags`, `fluctuation`) ";
    $sqlQuery .= "VALUES ('$article_id', '$user_id', '$content_diff', '$tags_diff', ".(strlen($article_content_previous) - strlen($article_content)).")";

    if ($db->query($sqlQuery) === TRUE) {
      header('Location: read.php?t='.$article_title."&update=1");
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

  <h1><?php echo $article_title;?> <span class="badge"><abbr title="이 지식의 조회수"><?php echo "+".$article_hits;?></abbr></span></h1><br/>
  <div class="well well-sm">
    자세한 편집 방법은 <a href="read.php?t=편집 방법">편집 방법</a> 문서를 참조하세요.<br/>
    <em>지식에 대해 고의적인 훼손을 가하거나 악의적인 내용을 작성할 경우 차단될 수 있습니다.</em>
  </div>
  <?php
  if ($error) {
    echo "<div class=\"alert alert-danger\" role=\"alert\">".$error_message."</div>";
  }?>
  <form action="edit.php" method="post">
    <div class="form-group">
      <label for="content">내용</label>
      <textarea type="text" name="article-content" class="form-control" id="content" data-provide="markdown" rows="23" style="padding: 10px"><?php echo $article_content;?></textarea>
    </div>
    <div class="form-group">
      <label for="tags">태그 <small>(공백으로 구분해 주세요)</small></label>
      <input type="text" name="article-tags" class="form-control"  id="tags" value="<?php echo $article_tags;?>">
    </div>
    <input type="hidden" name="article-title" value="<?php echo $article_title;?>">
    <input type="hidden" name="article-id" value="<?php echo $article_id;?>">
    <input type="hidden" name="article-content-previous" value="<?php echo $article_content;?>">
    <input type="hidden" name="article-tags-previous" value="<?php echo $article_tags;?>">
    <div class="text-center">
      <button type="submit" class="btn btn-default" onclick="window.onbeforeunload=null;">업데이트</button>
      <a href="read.php?t=<?php echo $article_title;?>" class="btn btn-danger" role="button">취소</a>
    </div>
  </form>
</div>
<script type="text/javascript">
  $("#content").markdown({language:'kr'});
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