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

const MAX_REVISIONS = 20;

function main() {
    
    global $session;
    global $db_connect_info;

    $http_article_title  = trim($_GET['t']);
    $http_article_id     = trim($_GET['i']);
    $http_revisions_page = intval(isset($_GET['p']) ? $_GET['p'] : '0');
    
    $read_by_id = !empty($http_article_id);
    
    if (empty($http_article_title) && empty($http_article_id))
        return array(
            'result'=>false,
            ''
        );
    
    $db = new YwDatabase($db_connect_info);
    
    if (!$db->connect())
        return array(
            'result'=>false,
            '서버와의 연결에 실패했습니다'
        );
    
    if ($read_by_id)
        $query = "SELECT * FROM " . ARTICLE_TABLE . " WHERE `id`='$http_article_id' LIMIT 1;";
    else
        $query = "SELECT * FROM " . ARTICLE_TABLE . " WHERE `title`='$http_article_title' LIMIT 1;";
    
    if (!$db->query($query))
        return array(
            'result'=>false,
            '글을 읽어오던 중 서버 에러가 발생했습니다'
        );
    
    if ($db->total_results() < 1) {
        if (!$read_by_id)
            navigateTo(HREF_SUGGEST . '?t=' . $http_article_title);
        return array(
            'result'=>false,
            '존재하지 않는 지식입니다'
        );
    }
    
    $article = $db->get_result();
    
    if (!$db->query("SELECT * FROM " . REVISION_TABLE . " WHERE `article_id`=" . $article['id'] . " ORDER BY `timestamp` DESC LIMIT " . ($http_revisions_page * MAX_REVISIONS) . "," . MAX_REVISIONS . ";"))
        return array(
            'result'=>false,
            'page'=>$http_revisions_page,
            '지식의 역사를 불러오는데 실패했습니다'
        );

    $article['revisions'] = array();       
    while ($result = $db->get_result())
        array_push($article['revisions'], $result);    
    
    return array(
        'result'=>true,
        'page'=>$http_revisions_page,
        'article'=>$article,
        ''
    );
}

$page_response = main();
$page_title    = $page_response['article']['title'] . "의 역사";
$page_location = HREF_REVISIONS . '/' . $page_response['article']['id'];

include 'frame.header.php';
?>

<style type="text/css">
  #c {
  display: table-cell;
  vertical-align: middle;
  }
</style>
<div class="container">
  <h1>
    <?php echo '<a style="text-decoration: none;" href="/pages/'.$page_response['article']['title'].'">'.$page_response['article']['title'].'</a>';?>
    <span class="badge"><abbr title="이 지식의 조회수"><?php echo "+".$page_response['article']['hits'];?></abbr></span></h1>
  <div class=" text-right">
    <div class="btn-group" role="group">
      <a type="button" href="<?php echo HREF_WRITE.'/'.$page_response['article']['title'];?>" class="btn btn-default" role="button">지식 업데이트하기</a>
    </div>
    <hr>
  </div>
  <table class="table table-hover">
    <thead>
      <tr>
        <th class="text-center" style="width: 2%">#</th>
        <th class="text-center" style="width: 8%">버전</th>
        <th class="text-center" style="width: 25%">편집자</th>
        <th style="width: 10%">변동</th>
        <th class="text-center" style="width: 15%">비교</th>
        <th class="text-center" style="width: 25%">편집 시간</th>
      </tr>
    </thead>
    <tbody class="text-center">
      <?php
        if (count($page_response['article']['revisions']) < 1) {
            echo '<tr><td></td><td></td><td><em>아직 기여 기록이 없습니다.</em></td><td></td><td></td><td></td></tr>';
        } 
        
        $next_id = 0; // 무설정시 원본글과 대조

        foreach ($page_response['article']['revisions']  as $result) {
            echo '<tr>';
            echo '<td id="c"><a href="'.HREF_REVISION.'?i='.$result["id"].'&j='.$next_id.'">'.$result["id"].'</a></td>';
            echo '<td id="c"><a href="'.HREF_REVISION.'?i='.$result["id"].'&j='.$next_id.'">'.$result["revision"].'</a></td>';
            


            if (strlen($result["comment"]) > 0) {
                echo '<td id="c"><a href="'.HREF_PROFILE.'/'.$result["user_name"].'"">'.$result["user_name"].'</a><br>('.$result["comment"].')</td>';
            } else {
                echo '<td id="c"><a href="'.HREF_PROFILE.'/'.$result["user_name"].'"">'.$result["user_name"].'</a></td>';
            }
        
            $fluctuation = intval($result["fluctuation"]);    
            if($fluctuation > 0) {
                echo '<td id="c"><span style="color:green"><span class="glyphicon glyphicon-arrow-up" aria-hidden="true" ></span> '.$fluctuation.'</span>';
            } else if ($fluctuation == 0) {
                echo '<td id="c"><span style="color:grey"><span class="glyphicon glyphicon-minus" aria-hidden="true" ></span> 0</span>';
            } else {
                echo '<td id="c"><span style="color:red"><span class="glyphicon glyphicon-arrow-down" aria-hidden="true" ></span> '.abs($fluctuation).'</span>';
            }
            echo '</td>';
            echo '<td id="c"> <a href="'.HREF_REVISION.'?i='.$result["id"].'&j='.$next_id.'" class="btn btn-xs btn-default" type="button">비교</a>';
           
            echo '</td>';
            echo '<td id="c">'.$result["timestamp"].'</td>';
            echo '</tr>';

            $next_id = $result["id"];
        }?>
    </tbody>
  </table>
  <nav>
    <ul class="pager">
      <?php
        if($page_response['page'] > 0) {
          echo '<li class="previous"><a href="'.$page_location.'?p='.($page_response['page'] - 1).'"><span aria-hidden="true">&larr;</span> 최근 기록 보기</a></li>';
        }
        if (count($page_response['article']['revisions']) >= MAX_REVISIONS) {
          echo '<li class="next"><a href="'.$page_location.'?p='.($page_response['page'] + 1).'">이전 기록 보기 <span aria-hidden="true">&rarr;</span></a></li>';
        }
        ?>
    </ul>
  </nav>
</div>

<?php include 'frame.footer.php';?>