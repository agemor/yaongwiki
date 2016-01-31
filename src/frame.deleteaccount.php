<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */

require_once 'common.php';?>

<h3>계정 삭제</h3>
<hr/>
<div class="col-md-6" style="min-height:300px;">
  <blockquote>
    <p>&nbsp;&nbsp;<a href="<?php echo HREF_READ;?>/계정">계정</a>을 삭제해도 <a href="<?php echo HREF_READ;?>/기여">기여</a>한 <a href="<?php echo HREF_READ;?>/지식">지식</a>들은 삭제되지 않습니다. 부적절한 내용에 대해서는 <code>중재자</code>에게 신고해 주세요.</p>
    <p>&nbsp;&nbsp;대신 <a href="<?php echo HREF_READ;?>/기여">기여</a> 기록은 삭제되며 복구할 수 없습니다.</p>
  </blockquote>
</div>
<div class="col-md-6">
  <div class="well">
    <?php 
      if (!$page_response['result'] && $page_focus == 4) {
          echo "<div class=\"alert alert-dismissible alert-danger\" role=\"alert\">".$page_response['message'];
          echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
          echo '</div>';
      } else if(!empty($page_response['message']) && $page_focus == 4) {
          echo "<div class=\"alert alert-dismissible alert-success\" role=\"alert\">".$page_response['message'];
          echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
          echo '</div>';
      }?>
    <form action="<?php echo HREF_DASHBOARD;?>" method="post">
      <div style="margin-bottom: 10px" class="input-group">
        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
        <input type="password" name="user-drop-password" class="form-control" placeholder="비밀번호" required>
      </div>
      <button class="btn btn-primary btn-block" type="submit">삭제하기</button>
    </form>
  </div>
</div>