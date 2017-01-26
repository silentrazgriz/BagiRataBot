<?php
namespace App\Core;


use App\Models\Cache;
use Illuminate\Support\Facades\DB;

class CacheManager
{
	public static function createCache($fbId) {
		DB::transaction(function() use ($fbId) {
			Cache::create([
				"fbId" => $fbId,
				"userProfile" => ApiManager::requestUserProfile($fbId),
				"currentEvent" => "",
				"command" => "",
				"messages" => array(),
				"value" => array()
			]);
		});
	}

	public static function setCurrentEvent($fbId, $currentEvent) {
		DB::transaction(function() use ($fbId, $currentEvent) {
			Cache::where("fbId", $fbId)->update(["currentEvent" => $currentEvent]);
		});
	}

	public static function storeCommand($fbId, $command) {
		DB::transaction(function() use ($fbId, $command) {
			Cache::where("fbId", $fbId)->update(["command" => $command]);
		});
	}

	public static function storeMessages($fbId, $message) {
		DB::transaction(function () use ($fbId, $message) {
			$cache = Cache::where("fbId", $fbId)->first();
			$cache->messages = array_merge($cache->messages, array(strtolower($message)));
			$cache->save();
		});
	}


	public static function storeUserProfile($fbId, $userProfile) {
		DB::transaction(function() use ($fbId, $userProfile) {
			Cache::where("fbId", $fbId)->update(["userProfile" => $userProfile]);
		});
	}

	public static function storeValue($fbId, $value) {
		DB::transaction(function() use ($fbId, $value) {
			Cache::where("fbId", $fbId)->update(["value" => $value]);
		});
	}

	public static function clearMessages($fbId) {
		DB::transaction(function () use ($fbId) {
			Cache::where("fbId", $fbId)->update(["messages" => "[]"]);
		});
	}

	public static function clearValue($fbId) {
		DB::transaction(function () use ($fbId) {
			Cache::where("fbId", $fbId)->update(["value" => "[]"]);
		});
	}

	public static function clearCommand($fbId) {
		DB::transaction(function () use ($fbId) {
			Cache::where("fbId", $fbId)->update(["command" => ""]);
		});
	}

	public static function clear($fbId) {
		DB::transaction(function () use ($fbId) {
			Cache::where("fbId", $fbId)->update(["messages" => "[]", "value" => "[]"]);
		});
	}

	public static function get($fbId) {
		if (Cache::where("fbId", $fbId)->count() == 0) {
			self::createCache($fbId);
		}
		return Cache::where("fbId", $fbId)->first();
	}
}