<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 02. 01
 */

require_once 'libs/recaptcha/autoload.php';

const RECAPTCHA_PRIVATE_KEY = '6LewehYTAAAAAFZJJRnqErvewMQ_dY8oCxW7g2n6';
const RECAPTCHA_PUBLIC_KEY = '6LewehYTAAAAAMoePWzUQITyPGXSXLppuarmvjrm';

function getReCaptcha() {
    $recaptcha = new \ReCaptcha\ReCaptcha(RECAPTCHA_PRIVATE_KEY);
    $response  = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
    return $response->isSuccess();
}
?>