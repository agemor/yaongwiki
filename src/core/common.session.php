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

class YwSession {
    
    public $name;
    public $id;
    public $permission;
    public $ip;
    
    public $time;
    
    function __construct() {

        $this->name       = $_SESSION['name'];
        $this->id         = $_SESSION['id'];
        $this->permission = $_SESSION['permission'];
        $this->ip         = $_SERVER['REMOTE_ADDR'];
        
        if (!isset($_SESSION['pageview'])) {
            $_SESSION['pageview'] = array();
        }
    }
    
    public function start($name, $id, $permission) {
        $this->name       = $name;
        $this->id         = $id;
        $this->permission = $permission;
        $this->time       = time();
        
        $_SESSION['name']       = $name;
        $_SESSION['id']         = $id;
        $_SESSION['permission'] = $permission;
        $_SESSION['time']       = $time;
    }
    
    public function destroy() {
        session_destroy();
    }
    
    public function started() {
        return !empty($_SESSION['id']);
    }
    
    public function visit($article_id) {
        if (in_array($article_id, $_SESSION['pageview']))
            return false;
        array_push($_SESSION['pageview'], $article_id);
        return true;
    }
    
    public function setPermission($permission) {
        $this->permission       = $permission;
        $_SESSION['permission'] = $permission;
    }
}

$session = new YwSession();

?>