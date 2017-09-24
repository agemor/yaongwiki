<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 23
 */

require_once YAONGWIKI_CORE . "/page.revision.processor.php";

$page = process();

if (isset($page["redirect"]) && $page["redirect"] == true) {
    $redirect->redirect();  
}

$page["title"] = $page["revision"]["article_title"] . " (Revision " .$page["revision"]["revision"]. ")";

require_once __DIR__ . "/frame.header.php";
?>
<div class="container">
  <div class="title my-4">
    <h2>
    <?php echo($page["revision"]["article_title"]);?>
    <small class="text-muted"> (<em>Revision <?php echo($page["revision"]["revision"]);?>)</em></small>
    <h2>
  </div>
  <p><?php echo($page["revision"]["comment"]);?></p>
  <div class="text-right mb-3">
      <div class="btn-group" role="group">
        <a class="btn btn-default" href="./?read&i=<?php echo($page['revision']['article_id']);?>" >Read article</a>
        <a class="btn btn-default" href="./?revision&i=<?php echo($page['revision']['id']);?>&rollback=true">Rollback to this version</a>
        <a class="btn btn-default" href="./?revision-history&i=<?php echo($page['revision']['article_id']);?>">Revision History</a>
      </div>
    </div>
  <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>

  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a id="originalViewButton" class="tab-button nav-link" href="#" onclick="switchView(event, 'originalView')">Original view</a>
    </li>
    <li class="nav-item">
      <a id="diffViewButton" class="tab-button nav-link active" href="#" onclick="switchView(event, 'diffView')">Diff view</a>
    </li>
  </ul>

  <div id="diffViewContent" class="tab-content">
    <pre><code id="diffViewContentDisplay"></code></pre>
  </div>
  <div id="originalViewContent" class="tab-content mt-5">
    <ol class="breadcrumb">
    <?php 
    if (count($page["article"]["tags"]) == 0) { ?>
      <li class="breadcrumb-item">No tags</li>
    <?php }
    for ($i = 0; $i < count($page["article"]["tags"]); $i++) { ?>
      <li class="breadcrumb-item"><a href="./?search&q=@<?php echo($page["article"]["tags"][$i]);?>"><?php echo($page["article"]["tags"][$i]);?></a></li>
    <?php } ?>
    </ol>
    <div class="text-content my-4">
    <?php echo($page["article"]["content"]);?><br/>
    </div>
  </div>

  <div id="revContentText" style="display:none"><?php
      echo("Tags: ");
      echo($page["revision"]["snapshot_tags"]);
      echo("\n");
      echo("Content: ");
      echo("\n");
      echo($page["revision"]["snapshot_content"]);?>
  </div>
  <div id="revCompContentText" style="display:none"><?php
      echo("Tags: ");
      echo($page["comparison_target"]["snapshot_tags"]);
      echo("\n");
      echo("Content: ");
      echo("\n");
      echo($page["comparison_target"]["snapshot_content"]);?>
  </div>
</div>

<script src=".<?php echo(YAONGWIKI_DIR);?>/themes/default/js/diff.js"></script>
<script>

window.onload = function() {

    var diffViewButton = document.getElementById("diffViewButton");
    var originalViewButton = document.getElementById("originalViewButton");
    var diffViewContent = document.getElementById("diffViewContent");
    var originalViewContent = document.getElementById("originalViewContent");
    var diffViewContentDisplay = document.getElementById("diffViewContentDisplay");
    var revContentText = document.getElementById("revContentText");
    var revCompContentText = document.getElementById("revCompContentText");
    
    var diffCalculated = false;

    diffViewButton.onclick = function(event) {
        if (!diffCalculated) {
            diffViewContentDisplay.appendChild(getDiffElement(revCompContentText.textContent, revContentText.textContent));
            displayLineNumbers();
            diffCalculated = true;
        }
        switchView(event, "diffViewContent");
    }
    originalViewButton.onclick = function(event) { switchView(event, "originalViewContent");}

    switchView(null, "originalViewContent");
}

function getDiffElement(text1, text2) {

    var diff = JsDiff.diffChars(text1, text2);
    var fragment = document.createDocumentFragment();

    diff.forEach(function(part){
        var color = part.added ? 'green' : part.removed ? 'red' : 'grey';
        var span = document.createElement(part.removed ? 'del' : 'span' );
        span.style.color = color;
        span.appendChild(document.createTextNode(part.value));
        fragment.appendChild(span);
    });

    return fragment;    
}

function displayLineNumbers() {

    var pre = document.getElementsByTagName('pre');
    var pl = pre.length;

    for (var i = 0; i < pl; i++) {

        pre[i].innerHTML = '<span class="line-number"></span>' + pre[i].innerHTML + '<span class="cl"></span>';
        var num = pre[i].innerHTML.split(/\n/).length;

        for (var j = 0; j < num; j++) {

            var line_num = pre[i].getElementsByTagName('span')[0];
            line_num.innerHTML += '<span>' + (j + 1) + '</span>';
        }
    }
}

function switchView(event, viewName) {
    var i, tabcontent, tablinks;

    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    tablinks = document.getElementsByClassName("tab-button");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    document.getElementById(viewName).style.display = "block";
    if (event != null) {
      evt.currentTarget.className += " active";
    }
}

</script>

<?php 
require_once __DIR__ . "/frame.footer.php";
?>