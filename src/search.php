<?php
require 'db.php';
require 'session.php';

$MAX_ARTICLES = 30;
$MAX_PAGINATION = 10;
$PREVIEW_CONTENT = 340;

$query = strtolower(trim(!empty($_GET['q']) ? $_GET['q'] : $_POST['q']));
$page = intval(!empty($_GET['p']) ? $_GET['p'] : (!empty($_POST['p']) ? $_POST['p'] : 0));

$error = false;
$error_message;

$tagSearch = false;

// 첫 문자가 '#'라면 태그 검색을 활성화
if (strlen($query) > 1) {
    if (strcmp($query{0}, "@") == 0)
        $tagSearch = true;
}
// 태그검색 (맨앞문자가 #)
if ($tagSearch) {

    // '# 기준으로 스플릿
    $tagKeywords = explode('@', preg_replace('/\s+/', '', substr($query, 1)));

    $sqlQuery = "SELECT * FROM `$db_articles_table` WHERE ";
    for ($i = 0; $i < count($tagKeywords); $i++) {
        if (strlen($tagKeywords[$i]) > 0) {
            $sqlQuery .= ($i > 0 ? " OR " : "");
            $sqlQuery .= "`tags` LIKE '%".$tagKeywords[$i] . "%'";
        }
    }
    $sqlQuery .= " ORDER BY `hits` DESC";

    // ft_min_word_len 제약때문에 호스팅에서는 사용이 힘들다.
    /*
    $match = "MATCH(`tags`) AGAINST('";
    for ($i = 0; $i < count($tagKeywords); $i++) {
        if (strlen($tagKeywords[$i]) > 0) {
            $match .= ($i > 0 ? " " : "") . $tagKeywords[$i];
        }
    }
    $match .= "' IN BOOLEAN MODE)";
    // 쿼리문 생성

    $sqlQuery = "SELECT *, ";
    $sqlQuery .= $match." AS relevance ";
    $sqlQuery .= "FROM `$db_articles_table` ";
    $sqlQuery .= "WHERE ".$match." ";
    $sqlQuery .= "ORDER BY (relevance * `hits`) DESC";
    */
}

// 일반검색
else if (strlen($query) > 0) {

    // 공백 하나로 만들어서 키워드별로 분류
    $query = preg_replace('/\s+/', ' ', $query);
    $keywords = explode(' ', $query);


    // 비효율적인 검색 쿼리... 그러나 쓰자.
    $sqlQuery = "SELECT * FROM `$db_articles_table` WHERE ";
    for ($i = 0; $i < count($keywords); $i++) {
        if (strlen($keywords[$i]) > 0) {
            $sqlQuery .= ($i > 0 ? " OR " : "");
            $sqlQuery .= "`title` LIKE" . "'%".$keywords[$i] . "%' OR";
            $sqlQuery .= "`content` LIKE" . "'%".$keywords[$i] . "%' OR";
            $sqlQuery .= "`tags` LIKE" . "'%".$keywords[$i] . "%'";
        }
    }
    $sqlQuery .= " ORDER BY `hits` DESC";


    // ft_min_word_len 제약때문에 호스팅에서는 사용이 힘들다.
    /*
    $against = "AGAINST('";
    for ($i = 0; $i < count($keywords); $i++) {
        if (strlen($keywords[$i]) > 0) {
            $against .= ($i > 0 ? " " : "") . "*".$keywords[$i] . "*";
        }
    }
    $against .= "' IN BOOLEAN MODE)";
    
    // 쿼리문 생성
    $sqlQuery = "SELECT * ";
    $sqlQuery .= "FROM `$db_articles_table` ";
    $sqlQuery .= "WHERE MATCH(`title`, `summary`, `content`, `tags`) ".$against." ";
    $sqlQuery .= "ORDER BY `hits` DESC";
    */
    // 가중치가 있는 쿼리. 느려서 쓰지 말자.
    /*$sqlQuery = "SELECT *, ";
    $sqlQuery .= "MATCH(`title`) ".$against." AS level1, ";
    $sqlQuery .= "MATCH(`summary`, `tags`) ".$against." AS level2, ";
    $sqlQuery .= "MATCH(`content`) ".$against." AS level3 ";
    $sqlQuery .= "FROM `$db_articles_table` ";
    $sqlQuery .= "WHERE MATCH(`title`, `summary`, `content`, `tags`) ".$against." ";
    $sqlQuery .= "ORDER BY ((level1 * 5) + (level2 * 3) + (level3) + (`hits` / 100)) DESC";*/
}

// 검색어 없음
else {
    $error = true;
    $error_message = "검색어가 없습니다.";
}

if(!$error) {

    // 서버 연결
    $db = new mysqli($db_server, $db_user, $db_password, $db_name);

    if ($db->connect_errno) {
        $error = true;
        $error_message = "서버 연결에 실패하였습니다. (".$db->connect_error.")";
    }
    store_log($db, $loggedin ? $user_name : $user_ip, "검색", $query);
}


$page_title = $query." - 검색 결과";
$page_location = "search.php?q=".$query;

include 'header.php';

echo "<div class=\"container\">";

function highlight($text, $words) {
    $split_words = explode( " " , $words );
    foreach($split_words as $word) {
        $text = preg_replace("|($word)|Ui", "<mark>$1</mark>" , $text );
    }
    return $text;
}

function truncate($string, $limit, $break=".", $pad="...") {
  if(strlen($string) <= $limit) return $string;
  if(false !== ($breakpoint = strpos($string, $break, $limit))) {
    if($breakpoint < strlen($string) - 1) {
      $string = substr($string, 0, $breakpoint) . $pad;
    }
  }
  return $string;
}

function splitTags($text) {
  $words = explode(' ', $text);
  $text = "";
  for($i = 0; $i < count($words); $i++) {
    if (strlen($words[$i]) > 0 ) {
      $text .= ($i > 0 ? "&nbsp;&nbsp;" : "")."<a href=\"search.php?q=".$words[$i]."\">#".$words[$i]."</a>";
    }
  }
  return $text;
}

function purify($text) {
  return preg_replace("/[#\&\+\-@=\/\\\:;\'\"\^`~\_|\*$#<>\[\]\{\}]/i", "", $text);
}

$start_time = microtime(true);

if (!$error) {
  if ($result = $db->query($sqlQuery)) {

    $total_articles = $result->num_rows;
    $result->free();

    $sqlQuery .= " LIMIT ".($page * $MAX_ARTICLES).", ".$MAX_ARTICLES;

    if ($result = $db->query($sqlQuery)) {
      echo "<div class=\"well well-sm\"><b>".$query."</b>에 대해 ".$total_articles."항목을 찾았습니다. (".(round(microtime(true) - $start_time, 5))."초)</div>";
      echo "<div class=\"row\">\n";

      while ($row = $result->fetch_assoc()) {
        echo "<div class=\"col-md-12\">";
        echo "<h4><a href=\"read.php?t=".$row["title"]."\">".$row["title"]."</a> <span class=\"badge\">+".$row["hits"]."</span></h4>";
        echo "<p>".highlight(purify(truncate($row["content"], $PREVIEW_CONTENT)), $query)."</p>";
        echo "<h5>".splitTags($row["tags"])."</h5><br/>";
        echo "</div>";
      }

      $result->free();

    } else {
      $error = true;
      $error_message = "쿼리 실행에 실패하였습니다.";
    }
    echo "</div>";
    echo "<div class=\"well well-sm\">원하는 지식이 없다면, <a href=\"create.php\"><b>직접 지식을 추가</b></a>해 보세요</div>";

     // 페이지 번호 표시하기
    if ($total_articles > $MAX_ARTICLES) {
      $total_pages = $total_articles % $MAX_ARTICLES;
      $firstPage = floor($total_articles / ($MAX_PAGINATION + 1)) * $MAX_PAGINATION;
      $lastPage = $firstPage + $MAX_PAGINATION;
      echo "<div class=\"text-center\">\n<ul class=\"pagination\">\n";

      if ($firstPage > 0) {
        echo "<li><a href=\"search.php?q=".$query."&p=".($firstPage - 1)."\" aria-label=\"Previous\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
      }
      for ($i = $firstPage; $i < min($total_pages, $lastPage); $i++) {
        if ($i == $p) {
          echo "<li class=\"active\"><a href=\"search.php?q=".$query."&p=".$i."\">".($i + 1)."<span class=\"sr-only\">(current)</span></a></li>";
        } else {
          echo "<li><a href=\"search.php?q=".$query."&p=".$i."\">".($i + 1)."</a></li>";
        }
      }
      if ($lastPage < $total_pages) {
        echo "<li><a href=\"search.php?q=".$query."&p=".$lastPage."\" aria-label=\"Next\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
      }
      echo "</ul>\n</div>\n";
    }
      

  } else {
    $error = true;
    $error_message = "쿼리 실행에 실패하였습니다.".$sqlQuery;
  }
}

if($error) {
  echo "<div class=\"alert alert-danger\" role=\"alert\">".$error_message."</div>";
}

include 'footer.php';
?>