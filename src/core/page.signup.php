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
require_once 'tools/tool.recaptcha.php';

function main() {
    
    global $session;
    global $db_connect_info;
    global $http_user_name;
    global $http_user_email;

    if ($session->started())
        navigateTo(HREF_MAIN);

    $http_user_name        = trim($_POST['user-name']);
    $http_user_password    = trim($_POST['user-password']);
    $http_user_password_re = trim($_POST['user-password-re']);
    $http_user_email       = trim($_POST['user-email']);
    
    // 입력 값의 유효성을 검증한다.
    if (empty($http_user_name) || empty($http_user_password) || empty($http_user_email))
        return array(
            'result'=>true,
            'message'=>''
        );
    
    if (strlen($http_user_name) < 2)
        return array(
            'result'=>false,
            'message'=>'아이디는 3자 이상으로 입력해 주세요'
        );
    
    if (strlen($http_user_password) < 5)
        return array(
            'result'=>false,
            'message'=>'비밀번호는 4자 이상으로 입력해 주세요'
        );
    
    if (strcmp($http_user_password, $http_user_password_re) != 0)
        return array(
            'result'=>false,
            'message'=>'비밀번호와 비밀번호 확인이 일치하지 않습니다'
        );
    
    // 이메일 포멧의 유효성을 검증한다.
    if (!filter_var($http_user_email, FILTER_VALIDATE_EMAIL))
        return array(
            'result'=>false,
            'message'=>'올바르지 않은 이메일 주소입니다'
        );
    
    // reCAPTCHA를 검증한다.
    if (!getReCaptcha())
        return array(
            'result'=>false,
            'message'=>'reCAPTCHA가 올바르게 입력되지 않았습니다'
        );
    
    $db = new YwDatabase($db_connect_info);
    
    // 데이터베이스 연결을 체크한다.
    if (!$db->connect())
        return array(
            'result'=>false,
            'message'=>'서버와의 연결에 실패했습니다'
        );
    
    // 아이디와 이메일 유효성을 검증한다.
    if (!$db->query("SELECT `name` FROM " . USER_TABLE . " WHERE `name`='" . $db->purify($http_user_name) . "' OR `email`='" . $db->purify($http_user_email) . "';"))
        return array(
            'result'=>false,
            'message'=>'유저 정보를 불러오는데 실패하였습니다'
        );
    
    if ($db->total_results() > 0) {
        $result = $db->get_result();
        if (strcmp($http_user_name, $result['name']) == 0)
            return array(
                'result'=>false,
                'message'=>'이미 사용중인 아이디입니다'
            );
        else
            return array(
                'result'=>false,
                'message'=>'이미 사용중인 이메일 주소입니다'
            );
    }
    
    // 서버로 데이터를 전송한다.
    if (!$db->query("INSERT INTO " . USER_TABLE . " (`name`, `password`, `email`) VALUES ('" . $db->purify($http_user_name) . "', '" . passwordHash($http_user_password) . "', '" . $db->purify($http_user_email) . "');"))
        return array(
            'result'=>false,
            'message'=>'계정을 생성하는데 실패했습니다'
        );
    
    $db->log($http_user_name, LOG_SIGNUP, '1');
    $db->close();
    
    navigateTo(HREF_SIGNIN . '?signup=1');
    
    return array(
        'result'=>true,
        'message'=>''
    );
}

$page_response = main();
$page_title    = "계정 만들기";
$page_location = HREF_SIGNUP;

include 'frame.header.php';
?>

<div class="container">
  <h1><a href="#" style="text-decoration: none;">계정 만들기</a></h1><br/>
  <hr/>
  <div class="row">
    <div class="col-md-6">
      <blockquote>
        <p>연세위키 계정을 만들면 자유롭게 지식을 수정하고 추가하는 열쇠가 당신의 손에 쥐여집니다.</p>
        <footer>우리 동아리 들어오실래요? <cite> - 공대 K모 군</cite></footer>
      </blockquote>
      <br>
      <div class="panel panel-default" style="width:auto; margin:auto;">
        <div class="panel-heading">
          <h3 class="panel-title">이용 약관 동의</h3>
        </div>
        <div class="panel-body">
        <p>1. 연세위키는 <a href="<?php echo HREF_READ.'/운영 방침';?>">운영 방침</a>에 따라 연세대학교 재학생의 편의를 최대화하는 방향으로 운영됩니다.</p>
        <p>2. 가능한 한 최소한의 <abbr title="아이디, 이메일, 암호화된 학번 등">개인정보</abbr>만을 회원 식별을 위해 저장하며 이외의 용도로는 사용하지 않습니다.</p>
        <p>3. 저작물에 대한 모든 권리와 책임은 사용자에게 귀속되며, 연세위키는 발생하는 문제에 대해 책임을 지지 않습니다.</p>
        <br/>
        <p style="margin-bottom:-1px">전문은 <a href="<?php echo HREF_READ.'/이용약관';?>">이용약관</a>, <a href="<?php echo HREF_READ.'/개인정보 처리방침';?>">개인정보 처리방침</a>에서 확인할 수 있습니다.</p>

        </div>
      </div>
      <br/>
    </div>
    <div class="col-md-6">
      <form class="form-signin" action="<?php echo HREF_SIGNUP;?>" method="post">
        <div class="panel panel-default" style="width:auto margin:auto;">
          <div class="panel-heading">
            <h3 class="panel-title">계정 정보 입력</h3>
          </div>
          <div class="panel-body">
            <?php
              if (!$page_response['result']) {
                  echo '<div class="alert alert-dismissible alert-danger" role="alert">'.$page_response['message'];
                  echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                  echo '</div>';
              }?>
            <div style="margin-bottom: 10px" class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input type="text" name="user-name" class="form-control" placeholder="아이디" value="<?php echo $http_user_name;?>" required autofocus>
            </div>
            <div style="margin-bottom: 10px" class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
              <input type="text" name="user-email" class="form-control" placeholder="이메일" value="<?php echo $http_user_email;?>" required>
            </div>
            <div style="margin-bottom: -1px" class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
              <input type="password" name="user-password" class="form-control" placeholder="비밀번호" required>
            </div>
            <div style="margin-bottom: 10px" class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
              <input type="password" name="user-password-re" class="form-control" placeholder="비밀번호 다시 입력" required>
            </div>
            <script src='https://www.google.com/recaptcha/api.js'></script>
            <div class="g-recaptcha" style="margin-top: 10px; margin-bottom: 20px; float: right;" data-sitekey="<?php echo RECAPTCHA_PUBLIC_KEY;?>" ></div>
            <br/>
            <button class="btn btn-primary btn-block" type="submit">약관 동의 및 계정 등록하기</button>
            <a class="btn btn-default btn-block" href="<?php echo HREF_SIGNIN;?>" role="button">취소하기</a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <br/><br/>
</div>

<?php include 'frame.footer.php';?>