<?php
namespace App\Core\Actions;

use App\Core\ApiManager;
use App\Core\CacheManager;
use App\Core\ReplyManager;
use App\Models\Event;

class MainMenuAction implements IAction
{
	public function run($fbId)
	{
		$userEvents = Event::where("fbId", $fbId)->count();
		// Create new event menu as default
		$replies = [
			ApiManager::makeQuickReply("Create new event", "event_create"),
		];
		if ($userEvents > 0) {
			// If there is user events available, provide with Choose event, Forget event and List event
			array_unshift($replies, ApiManager::makeQuickReply("Change my current event", "event_choose"));
			array_push($replies, ApiManager::makeQuickReply("Forget an event", "event_delete"), ApiManager::makeQuickReply("See my events", "event_list"));
		}

		CacheManager::clear($fbId);
		CacheManager::storeCommand($fbId, "main_menu");
		ReplyManager::quickReply($fbId, "What do you want to do [first_name]?", $replies);
	}
}