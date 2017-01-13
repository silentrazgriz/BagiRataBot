<?php
namespace App\Core;


class ReplyManager
{
	private static $messages = [
		"greeting" => "",
		"help" => "",
		"mainMenu" => "",
	];

	public static function reply($fbId, $template) {
		ApiManager::sendMessage($fbId, self::$messages[$template]);
	}
}