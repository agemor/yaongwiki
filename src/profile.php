<?php
require 'db.php';
require 'session.php';

$PERMISSION_LEVEL = array(
	array("독자", "info"), 
	array("편집자", "warning"), 
	array("중재자", "primary"), 
	array("관리자", "danger")
);

$MAX_REVISIONS = 30;

$target_user_name = stripslashes(trim(!empty($_GET["name"]) ? $_GET["name"] : $_POST["user-name"]));

if (empty($target_user_name)) {
	if ($loggedin) {
		$target_user_name = $user_name;
	} else {
		header("Location: 404.php");
		exit();
	}
}

$error = false;
$error_message = "";

// 데이터베이스 연결
$db = new mysqli($db_server, $db_user, $db_password, $db_name);
if ($db->connect_errno) {
  exit($db->connect_error);
} 

// 유저 정보 읽어오기
$sqlQuery = "SELECT * FROM `$db_users_table` WHERE ";
$sqlQuery .= "`name`='".$db->real_escape_string($target_user_name)."' LIMIT 1;";
$result = $db->query($sqlQuery);

store_log($db, $loggedin ? $user_name : $user_ip, "유저 정보 열람", $target_user_name);

if ($result->num_rows < 1) {
  header('Location: 404.php');
  exit();
}

$row = $result->fetch_assoc();
$target_user_id = $row["id"];
$target_user_permission = intval($row["permission"]);
$target_user_info = $row["info"];
$target_user_timestamp = $row["timestamp"];

if ($result->num_rows < 1) {
  	header('Location: 404.php');
  	exit();
}

// 자기소개 업데이트 (변경되었으면)
if (isset($_POST["user-name"]) && $target_user_id == $user_id) {

	// XSS 방지
	$target_user_info = htmlentities($_POST["user-info"]);

	if(!empty($target_user_info)){

		$sqlQuery = "UPDATE `$db_users_table` SET ";
		$sqlQuery .= "`info`='".$db->real_escape_string($target_user_info)."' ";
		$sqlQuery .= "WHERE `id`=".$user_id.";";
		store_log($db, $user_name, "자기소개 수정", $sqlQuery);

		if ($db->query($sqlQuery) === false) {
			$error = true;
			$error_message = "서버 등록에 실패했습니다.";
		}
	} 
}

// 기여 목록 가져오기
if (isset($_GET["p"])) {
	$revisions_page = $_GET["p"];
} else {
	$revisions_page = 0;
}

$sqlQuery = "SELECT * FROM `$db_revisions_table` WHERE ";
$sqlQuery .= "`user_name`='".$db->real_escape_string($target_user_name)."' ";
$sqlQuery .= "ORDER BY `timestamp` DESC ";
$sqlQuery .= "LIMIT ".($revisions_page * $MAX_REVISIONS).",".$MAX_REVISIONS.";";
$result = $db->query($sqlQuery);



$page_title = "유저 정보 보기 - 야옹위키";
$page_location = "profile.php?name=".$target_user_name;
include 'header.php';?>

<div class="container">
<h1>
<?php
echo $target_user_name;
echo '  <a role="button" class="btn btn-xs btn-'.$PERMISSION_LEVEL[$target_user_permission][1].'">'.$PERMISSION_LEVEL[$target_user_permission][0].'</a>';?>
</h1>
<hr/>
<div class="row">
    <div class="col-md-12">
    	<blockquote>
	        <p><?php
	        if (empty($target_user_info)) {
	        	echo "<em>자기소개 정보가 없습니다.</em>";
	        } else {
	        	echo $target_user_info;
	    	}
	    	if ($target_user_id == $user_id) {
	    		echo ' <button style="margin-bottom: 4px" class="btn btn-xs btn-default" type="button" data-toggle="collapse" data-target="#edit" aria-expanded="false" aria-controls="edit">
	    		<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> 수정
	    		</button>
	    		<div class="collapse" id="edit"><div class="well well-sm">
	    			<form action="profile.php" method="post">
	    				<input type="hidden" name="user-name" value="'.$target_user_name.'">
		    			<textarea style="margin-bottom: 8px" class="form-control" rows="4" name="user-info" maxlength="1000">'.$target_user_info.'</textarea>
		    			<div class="text-center"><p>
						  <button type="submit" class="btn btn-primary btn-sm">저장</button>
						  <button type="button" class="btn btn-default btn-sm" data-toggle="collapse" data-target="#edit">취소</button>
						</p></div>
					</form>
	    		</div></div>';
	    	}?>
	    	</p>
	        <footer>계정 생성일 <cite title="Source Title"><?php echo $target_user_timestamp;?></cite></footer>
      	</blockquote>

<table class="table">
	<thead>
	    <tr >
	    	<th class="text-center" style="width: 10%">#</th>
	    	<th class="text-center" style="width: 45%">항목</th>
	        <th style="width: 10%">변동</th>
	        <th class="text-center" style="width: 15%">비교</th>
	        <th class="text-center" style="width: 25%">편집 시간</th>
	    </tr>
	</thead>
	<tbody>
	<?php

	if ($result = $db->query($sqlQuery)) {

		$result_count = $result->num_rows;

		while ($row = $result->fetch_assoc()) {
			echo '<tr>';
			echo '<td class="text-center"><a href="revision.php?i='.$row["id"].'">'.$row["id"].'</a></td>';

			if (strlen($row["comment"]) > 0) {
				echo '<td class="text-center"><a href="read.php?i='.$row["article_id"].'"">'.$row["article_title"].'</a> ('.$row["comment"].')</td>';
			} else {
				echo '<td class="text-center"><a href="read.php?i='.$row["article_id"].'"">'.$row["article_title"].'</a></td>';
			}

			$fluctuation = intval($row["fluctuation"]);
			if($fluctuation > 0) {
				echo '<td><span style="color:green"><span class="glyphicon glyphicon-arrow-up" aria-hidden="true" ></span> '.$fluctuation.'</span>';
			} else if ($fluctuation == 0) {
				echo '<td><span style="color:grey"><span class="glyphicon glyphicon-minus" aria-hidden="true" ></span> 0</span>';
			} else {
				echo '<td><span style="color:red"><span class="glyphicon glyphicon-arrow-down" aria-hidden="true" ></span> '.abs($fluctuation).'</span>';
			}

			echo '</td>';
			echo '<td class="text-center">
			<a href="revisions.php?i='.$row["article_id"].'" class="btn btn-xs btn-default" type="button">역사</a> 
			<a href="revision.php?i='.$row["id"].'&compare=1" class="btn btn-xs btn-default" type="button">비교</a></td>';
			echo '<td class="text-center">'.$row["timestamp"].'</td>';
			echo '</tr>';
	      }

	      $result->free();
	  }

	$db->close();?>
	</tbody>
</table>

<nav>
  <ul class="pager">

<?php
if($revisions_page > 0) {
	echo '<li class="previous"><a href="profile.php?name='.$target_user_name.'&p='.($revisions_page - 1).'"><span aria-hidden="true">&larr;</span> 새 기여 보기</a></li>';
}
if ($result_count >= $MAX_REVISIONS) {
	echo '<li class="next"><a href="profile.php?name='.$target_user_name.'&p='.($revisions_page + 1).'">이전 기여 보기 <span aria-hidden="true">&rarr;</span></a></li>';
}
?>
  </ul>
</nav>
</div>
<br/><br/>
       
</div>
<?php include 'footer.php';?>