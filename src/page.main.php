<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 02. 01
 */

require_once 'common.php';
require_once 'common.db.php';
require_once 'common.session.php';
require_once 'tools/tool.recent-updates.php';
require_once 'tools/tool.popular.php';

const PLACEHOLDERS = array("새물샘 운영시간", "신임 총장", "신촌 맛집", "중앙동아리", "공대 역사", "M6724 막차시간", "밥버거 토핑 추천", "송도 배달음식", "화계 운영시간");


function main() {
    
    global $session;
    global $db_connect_info;
    
    $db = new YwDatabase($db_connect_info);
    
    // 데이터베이스 연결을 체크한다.
    if (!$db->connect())
        return array(
            'result' => false,
            'message' => '서버와의 연결에 실패했습니다'
        );
    
    $now_time = intval(date("H")) * 60 + intval(date("i"));
    if ($now_time > 19 * 60) {
        $now_date = date('Y-m-d', strtotime('+1 day'));
    } else {
        $now_date = date("Y-m-d");
    }
    return array(
        'result' => true,
        'recent' => getRecentUpdates($db),
        'popular' => getPopularArticles($db),
        'date' => $now_date
    );
    
}

function truncate($text) {
    if (mb_strlen($text) > 4)
        return mb_substr($text, 0, 5) . "...";
    else
        return $text;
}

$page_response = main();
$page_title    = "연세위키";
$page_location = HREF_MAIN;
?>
<!DOCTYPE html>
<html lang="ko">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="./favicon.ico">
    <title><?php echo $page_title;?></title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="./theme/bootstrap.yeti.css" rel="stylesheet">
    <link href="./page.main.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="./js/typeahead.js"></script>
    <script src="./js/analytics.js"></script>
  </head>
  <body>
    <ul class="pager" style="margin-right:15px;">
      <?php
        if ($session->started()) {
            echo '<li class="next"><a href="'.HREF_PROFILE.'/'.$session->name.'" style="color:grey">내 프로필</a></li>';
        } else {
            echo '<li class="next"><a href="'.HREF_SIGNIN.'?redirect='.$page_location.'" style="color:grey">로그인</a></li>';
        }
        ?>
      <li class="next"><a href="./recent" style="color:grey">최근 변경</a></li>
      <li class="next"><a href="./create" style="color:grey">+ 항목 추가</a></li>
    </ul>
    <div class="container">
      <div class="text-center" style="max-width:700px; margin: auto;">
        <div class="logo">
          <img style="max-width:260px; margin-bottom:40px; margin-top:30px" src="./assets/yonsei-wiki-logo-main.png">
        </div>
        <div class="input-group input-group-lg" role="search">
          <input type="text" class="form-control typeahead" id="search-keyword" placeholder="<?php echo PLACEHOLDERS[array_rand(PLACEHOLDERS)];?>" data-provide="typeahead" >
          <span class="input-group-btn">
            <button class="btn btn-primary" id="search-button">검색</button>
            <script src="./js/search.js"></script>
          </span>
        </div>
        <div style="margin-top:25px">
          <h5>
            <a href="./pages/<?php echo $page_response['date']." 국제캠퍼스 학식 정보"; ?>">학식</a>
            &nbsp;&nbsp;&nbsp;
            <a href="http://ysweb.yonsei.ac.kr/ysbus.jsp">셔틀</a>
            &nbsp;&nbsp;&nbsp;
            <?php 
              foreach ($page_response['popular'] as $popular) {
                  echo '<a href="'.HREF_READ.'/'.$popular['id'].'">'.truncate($popular['title']).'</a>';
                  echo '&nbsp;&nbsp;&nbsp;';
              }
              
              foreach ($page_response['recent'] as $recent) {
                  echo '<a href="'.HREF_READ.'/'.$recent['id'].'">'.truncate($recent['title']).'</a>';
              }
              ?>
          </h5>
        </div>
      </div>
    </div>
    <footer class="footer">
      <div class="container">
        <p class="text-muted text-center">&copy; 2016 연세위키</p>
      </div>
    </footer>
  </body>
</html>