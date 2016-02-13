<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */

require_once 'common.php';
require_once 'common.db.php';
require_once 'common.session.php';

function main() {
    
    global $session;
    global $db_connect_info;
    
    $http_revision_id        = trim($_GET['i']);
    $http_revision_target_id = trim($_GET['j']);
    $http_pure               = !empty($_GET['pure']);
    $http_rollback           = !empty($_GET['rollback']);
    
    $orginal_target = intval($http_revision_target_id) == 0;
    
    if (empty($http_revision_id) || !isset($http_revision_target_id))
        return array(
            'result' => false,
            'message' => ''
        );
    
    $db = new YwDatabase($db_connect_info);
    
    // 데이터베이스 연결을 체크합니다.
    if (!$db->connect())
        return array(
            'result' => false,
            'message' => '서버와의 연결에 실패했습니다'
        );
    
    if (!$db->query("SELECT * FROM " . REVISION_TABLE . " WHERE `id`='" . $http_revision_id . "' LIMIT 1;"))
        return array(
            'result' => false,
            'message' => '글을 읽어오던 중 서버 에러가 발생했습니다'
        );
    
    if ($db->total_results() < 1)
        return array(
            'result' => false,
            'message' => '존재하지 않는 지식입니다'
        );
    
    $original = $db->get_result();
    
    if (!$db->query("SELECT * FROM " . ARTICLE_TABLE . " WHERE `id`='" . $original['article_id'] . "' LIMIT 1;"))
        return array(
            'result' => false,
            'message' => '글을 읽어오던 중 서버 에러가 발생했습니다'
        );
    
    if ($http_rollback) {
        
        $article = $db->get_result();

        if (!$session->started())
           return array(
                'result' => false,
                'message' => '로그인한 사용자만 되돌릴 수 있습니다.'
            ); 
        
        if ($session->permission < intval($article['permission']))
            return array(
                'result' => false,
                'message' => '되돌리기 위한 권한이 부족합니다'
            );
        
        if (!$db->query("UPDATE " . ARTICLE_TABLE . " SET " . "`content`='" . $original['snapshot_content'] . "', " . "`title`='" . $original['article_title'] . "', " . "`tags`='" . $original['snapshot_tags'] . "' " . "WHERE `id`='" . $original['article_id'] . "';"))
            return array(
                'result' => false,
                'message' => '되돌리기에 실패했습니다'
            );
        
        if (!$db->query("SELECT `revision` FROM " . REVISION_TABLE . " WHERE `article_id`='" . $original['article_id'] . "' ORDER BY `timestamp` DESC LIMIT 1;"))
            return array(
                'result' => false,
                'message' => '되돌리기에 실패했습니다'
            );
        
        if ($db->total_results() < 1) {
            $article_recent_revision_number = 0;
        } else {
            $result                         = $db->get_result();
            $article_recent_revision_number = intval($result['revision']);
        }
        
        if (!$db->query("INSERT INTO " . REVISION_TABLE . " (`article_id`, `article_title`, `revision`, `user_name`, `snapshot_content`, `snapshot_tags`, `fluctuation`, `comment`) " . "VALUES (" . "'" . $article['id'] . "', " . "'" . $article['title'] . "', " . "'" . ($article_recent_revision_number + 1) . "', " . "'" . $session->name . "', " . "'" . $article['content'] . "', " . "'" . $article['tags'] . "', " . (strlen($original['snapshot_content']) - strlen($article['content'])) . ", " . "'" . $original['revision'] . "에서 복구함');"))
            return array(
                'result' => false,
                'message' => '되돌리기에 실패했습니다'
            );
        
        // 되돌리기 성공
        navigateTo(HREF_READ . '/' . $article['id']);
        
        return array(
            'result' => true
        );
    }
    
    // j값이 0일 경우 원본을 비교 대상으로 지정한다.
    if ($orginal_target)
        $query = "SELECT * FROM " . ARTICLE_TABLE . " WHERE `id`='" . $original['article_id'] . "' LIMIT 1;";
    else
        $query = "SELECT * FROM " . REVISION_TABLE . " WHERE `id`='" . $http_revision_target_id . "' LIMIT 1;";
    
    if (!$db->query($query))
        return array(
            'result' => false,
            'message' => '글을 읽어오던 중 서버 에러가 발생했습니다'
        );
    
    if ($db->total_results() < 1)
        return array(
            'result' => false,
            'message' => '존재하지 않는 지식입니다'
        );
    
    if ($orginal_target) {
        
        $result                       = $db->get_result();
        $revision['id']               = 0;
        $revision['article_title']    = $result['title'];
        $revision['revision']         = '현재';
        $revision['snapshot_content'] = $result['content'];
        $revision['snapshot_tags']    = $result['tags'];
        $revision['timestamp']        = $result['timestamp'];
        
    } else {
        $revision = $db->get_result();
    }
    
    $db->close();
    return array(
        'result' => true,
        'original' => $original,
        'revision' => $revision,
        'original_json' => json_encode($original),
        'revision_json' => json_encode($revision)
    );
}

function parseTags($tags) {
    $chunks = explode(' ', $tags);
    $tags   = "";
    for ($i = 0; $i < count($chunks); $i++) {
        if (strlen($chunks[$i]) > 0)
            $tags .= ($i > 0 ? '&nbsp;&nbsp;' : '') . '<a href="' . HREF_SEARCH . '/' . $chunks[$i] . '">#' . $chunks[$i] . '</a>';
    }
    return $tags;
}

$page_response = main();
$page_title    = $page_response['revision']['article_title'];
$page_location = HREF_REVISION . '?i=' . $page_response['original']['id'] . '&j=' . $page_response['revision']['id'];

include 'frame.header.php';
?>

<div class="container">
  <h1><?php echo '<a style="text-decoration: none;" href="'.HREF_REVISIONS.'/'.$page_response['original']['article_id'].'">'.$page_response['original']['article_title'].'</a><small> (버전 '.$page_response['original']['revision'].' / <em>'.$page_response['original']['timestamp'].'</em>)</small>';?></h1>
  <?php
    if (!$page_response['result']) {
        echo '<div class="alert alert-dismissible alert-danger" role="alert">'.$page_response['message'];
        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        echo '</div>';
    }?>
  <div class=" text-right">
    <div class="btn-group" role="group">
      <a type="button" href="<?php echo HREF_REVISIONS.'/'.$page_response['original']['article_id'];?>" class="btn btn-default" role="button">다른 버전 보기</a>
      <?php if (!isset($_GET['pure'])) {?>
      <a type="button" href="<?php echo $page_location.'&pure=1';?>" class="btn btn-default" role="button">원본 보기</a>
      <?php }?>

      <?php
        if ($session->started())
            echo '<a type="button" href="'.$page_location.'&rollback=1" class="btn btn-default" role="button">이 버전으로 되돌리기</a>';
      ?>
    </div>
    <hr/>
  </div>
  <?php if (!isset($_GET['pure'])) {?>
  <div class="well well-sm">
    <?php
      if ($page_response['revision']['revision'] == 0) {
          $link = HREF_READ . '/' . $page_response['original']['article_id'];
      } else {
          $link = HREF_REVISION.'?i='.$page_response['revision']['id'].'&j=0';
      }
      echo '<a href="'.$link.'"><em>'.$page_response['revision']['article_title'].' (버전 '.$page_response['revision']['revision']. ')</em></a>와 비교한 결과입니다.';
      ?>
  </div>
  <div id='content'></div>
  <br/>
  <div id='tags' class="well well-sm"></div>
  <script src="/js/diff.min.js"></script>
  <script>
    var original = <?php echo $page_response['original_json'];?>;
    var revision = <?php echo $page_response['revision_json'];?>;
  </script>
  <script src="/js/revision.js"></script>
  <?php } else { 
    echo $page_response['original']['snapshot_content'].'<br/><br/><div id="tags" class="well well-sm">';
    echo !empty($page_response['original']['snapshot_tags']) ? parseTags($page_response['original']['snapshot_tags']) : "<em>이 지식에는 아직 분류가 없습니다. 분류를 추가해 주세요!</em>";
    echo '</div>';
    }?>
</div>

<?php include 'frame.footer.php';?>