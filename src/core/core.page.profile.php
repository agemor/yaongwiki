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

    $http_user_name        = trim(!empty($_GET['name']) ? $_GET['name'] : $_POST['user-name']);
    $http_user_info        = strip_tags($_POST['user-info']);
    $http_user_commit_page = intval(!empty($_GET['p']) ? $_GET['p'] : '0');

    if (empty($http_user_name))
        return array(
            'result'=>false,
            'message'=>'존재하지 않는 유저입니다'
        );
    
    if (strlen($http_user_name) < 3)
        return array(
            'result'=>false,
            'message'=>'존재하지 않는 유저입니다'
        );
    
    $db = new YwDatabase($db_connect_info);
    
    // 데이터베이스 연결을 체크합니다.
    if (!$db->connect())
        return array(
            'result'=>false,
            'message'=>'서버와의 연결에 실패했습니다'
        );
    
    // 아이디가 유효한지 확인합니다.
    if (!$db->query("SELECT * FROM " . USER_TABLE . " WHERE `name`='" . $db->purify($http_user_name) . "';"))
        return array(
            'result'=>false,
            'message'=>'유저 정보를 불러오는데 실패했습니다'
        );
    
    if ($db->total_results() < 1)
        return array(
            'result'=>false,
            'message'=>'존재하지 않는 아이디입니다'
        );
    
    $user = $db->get_result();
    
    if (!$db->query("SELECT * FROM " . REVISION_TABLE . " WHERE `user_name`='" . $db->purify($http_user_name) . "' ORDER BY `timestamp` DESC LIMIT " . ($http_user_commit_page * MAX_REVISIONS) . "," . MAX_REVISIONS . ";"))
        return array(
            'result'=>false,
            'user'=>$user,
            'message'=>'유저 기여 정보를 불러오는데 실패했습니다'
        );
    $user['contributions'] = array();    
    while ($result = $db->get_result())
        array_push($user['contributions'], $result);

    // 만약 자기소개 업데이트를 요청했다면,
    if (!empty($http_user_info) && $session->id == $user['id'] && strcmp($user['info'], $http_user_info) != 0) {
        if (!$db->query("UPDATE " . USER_TABLE . " SET `info`='" . $db->purify($http_user_info) . "' WHERE `name`='" . $db->purify($http_user_name) . "';"))
            return array(
                'result'=>false,
                'user'=>$user,
                'page'=>$http_user_commit_page,
                'message'=>'유저 정보를 업데이트하는데 실패했습니다'
            );
        $user['info'] = $http_user_info;
        $db->log($http_user_name, LOG_UPDATE_USER_INFO, $http_user_info);
    }
    
    return array(
        'result'=>true,
        'user'=>$user,
        'page'=>$http_user_commit_page,
        'message'=>''
    );
}

$page_response = main();
$page_title    = $page_response['user']['name'] . '의 프로필';
$page_location = HREF_PROFILE . '/' . $page_response['user']['name'];


if (!$page_response['result'])
   navigateTo(HREF_404);

include 'frame.header.php';
?>

<style type="text/css">
  #c {
  display: table-cell;
  vertical-align: middle;
  }
</style>
<div class="container">
<h1><a href="#" style="text-decoration: none;">
<?php
    $permission_info = permissionInfo(intval($page_response['user']['permission']));
    echo $page_response['user']['name'];
    echo '  <a role="button" class="btn btn-xs btn-'
       .$permission_info['color'].'">'.$permission_info['description'].'</a>';?>
</a></h1><br/><hr/>
<?php
if (!$page_response['result']) {
  echo '<div class="alert alert-dismissible alert-danger" role="alert">'.$page_response['message'];
  echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
  echo '</div>';
}?>

<div class="row">
  <div class="col-md-12">
    <blockquote>
      <p>
      <?php
      if (empty($page_response['user']['info'])) {
          echo "<em>자기소개 정보가 없습니다.</em>";
      } else {
          echo $page_response['user']['info'];
      }
      if ($session->id == $page_response['user']['id']) {?>
      <button style="margin-bottom: 4px" class="btn btn-xs btn-default" type="button" data-toggle="collapse" data-target="#edit" aria-expanded="false" aria-controls="edit">
      <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> 수정
      </button>
      <div class="collapse" id="edit">
        <div class="well well-sm">
          <form action="<?php echo $page_location;?>" method="post">
            <textarea style="margin-bottom: 8px" class="form-control" rows="4" name="user-info" maxlength="1000"><?php echo $page_response['user']['info'];?></textarea>
            <div class="text-center">
              <p>
                <button type="submit" class="btn btn-primary btn-sm">저장</button>
                <button type="button" class="btn btn-default btn-sm" data-toggle="collapse" data-target="#edit">취소</button>
              </p>
            </div>
          </form>
        </div>
      </div>
      <?php }?>
      </p>
      <footer>계정 생성일 <cite><?php echo $page_response['user']['timestamp'];?></cite>
      </footer>
    </blockquote>
    <table class="table table-hover">
      <thead>
        <tr>
          <th class="text-center" style="width: 10%">#</th>
          <th class="text-center" style="width: 45%">항목</th>
          <th class="text-center" style="width: 15%">변동</th>
          <th class="text-center" style="width: 15%">비교</th>
          <th class="text-center" style="width: 25%">편집 시간</th>
        </tr>
      </thead>
      <tbody class="text-center">
        <?php 
        if (count($page_response['user']['contributions']) < 1) {
            echo '<tr><td></td><td><em>아직 기여 기록이 없습니다.</em></td><td></td><td></td><td></td></tr>';
        }

        foreach ($page_response['user']['contributions'] as $result) {

            echo '<tr>';
            echo '<td id="c"><a href="'.HREF_REVISION.'?i='.$result["id"].'">'.$result["id"].'</a></td>';

            if (strlen($result["comment"]) > 0) {
                echo '<td id="c"><a href="'.HREF_READ.'/'.$result["article_id"].'"">'.$result["article_title"].'</a><br>('.$result["comment"].')</td>';
            } else {
                echo '<td id="c"><a href="'.HREF_READ.'/'.$result["article_id"].'"">'.$result["article_title"].'</a></td>';
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
            echo '<td>
                <a href="'.HREF_REVISIONS.'/'.$result["article_id"].'" class="btn btn-xs btn-default" type="button">역사</a> 
                <a href="'.HREF_REVISION.'?i='.$result["id"].'&compare=1" class="btn btn-xs btn-default" type="button">비교</a></td>';
            echo '<td id="c">'.$result["timestamp"].'</td>';
            echo '</tr>'; 
        }
        ?>
      </tbody>
    </table>
    <nav>
      <ul class="pager">
        <?php
        if($page_response['page'] > 0) {
            echo '<li class="previous"><a href="'.$page_location.'&p='.($page_response['page'] - 1).'"><span aria-hidden="true">&larr;</span> 최근 기여 보기</a></li>';
        }
        if (count($page_response['user']['contributions']) >= MAX_REVISIONS) {
            echo '<li class="next"><a href="'.$page_location.'&p='.($page_response['page'] + 1).'">이전 기여 보기 <span aria-hidden="true">&rarr;</span></a></li>';
        }
        ?>
      </ul>
    </nav>
  </div>
  <br/>
  <br/>
</div>

<?php include 'frame.footer.php';?>