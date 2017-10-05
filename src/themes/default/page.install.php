<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 08
 */

require_once YAONGWIKI_CORE_DIR . "/page.install.processor.php";

$page = process();
$http_vars = HttpVarsManager::get_instance();

if (isset($page["redirect"])) {
    redirect($page["redirect"]);
    exit();
}

$page["title"] = "Installation - YaongWiki";
?>

<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title><?php echo($page["title"]);?></title>

  <link href=".<?php echo(YAONGWIKI_THEME_INCLUDE_DIR);?>/css/bootstrap.min.css" rel="stylesheet">
  <link href=".<?php echo(YAONGWIKI_THEME_INCLUDE_DIR);?>/css/default.css" rel="stylesheet">
</head>
<body>
<div class="container">

  <div class="title my-4">
    <h2>Installation</h2>
  </div>

  <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>

  <?php if (isset($page["message"]) && $page["message"] == "success") { ?>
  <p>Installation was successful.</p>
  <p>Now please register admin account from <a href="./?signup">here</a>,
  or <a href="./">go main page</a> to take a look.</p>
  
  <?php } else { ?>
  <form action="/" method="post">
    <div class="row my-4">
      <div class="col-md-6">
        <p>Installing YaongWiki in the database. If it is already installed, it can not be overwritten. Please delete YaongWiki tables from the database and try again.</p>
        <p>Please fill out below form.</p>
        <div class="form-group">
          <label for="hostInput">DB Host</label>
          <input type="text" name="db-host" class="form-control" id="hostInput" placeholder="localhost:3306" value="<?php echo($http_vars->get("db-host"));?>" required>
        </div>
        <div class="form-group">
          <label for="userInput">DB User</label>
          <input type="text" name="db-user" class="form-control" id="userInput" placeholder="root" value="<?php echo($http_vars->get("db-user"));?>" required>
        </div>
        <div class="form-group">
          <label for="passwordInput">DB User Password</label>
          <input type="password" name="db-password" class="form-control" id="passwordInput" value="<?php echo($http_vars->get("db-password"));?>" required>
        </div>
        <div class="form-group">
          <label for="nameInput">DB Name</label>
          <input type="text" name="db-name" class="form-control" id="nameInput" placeholder="yaongwiki" value="<?php echo($http_vars->get("db-name"));?>" required>
        </div>
        <div class="form-group">
          <label for="prefixInput">YaongWiki Table Prefix</label>
          <input type="text" name="db-prefix" class="form-control" id="prefixInput" value="<?php echo($http_vars->get("db-prefix"));?>" placeholder="">
          <small id="prefixInputHelper" class="text-muted">This is optional parameter.</small>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="termsTextArea">Terms of Use</label>
          <textarea class="form-control" id="termsTextArea" rows="20" readonly>
          <?php echo(file_get_contents(YAONGWIKI_CORE_DIR . "/license.txt")); ?>
          </textarea>
        </div>
        <div class="form-check">
          <label class="form-check-label">
          <input class="form-check-input" type="checkbox" value="" required> I agree the terms of use
          </label>
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-primary">Start Setup</button>
        </div>
      </div>
    </div>
  </form>
  <?php } ?>
</div>
</body>
</html>