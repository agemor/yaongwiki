<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 12
 */

require_once __DIR__ . "/module.email.account.php";

CONST EMAIL_ENCODING = "ISO-8859-1";

class EmailManager {

    private static $instance;

    private function __construct() {
    }

    public static function get_instance() {

		if (!isset(self::$instance)) { 
			self::$instance = new self();
        }
        
		return self::$instance;
	}

    public function send($email, $name, $to, $subject, $message) {

        $headers  = "From: " . $name . " <" . $email . ">" . PHP_EOL;
        $headers .= "X-Sender: " . $name . " <" . $email . ">" . PHP_EOL;
        $headers .= 'X-Mailer: PHP/' . phpversion() . PHP_EOL;
        $headers .= "X-Priority: 3" . PHP_EOL;
        $headers .= "Return-Path: " . $email . PHP_EOL;
        $headers .= "MIME-Version: 1.0"  . PHP_EOL;
        $headers .= "Content-Type: text/html; charset=" . EMAIL_ENCODING  . PHP_EOL;


        return mail($to, $subject, $message, $headers);
    }
}

