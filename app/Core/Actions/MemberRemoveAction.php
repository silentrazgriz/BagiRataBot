<?php


namespace App\Core\Actions;


use App\Core\Actions\Structures\IAction;
use App\Core\ApiManager;
use App\Core\CacheManager;
use App\Core\ReplyManager;
use App\Models\Event;

class MemberRemoveAction implements IAction
{
	public function message($fbId)
	{
		// Remove friend to member list
		$cache = CacheManager::get($fbId);
		$name = $cache->messages[count($cache->messages) - 1];

		$event = Event::where("fbId", $fbId)->where("name", $cache->currentEvent)->first();
		if (in_array($name, $event->members)) {
			$event->members = array_values(array_diff($event->members, array($name)));
			$event->save();

			ReplyManager::reply($fbId, "'$name' removed from '[current_event]'");
		} else {
			ReplyManager::reply($fbId, "'$name' not exists");
		}
		CacheManager::clear($fbId);

		ReplyManager::quickReply($fbId, "Any other person leaving?", [
				ApiManager::makeQuickReply("Yes", "member_remove_another"),
				ApiManager::makeQuickReply("It's done", "event_menu")
		]);
	}

	public function quickReply($fbId)
	{
		// Ask user to add more
		$cache = CacheManager::get($fbId);
		CacheManager::clear($fbId);
		if ($cache->command == "member_remove_another") {
			CacheManager::storeCommand($fbId, "member_remove");
		}

		ReplyManager::reply($fbId, "Who's leaving?");
	}

	public function postback($fbId)
	{
		// TODO: Implement postback() method.
	}

}