<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 04
 */

require_once "session.php";

class RedirectManager {

    public $redirect_url;
    private $session;
    
    public function __construct() {

        $session = Session::get_session();

        $this->redirect_url = $session.get("redirect_url");

        $this->session = $session;
    }

    function set($url) {
        $this->redirect_url = $redirect_url;
        $this->session->set("redirect_url", $redirect_url);
    }

    function redirect() {

        header("Location: " . $redirect_url);

        $redirect_url = null;
        $this->session->set("redirect_url", null);

        exit();
    }
}

$redirect = new RedirectManager();