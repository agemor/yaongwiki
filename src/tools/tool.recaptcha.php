<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 02. 01
 */

const RECAPTCHA_PUBLIC_KEY = '6LfLFyYTAAAAAE0pczd8EZIuKpY2ylZytYfUD_oX';
const RECAPTCHA_PRIVATE_KEY = '6LfLFyYTAAAAABI18Db9cDTmvJZZDcq3jgmGuQ5A';

function getReCaptcha() {

	$secret = RECAPTCHA_PRIVATE_KEY;
	$response = $_POST["g-recaptcha-response"];
	$remoteip = $_SERVER['REMOTE_ADDR'];

	$verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}&remoteip={$remoteip}&");

	if ($verifyResponse == false)
		return true;

	$responseData = json_decode($verifyResponse);

	return $responseData->success;
}
?>