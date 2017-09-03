<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 12
 */

include "core.email.account.php";

CONST EMAIL_ENCODING = "utf-8";

class Email {

    public $headers;

    public function __construct() {

        $headers  = "From: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM . ">\n";
        $headers .= "X-Sender: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM . ">\n";
        $headers .= 'X-Mailer: PHP/' . phpversion();
        $headers .= "X-Priority: 3\n";
        $headers .= "Return-Path: " . EMAIL_FROM . "\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=" . EMAIL_ENCODING . "\n";

        $this->headers = $headers;
    }

    public function send($to, $subject, $message) {
        return mail($to, $subject, $message, $this->headers);
    }
}

$email = new Email();