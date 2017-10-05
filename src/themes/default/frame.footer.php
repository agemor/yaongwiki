<?php
  $site_description = SettingsManager::get_instance()->get("site_description");
  if ($site_description == null) {
    $site_description = "YaongWiki";
  }
?>
    <div style="padding: 15px"></div>
    <footer class="footer">
      <div class="container">
        <p class="text-muted my-3"><?php echo($site_description);?></p></div>
      </div>
    </footer>
    <script src=".<?php echo(YAONGWIKI_DIR);?>/themes/default/js/jquery-3.2.1.min.js"></script>
    <script src=".<?php echo(YAONGWIKI_DIR);?>/themes/default/js/popper.min.js"></script>
    <script src=".<?php echo(YAONGWIKI_DIR);?>/themes/default/js/bootstrap.min.js"></script>
  </body>
</html>