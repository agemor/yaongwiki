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
require_once 'tools/tool.recent-updates.php';

function main() {

    global $db_connect_info;

    $db = new YwDatabase($db_connect_info);
    
    // 데이터베이스 연결을 체크합니다.
    if (!$db->connect())
        return array(
            'result'=>false,
            'message'=>'서버와의 연결에 실패했습니다'
        );

    $recent_updates = getRecentUpdates($db);

    return array(
        'result'=>true,
        'recent_updates'=>$recent_updates
      );
    
}

$now_time = intval(date("H")) * 60 + intval(date("i"));
if ($now_time > $dinner_end) {
    $now_date = date('Y-m-d', strtotime('+1 day'));
} else {
    $now_date = date("Y-m-d");
}

$page_response = main();
$page_title = "메인";
$page_location = HREF_MAIN;
include 'frame.header.php';
?>

<div class="container">
  <div class="row">
    <div class="col-sm-6 col-md-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><a href="/pages/<?php echo $now_date." 국제캠퍼스 학식 정보"; ?>">학식 메뉴</a></h3>
        </div>
        <div class="panel-body">
          <div>
            <ul class="nav nav-tabs" role="tablist">
              <li role="presentation" class="active"><a href="#dormitory1" aria-controls="home" role="tab" data-toggle="tab">1기숙사</a></li>
              <li role="presentation"><a href="#dormitory2" aria-controls="profile" role="tab" data-toggle="tab">2기숙사</a></li>
              <li role="presentation"><a href="#uml" aria-controls="messages" role="tab" data-toggle="tab">하늘샘</a></li>
            </ul>
            <div class="tab-content">
              <div role="tabpanel" id="dormitory1" class="tab-pane active"></div>
              <div role="tabpanel" id="dormitory2" class="tab-pane fade"></div>
              <div role="tabpanel" id="uml" class="tab-pane fade"></div>
            </div>
            <script src="/js/todaymenu.js"></script>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">꾸르잼 링크</h3>
        </div>
        <div class="panel-body">
          <span class="label label-primary">학교생활</span><br>
          <h5>
            <strong>학교생활</strong>
            <hr>
          </h5>
          새내기를 위한 재학생의 가이드<br>
          셔틀버스 예약<br>
          송도 맛집<br>
          송도 주요 동아리<br>
          3월 주요 학사일정<br>
          연세위키에 처음이신가요?<br><br>
          <span class="label label-primary">학교 뉴스</span><br>
          김용학 신임 총장의 정책<br>
          라온샘 주말 영업시간<br><br>
          <span class="label label-primary">최근 이슈</span><br>
          교환학생
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">많이 검색된 지식</h3>
        </div>
        <div class="panel-body">
          핫이슈 & 뉴스
          총학생회 선거 논란, 새물샘 주말영업
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">주요 링크</h3>
        </div>
        <div class="panel-body">
          <a href="#">연세포탈</a> · 
          <a href="#">YSCEC</a> ·
          <a href="#">송도학사</a> ·
          <a href="#">전자출결</a>
        </div>
      </div>
    </div>
  </div>
  <div class="jumbotron">
    <h2>송도 최강 집단지성</h2>
    <br/>
    <p>위키는 분명 유용한 정보를 여럿이 자유롭게 공유하는 도구로써 홀륭한 방법임에 틀림없습니다. 하지만 초보자에겐 사용하기 복잡하고, 편집하기 두려운 지금의 위키, 대안이 필요했습니다.</p>
    <p>누구나 빠르고 쉽게 지식 공유에 참여할 수 있는 연세위키! 지식의 경계선을 넓혀 보세요.</p>
    <a class="btn btn-lg btn-primary" href="#" role="button">자세히 알아보기 &raquo;</a>
  </div>
  <div class="row">
    <div class="col-md-4">
      <h3>공지사항</h3>
      <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
      <p><a class="btn btn-default" href="#" role="button">목록 보기 &raquo;</a></p>
    </div>
    <div class="col-md-4">
      <h3>도움이 필요한 지식</h3>
      <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
      <p><a class="btn btn-default" href="#" role="button">목록 보기 &raquo;</a></p>
    </div>
    <div class="col-md-4">
      <h3>최근 업데이트된 지식</h3>
      <?php
        
        foreach ($page_response['recent_updates'] as $result)
            echo '<a href="'.HREF_READ.'/'.$result.'">'.$result.'</a>&nbsp&nbsp;';
        ?>
      <p><a class="btn btn-default" href="#" role="button">목록 보기 &raquo;</a></p>
    </div>
  </div>
</div>

<?php include 'frame.footer.php';?>
