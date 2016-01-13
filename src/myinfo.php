<?php
$page_title = "계정 관리 - 야옹위키";
$page_location = "404.php";
require 'session.php';
include 'header.php';?>

<div class="container">

<h1>계정 관리</h1>
<hr/>
<ul class="nav nav-tabs" role="tablist">
	<li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab" data-toggle="tab">메인</a></li>
	<li role="presentation"><a href="#auth" aria-controls="auth" role="tab" data-toggle="tab">재학생 인증</a></li>
	<li role="presentation"><a href="#email" aria-controls="email" role="tab" data-toggle="tab">이메일 변경</a></li>
	<li role="presentation"><a href="#password" aria-controls="password" role="tab" data-toggle="tab">비밀번호 변경</a></li>
	<li role="presentation"><a href="#dropout" aria-controls="dropout" role="tab" data-toggle="tab">계정 삭제</a></li>
</ul>

<div class="tab-content">
	<div role="tabpanel" class="tab-pane active fade" id="main">...</div>
	<div role="tabpanel" class="tab-pane fade" id="auth">
		<div class="container" style="padding:10px">
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-body">
						<p><div class="text-center"><a href="http://www.yonsei.ac.kr"><img src="assets/yonsei-logo.png" alt="연세대학교 로고" style="max-width:170px; margin-bottom: 15px;"></a></div></p>
						<p>&nbsp;&nbsp;<a href="#">연세위키</a>에서는 정보의 신뢰성 유지와 <a href="#">반달리즘</a> 방지를 위해 <a href="#">재학생</a> 외의 작성을 금지하고 있습니다. 재학생 인증이 완료되면 자유로운 항목 추가와 수정 권한을 가진 <code>재학생</code>으로 레벨이 올라갑니다.</p>
						<p>&nbsp;&nbsp;재학 여부 확인은 오른쪽의 <a href="http://portal.yonsei.ac.kr">연세포탈</a> 로그인 인증으로 간단하게 할 수 있습니다.</p>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="well">
					<label style="margin-bottom: 15px" for="user-password">모든 정보는 인증 목적 외로 사용되거나 저장되지 않습니다.</label>	
					<div style="margin-bottom: 10px" class="input-group">
						<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
						<input type="text" id="studentId" name="student-id" class="form-control" placeholder="학번">
					</div>
					<div style="margin-bottom: 17px" class="input-group">
						<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
						<input type="password" id="studentPassword" name="student-password" class="form-control" placeholder="포탈 비밀번호">
					</div>
				
					<button style="margin-bottom: 6px" class="btn btn-primary btn-block" type="submit">재학생 인증</button>
					<a class="btn btn-default btn-block" href="javascript:finderPopup();">아이디/비밀번호 찾기</a>
				</div>
			</div>
		</div>
	</div>
	<div role="tabpanel" class="tab-pane fade" id="email">
		<div class="container" style="padding:10px">


			<div class="col-md-6">
				<div class="well">
					<div style="margin-bottom: 10px" class="input-group">
		  	          <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
		  	          <input type="text" id="user-email" name="user-email" class="form-control" placeholder="이메일" value="'.$target_user_email.'" required>
	  	        	</div>
	  	        </div>
			</div>
		</div>

		

	</div>
	<div role="tabpanel" class="tab-pane fade" id="password">.qegqeg..</div>
	<div role="tabpanel" class="tab-pane fade" id="dropout">.qegqeg..</div>
</div>

<script type="text/javascript">
function finderPopup(){
	win = window.open('https://infra.yonsei.ac.kr/lauth/yscert/yscert_1.jsp',"yscertpopup","width=450,height=550,top=50,left=50,menubar=no,resizable=no,status=no,toolbar=no,location=no,scrolls=no,directories=no");   
	win.focus();
}
</script>

<?php include 'footer.php';?>