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
    Dashboard
    <h2>
  </div>
  <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>

  <!-- Nav tabs -->
<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" data-toggle="tab" href="#home" role="tab">Account status</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#profile" role="tab">Change password</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#messages" role="tab">Change email</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Delete account</a>
  </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
  <div class="tab-pane active" id="home" role="tabpanel">
  
<div class="row">

<div class="col-md-6" style="min-height:300px;">
  <h4>내 정보</h4>
  <table class="table">
    <thead>
      <tr>
        <th><span class="glyphicon glyphicon-user" aria-hidden="true"></span> 아이디</th>
        <th><a href="#"><?php echo '<a href="'.HREF_PROFILE.'/'.$page['user']['name'].'">'.$page['user']['name'].'</a>';?></a></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> 이메일</td>
        <td><?php echo $page['user']['email'];?></td>
      </tr>
      <tr>
        <td><span class="glyphicon glyphicon-star" aria-hidden="true"></span> 등급</td>
        <td>
          <?php
            $info = (intval($page['user']['permission']));
            echo '<a href="./pages/'.$info['description'].'">'.$info['description'].'</a>';?>
        </td>
      </tr>
      <tr>
        <td><span class="glyphicon glyphicon-time" aria-hidden="true"></span> 생성일</td>
        <td><?php echo $page['user']['timestamp'];?></td>
      </tr>
    </tbody>
  </table>
  <br/>
</div>
<div class="col-md-6">
  <h4 style="margin-top: 16px;">최근 3일간 <a href="<?php echo HREF_READ;?>/로그인">로그인</a> 기록</h4>
  <table class="table table-hover table-condensed">
    <thead>
      <tr>
        <th class="text-center" style="width: 50%">시간</th>
        <th class="text-center" style="width: 30%">IP 주소</th>
        <th class="text-center" style="width: 20%">결과</th>
      </tr>
    </thead>
    <tbody class="text-center">
      <?php
        foreach ($page['user']['login_history'] as $result) {
            echo '<tr>';
            echo '<td>'.$result["timestamp"].'</td>';
            echo '<td>'.$result["ip"].'</td>';
            echo '<td>'.(($result["data"] == "0") ? "실패" : "성공").'</td>';
            echo '</tr>';
        }?>    
    </tbody>
  </table>
</div>
</div>






  
  
  </div>
  <div class="tab-pane" id="profile" role="tabpanel">...</div>
  <div class="tab-pane" id="messages" role="tabpanel">...</div>
  <div class="tab-pane" id="settings" role="tabpanel">...</div>
</div>
  
</div>


<?php
require_once __DIR__ . "/frame.footer.php";
?>