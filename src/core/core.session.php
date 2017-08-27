<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */
 
error_reporting(E_ERROR | E_WARNING | E_PARSE);

session_start();

class User {
    
    public $name;
    public $id;
    public $permission;
    public $ip;
    
    public $time;
    
    function __construct() {

        $this->name = $_SESSION['name'];
        $this->id = $_SESSION['id'];
        $this->permission = $_SESSION['permission'];
        $this->ip = $_SERVER['REMOTE_ADDR'];
        
        if (!isset($_SESSION['pageview'])) {
            $_SESSION['pageview'] = array();
        }
    }
    
    public function signin($name, $id, $permission) {

        $this->name = $name;
        $this->id = $id;
        $this->permission = $permission;
        $this->time = time();
        
        $_SESSION['name'] = $name;
        $_SESSION['id'] = $id;
        $_SESSION['permission'] = $permission;
        $_SESSION['time'] = $time;
    }
    
    public function signout() {

        session_destroy();
    }
    
    public function signined() {

        return !empty($_SESSION['id']);
    }
    
    public function visit($article_id) {

        if (in_array($article_id, $_SESSION['pageview']))
            return false;

        array_push($_SESSION['pageview'], $article_id);

        return true;
    }
    
    public function set_permission($permission) {

        $this->permission       = $permission;
        $_SESSION['permission'] = $permission;
    }
    
}

class PostManager {
    
    public $values;
    
    function __construct() {

        $this->values = $_SESSION["post_values"];
    }

    public function set($values) {

        $this->values = $values;
        $_SESSION["post_values"] = $values;
    }

    public function clear() {

        $this->values = null;
        $_SESSION["post_values"] = null;
    }

    public function retrieve($key) {
        
        if (!isset($this->values))
            return null;
        
        if (!array_key_exists($key, $this->values)) {
            return null;
        }

        $value = $this->values[$key];
        
        return trim(strip_tags(empty($value ? null : $value)));
    }
}

class RedirectManager {

    public $redirect_url;
    
    function __construct() {

        $this->redirect_url = $_SESSION["redirect_url"];
    }

    function set($url) {

        $this->redirect_url = $redirect_url;
        $_SESSION["redirect_url"] = $redirect_url;
    }

    function redirect() {

        header("Location: " . $redirect_url);

        $redirect_url = null;
        $_SESSION["redirect_url"] = null;

        exit();
    }
}

$user = new User();
$post = new PostManager();
$redirect = new RedirectManager();

?>