<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 09
 */

require_once YAONGWIKI_CORE . "/page.signin.processor.php";

$page = process();
$settings = SettingsManager::get_instance();
$http_vars = HttpVarsManager::get_instance();

if (isset($page["redirect"])) {
    redirect($page["redirect"]);
    exit();
}

$page["title"] = "Sign in" . " - " . $settings.get("site_title");

require_once __DIR__ . "/frame.header.php";
?>
<div class="container">
  <div class="title my-4">
    <h2>
    Sign In
    <h2>
  </div>
  <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>
	<form action="./?signin&redirect=<?php echo($http_vars->get("redirect"));?>" method="post">
		<div class="row my-4">
			<div class="col-md-6">
				<p>Please enter your account name and password.</p>
				<div class="form-group">
					<label for="nameInput">User Name</label>
					<input type="text" name="user-name" class="form-control" id="nameInput" value="<?php echo($http_vars->get("user-name"));?>" required>
				</div>
				<div class="form-group">
					<label for="passwordInput">User Password</label>
					<input type="password" name="user-password" class="form-control" id="passwordInput" required>
				</div>
				<button type="submit" class="btn btn-primary">Sign in</button>
				<a href="./" class="btn btn-default">Go back</a>
			</div>
			<div class="col-md-6">
				<p>If you do not have an account, <a href="/?signup">Create an account</a>.</p>
				<p>Forgot password? <a href="/?reset">Reset your password</a>.</p>
			</div>
		</div>
	</form>
</div>
<?php
require_once __DIR__ . "/frame.footer.php";
?>