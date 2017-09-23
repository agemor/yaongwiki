<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 19
 */

require_once YAONGWIKI_CORE . "/page.revision-history.processor.php";

$page = process();

if (isset($page["redirect"]) && $page["redirect"] == true) {
    $redirect->redirect();  
}

$page["title"] = "Revision History: " . $page["article"]["title"];

require_once __DIR__ . "/frame.header.php";
?>

<div class="container">
  <div class="title my-4">
    <h2>
    Revision History <em>(<?php echo($page["article"]["title"]); ?>)</em>
    <h2>
  </div>
  <div class="text-right mb-3">
      <div class="btn-group" role="group">
        <a class="btn btn-default" href="./?read&i=<?php echo($page['article']['id']);?>" >Read article</a>
      </div>
    </div>
  <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>

  <div style="padding: 10px"></div>
  <table class="table table-hover">
    <thead>
      <tr>
        <th class="text-center" style="width: 2%">#</th>
        <th class="text-center" style="width: 15%">Editor</th>
        <th class="text-center" style="width: 25%">Comment</th>
        <th class="text-center" style="width: 10%">Fluctuation</th>
        <th class="text-center" style="width: 20%">Date</th>
      </tr>
    </thead>
    <tbody class="text-center">
      <?php
        if (!isset($page["history"]) || count($page["history"]) < 1) {
            echo '<tr><td colspan="6">No record</td></tr>';
        } else {
            foreach ($page["history"]  as $result) {
                echo '<tr>';
                echo '<td><a href="./?revision&i=' . $result["id"] . '">' . $result["id"] . '</a></td>';
                echo '<td><a href="./?profile&name=' . $result["user_name"] . '"">' . $result["user_name"] . '</a></td>';
                echo '<td>' . $result["comment"] . '</td>';
                
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
            }
        }?>
    </tbody>
  </table>
  <nav>
    <ul class="pagination">
      <?php
        for ($i = 0; $i < intval($page["article"]["revisions"]) / MAX_REVISIONS; $i++) {
            $li_class = "page-item" . (intval($page["page"]) == $i ? " active" : "");
            $li_href = "./?revision-history&i=" . $page["article"]["id"] . "&p=" . $i;
            $li_text = $i + 1;
            echo('<li class="' . $li_class . '"><a class="page-link" href="' . $li_href . '">' . $li_text . '</a></li>');
        }
        ?>
    </ul>
  </nav>
  
</div>
<?php
require_once __DIR__ . "/frame.footer.php";
?>