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

const REDIRECT_KEYWORD = '#이동하기 ';

function main() {
    
    global $session;
    global $db_connect_info;

    $http_article_title = trim($_GET['t']);
    $http_article_id    = trim($_GET['i']);
    $http_no_redirect   = isset($_GET['no-redirect']);
    
    $read_by_id = !empty($http_article_id);
    
    if (empty($http_article_title) && empty($http_article_id))
        return array(
            'result'=>false,
            'message'=>''
        );
    
    $db = new YwDatabase($db_connect_info);
    
    // 데이터베이스 연결을 체크합니다.
    if (!$db->connect())
        return array(
            'result'=>false,
            'message'=>'서버와의 연결에 실패했습니다'
        );
    
    if ($read_by_id)
        $query = "SELECT * FROM " . ARTICLE_TABLE . " WHERE `id`='$http_article_id' LIMIT 1;";
    else
        $query = "SELECT * FROM " . ARTICLE_TABLE . " WHERE `title`='$http_article_title' LIMIT 1;";
    
    if (!$db->query($query))
        return array(
            'result'=>false,
            'message'=>'글을 읽어오던 중 서버 에러가 발생했습니다'
        );
    
    if ($db->total_results() < 1) {
        if (!$read_by_id)
            navigateTo(HREF_SUGGEST . '?t=' . $http_article_title);
        return array(
            'result'=>false,
            'message'=>'존재하지 않는 지식입니다'
        );
    }
    
    $article          = $db->get_result();
    $article_id      = intval($result['id']);
    $article_title   = $result['title'];
    $article_content = $result['content'];
    $article_tags    = $result['tags'];
    $article_hits    = $result['hits'];
    
    // 리다이렉트 문서인지 체크
    $stripped_content = trim(strip_tags($article['content']));
    if (!$http_no_redirect && startsWith($stripped_content, REDIRECT_KEYWORD))
        navigateTo(HREF_READ . '/' . trim(explode(' ', $stripped_content)[1]) . '?from=' . $article['title']);
    
    // 조회수 증가
    if ($session->visit(intval($article['id'])))
        $db->query("UPDATE " . ARTICLE_TABLE . " SET `hits`=`hits`+1, `today_hits`=`today_hits`+1 WHERE `id`='" . $article['id'] . "';");
    
    $db->close();
    return array(
        'result'=>true,
        'article'=>$article,
        'message'=>''
    );
}

function startsWith($haystack, $needle) {
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}


function parseTags($tags) {
    $chunks = explode(' ', $tags);
    $tags   = "";
    for ($i = 0; $i < count($chunks); $i++) {
        if (strlen($chunks[$i]) > 0)
            $tags .= ($i > 0 ? '&nbsp;&nbsp;' : '') . '<a href="' . HREF_SEARCH . '?' . $chunks[$i] . '">#' . $chunks[$i] . '</a>';
    }
    return $tags;
}

$page_response = main();
$page_title    = $page_response['article']['title'];
$page_location = HREF_READ . '/' . $page_response['article']['title'];

if (!$page_response['result'])
    navigateTo(HREF_404);

include 'frame.header.php';
?>

<div class="container">
  <h1><?php echo '<a style="text-decoration: none;" href="'.$page_location.'">'.$page_response['article']['title'].'</a>'?> <span class="badge"><abbr title="이 지식의 조회수"><?php echo "+".$page_response['article']['hits'];?></abbr></span></h1>
  <div class=" text-right">
    <div class="btn-group" role="group">
      <a type="button" href="<?php echo HREF_REVISIONS.'/'.$page_response['article']['title'];?>" class="btn btn-default" role="button">이전 버전 보기</a>
      <a type="button" href="<?php echo HREF_WRITE.'/'.$page_response['article']['title'];?>" class="btn btn-default" role="button">지식 업데이트하기</a>
    </div>
    <hr/>
  </div>
  <?php
    if (isset($_GET["update"])) {
        echo "<div class=\"alert alert-dismissible alert-success\" role=\"alert\">지식을 새로 업데이트했습니다";
        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        echo '</div>';
    } else if (!empty($_GET["from"]) ) {
        echo "<div class=\"alert alert-info\" role=\"alert\"><b><a style='color:#FFFFFF' href='".$_GET["from"]. "&no-redirect=1'>".$_GET["from"]."</a></b> 에서 넘어왔습니다";
        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        echo '</div>';
    }
  ?>

  <?php echo $page_response['article']['content'];?>
  <br/>
  <div class="well well-sm"><?php echo !empty($page_response['article']['tags']) ? parseTags($page_response['article']['tags']) : "<em>이 지식에는 아직 분류가 없습니다. 분류를 추가해 주세요!</em>";?></div>
</div>

<?php include 'frame.footer.php';?>