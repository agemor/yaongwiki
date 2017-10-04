<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 10. 03
 */

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/session.php";

class SettingsManager {

    private static $instance;

    private $db;
    private $session;
    private $settings_data;

    public $available = false;
    
    private function __construct() {

        $this->db = Database::get_instance();
        $this->session = Session::get_instance();

        if ($this->session->get("settings") == null) {
            $this->load();
        } else {
            $this->settings_data = $this->session->get("settings");
            $this->available = true;
        }
    }

    public static function get_instance() {

		if (!isset(self::$instance)) { 
			self::$instance = new self();
        }
        
		return self::$instance;
	}

    public function load() {
        
        if (!$this->db->connect()) {
            $this->available = false;
            return;
        }

        $response = $this->db->in(DB_SETTINGS_TABLE)
                             ->select("*")
                             ->go_and_get_all();

        if (!$response) {
            $this->available = false;
            return;
        }

        $settings_data = array();

        foreach ($response as $data) {
            $settings_data[$data["name"]] = $data["value"];
        }

        $this->session->set("settings", $settings_data);
        $this->settings_data = $settings_data;
        $this->available = true;
    }

    public function get($name) {
        if (array_key_exists($name, $this->settings_data)) {
            return $this->settings_data[$name];
        } else {
            return null;
        }        
    }

    public function settings_count() {
        return count($this->settings_data);
    }
}