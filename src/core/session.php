<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 04
 */
 
class Session {

    private static $instance;

    private $session_started = false;
    private $session_state = 0;
        
    private function __construct() {
    }
   
    public static function get_session() {

        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        
        self::$instance->start_session();
        
        return self::$instance;
    }
    
    public function start_session() {

        if ($this->session_started == false) {
            $this->session_started = true;
            $this->session_state = session_start();
        }
        
        return $this->session_state;
    }
    
    
    public function set($name, $value) {
        $_SESSION[$name] = $value;
    }
    
    public function get($name) {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return null;
    }
    
    public function __isset($name) {
        return isset($_SESSION[$name]);
    }
    
    
    public function __unset($name) {
        unset($_SESSION[$name]);
    }
    
    public function destroy() {

        if ($this->session_started == true) {
            $this->session_started = false;
            $this->session_state = !session_destroy();
            unset($_SESSION);
            
            return true;
        }
        
        return false;
    }
}