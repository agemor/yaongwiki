<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 04
 */

require_once __DIR__ . "/session.php";

class RedirectManager {

    public $redirect_url;
    private $session;
    
    public function __construct() {

        $session = Session::get_instance();

        $this->redirect_url = $session->get("redirect_url");

        $this->session = $session;
    }

    public function set($url) {
        $this->redirect_url = $url;
        $this->session->set("redirect_url", $url);
    }

    public function redirect() {

        if ($this->redirect_url == null) {
            return;
        }

        header("Location: " . $this->redirect_url);

        $this->redirect_url = null;
        $this->session->set("redirect_url", null);

        exit();
    }
}

$redirect = new RedirectManager();