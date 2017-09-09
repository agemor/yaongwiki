<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 08
 */

require_once CORE_DIRECTORY . "/page.install.processor.php";

$page = process();

if (isset($page["redirect"]) && $page["redirect"] == true) {
    $redirect->redirect();  
}

$page["title"] = "YaongWiki Installation";

require_once __DIR__ . "/frame.header.php";
?>
<div class="container">
  <div class="title my-4">
    <h2>
    YaongWiki Installation
    <h2>
  </div>
  <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>
  <form action="/" method="post">
    <div class="row my-4">
      <div class="col-md-6">
        <p>Installing YaongWiki in the database. If it is already installed, it can not be overwritten. Please delete YaongWiki tables from the database and try again.</p>
        <p>Please fill out below form.</p>
        <div class="form-group">
          <label for="hostInput">DB Host</label>
          <input type="text" name="db-host" class="form-control" id="hostInput" placeholder="localhost:3306" value="<?php echo($post->retrieve('db-host'));?>" required>
        </div>
        <div class="form-group">
          <label for="userInput">DB User</label>
          <input type="text" name="db-user" class="form-control" id="userInput" placeholder="root" value="<?php echo($post->retrieve('db-user'));?>" required>
        </div>
        <div class="form-group">
          <label for="passwordInput">DB User Password</label>
          <input type="password" name="db-password" class="form-control" id="passwordInput" value="<?php echo($post->retrieve('db-password'));?>" required>
        </div>
        <div class="form-group">
          <label for="nameInput">DB Name</label>
          <input type="text" name="db-name" class="form-control" id="nameInput" placeholder="yaongwiki" value="<?php echo($post->retrieve('db-name'));?>" required>
        </div>
        <div class="form-group">
          <label for="prefixInput">YaongWiki Table Prefix</label>
          <input type="text" name="db-prefix" class="form-control" id="prefixInput" value="<?php echo($post->retrieve('db-prefix'));?>" placeholder="">
          <small id="prefixInputHelper" class="text-muted">This is optional parameter.</small>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="termsTextArea">Terms of Use</label>
          <textarea class="form-control" id="termsTextArea" rows="20" readonly>
          51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
          Everyone is permitted to copy and distribute verbatim copies
          of this license document, but changing it is not allowed.
          Preamble
          The licenses for most software are designed to take away your
          freedom to share and change it.  By contrast, the GNU General Public
          License is intended to guarantee your freedom to share and change free
          software--to make sure the software is free for all its users.  This
          General Public License applies to most of the Free Software
          Foundation's software and to any other program whose authors commit to
          using it.  (Some other Free Software Foundation software is covered by
          the GNU Lesser General Public License instead.)  You can apply it to
          your programs, too.
          When we speak of free software, we are referring to freedom, not
          price.  Our General Public Licenses are designed to make sure that you
          have the freedom to distribute copies of free software (and charge for
          this service if you wish), that you receive source code or can get it
          if you want it, that you can change the software or use pieces of it
          in new free programs; and that you know you can do these things.
          </textarea>
        </div>
        <div class="form-check">
          <label class="form-check-label">
          <input class="form-check-input" type="checkbox" value="" required> I agree the terms of use
          </label>
        </div>
        <button type="submit" class="btn btn-primary">Start Setup</button>
      </div>
    </div>
  </form>
</div>
<?php
require_once __DIR__ . "/frame.footer.php";
?>