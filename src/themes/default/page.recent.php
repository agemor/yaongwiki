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

const MAX_RECENT_CHANGED = 30;

function main() {

	global $session;
    global $db_connect_info;

     $db = new YwDatabase($db_connect_info);
    
    // 데이터베이스 연결을 체크한다.
    if (!$db->connect())
        return array(
            'result'=>false,
            'message'=>'서버와의 연결에 실패했습니다'
        );
    
    if (!$db->query("SELECT * FROM " . REVISION_TABLE . " WHERE `id` IN (SELECT MAX(`id`) FROM " . REVISION_TABLE . " GROUP BY `article_id`) ORDER BY `id` DESC LIMIT " . MAX_RECENT_CHANGED . ";"))
        return array(
            'result'=>false,
            'message'=>'서버와의 연결에 실패했습니다'
        );
    
    if ($db->total_results() < 1)
        return array(
            'result'=>false,
            'message'=>'서버와의 연결에 실패했습니다'
        );
    
    $result_array = array();
    
    while ($result = $db->get_result()) {
        array_push($result_array, $result);
    }
    
    return array(
            'result'=>true,
            'recent'=>$result_array,
            'message'=>''
        );
}

$page_response = main();
$page_title = "최근 변경된 지식";
$page_location = HREF_RECENT;

include 'frame.header.php';?>

<div class="container">
  <h1><a style="text-decoration: none;" href="#">최근 변경된 지식</a></h1>
  <br/>
  <hr/>
  <blockquote>
    <p>최근 업데이트된 30개의 지식 목록입니다.</p>
    <footer>다들 열심히 사는군...  <cite> - 중도에서 공부하는 C군</cite></footer>
  </blockquote>

  <table class="table table-hover">
    <thead>
      <tr>
        <th class="text-center" style="width: 2%">#</th>
        <th class="text-center" style="width: 25%">지식</th>
        <th class="text-center" style="width: 15%">편집자</th>
        <th class="text-center" style="width: 10%">변동</th>
        <th class="text-center" style="width: 10%">비교</th>
        <th class="text-center" style="width: 20%">편집 시간</th>
      </tr>
    </thead>
    <tbody class="text-center">
      <?php
        if (count($page_response['recent']) < 1) {
            echo '<tr><td></td><td></td><td><em>아직 기여 기록이 없습니다.</em></td><td></td><td></td><td></td></tr>';
        } 
        
        $next_id = 0; // 무설정시 원본글과 대조

        foreach ($page_response['recent']  as $result) {
            echo '<tr>';
            echo '<td id="c"><a href="'.HREF_REVISION.'?i='.$result["id"].'&j='.$next_id.'">'.$result["id"].'</a></td>';
            echo '<td id="c"><a href="'.HREF_READ.'/'.$result["article_id"].'">'.$result["article_title"].'</a></td>';
            


            if (strlen($result["comment"]) > 0) {
                echo '<td id="c"><a href="'.HREF_PROFILE.'/'.$result["user_name"].'"">'.$result["user_name"].'</a><br>('.$result["comment"].')</td>';
            } else {
                echo '<td id="c"><a href="'.HREF_PROFILE.'/'.$result["user_name"].'"">'.$result["user_name"].'</a></td>';
            }
        
            $fluctuation = intval($result["fluctuation"]);    
            if($fluctuation > 0) {
                echo '<td id="c"><span style="color:green"><span class="glyphicon glyphicon-arrow-up" aria-hidden="true" ></span> '.$fluctuation.'</span>';
            } else if ($fluctuation == 0) {
                echo '<td id="c"><span style="color:grey"><span class="glyphicon glyphicon-minus" aria-hidden="true" ></span> 0</span>';
            } else {
                echo '<td id="c"><span style="color:red"><span class="glyphicon glyphicon-arrow-down" aria-hidden="true" ></span> '.abs($fluctuation).'</span>';
            }
            echo '</td>';
            echo '<td id="c"> <a href="'.HREF_REVISION.'?i='.$result["id"].'&j='.$next_id.'" class="btn btn-xs btn-default" type="button">비교</a>';
           
            echo '</td>';
            echo '<td id="c">'.$result["timestamp"].'</td>';
            echo '</tr>';

            $next_id = $result["id"];
        }?>
    </tbody>
  </table>


</div>

<?php include 'frame.footer.php';?>