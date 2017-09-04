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

    $http_article_title = trim(strip_tags(empty($_POST['article-title']) ? '' : $_POST['article-title']));

    if (!$session->started())
        navigateTo(HREF_SIGNIN . '?redirect=' . HREF_CREATE . '?t=' . $http_article_title);

    if (strlen(preg_replace('/\s+/', '', $http_article_title)) < 2)
        return array(
            'result'=>true,
            'message'=>''
        );
    
    if (is_numeric($http_article_title))
        return array(
            'result'=>false,
            'message'=>'지식 제목으로 숫자를 사용할 수 없습니다'
        );
    
    $db = new YwDatabase($db_connect_info);
    
    if (!$db->connect())
        return array(
            'result'=>false,
            'title'=>$http_article_title,
            'message'=>'서버와의 연결에 실패했습니다'
        );
    
    if (!$db->query("SELECT 1 FROM " . ARTICLE_TABLE . " WHERE `title`='" . $http_article_title . "';"))
        return array(
            'result'=>false,
            'title'=>$http_article_title,
            'message'=>'지식 정보를 조회하는데 실패했습니다'
        );
    
    if ($db->total_results() > 0)
        return array(
            'result'=>false,
            'title'=>$http_article_title,
            'message'=>'이미 존재하는 지식입니다'
        );
    
    // 지식 등록
    if (!$db->query("INSERT INTO " . ARTICLE_TABLE . " (`title`) VALUES ('" . $db->purify($http_article_title) . "');"))
        return array(
            'result'=>false,
            'title'=>$http_article_title,
            'message'=>'지식을 추가하는 중 서버 오류가 발생했습니다'
        );
    
    $db->log($session->name, LOG_CREATE, $http_article_title);
    $db->close();
    
    navigateTo(HREF_WRITE . '/' . $http_article_title);
    
    return array(
        'result'=>true,
        'title'=>$http_article_title,
        ''
    );
}

$page_response = main();
$page_title    = '새 지식 만들기';
$page_location = HREF_CREATE . '?t=' . $page_response['title'];

include 'frame.header.php';
?>

<div class="container">
  <h2><a href="#" style="text-decoration: none;">새 지식 만들기</a></h2><br>
  <hr/>
  <div class="row">
    <div class="col-md-6">
      <blockquote>
        <p>새 지식을 만들기 전 체크리스트</p>
        <br/>
        <h5>1. 비슷하거나 동일한 지식이 있진 않은지 </h5>
        <h5>2. 들어갈 수 있는 내용이 충분한 지식인지</h5>
        <h5>3. 누군가를 불쾌하게 만들 내용은 아닌지</h5>
        <br/>
        <footer>날씨가 점점 추워지네... <cite> - 진리관C를 청소하시는 아주머니</cite></footer>
      </blockquote>
    </div>
    <div class="col-md-6">
      <form class="form-signin" style="width:auto; margin:auto;" action="<?php echo $page_location;?>" method="post">
        <div class="well">
          <?php
            if (!$page_response['result']) {
                echo '<div class="alert alert-dismissible alert-danger" role="alert">'.$page_response['message'];
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
            }?>
          <div style="margin-bottom: 20px" class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-pencil"></i></span>
            <input type="text" name="article-title" class="form-control" placeholder="지식 제목" value="<?php echo $_GET['t'];?>" required autofocus>
          </div>
          <button class="btn btn-primary btn-block" type="submit">만들기</button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>
<?php include 'frame.footer.php';?>