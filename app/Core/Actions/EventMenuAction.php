<?php


namespace App\Core\Actions;


use App\Core\Actions\Structures\IAction;
use App\Core\ApiManager;
use App\Core\CacheManager;
use App\Core\ReplyManager;
use App\Models\Event;

class EventMenuAction implements IAction
{
	public function message($fbId)
	{
		$this->quickReply($fbId);
	}

	public function quickReply($fbId)
	{
		// Generate event menus
		$replies = [
				ApiManager::makeQuickReply("Transactions", "transaction_menu"),
				ApiManager::makeQuickReply("Members", "member_menu"),
				ApiManager::makeQuickReply("Event summary", "event_summary"),
				ApiManager::makeQuickReply("Detailed report", "event_detail"),
				ApiManager::makeQuickReply("Back to main menu", "main_menu")
		];
		$cache = CacheManager::get($fbId);
		$event = Event::where("fbId", $fbId)->where("name", $cache->currentEvent)->first();

		CacheManager::clear($fbId);
		ReplyManager::reply($fbId, "We are currently in '[current_event]'");
		ReplyManager::reply($fbId, "People in your events:\n" . implode("\n", $event->members));
		ReplyManager::quickReply($fbId, "What do you want to do [first_name]?", $replies);
	}

	public function postback($fbId)
	{
		// TODO: Implement postback() method.
	}

}