<?php
namespace App\Core\Actions;


use App\Core\Actions\Structures\IAction;
use App\Core\ApiManager;
use App\Core\CacheManager;
use App\Core\ReplyManager;
use App\Models\Event;

class EventChooseAction implements IAction
{
	public function message($fbId)
	{
		$cache = CacheManager::get($fbId);
		$chosenEvent = $cache->messages[count($cache->messages) - 1];
		if (Event::where("fbId", $fbId)->where("name", $chosenEvent)->count() > 0) {
			// Set current event if there is matching event with user input
			CacheManager::setCurrentEvent($fbId, $chosenEvent);
			CacheManager::clear($fbId);
			ReplyManager::reply($fbId, "Success, your current event is '$chosenEvent'");

			$eventMenu = new EventMenuAction();
			$eventMenu->quickReply($fbId);
		} else {
			// Event not found, ask user to input again
			CacheManager::clear($fbId);
			ReplyManager::reply($fbId, "We can't find '[current_event]', please try another event name");
		}
	}

	public function quickReply($fbId)
	{
		// Provide user with their latest 5 events using quick replies
		$events = Event::where("fbId", $fbId)->orderBy("created_at", "desc")->take(5)->get();
		$replies = [];
		foreach ($events as $event) {
			array_unshift($replies, ApiManager::makeQuickReply($event->name, "event_choose"));
		}
		ReplyManager::quickReply($fbId, "Please choose one of your event", $replies);
	}

	public function postback($fbId)
	{
		// TODO: Implement postback() method.
	}


}