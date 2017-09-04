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
require_once 'tools/tool.yonsei-auth.php';

function main() {

    global $session;
    global $db_connect_info;
    global $page_focus;

    $http_user_email           = trim($_POST['user-email']);
    $http_user_password        = $_POST['user-password'];
    $http_user_new_password    = $_POST['user-new-password'];
    $http_user_new_password_re = $_POST['user-new-password-re'];
    $http_student_id           = trim($_POST['student-id']);
    $http_student_password     = $_POST['student-password'];
    $http_user_password_drop   = $_POST['user-drop-password'];

    // 0: 계정 정보, 1: 재학생 인증, 2: 이메일 변경, 4: 비번 변경, 4: 계정 삭제
    $page_focus = 0;
    
    if (!$session->started())
        navigateTo(HREF_MAIN);
    
    $db = new YwDatabase($db_connect_info);
    
    if (!$db->connect())
        return array(
            'result'=>false,
            'message'=>'서버와의 연결에 실패했습니다'
        );
    
    // 유저 정보 불러오기
    if (!$db->query("SELECT * FROM " . USER_TABLE . " WHERE `id`=" . $session->id . ";"))
        return array(
            'result'=>false,
            'message'=>'유저 정보를 불러오는데 실패했습니다'
        );
    
    $user = $db->get_result();
    $user['login_history'] = array();

    // 최근 3일간 로그인 기록 가져오기
    if (!$db->query("SELECT * FROM " . LOG_TABLE . " WHERE `user_name`='" . $user['name'] . "' AND `behavior`='signin' AND `timestamp` >= (CURDATE() - INTERVAL 3 DAY) " . "ORDER BY `timestamp` DESC LIMIT 30;"))
        return array(
            'result'=>false,
            'user'=>$user,
            'message'=>'최근 로그인 기록을 로드하는데 실패했습니다'
        );

    while ($result = $db->get_result())
        array_push($user['login_history'], $result);

    if (!empty($http_student_id)) {
        
        $page_focus = 1;
        
        // 중복 학번 검사
        if (!$db->query("SELECT 1 FROM " . USER_TABLE . " WHERE `code`='" . $db->purify($http_student_id) . "';"))
            return array(
                'result'=>false,
                'user'=>$user,
                'message'=>'학번을 조회하지 못했습니다'
            );
        
        if ($db->total_results() > 0)
            return array(
                'result'=>false,
                'user'=>$user,
                'message'=>'이미 인증에 사용된 연세포탈 계정입니다'
            );
        
        // 포탈 로그인 인증
        if (!getYonseiAuth($http_student_id, $http_student_password))
            return array(
                'result'=>false,
                'message'=>'학번이나 비밀번호가 올바르지 않습니다'
            );
        
        if (!$db->query("UPDATE " . USER_TABLE . " SET `code`='" . $http_student_id . "'" . (intval($user['permission']) < 1 ? ", `permission`=1" : "") . " WHERE `id`=" . $user['id'] . ";"))
            return array(
                'result'=>false,
                'message'=>'서버 오류로 인증을 완료하지 못했습니다'
            );
        
        if ($user_permission < 1)
            $session->setPermission(1);
        
        $db->log($session->name, LOG_STUDENT_AUTH, $http_student_id);
        
        navigateTo(HREF_DASHBOARD . '?auth=1');
        
        return array(
            'result'=>true,
            'user'=>$user,
            'message'=>'재학생 인증을 완료했습니다'
        );
    }
    
    // 이메일 변경
    if (!empty($http_user_email)) {
        
        $page_focus = 2;
        
        if (!filter_var($http_user_email, FILTER_VALIDATE_EMAIL))
            return array(
                'result'=>false,
                'user'=>$user,
                'message'=>'이메일 주소가 올바르지 않습니다'
            );
        
        if (strcmp($user['email'], $http_user_email) == 0)
            return array(
                'result'=>false,
                'user'=>$user,
                'message'=>'동일한 이메일 주소가 입력되었습니다'
            );
        
        if (!$db->query("SELECT 1 FROM " . USER_TABLE . " WHERE `email`='" . $db->purify($http_user_email) . "';"))
            return array(
                'result'=>false,
                'user'=>$user,
                'message'=>'이메일 주소 조회에 실패했습니다'
            );
        
        if ($db->total_results() > 0)
            return array(
                'result'=>false,
                'user'=>$user,
                'message'=>'이미 사용중인 이메일 주소입니다'
            );
        
        if (!$db->query("UPDATE " . USER_TABLE . " SET `email`='" . $db->purify($http_user_email) . "' WHERE `id`=" . $user['id'] . ";"))
            return array(
                'result'=>false,
                'user'=>$user,
                'message'=>'이메일 주소 변경에 실패하였습니다'
            );
        
        $db->log($session->name, LOG_CHANGE_EMAIL, $user['email']);
        
        $user['email'] = $http_user_email;
        
        return array(
            'result'=>true,
            'user'=>$user,
            'message'=>'이메일 주소를 변경하였습니다'
        );
    }
    
    // 비밀번호 변경
    if (!empty($http_user_new_password)) {
        
        $page_focus = 3;
        
        if (strcmp($http_user_new_password, $http_user_new_password_re) != 0)
            return array(
                'result'=>false,
                'user'=>$user,
                'message'=>'비밀번호와 비밀번호 확인이 일치하지 않습니다'
            );
        
        if (strlen($http_user_new_password) < 4)
            return array(
                'result'=>false,
                'user'=>$user,
                'message'=>'비밀번호는 4자 이상으로 입력해 주세요'
            );
        
        $http_user_password = passwordHash($http_user_password);
        $http_user_new_password = passwordHash($http_user_new_password);

        if (strcmp($user['password'], $http_user_password) != 0)
            return array(
                'result'=>false,
                'user'=>$user,
                'message'=>'현재 비밀번호가 올바르지 않습니다'
            );
        
        if (!$db->query("UPDATE " . USER_TABLE . " SET `password`='" . $http_user_new_password . "' WHERE `id`=" . $user['id'] . ";"))
            return array(
                'result'=>false,
                'user'=>$user,
                'message'=>'서버 오류로 비밀번호를 변경하지 못했습니다'
            );
        
        $db->log($session->name, LOG_CHANGE_PASSWORD, $user['password']);
        
        return array(
            'result'=>true,
            'user'=>$user,
            'message'=>'비밀번호를 변경하였습니다.'
        );
    }
    
    // 계정 삭제
    if (!empty($http_user_password_drop)) {
        
        $page_focus = 4;
        
        if (strcmp($user['password'], passwordHash($http_user_password_drop)) != 0)
            return array(
                'result'=>false,
                'user'=>$user,
                'message'=>'비밀번호가 올바르지 않습니다'
            );
        
        if (!$db->query("DELETE FROM " . USER_TABLE . " WHERE `id`=" . $user['id'] . ";"))
            return array(
                'result'=>false,
                'user'=>$user,
                'message'=>'서버 오류로 계정을 삭제하지 못했습니다'
            );
        
        $db->log($session->name, LOG_DELETE_ACCOUNT, '');
        
        navigateTo(HREF_SIGNOUT);
        
        return array(
            'result'=>true,
            'user'=>$user,
            'message'=>''
        );
    }

    return array(
        'result'=>true,
        'user'=>$user
    );
}

$page_response = main();
$page_title    = '대시보드';
$page_location = HREF_DASHBOARD;

include 'frame.header.php';
?>

<div class="container">
<h1 ><a href="#" style="text-decoration: none;">대시보드</a></h1><br/>
<ul class="nav nav-tabs" role="tablist">
  <li role="presentation" class="<?php if($page_focus==0) {echo ' active';}?>"><a href="#main" aria-controls="main" role="tab" data-toggle="tab">계정 정보</a></li>
  <?php
    if (empty($page_response['user']['code'])){
        echo '<li role="presentation"'.(($page_focus==1) ? ' class="active"' : '').'><a href="#auth" aria-controls="auth" role="tab" data-toggle="tab">재학생 인증</a></li>';
    }?>
  <li role="presentation" class="<?php if($page_focus==2) {echo ' active';}?>"><a href="#email" aria-controls="email" role="tab" data-toggle="tab">이메일 변경</a></li>
  <li role="presentation" class="<?php if($page_focus==3) {echo ' active';}?>"><a href="#password" aria-controls="password" role="tab" data-toggle="tab">비밀번호 변경</a></li>
  <li role="presentation" class="<?php if($page_focus==4) {echo ' active';}?>"><a href="#dropout" aria-controls="dropout" role="tab" data-toggle="tab">계정 삭제</a></li>
</ul>
<div class="tab-content">
  
  <div role="tabpanel" class="tab-pane<?php echo (($page_focus == 0) ? ' active' : ' fade');?>" id="main">
    <?php include 'frame.myinfo.php';?>
  </div>
  <div role="tabpanel" class="tab-pane<?php echo (($page_focus == 1) ? ' active' : ' fade');?>" id="auth">
    <?php include 'frame.yonseiauth.php';?>
  </div>
  <div role="tabpanel" class="tab-pane<?php echo (($page_focus == 2) ? ' active' : ' fade');?>" id="email">
    <?php include 'frame.changeemail.php';?>
  </div>
  <div role="tabpanel" class="tab-pane<?php echo (($page_focus == 3) ? ' active' : ' fade');?>" id="password">
    <?php include 'frame.changepassword.php';?>
  </div>
  <div role="tabpanel" class="tab-pane<?php echo (($page_focus == 4) ? ' active' : ' fade');?>" id="dropout">
    <?php include 'frame.deleteaccount.php';?>
  </div>
</div>
</div>

<?php include 'frame.footer.php';?>