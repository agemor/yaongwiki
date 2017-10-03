<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 31
 */

require_once __DIR__ . "/session.php";

class UserManager {
    
    private static $instance;

    private $session;
    private $user_data;
    private $page_view;
    
    private function __construct() {

        $this->session = Session::get_instance();

        if ($this->session->get("page_view") == null) {
            $this->session->set("page_view", array());
        }

        // 로그인된 상태가 아니라면 익명 로그인
        if ($this->session->get("user_data") == null) {
            $this->anonymous_signin();
        }

        $this->user_data = $session->get("user_data");
        $this->page_view = $session->get("page_view");
    }

    public static function get_instance() {

		if(!isset(self::$instance)) { 
			self::$instance = new self();
        }
        
		return self::$instance;
    }

    /**
     * 익명 로그인
     */
    private function anonymous_signin() {
        
        $anonymous_user_data = array(
            "name" => $_SERVER["REMOTE_ADDR"],
            "id" => "-1",
            "permission" => "0",
            "time" => time()
        );

        $this->user_data = $anonymous_user_data;
        $this->session->set("user_data", $anonymous_user_data);
    }
    
    /**
     * 기명 로그인
     */
    public function signin($name, $id, $permission) {

        $user_data = array(
            "name" => $name,
            "id" => $id,
            "permission" => $permission,
            "time" => time()
        );

        $this->user_data = $user_data;
        $this->session->set("user_data", $user_data);
    }
    
    public function signout() {

        $this->session->set("user_data", null);
    }
    
    public function signined() {

        return $this->session->get("user_data") !== null;
    }

    /**
     * (실제로) 로그인하였는지의 여부
     */
    public function authorized() {

        if ($this->signined()) {
            if (intval($this->user_data["id"]) >= 0) {
                return true;
            }
        }
        return false;
    }

    public function get($key) {
        
        if ($this->signined() && array_key_exists($key, $this->user_data)) {
            return $this->user_data[$key];
        }
        return null;
    }
    
    public function visit($article_id) {

        if (in_array($article_id, $this->page_view)) {
            return false;
        }

        array_push($this->page_view, $article_id);
        $this->session->set("page_view", $this->page_view);

        return true;
    }
}