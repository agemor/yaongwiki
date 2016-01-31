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

    $http_user_name     = trim(strip_tags($_POST['user-name']));
    $http_user_password = trim($_POST['user-password']);
    $http_redirect      = empty($_POST['redirect']) ? HREF_MAIN : $_POST['redirect'];
    
    if ($session->started())
        navigateTo(HREF_MAIN);
    
    if (empty($http_user_name) || empty($http_user_password))
        return array(
            'result'=>true,
            'message'=>''
        );

    $db = new YwDatabase($db_connect_info);
    
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
    
    $result = $db->get_result();
    
    // 비밀번호가 일치하는지 확인합니다.
    if (strcmp(passwordHash($http_user_password), $result['password']) != 0) {
        $db->log($session->ip, LOG_SIGNIN, '0');
        return array(
            'result'=>false,
            'message'=>'비밀번호가 올바르지 않습니다'
        );
    }

    // 세션 등록
    $session->start($result['name'], $result['id'], intval($result['permission']));
    $db->log($session->name, LOG_SIGNIN, '1');
    $db->close();
    
    navigateTo($http_redirect);
    
    return array(
        'result'=>true,
        'message'=>''
    );
}

$page_response = main();
$page_title    = "로그인";
$page_location = HREF_SIGNIN;

include 'frame.header.php';
?>

<div class="container">
  <h1><a href="#" style="text-decoration: none;">로그인</a></h1><br/>
  <hr/>
  <div class="row">
    <div class="col-md-6">
      <blockquote>
        <p>모든 기능을 사용하기 위해선 로그인이 필요합니다.</p>
        <footer>스피드게이트에 카드 찍고 들어오세요! <cite> - 2기숙사 D동 사감</cite></footer>
      </blockquote>
    </div>
    <div class="col-md-6">
      <form style="width:auto; margin:auto;" action="<?php echo HREF_SIGNIN;?>" method="post">
        <div class="well">
          <?php
            if (!$page_response['result']) {
                echo "<div class=\"alert alert-dismissible alert-danger\" role=\"alert\">".$page_response[message];
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
            } else if (!empty($_GET["signup"])) {
                echo "<div class=\"alert alert-success\" role=\"alert\">계정을 생성하였습니다</div>";
            }?>
          <div style="margin-bottom: 10px" class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input type="text" name="user-name" class="form-control" placeholder="아이디" value="<?php echo $http_user_name;?>" required <?php if($page_response[0]) { echo "autofocus"; }?>>
          </div>
          <div style="margin-bottom: 10px" class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
            <input type="password" name="user-password" class="form-control" placeholder="비밀번호" required <?php if(!$page_response[0]) { echo "autofocus"; }?>>
          </div>
          <div class="checkbox text-right">
            <label><input type="checkbox" value="remember-me">정보 기억하기</label>
          </div>
          <input type="hidden" name="redirect" value="<?php echo $_GET['redirect'];?>">
          <button class="btn btn-primary btn-block" type="submit">로그인</button>
          <a class="btn btn-default btn-block" href="<?php echo HREF_SIGNUP;?>" role="button">계정 생성하기</a>
          <a class="btn btn-default btn-block" href="<?php echo HREF_RESET;?>" role="button">아이디/비밀번호 찾기</a>
        </div>
      </form>
    </div>
  </div>
  <br/><br/>
</div>

<?php include 'frame.footer.php';?>