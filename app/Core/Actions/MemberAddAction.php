<?php


namespace App\Core\Actions;


use App\Core\Actions\Structures\IAction;
use App\Core\ApiManager;
use App\Core\CacheManager;
use App\Core\ReplyManager;
use App\Models\Event;

class MemberAddAction implements IAction
{
	public function message($fbId)
	{
		// Add friend to member list
		$cache = CacheManager::get($fbId);
		$name = $cache->messages[count($cache->messages) - 1];

		$event = Event::where("fbId", $fbId)->where("name", $cache->currentEvent)->first();
		if (in_array($name, $event->members)) {
			ReplyManager::reply($fbId, "'$name' already exists");
		} else {
			$event->members = array_merge($event->members, array($name));
			$event->save();

			ReplyManager::reply($fbId, "'$name' added to '[current_event]'");
		}
		CacheManager::clear($fbId);

		ReplyManager::quickReply($fbId, "Any other person you want to add?", [
				ApiManager::makeQuickReply("Yes", "member_add_another"),
				ApiManager::makeQuickReply("It's done", "event_menu")
		]);
	}

	public function quickReply($fbId)
	{
		// Ask user to add more
		$cache = CacheManager::get($fbId);
		CacheManager::clear($fbId);
		if ($cache->command == "member_add_another") {
			CacheManager::storeCommand($fbId, "member_add");
		}

		ReplyManager::reply($fbId, "Please tell us your friend name!");
	}

	public function postback($fbId)
	{
		// TODO: Implement postback() method.
	}


}