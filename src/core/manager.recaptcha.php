<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 12
 */

class ReCaptchaManager {

    private static $instance;

    private $remote_ip;
    
    private function __construct() {
        $this->remote_ip = $_SERVER["REMOTE_ADDR"];
    }

    public static function get_instance() {

		if (!isset(self::$instance)) { 
			self::$instance = new self();
        }
        
		return self::$instance;
	}

    public function verify($private_key, $response) {
        // response : $_POST["g-recaptcha-response"];
        $remote_ip = $this->remote_ip;
        
        $verification = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$private_key}&response={$response}&remoteip={$remote_ip}&");
            
        if ($verification == false) {
            return true;
        }
            
        $verification_data = json_decode($verification);
        return $verification_data->success;
    }
}