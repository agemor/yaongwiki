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
require_once 'common.session.php';

const MAX_ARTICLES = 10;
const MAX_PAGINATION = 10;
const CONTENT_PREVIEW_LENGTH = 340;
const FULL_TEXT_SEARCH = false;

function main() {

    $http_query = trim(empty($_GET['q']) ? $_POST['q'] : $_GET['q']);
    $http_page  = intval(empty($_GET['p']) ? '0' : $_GET['p']);
    
    if (empty($http_query))
        return array(
            'result'=>false,
            'message'=>'검색어가 없습니다'
        );
    
    // 검색 모드 
    $tag_search_mode = (strlen($http_query) > 1) && (strcmp($http_query{0}, "@") == 0);
    if ($tag_search_mode)
        $http_query = substr($http_query, 1);
    
    // 쿼리 유효성 검증
    $http_query = preg_replace('/\s+/', ' ', $http_query);
    if (strlen($http_query) < 1)
        return array(
            'result'=>false,
            'message'=>'검색어가 없습니다'
        );
    
    // 검색 쿼리 취득
    $keywords = explode(' ', $http_query);
    $query    = $tag_search_mode ? getTagSearchQuery($keywords) : getContentSearchQuery($keywords);
    
    global $db_connect_info;
    $db = new YwDatabase($db_connect_info);
    
    if (!$db->connect())
        return array(
            'result'=>false,
            'message'=>'서버와의 연결에 실패했습니다'
        );
    
    // 정확히 제목이 일치하는 항목이 있으면 바로 이동
    if (count($keywords) == 1) {
        if (!$db->query("SELECT 1 FROM " . ARTICLE_TABLE . " WHERE `title`='" . $keywords[0] . "';"))
            return array(
                'result'=>false,
                'message'=>'검색 결과를 가져오는데 실패했습니다'.("SELECT 1 FROM " . ARTICLE_TABLE . " WHERE `title`='" . $keywords[0] . "';")
            );
        if ($db->total_results() > 0)
            navigateTo(HREF_READ . '/' . $keywords[0]);
    }
    
    $start_time = microtime(true);
    
    // 전체 검색 결과를 얻기 위해 먼저 서치
    if (!$db->query($query))
        return array(
            'result'=>false,
            'message'=>'검색 결과를 가져오는데 실패했습니다2'
        );
    
    $elapsed_time   = round(microtime(true) - $start_time, 5);
    $total_articles = $db->total_results();
    
    // 현재 페이지 결과 가져오기
    $query .= " LIMIT " . ($http_page * MAX_ARTICLES) . ", " . MAX_ARTICLES . ";";
    
    if (!$db->query($query))
        return array(
            'result'=>false,
            'message'=>'검색 결과를 가져오는데 실패했습니다3'
        );

    $search_result = array();
    while ($result = $db->get_result()) {
        array_push($search_result, $result);
    }
    
    return array(
        'result'=>true,
        'search_result'=>$search_result,
        'keywords'=>$keywords,
        'total_results'=>$total_articles,
        'elapsed_time'=>$elapsed_time
    );
}

function getTagSearchQuery($keywords, $fulltext = FULL_TEXT_SEARCH) {
    
    // FullText 검색
    if ($fulltext) {
        $match = "MATCH(`tags`) AGAINST('";
        for ($i = 0; $i < count($keywords); $i++) {
            if (strlen($keywords[$i]) > 0)
                $match .= ($i > 0 ? ' ' : '') . $keywords[$i];
        }
        $match .= "' IN BOOLEAN MODE)";
        
        $query = "SELECT *, ";
        $query .= $match . " AS relevance ";
        $query .= "FROM " . ARTICLE_TABLE . " ";
        $query .= "WHERE " . $match . " ";
        $query .= "ORDER BY (relevance * `hits`) DESC";
    }
    
    // 일반 검색
    else {
        
        $query = "SELECT * FROM " . ARTICLE_TABLE . " WHERE ";
        for ($i = 0; $i < count($keywords); $i++) {
            if (strlen($keywords[$i]) > 0) {
                $query .= ($i > 0 ? " OR " : "");
                $query .= "`tags` LIKE '%" . $keywords[$i] . "%'";
            }
        }
        $query .= " ORDER BY `hits` DESC";
    }
    
    return $query;
}

function getContentSearchQuery($keywords, $fulltext = FULL_TEXT_SEARCH) {
    
    // FullText 검색
    if ($fulltext) {
        
        $against = "AGAINST('";
        for ($i = 0; $i < count($keywords); $i++) {
            if (strlen($keywords[$i]) > 0) {
                $against .= ($i > 0 ? " " : "") . "*" . $keywords[$i] . "*";
            }
        }
        $against .= "' IN BOOLEAN MODE)";
        
        // 쿼리문 생성
        $query = "SELECT * ";
        $query .= "FROM " . ARTICLE_TABLE . " ";
        $query .= "WHERE MATCH(`title`, `content`) " . $against . " ";
        $query .= "ORDER BY `hits` DESC";
    }
    
    // 일반 검색
    else {
        
        $query = "SELECT * FROM " . ARTICLE_TABLE . " WHERE ";
        for ($i = 0; $i < count($keywords); $i++) {
            if (strlen($keywords[$i]) > 0) {
                $query .= ($i > 0 ? " OR " : "");
                $query .= "`title` LIKE" . "'%" . $keywords[$i] . "%' OR";
                $query .= "`content` LIKE" . "'%" . $keywords[$i] . "%' OR";
                $query .= "`tags` LIKE" . "'%" . $keywords[$i] . "%'";
            }
        }
        $query .= " ORDER BY `hits` DESC";
    }
    
    return $query;
}

function highlight($text, $keywords) {
    foreach ($keywords as $keyword) {
        $text = preg_replace("|($keyword)|Ui", "<mark>$1</mark>", $text);
    }
    return $text;
}

function truncate($text, $limit, $break = ".", $pad = "...") {
    if (strlen($text) <= $limit)
        return $text;
    if (false !== ($breakpoint = strpos($text, $break, $limit))) {
        if ($breakpoint < strlen($text) - 1) {
            $text = substr($text, 0, $breakpoint) . $pad;
        }
    }
    return $text;
}

function parseTags($tags) {
    $chunks = explode(' ', $tags);
    $tags   = "";
    for ($i = 0; $i < count($chunks); $i++) {
        if (strlen($chunks[$i]) > 0)
            $tags .= ($i > 0 ? '&nbsp;&nbsp;' : '') . '<a href="' . HREF_SEARCH . '?' . $chunks[$i] . '">#' . $chunks[$i] . '</a>';
    }
    return $tags;
}

$page_response = main();

if ($page_response['result']) {
    $search_query = implode(' ', $page_response['keywords']);
    $page_title    = $search_query . ' 에 대한 검색 결과';
    $page_location = HREF_SEARCH . '?' . $search_query;
} else {
    $page_title    = '검색 결과 없음';
    $page_location = HREF_SEARCH;
}

include 'frame.header.php';
?>

<div class="container">

  <?php
    if (!$page_response['result']) {
        echo '<div class="alert alert-dismissible alert-danger" role="alert">'.$page_response['message'];
        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        echo '</div>';
    } else {
        echo '<div class="well well-sm">';
        echo '<b>'.implode(' ', $page_response['keywords']).'</b>에 대해 '
             .$page_response['total_results'].'항목을 찾았습니다. ('. $page_response['elapsed_time'].'초)';
        echo '</div>';
    }?>
  <div class="row">
    <?php if ($page_response['result']) {
    foreach ($page_response['search_result'] as $result) {
        echo '<div class="col-md-12">';
        echo '<h4><a href="'.HREF_READ.'/'.$result["title"].'">'.$result["title"].'</a> <span class="badge">+'.$result["hits"].'</span></h4>';
        echo '<p>'.highlight(truncate(strip_tags($result["content"]), CONTENT_PREVIEW_LENGTH), $page_response['keywords']).'</p>';
        echo '<h5>'.parseTags($result["tags"]).'</h5><br/>';
        echo '</div>';
    }}?>
  </div>
  <?php if ($page_response['result']) {
    if ($page_response['total_results'] > MAX_ARTICLES) {

        $total_pages = ceil($page_response['total_results'] / MAX_ARTICLES);
        $firstPage = floor($page / MAX_PAGINATION) * MAX_PAGINATION;
        $lastPage = $firstPage + MAX_PAGINATION;
      
        echo '<div class="text-center"><ul class="pagination">';

        if ($firstPage > 0) {
            echo '<li><a href="'.$page_location.'&p='.($firstPage - 1).'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        }
        for ($i = $firstPage; $i < min($total_pages, $lastPage); $i++) {
            if ($i == $page) {
                echo '<li class="active"><a href="'.$page_location.'&p='.$i.'>'.($i + 1).'<span class="sr-only">(current)</span></a></li>';
            } else {
                echo '<li><a href="'.$page_location.'&p='.$i.'">'.($i + 1).'</a></li>';
            }
        }
        if ($lastPage < $total_pages) {
          echo '<li><a href="'.$page_location.'&p='.$lastPage.'" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
        }
        echo '</ul></div>';
    }}?>
  <div class="well well-sm">원하는 지식이 없다면, <a href="<?php echo HREF_CREATE;?>"><b>직접 지식을 추가</b></a>해 보세요</div>
</div>

<?php include 'frame.footer.php';?>