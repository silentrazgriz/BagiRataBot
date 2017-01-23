<?php
namespace App\Core;


use App\Models\Chat;
use Illuminate\Support\Facades\Log;

class ReplyManager
{
	public static function reply($fbId, $message)
	{
		//ApiManager::sendMessage(ApiManager::makeMessage($fbId, self::replaceCodeInMessage($fbId, $message)));
		Chat::create([
			"message" => self::replaceCodeInMessage($fbId, $message)
		]);
	}

	public static function quickReply($fbId, $message, array $replies)
	{
		//ApiManager::sendMessage(ApiManager::makeQuickReplyMessage($fbId, self::replaceCodeInMessage($fbId, $message), $replies));
		Chat::create([
			"quickReplies" => $replies,
			"message" => self::replaceCodeInMessage($fbId, $message)
		]);
	}

	private static function replaceCodeInMessage($fbId, $message)
	{
		try {
			$cache = CacheManager::get($fbId);
			return str_replace(
				array(
					"[first_name]",
					"[last_name]",
					"[current_event]"
				),
				array(
					$cache->userProfile->first_name,
					$cache->userProfile->last_name,
					$cache->currentEvent
				),
				$message
			);
		} catch (\Exception $e) {
			Log::error("ReplyManager::replaceCodeInMessage", ["code" => $e->getCode(), "message" => $e->getMessage()]);
			return $message;
		}
	}
}