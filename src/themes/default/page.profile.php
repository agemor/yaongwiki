<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 20
 */

require_once YAONGWIKI_CORE . "/page.profile.processor.php";

$page = process();

if (isset($page["redirect"]) && $page["redirect"] == true) {
    $redirect->redirect();  
}

$page["title"] = "Profile of " . $page["user"]["name"];

require_once __DIR__ . "/frame.header.php";
?>

<div class="container">
  <div class="title my-4">
    <h2>
    <?php echo($page["user"]["name"]);?>
    <small class="text-muted"> (Level: <em><?php echo($page["user"]["permission"]);?>)</em></small>
    <h2>
  </div>
  <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>

  <h5 class="mt-5">Profile</h5>
  <span>Joined: <em><?php echo($page["user"]["timestamp"]);?></em></span><br/>
  <form id="intro">
    <span id="introText" class="mr-2"><?php echo($page["user"]["info"]);?></span>
    <button type="button" class="btn btn-default btn-sm my-2" id="introButton">Edit</button>
  </form>

  <h5 class="mt-5">Contribution History</h5>
  <table class="table table-hover">
      <thead>
        <tr>
          <th class="text-center" style="width: 10%">#</th>
          <th class="text-center" style="width: 45%">Article</th>
          <th class="text-center" style="width: 15%">Comment</th>
          <th class="text-center" style="width: 15%">Fluctuation</th>
          <th class="text-center" style="width: 25%">Date</th>
        </tr>
      </thead>

      <tbody class="text-center">
      <?php
        if (count($page["user"]["contributions"]) < 1) {
            echo '<tr><td colspan="6">No record</td></tr>';
        } 
        $next_id = 0;
        foreach ($page["user"]["contributions"]  as $result) {
            echo '<tr>';
            echo '<td><a href="./?revision&i=' . $result["id"] . '">' . $result["id"] . '</a></td>';
            echo '<td><a href="./?read&i=' . $result["article_id"] . '">' . $result["article_title"] . '</a></td>';
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
        }?>
    </tbody>
  </table>

  <nav>
    <ul class="pagination">
      <?php
        $total_pages = ceil((intval($page["user"]["total_contributions"])) / (float) MAX_REVISIONS);
        if ($total_pages > 1) {
            for ($i = 0; $i < $total_pages; $i++) {
                $li_class = "page-item" . (intval($page["page"]) == $i ? " active" : "");
                $li_href = "./?profile&name=" . $page["user"]["name"] . "&p=" . $i;
                $li_text = $i + 1;
                echo('<li class="' . $li_class . '"><a class="page-link" href="' . $li_href . '">' . $li_text . '</a></li>');
            }
        }
        ?>
    </ul>
  </nav>

</div>
<script>

window.onload = function() {

    var intro = document.getElementById("intro");
    var introText = document.getElementById("introText");
    var introButton = document.getElementById("introButton");
    var introCancelButton = introButton.cloneNode(true);
    var introInput = document.createElement("input");

    var originalText = introText.textContent;
    var editMode = false;

    introInput.type = "text";
    introInput.classList.add("form-control");
    introCancelButton.textContent = "Cancel";
    introCancelButton.classList.add("mx-2");

    introButton.onclick = introCancelButton.onclick =  function(event) {
        editMode = !editMode;
        if (editMode) {
            introInput.value = originalText;
            intro.removeChild(introText);
            intro.insertBefore(introInput, introButton);
            intro.appendChild(introCancelButton, introButton);
            introButton.textContent = "Save";
        } else {
            if (event.target == introCancelButton) {
                intro.removeChild(introInput);
                intro.removeChild(introCancelButton);
                intro.insertBefore(introText, introButton);
                introButton.textContent = "Edit";
            } else {
                post("./?profile&name=<?php echo($page["user"]["name"]);?>", {
                    "user-name": "<?php echo($page["user"]["name"]);?>",
                    "user-info": introInput.value
                });
            }
        }
    }
};

function post(path, params, method) {
    method = method || "post"; 

    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
         }
    }

    document.body.appendChild(form);
    form.submit();
}
</script>

<?php
require_once __DIR__ . "/frame.footer.php";
?>