<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 12
 */

require_once __DIR__ . "/common.php";

function process() {
    
    $db = Database::get_instance();
    $user = UserManager::get_instance();
    $log = LogManager::get_instance();
    $http_vars = HttpVarsManager::get_instance();
    $recaptcha = ReCaptchaManager::get_instance();
    $email = EmailManager::get_instance();
    $settings = SettingsManager::get_instance();
          
    $http_user_email = $http_vars->get("user-email");
    $http_recaptcha = $http_vars->get("g-recaptcha-response");

    if ($user->authorized()) {
        return array(
            "result" => false,
            "redirect" => "./"
        );
    }

    if (empty($http_user_email)) {
        return array(
            "result" => true
        );
    }
    
    if (!filter_var($http_user_email, FILTER_VALIDATE_EMAIL)) {
        return array(
            "result" => false,
            "message" => STRINGS["EPRS0"]
        );
    }

    if (!$db->connect()) {
        return array(
            "result" => false,
            "message" => STRINGS["ESDB0"],
            "redirect" => "./?out-of-service"
        );
    }
    
    $recaptcha_enable = (strtolower($settings->get("recaptcha_enable")) == "true");
    if ($recaptcha_enable && !$recaptcha->verify($settings->get("recaptcha_private_key"), $http_recaptch_response)) {
        return array(
            "result" => false,
            "message" => STRINGS["EPRS1"]
        );
    }
    
    $user_data = $db->in(DB_USER_TABLE)
                    ->select("*")
                    ->where("email", "=", $http_user_email)
                    ->go_and_get();

    if (!$user_data) {
        return array(
            "result" => false,
            "message" => STRINGS["EPRS2"]
        );
    }

    $generated_password = bin2hex(openssl_random_pseudo_bytes(6));
    
    $response = $db->in(DB_USER_TABLE)
                   ->update("password", hash_password($generated_password))
                   ->where("email", "=", $http_user_email)
                   ->go();

    if (!$response) {
        return array(
            "result" => false,
            "message" => STRINGS["EPRS3"]
        );
    }

    $email_content_table = array(
        "{NAME}" => $user_data["name"],
        "{PASSWORD}" => $generated_password
    );
    
    $email_subject = STRINGS["SPRS0"];
    $email_content = strtr(STRINGS["SPRS1"], $email_content_table);

    if (!$email->send($http_user_email, $email_subject, $email_content)) {
        return array(
            "result" => false,
            "message" => STRINGS["EPRS4"]
        );
    }
    
    $log->create($user_data["name"], "reset", "");
    
    return array(
        "result" => true,
        "message" => "success"
    );
}