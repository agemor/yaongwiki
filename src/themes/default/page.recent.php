<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 19
 */

require_once YAONGWIKI_CORE . "/page.recent.processor.php";

$page = process();

if (isset($page["redirect"]) && $page["redirect"] == true) {
    $redirect->redirect();  
}

$page["title"] = "Recent Articles";

require_once __DIR__ . "/frame.header.php";
?>

<div class="container">
  <div class="title my-4">
    <h2>
    Recent Articles
    <small class="text-muted">(Latest 30)</small>
    </h2>
    </div>
    <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>
  <div style="padding: 20px"></div>
  <table class="table table-hover">
    <thead>
      <tr>
        <th class="text-center" style="width: 2%">#</th>
        <th class="text-center" style="width: 25%">Article</th>
        <th class="text-center" style="width: 15%">Editor</th>
        <th class="text-center" style="width: 10%">Fluctuation</th>
        <th class="text-center" style="width: 20%">Date</th>
      </tr>
    </thead>
    <tbody class="text-center">
      <?php
        if (count($page["recent"] ) < 1) {
            echo '<tr><td colspan="6">No record</td></tr>';
        } 
        $next_id = 0;
        foreach ($page["recent"]  as $result) {
            echo '<tr>';
            echo '<td><a href="./?revision&i=' . $result["id"] . '">' . $result["id"] . '</a></td>';
            echo '<td><a href="./?read&i=' . $result["article_id"] . '">' . $result["article_title"] . '</a></td>';
            echo '<td><a href="./?profile&name=' . $result["user_name"] . '"">' . $result["user_name"] . '</a>';
            if (strlen($result["comment"]) > 0) {
                echo '<br>(' . $result["comment"] . ')';
            }
            echo '</td>';
            $fluctuation = intval($result["fluctuation"]);    
            if($fluctuation > 0) {
                echo '<td><span style="color:green">+' . $fluctuation . '</span>';
            } else if ($fluctuation == 0) {
                echo '<td><span style="color:grey">-</span>';
            } else {
                echo '<td><span style="color:red">-' . abs($fluctuation) . '</span>';
            }
            echo '</td>';
            echo '<td>' . $result["timestamp"] . '</td>';
            echo '</tr>';
            $next_id = $result["id"];
        }?>
    </tbody>
  </table>
</div>
<?php
require_once __DIR__ . "/frame.footer.php";
?>