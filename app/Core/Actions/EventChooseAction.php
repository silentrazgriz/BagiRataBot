<?php
namespace App\Core\Actions;


use App\Core\ApiManager;
use App\Core\CacheManager;
use App\Core\ReplyManager;
use App\Models\Event;

class EventChooseAction implements IAction
{
	public function run($fbId)
	{
		$cache = CacheManager::get($fbId);
		if ($cache->command == "event_choose") {
			$chosenEvent = end($cache->messages);
			if (Event::where("fbId", $fbId)->where("name", $chosenEvent)->count() > 0) {
				// Set current event if there is matching event with user input
				CacheManager::setCurrentEvent($fbId, $chosenEvent);
				CacheManager::clear($fbId);

				ReplyManager::reply($fbId, "Success, your current event is '$chosenEvent'");

				$eventMenu = new EventMenuAction();
				$eventMenu->run($fbId);
			} else {
				// Event not found, ask user to input again
				CacheManager::clearMessages($fbId);
				ReplyManager::reply($fbId, "We can't find '[currentEvent]', please try another event name");
			}
		} else {
			// Provide user with their latest 5 events using quick replies
			$events = Event::where("fbId", $fbId)->orderBy("created_at", "desc")->take(5)->get();
			$replies = [];
			foreach ($events as $event) {
				array_unshift($replies, ApiManager::makeQuickReply($event->name, "event_choose"));
			}

			CacheManager::storeCommand($fbId, "event_choose");
			ReplyManager::quickReply($fbId, "Please choose one of your event", $replies);
		}
	}
}