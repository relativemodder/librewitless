<?php
require_once __DIR__ . "/../config/telegram.php";

class Telegram {
	public static function makeApiRequest($method_name, $request_body = array()) {
		$url = "https://api.telegram.org/bot" . TOKEN .  "/$method_name";

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt(
			$curl, 
			CURLOPT_HTTPHEADER, 
			array(
				'Accept: application/json', 
				'Content-Type: application/json'
			)
		);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$data = json_encode($request_body);

		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		$resp = curl_exec($curl);
		curl_close($curl);

		return json_decode($resp, true);
	}

	public static function setWebhook($url) {
		return Telegram::makeApiRequest("setWebhook", ["url" => $url]);
	}

	public static function sendMessage($chat_id, $text, $additional_params = []) {
		return Telegram::makeApiRequest(
			"sendMessage", 
			array_merge(
				[
					"chat_id" => $chat_id,
					"text" => $text
				], $additional_params
			)
		);
	}

	public static function getMe() {
		return Telegram::makeApiRequest("getMe")["result"];
	}

	public static function logInfo($info) {
		Telegram::sendMessage(631187906, $info);
	}

	public static function logArray($array) {
		Telegram::sendMessage(631187906, json_encode($array));
	}
}