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

class Email {

    public $headers;

    public function __construct() {

        $headers  = "From: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM . ">" . PHP_EOL;
        $headers .= "X-Sender: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM . ">" . PHP_EOL;
        $headers .= 'X-Mailer: PHP/' . phpversion() . PHP_EOL;
        $headers .= "X-Priority: 3" . PHP_EOL;
        $headers .= "Return-Path: " . EMAIL_FROM . PHP_EOL;
        $headers .= "MIME-Version: 1.0"  . PHP_EOL;
        $headers .= "Content-Type: text/html; charset=" . EMAIL_ENCODING  . PHP_EOL;

        $this->headers = $headers;
    }

    public function send($to, $subject, $message) {
        return mail($to, $subject, $message, $this->headers);
    }
}

$email = new Email();