<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 10. 03
 */

require_once YAONGWIKI_CORE . "/page.search.processor.php";

$page = process();

if (isset($page["redirect"]) && $page["redirect"] == true) {
    $redirect->redirect();  
}

$page["title"] = "Search results for " . implode($page["keywords"], " ");

require_once __DIR__ . "/frame.header.php";
?>

<div class="container">

  <?php
    if (!$page['result']) {
        echo '<div class="alert alert-dismissible alert-danger" role="alert">'.$page['message'];
        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        echo '</div>';
    } else {
        echo '<div class="well well-sm">';
        echo '<b>'.implode(' ', $page['keywords']).'</b>에 대해 '
             .$page['total_results'].'항목을 찾았습니다. ('. $page['elapsed_time'].'초)';
        echo '</div>';
    }?>
  <div class="row">
    <?php if ($page['result']) {
    foreach ($page['search_result'] as $result) {
        echo '<div class="col-md-12">';
        echo '<h4><a href="'.HREF_READ.'/'.$result["title"].'">'.$result["title"].'</a> <span class="badge">+'.$result["hits"].'</span></h4>';
        echo '<p>'.highlight(truncate(strip_tags($result["content"]), CONTENT_PREVIEW_LENGTH), $page['keywords']).'</p>';
        echo '<h5>'.parseTags($result["tags"]).'</h5><br/>';
        echo '</div>';
    }}?>
  </div>
  <?php if ($page['result']) {
    if ($page['total_results'] > MAX_ARTICLES) {

        $total_pages = 0;//ceil($page['total_results'] / MAX_ARTICLES);
        $firstPage = 0;//floor($page / MAX_PAGINATION) * MAX_PAGINATION;
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