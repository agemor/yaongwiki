<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 02. 01
 */

require_once 'libs/phpmailer/class.phpmailer.php';
require_once 'libs/phpmailer/class.smtp.php';

const MAILER_USERNAME = 'opinionkit@gmail.com';
const MAILER_PASSWORD = 'Opinionkit16!';
const MAILER_FROM = '연세위키';

function getMailer($email, $title, $content) {
    
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->CharSet    = "EUC-KR";
    $mail->Encoding   = "base64";
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = MAILER_USERNAME;
    $mail->Password   = MAILER_PASSWORD;
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->setFrom(MAILER_USERNAME, MAILER_FROM);
    $mail->AddReplyTo(MAILER_USERNAME, MAILER_FROM);
    $mail->addAddress($email);
    
    $mail->isHTML(true);
    
    $mail->Subject = $title;
    $mail->Body    = $content;
    
    if (!$mail->send())
        return array(
            'result'=>false,
            '이메일 발송에 실패했습니다'
        );
    
    return array(
        'result'=>true,
        ''
    );
}
?>