<?php
namespace App\Core;

use GuzzleHttp\Client;

class ApiManager
{
	public static function requestUserProfile($fbId)
	{
		$client = new Client();
		$response = $client->request(
			"GET",
			"https://graph.facebook.com/v2.6/$fbId?fields=first_name,last_name,timezone,gender&access_token=" . env('FB_TOKEN')
		);
		CacheManager::storeUserProfile($fbId, $response->getBody());
		return strtolower(json_decode($response->getBody()));
	}

	public static function sendMultipleMessages($fbId, array $messages)
	{
		foreach ($messages as $message) {
			self::sendMessage($fbId, $message);
		}
	}

	public static function sendMessage($message)
	{
		$client = new Client();
		$response = $client->request(
			"POST",
			"https://graph.facebook.com/v2.6/me/messages?access_token=" . env('FB_TOKEN'),
			["json" => $message]
		);
	}

	public static function makeMessage($fbId, $message)
	{
		return [
			"recipient" => ["id" => $fbId],
			"message" => ["text" => $message]
		];
	}

	public static function makeQuickReplyMessage($fbId, $message, array $replies) {
		return [
			"recipient" => ["id" => $fbId],
			"message" => [
				"text" => $message,
				"quick_replies" => $replies
			]
		];
	}

	public static function makeQuickReply($title, $payload, $image_url = "", $content_type = "text") {
		return [
			"content_type" => $content_type,
			"title" => $title,
			"payload" => $payload,
			"image_url" => $image_url
		];
	}
}