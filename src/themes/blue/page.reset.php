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
require_once 'tools/tool.mailer.php';

function main() {

    global $session;
    global $db_connect_info;
    global $http_user_email;
    
    if ($session->started())
        navigateTo(HREF_MAIN);
    
    $http_user_email = trim($_POST['user-email']);
    
    // 입력 값의 유효성을 검증한다.
    if (empty($http_user_email))
        return array(
            'result'=>true,
            'message'=>''
        );
    
    // 이메일 포멧의 유효성을 검증한다.
    if (!filter_var($http_user_email, FILTER_VALIDATE_EMAIL))
        return array(
            'result'=>false,
            'message'=>'이메일 주소가 올바르지 않습니다'
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
    if (!$db->query("SELECT `name` FROM " . USER_TABLE . " WHERE `email`='" . $db->purify($http_user_email) . "';"))
        return array(
            'result'=>false,
            'message'=>'이메일 주소를 조회하는데 실패했습니다'
        );
    
    if ($db->total_results() < 1) {
        return array(
            'result'=>false,
            'message'=>'존재하지 않는 이메일 주소입니다'
        );
    }
    
    $result    = $db->get_result();
    $user_name = $result['name'];
    
    // 새로운 비밀번호를 생성한다.
    $generated_password = bin2hex(openssl_random_pseudo_bytes(6));
    
    if (!$db->query("UPDATE " . USER_TABLE . " SET `password`='" . passwordHash($generated_password) . "' WHERE `email`='" . $db->purify($http_user_email) . "';")) {
        return array(
            'result'=>false,
            'message'=>'비밀번호를 업데이트하는데 실패했습니다'
        );
    }
    
    $email_content = "<b>" . $user_name . "</b> 회원님의 새 비밀번호는 <b>" . $generated_password . "</b>입니다.";
    if (!getMailer($http_user_email, "연세위키 비밀번호를 알려드립니다", $email_content))
        return array(
            'result'=>false,
            'message'=>'이메일 발송에 실패했습니다'
        );
    
    $db->log($user_name, LOG_RESET, '1');
    $db->close();
    
    return array(
        'result'=>true,
        'message'=>'이메일로 아이디와 새로운 비밀번호를 전송했습니다'
    );
}

$page_response = main();
$page_title    = "계정 찾기";
$page_location = HREF_RESET;

include 'frame.header.php';
?>

<div class="container">
  <h1><a href="#" style="text-decoration: none;">계정 찾기</a></h1><br/>
  <hr/>
  <div class="row">
    <div class="col-md-6">
      <blockquote>
        <p>계정 생성 시 입력했던 이메일 주소를 입력해 주세요.</p>
        <footer>어쩌지... 생각이 안 나. <cite> - 첫 중간고사를 보는 새내기 L양</cite></footer>
      </blockquote>
    </div>
    <div class="col-md-6">
      <form style="width:auto; margin:auto;" action="<?php echo HREF_RESET;?>" method="post">
        <div class="well">
          <?php
            if(!$page_response['result']) {
            	echo '<div class="alert alert-danger alert-dismissible" role="alert">';
            	echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
            	echo $page_response['message'];
            	echo '</div>';
            } else if (!empty($page_response['message'])) {
            	echo '<div class="alert alert-success alert-dismissible" role="alert">';
            	echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
            	echo $page_response['message'];
            	echo '</div>';
            }?>
          <div style="margin-bottom: 10px" class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
            <input type="text" name="user-email" class="form-control" placeholder="이메일 주소" value="<?php echo $http_user_email;?>" required <?php if(!$error) { echo "autofocus"; }?>>
          </div>
          <script src='https://www.google.com/recaptcha/api.js'></script>
          <div class="g-recaptcha" style="margin-top: 10px; margin-bottom: 20px; float: right;" data-sitekey="<?php echo RECAPTCHA_PUBLIC_KEY;?>" ></div>
          <input type="hidden" name="redirect" value="<?php echo $redirect;?>">
          <button class="btn  btn-primary btn-block" type="submit">계정 정보 받기</button>
          <a class="btn btn-default btn-block" href="<?php echo HREF_SIGNIN;?>" role="button">취소하기</a>
        </div>
      </form>
    </div>
  </div>
  <br/><br/>
</div>

<?php include 'frame.footer.php';?>