<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 04
 */

require_once __DIR__ . "/session.php";

class FormDataManager {
    
    public $values;
    public $form_name;

    private $session;

    public function __construct($form_name) {

        $session = Session::get_session();
 
        $this->values = $session->get($form_name);
        $this->form_name = $form_name;
        $this->session = $session;
    }

    public function set($values) {

        $this->values = $values;
        $this->session->set($this->form_name, $values);
    }

    public function clear() {

        $this->values = null;
        $this->session->set($this->form_name, null);
    }

    public function retrieve($key) {
        
        if (!isset($this->values))
            return null;
        
        if (!array_key_exists($key, $this->values)) {
            return null;
        }

        $value = $this->values[$key];

        if (empty($value)) {
            return null;
        }
        
        return trim(strip_tags($value));
    }
}

$post = new FormDataManager("post-values");
$get = new FormDataManager("get-values");