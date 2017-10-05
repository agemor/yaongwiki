<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 10. 03
 */

if (isset($page["redirect"]) && $page["redirect"] == true) {
    $redirect->redirect();  
}

$page["title"] = "Out of Service";
?>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title><?php echo($page["title"]);?></title>

    <link href=".<?php echo(YAONGWIKI_THEME_DIR);?>/css/bootstrap.min.css" rel="stylesheet">
    <link href=".<?php echo(YAONGWIKI_THEME_DIR);?>/css/default.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
      <div class="title my-4">
        <h2>
        Out of Service
        <h2>
      </div>
      <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
      <div class="alert alert-danger" role="alert">
        <?php echo($page["message"]);?>
      </div>
      <?php } ?>
        <div class="row my-4">
          <div class="col-md-6">
            <p>
              The server currently out of service.
              It might have internal error or administrator is making some changes.
              Please contact system administrator for more information.
            </p>
          </div>
        </div>
    </div>
  </body>
</html>