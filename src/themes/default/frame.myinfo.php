<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1 
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */

require_once 'common.php';?>

<h3>계정 정보</h3>
<hr/>
<div class="col-md-6" style="min-height:300px;">
  <h4>내 정보</h4>
  <table class="table">
    <thead>
      <tr>
        <th><span class="glyphicon glyphicon-user" aria-hidden="true"></span> 아이디</th>
        <th><a href="#"><?php echo '<a href="'.HREF_PROFILE.'/'.$page_response['user']['name'].'">'.$page_response['user']['name'].'</a>';?></a></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> 이메일</td>
        <td><?php echo $page_response['user']['email'];?></td>
      </tr>
      <tr>
        <td><span class="glyphicon glyphicon-star" aria-hidden="true"></span> 등급</td> 
        <td>
          <?php
            $info = permissionInfo(intval($page_response['user']['permission']));
            echo '<a href="./pages/'.$info['description'].'">'.$info['description'].'</a>';?>
        </td>
      </tr>
      <tr>
        <td><span class="glyphicon glyphicon-certificate" aria-hidden="true"></span> 재학생 인증</td>
        <td>
          <?php
            if(!empty($page_response['user']['code'])) {
                echo '<abbr title="인증키: '.hash("sha256", $page_response['user']['code']).'">인증됨</abbr>';
            } else {
                echo '인증되지 않음';
            }?>
        </td>
      </tr>
      <tr>
        <td><span class="glyphicon glyphicon-time" aria-hidden="true"></span> 생성일</td>
        <td><?php echo $page_response['user']['timestamp'];?></td>
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
        foreach ($page_response['user']['login_history'] as $result) {
            echo '<tr>';
            echo '<td>'.$result["timestamp"].'</td>';
            echo '<td>'.$result["ip"].'</td>';
            echo '<td>'.(($result["data"] == "0") ? "실패" : "성공").'</td>';
            echo '</tr>';
        }?>    
    </tbody>
  </table>
</div>