<?php
namespace App\Core\BagiRata;

use GuzzleHttp\Client;

class SendMessage
{
	public static function callApiArray($fbId, array $messages) {
		foreach ($messages as $message) {
			self::callApi($fbId, $message);
		}
	}

	public static function callApi($fbId, $message) {
		$client = new Client();
		$response = $client->requestAsync(
				'POST',
				'https://graph.facebook.com/v2.6/me/messages?access_token='.config('token.fb_page'),
				['json' => self::makeMessage($fbId, $message)]
		);
	}

	public static function makeMessage($fbId, $message) {
		return ['recipient' => ['id' => $fbId], 'message' => ['text' => $message]];
	}
}