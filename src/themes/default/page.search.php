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

$page["title"] = "Search Results for " . implode($page["keywords"], " ");

const CONTENT_PREVIEW_LENGTH =100;


require_once __DIR__ . "/frame.header.php";
?>

<div class="container">
  <div class="title my-4">
    <h2>
    Search Results for <em><?php echo(implode($page["keywords"], " "));?></em>
    </h2>
    </div>
    <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
    <div class="alert alert-danger" role="alert">
      <?php echo($page["message"]);?>
    </div>
    <?php } ?>

  <div class="alert alert-light" role="alert">
    Found <?php echo(count($page["search_result"]));?> results. (<?php echo($page["elapsed_time"]);?>sec)
  </div>


  <div class="card-columns">
  <?php foreach ($page['search_result'] as $result) { ?>

    <div class="card mb-3" >
  <div class="card-body">
    <h4 class="card-title"><a href="./?read&t=<?php echo($result["title"]);?>"><?php echo($result["title"]);?></a></h4>
    <p class="card-text"><?php echo(highlight(truncate(strip_tags($result["content"]), CONTENT_PREVIEW_LENGTH), $page['keywords']));?></p>
  </div>
  <div class="card-footer">
      <small class="text-muted"><?php echo(parse_tags($result["tags"]));?></small>
    </div>
</div>




 <?php }?>

 </div>

</div>


<div class="container">

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

<?php

function parse_tags($tags) {
  $chunks = explode(' ', $tags);
  $tags   = "";
  for ($i = 0; $i < count($chunks); $i++) {
      if (mb_strlen($chunks[$i]) > 0)
          $tags .= ($i > 0 ? '&nbsp;&nbsp;' : '') . '<a href="./?search&q=@' . $chunks[$i] . '">#' . $chunks[$i] . '</a>';
  }
  return $tags;
}


require_once __DIR__ . "/frame.footer.php";
?>