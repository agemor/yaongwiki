<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 12
 */

require_once "core.recaptcha.account.php";

class ReCaptcha {

    public $remote_ip;
    
    public function __construct() {
        $this->remote_ip = $_SERVER["REMOTE_ADDR"];
    }

    public function verify($response) {
        // response : $_POST["g-recaptcha-response"];
        $secret = RECAPTCHA_PRIVATE_KEY;
        $remote_ip = $this->remote_ip;
        
        $verification = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}&remoteip={$remote_ip}&");
            
        if ($verification == false) {
            return true;
        }
            
        $verification_data = json_decode($verification);
        return $verification_data->success;
    }
}

$recaptcha = new ReCaptcha();