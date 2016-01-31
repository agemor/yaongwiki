<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */

require_once 'common.php';?>

<h3>재학생 인증</h3>
<hr/>
<div class="col-md-6">
  <blockquote>
    <p>&nbsp;&nbsp;<a href="<?php echo HREF_READ;?>/연세위키">연세위키</a>에서는 정보의 신뢰성 유지와 <a href="<?php echo HREF_READ;?>/반달리즘">반달리즘</a> 방지를 위해 <a href="<?php echo HREF_READ;?>/재학생">재학생</a> 외의 항목 추가와 수정을 금지하고 있습니다. </p>
    <p>&nbsp;&nbsp;재학생 인증이 완료되면 자유로운 항목 추가와 수정 권한을 가진 <code>편집자</code>로 등급이 올라갑니다.</p>
    <p>&nbsp;&nbsp;재학 여부 확인은 오른쪽의 <a href="http://portal.yonsei.ac.kr">연세포탈</a> 로그인 인증으로 간단하게 할 수 있습니다.</p>
    <br/>
    <footer>모든 정보는 인증 목적 외로 일체 사용되지 않습니다.</footer>
  </blockquote>
</div>
<div class="col-md-6">
  <div class="well">
    <p>
    <div class="text-center"><a href="http://www.yonsei.ac.kr"><img src="/assets/yonsei-logo.png" alt="연세대학교 로고" style="max-width:170px; margin-bottom: 15px;  margin-top: -10px;"></a></div>
    </p>
    <?php 
      if (!$page_response['result'] && $page_focus == 1) {
          echo "<div class=\"alert alert-dismissible alert-danger\" role=\"alert\">".$page_response['message'];
          echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
          echo '</div>';
      } else if(!empty($page_response['message']) && $page_focus == 1) {
          echo "<div class=\"alert alert-dismissible alert-success\" role=\"alert\">".$page_response['message'];
          echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
          echo '</div>';
      }?>
    <form action="<?php echo HREF_DASHBOARD;?>" method="post">
      <div style="margin-bottom: 10px" class="input-group">
        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
        <input type="text" name="student-id" class="form-control" placeholder="학번" required>
      </div>
      <div style="margin-bottom: 17px" class="input-group">
        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
        <input type="password" name="student-password" class="form-control" placeholder="포탈 비밀번호" required>
      </div>
      <button style="margin-bottom: 6px" class="btn btn-primary btn-block" type="submit">인증하기</button>
      <a class="btn btn-default btn-block" href="javascript:finderPopup();">아이디/비밀번호 찾기</a>
    </form>
  </div>
</div>
<script type="text/javascript">
  function finderPopup(){
      win = window.open('https://infra.yonsei.ac.kr/lauth/yscert/yscert_1.jsp',"yscertpopup","width=450,height=550,top=50,left=50,menubar=no,resizable=no,status=no,toolbar=no,location=no,scrolls=no,directories=no");   
      win.focus();
  }
</script>