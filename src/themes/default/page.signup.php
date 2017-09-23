<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 09
 */

require_once YAONGWIKI_CORE . "/page.signup.processor.php";

$page = process();

if (isset($page["redirect"]) && $page["redirect"] == true) {
    $redirect->redirect();  
}

$page["title"] = "YaongWiki Sign Up";

require_once __DIR__ . "/frame.header.php";
?>
<div class="container">
  <div class="title my-4">
    <h2>
    Sign Up
    <h2>
  </div>
  <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>
  <?php
  if (isset($page["message"]) && $page["message"] == "success") { ?>

  <p>Sign up successful! You can now <a href="./?signin">sign in</a>.</p>
    
  <?php } else { ?>
  <form action="/?signup" method="post">
    <div class="row my-4">
      <div class="col-md-6">
        <p>Please fill out below form.</p>
        <div class="form-group">
          <label for="nameInput">Name</label>
          <input type="text" name="user-name" class="form-control" id="nameInput" value="<?php echo($post->retrieve('user-name'));?>" required>
          <small id="nameInputHelper" class="text-muted">At least three letters long</small>
        </div>
        <div class="form-group">
          <label for="emailInput">Email Address</label>
          <input type="email" name="user-email" class="form-control" id="emailInput" value="<?php echo($post->retrieve('user-email'));?>" required>
        </div>
        <div class="form-group">
          <label for="passwordInput">Password</label>
          <input type="password" name="user-password" class="form-control" id="passwordInput" value="<?php echo($post->retrieve('user-password'));?>" required>
          <small id="nameInputHelper" class="text-muted">At least four letters long</small>
        </div>
        <div class="form-group">
          <label for="passwordReInput">Confirm Password</label>
          <input type="password" name="user-password-re" class="form-control" id="passwordReInput" value="<?php echo($post->retrieve('user-password-re'));?>" required>
        </div>
        <label for="recaptchInput">Recaptcha</label>
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_PUBLIC_KEY;?>"></div><br/>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="termsTextArea">Terms of Use</label>
          <textarea class="form-control" id="termsTextArea" rows="20" readonly>Terms for YaongWiki.
          </textarea>
        </div>
        <div class="form-check">
          <label class="form-check-label">
          <input class="form-check-input" type="checkbox" value="" required> I agree the terms of use
          </label>
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-primary">Sign up</button>
          <a href="./?signin" class="btn btn-default">Go back</a>
        </div>
      </div>
    </div>
  </form>
  <?php } ?>
</div>
<?php
require_once __DIR__ . "/frame.footer.php";
?>