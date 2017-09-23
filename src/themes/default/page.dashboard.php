<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 24
 */

require_once YAONGWIKI_CORE . "/page.dashboard.processor.php";

$page = process();

if (isset($page["redirect"]) && $page["redirect"] == true) {
    $redirect->redirect();  
}

$page["title"] = "Dashboard";

require_once __DIR__ . "/frame.header.php";
?>
<div class="container">
  <div class="title my-4">
    <h2>
    <?php echo($page["revision"]["article_title"]);?>
    <small class="text-muted"> (<em>Revision <?php echo($page["revision"]["revision"]);?>)</em></small>
    <h2>
  </div>
  <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>
  
</div>


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

<?php
require_once __DIR__ . "/frame.footer.php";
?>