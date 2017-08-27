<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */

require_once 'common.php';?>

<h3>이메일 변경</h3>
<hr/>
<div class="col-md-6" style="min-height:300px;">
  <blockquote>
    <p>&nbsp;&nbsp;<a href="<?php echo HREF_READ;?>/이메일">이메일</a> 주소는 <a href="<?php echo HREF_READ;?>/비밀번호">비밀번호</a>를 잊었을 때 임시 <a href="<?php echo HREF_READ;?>/비밀번호">비밀번호</a>를 받기 위한 수단으로 사용됩니다. 메일을 받을 수 있는 사용 가능한 주소를 입력해 주세요.</p>
  </blockquote>
</div>
<div class="col-md-6">
  <div class="well">
    <?php 
      if (!$page_response['result'] && $page_focus == 2) {
          echo "<div class=\"alert alert-dismissible alert-danger\" role=\"alert\">".$page_response['message'];
          echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
          echo '</div>';
      } else if(!empty($page_response['message']) && $page_focus == 2) {
          echo "<div class=\"alert alert-dismissible alert-success\" role=\"alert\">".$page_response['message'];
          echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
          echo '</div>';
      }?>
    <form action="<?php echo HREF_DASHBOARD;?>" method="post">
      <div style="margin-bottom: 10px" class="input-group">
        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
        <input type="text" name="user-email" class="form-control" placeholder="이메일" value="<?php echo $page_response['user']['email'];?>" required>
      </div>
      <button class="btn btn-primary btn-block" type="submit">변경하기</button>
    </form>
  </div>
</div>