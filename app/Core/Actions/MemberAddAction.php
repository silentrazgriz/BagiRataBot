<?php


namespace App\Core\Actions;


use App\Core\ApiManager;
use App\Core\CacheManager;
use App\Core\ReplyManager;
use App\Models\Event;

class MemberAddAction implements IAction
{
	public function run($fbId)
	{
		$cache = CacheManager::get($fbId);
		if ($cache->command == "member_add") {
			// Add friend to member list
			$name = $cache->messages[count($cache->messages) - 1];

			$event = Event::where("fbId", $fbId)->where("name", $cache->currentEvent)->first();
			$event->members = array_merge($event->members, array($name));
			$event->save();

			CacheManager::clear($fbId);

			ReplyManager::reply($fbId, "'$name' added to '[current_event]'");
			ReplyManager::quickReply($fbId, "Anyone other friend you want to add?", [
				ApiManager::makeQuickReply("Yes", "member_add_another"),
				ApiManager::makeQuickReply("It's done", "event_menu")
			]);
		} else if ($cache->command == "member_add_another") {
			CacheManager::clear($fbId);
			CacheManager::storeCommand($fbId, "member_add");
		} else {
			// Ask user
			CacheManager::clear($fbId);
			CacheManager::storeCommand($fbId, "member_add");

			ReplyManager::reply($fbId, "Please tell us your friend name!");
		}
	}
}