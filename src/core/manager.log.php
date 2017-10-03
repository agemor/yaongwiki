<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 10. 03
 */

require_once __DIR__ . "/db.php";

class LogManager {

    private static $instance;

    private $db;
    
    private function __construct() {

        $this->db = Database::get_instance();
    }

    public static function get_instance() {

		if (!isset(self::$instance)) { 
			self::$instance = new self();
        }
        
		return self::$instance;
	}

    public function create($name, $behavior, $data) {
        
        if (!$this->db->connect()) {
            return false;
        }

        $response = $db->in(DB_LOG_TABLE)
                       ->insert("user_name", $name)
                       ->insert("ip", $_SERVER["REMOTE_ADDR"])
                       ->insert("behavior", $behavior)
                       ->insert("data", $data)
                       ->go();

        return $response;
    }
}