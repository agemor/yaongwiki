<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 24
 */

require_once YAONGWIKI_CORE . "/page.dashboard.processor.php";

$page = process();

if (isset($page["redirect"])) {
    redirect($page["redirect"]);
    exit();
}

$page["title"] = "Dashboard";

require_once __DIR__ . "/frame.header.php";
?>
<div class="container">
  <div class="title my-4">
    <h2>
    Dashboard
    <h2>
  </div>
  <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>

  <!-- Nav tabs -->
<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" data-toggle="tab" href="#viewAccountPanel" role="tab">Account status</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#modifyAccountPanel" role="tab">Modify account</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#deleteAccountPanel" role="tab">Delete account</a>
  </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
  <div class="tab-pane active" id="viewAccountPanel" role="tabpanel">
  <div class="row mt-3">
    <div class="col-md-5">
      <div class="card mt-3">
        <div class="card-header">
          Account info
        </div>
        <div class="card-body">
          <dl class="row" style="margin-bottom: 0px">
            <dt class="col-sm-3 text-truncate">Name</dt>
            <dd class="col-sm-9"><a href="./?profile&name=<?php echo($page["user"]["name"]);?>"><?php echo($page["user"]["name"]);?></a></dd>
            <dt class="col-sm-3 text-truncate">Email</dt>
            <dd class="col-sm-9"><?php echo($page["user"]["email"]);?></dd>
            <dt class="col-sm-3 text-truncate">Registered Date</dt>
            <dd class="col-sm-9"><?php echo($page["user"]["timestamp"]);?></dd>
            <dt class="col-sm-3 text-truncate">Total Contributions</dt>
            <dd class="col-sm-9"><?php echo($page["user"]["total_contributions"]);?></dd>
            <dt class="col-sm-3 text-truncate">Permission Level</dt>
            <dd class="col-sm-9"><?php echo($page["user"]["permission"]);?></dd>
          </dl>
        </div>
      </div>
    </div>

    <div class="col-md-7">
      <div class="card mt-3">
        <div class="card-header">
          Recent activities
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-condensed borderless" style="margin-bottom: 0px; margin-top: -10px">
              <thead>
                <tr>
                  <th>Time</th>
                  <th>IP</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($page["user"]["logs"] as $result) { ?>
                <tr>
                  <td><?php echo($result["timestamp"]);?></td>
                  <td><?php echo(empty($result["ip"]) ? "127.0.0.1" : $result["ip"]);?></td>
                  <td><?php echo(($result["data"] == "0") ? "실패" : "성공");?></td>
                </tr>
                <?php }?>    
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>

  <div class="tab-pane" id="modifyAccountPanel" role="tabpanel">
  <div class="row mt-3">
    <div class="col-md-6">
      <div class="card mt-3">
        <div class="card-header">
          Change email
        </div>
        <div class="card-body">
        <form action="./?dashboard" method="post">
          <div class="form-group">
            <label for="newEmailInput">Email address</label>
            <input type="email" name="user-email" class="form-control" id="newEmailInput" aria-describedby="newEmailInputHelp" placeholder="New email address" value="<?php echo $page["user"]["email"];?>" required>
            <small id="newEmailInputHelp" class="form-text text-muted">Enter vaild new email address</small>
          </div>
          <button type="submit" class="btn btn-primary">Change email</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card mt-3">
      <div class="card-header">
        Change password
      </div>
      <div class="card-body">
        <form action="./?dashboard" method="post">
          <div class="form-group">
            <label for="newPasswordInput">New password</label>
            <input type="password" name="user-new-password" class="form-control" id="newPasswordInput" aria-describedby="newPasswordInputHelp" placeholder="New password">
            <small id="newPasswordInputHelp" class="form-text text-muted">At least three letters long</small>
          </div>
          <div class="form-group">
            <label for="newPasswordCheckInput">Check new password</label>
            <input type="password" name="user-new-password-re" class="form-control" id="newPasswordCheckInput" placeholder="Type agian new password">
          </div>
          <button type="submit" class="btn btn-primary">Change password</button>
        </form>
        </div>
      </div>
    </div>
  </div>
  </div>
  
  <div class="tab-pane" id="deleteAccountPanel" role="tabpanel">
  <div class="row mt-3">
    <div class="col-md-6">
      <div class="card mt-3">
        <div class="card-header">
          Delete account
        </div>
        <div class="card-body">
          <p>Deleting </p>
          <form action="./?dashboard" method="post">
            <div class="form-group">
              <label for="dropPasswordInput">Password</label>
              <input type="password" name="user-drop-password" class="form-control" id="dropPasswordInput" aria-describedby="dropPasswordInputHelp" placeholder="Current password">
            </div>
            <button type="submit" class="btn btn-primary">Delete account</button>
          </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</div>


<?php
require_once __DIR__ . "/frame.footer.php";
?>