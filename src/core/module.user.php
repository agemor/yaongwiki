<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 31
 */

require_once "session.php";

class User {
    
    public $name;
    public $id;
    public $permission;
    public $pageview;

    public $time;
    public $ip;

    private $session;
    
    public function __construct() {

        $session = Session::get_session();

        if ($session.get("pageview") == null) {
            $session.set("pageview", array());
        }

        $this->name = $session.get("name");
        $this->id = $session.get("id");
        $this->permission = $session.get("permission");
        $this->pageview = $session.get("pageview");

        $this->ip = $_SERVER["REMOTE_ADDR"];
        $this->session = $session;
    }
    
    public function signin($name, $id, $permission) {

        $this->name = $name;
        $this->id = $id;
        $this->permission = $permission;
        $this->time = time();
        
        $this->$session.set("name", $name);
        $this->$session.set("id", $id);
        $this->$session.set("permission", $permission);
        $this->$session.set("time", $time);
    }
    
    public function signout() {

        $this->$session.set("name", null);
        $this->$session.set("id", null);
        $this->$session.set("permission", null);
        $this->$session.set("time", null);
        $this->$session.set("pageview", null);
    }
    
    public function signined() {

        return $this->$session.get("id") !== null;
    }
    
    public function visit($article_id) {

        if (in_array($article_id, $this->pageview)) {
            return false;
        }

        array_push($this->pageview, $article_id);
        $this->$session.set("pageview", $this->pageview);

        return true;
    }
    
    public function set_permission($permission) {

        $this->permission = $permission;
        $this->$session.set("permission", $permission);
    }
    
}

$user = new User();