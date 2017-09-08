<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 08
 */

require_once CORE_DIRECTORY . "/page.create.processor.php";

$page = process();

$page["title"] = "Create Article";

require_once __DIR__ . "/frame.header.php";
?>

<div class="container">
  <div class="title my-4">
    <h2>
    Create Article
    <h2>
  </div>
  <?php if ($page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>
  <form action="/" method="post">
    <div class="row my-4">
      <div class="col-md-6">
        <p>Installing YaongWiki in the database. If it is already installed, it can not be overwritten. Please delete YaongWiki tables from the database and try again.</p>
        
      </div>
      <div class="col-md-6">
      </div>
    </div>
  </form>
</div>
<?php
require_once __DIR__ . "/frame.footer.php";
?>






<div class="container">
  <h2><a href="#" style="text-decoration: none;">새 지식 만들기</a></h2><br>
  <hr/>
  <div class="row">
    <div class="col-md-6">
      <blockquote>
        <p>새 지식을 만들기 전 체크리스트</p>
        <br/>
        <h5>1. 비슷하거나 동일한 지식이 있진 않은지 </h5>
        <h5>2. 들어갈 수 있는 내용이 충분한 지식인지</h5>
        <h5>3. 누군가를 불쾌하게 만들 내용은 아닌지</h5>
        <br/>
        <footer>날씨가 점점 추워지네... <cite> - 진리관C를 청소하시는 아주머니</cite></footer>
      </blockquote>
    </div>
    <div class="col-md-6">
      <form class="form-signin" style="width:auto; margin:auto;" action="<?php echo $page_location;?>" method="post">
        <div class="well">
          <?php
            if (!$page_response['result']) {
                echo '<div class="alert alert-dismissible alert-danger" role="alert">'.$page_response['message'];
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
            }?>
          <div style="margin-bottom: 20px" class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-pencil"></i></span>
            <input type="text" name="article-title" class="form-control" placeholder="지식 제목" value="<?php echo $_GET['t'];?>" required autofocus>
          </div>
          <button class="btn btn-primary btn-block" type="submit">만들기</button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>
<?php include 'frame.footer.php';?>