<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */

require_once 'common.php';?>

<h3>비밀번호 변경</h3>
<hr/>
<div class="col-md-6" style="min-height:300px;">
  <blockquote>
    <p>&nbsp;&nbsp;<a href="<?php echo HREF_READ;?>/비밀번호">비밀번호</a>는 타인에게 노출되지 않도록 관리에 신경 써 주세요. 보안을 위해 3개월에 한 번씩 변경하는 것을 추천합니다.</p>
  </blockquote>
</div>
<div class="col-md-6">
  <div class="well">
    <?php 
      if (!$page_response['result'] && $page_focus == 3) {
          echo "<div class=\"alert alert-dismissible alert-danger\" role=\"alert\">".$page_response['message'];
          echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
          echo '</div>';
      } else if(!empty($page_response['message']) && $page_focus == 3) {
          echo "<div class=\"alert alert-dismissible alert-success\" role=\"alert\">".$page_response['message'];
          echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
          echo '</div>';
      }?>
    <form action="<?php echo HREF_DASHBOARD;?>" method="post">
      <div style="margin-bottom: 20px" class="input-group">
        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
        <input type="password" name="user-password" class="form-control" placeholder="현재 비밀번호" required>
      </div>
      <label>비밀번호는 4자 이상으로 입력해 주세요.</label> 
      <div style="margin-bottom: -1px" class="input-group">
        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
        <input type="password" name="user-new-password" class="form-control" placeholder="새 비밀번호" required>
      </div>
      <div style="margin-bottom: 20px" class="input-group">
        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
        <input type="password" name="user-new-password-re" class="form-control" placeholder="새 비밀번호 재입력" required>
      </div>
      <button class="btn btn-primary btn-block" type="submit">변경하기</button>
    </form>
  </div>
</div>