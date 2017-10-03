<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 04
 */

require_once __DIR__ . "/session.php";

class HttpVarsManager {
    
    private static $instance;

    private $vars;
    private $session;

    private function __construct() {

        $this->session = Session::get_instance();
        $this->values = $this->session->get("http-vars");
        
    }

    public static function get_instance() {

		if(!isset(self::$instance)) { 
			self::$instance = new self();
        }
        
		return self::$instance;
	}

    public function set($get_vars, $post_vars) {

        $this->clear();

        foreach($get_vars as $key => $value) {
            $this->vars[$key] = $value;
        }

        foreach($post_vars as $key => $value) {
            $this->vars[$key] = $value;
        }

        $this->session->set("http-vars", $this->vars);
    }

    public function get($key) {
        
        if (!isset($this->vars))
            return null;
        
        if (!array_key_exists($key, $this->vars)) {
            return null;
        }

        $value = $this->vars[$key];

        if (empty($value)) {
            return null;
        }
        
        return trim(strip_tags($value));
    }

    public function clear() {

        $this->vars = array();
        $this->session->set("http-vars", $this->vars);
    }
}