<?php


namespace App\Core\Actions;


use App\Core\ApiManager;
use App\Core\CacheManager;
use App\Core\ReplyManager;

class EventMenuAction implements IAction
{
	public function run($fbId)
	{
		// Generate event menus
		$replies = [
			ApiManager::makeQuickReply("Transactions", "transaction_menu"),
			ApiManager::makeQuickReply("Members", "member_menu"),
			ApiManager::makeQuickReply("Event summary", "event_summary"),
			ApiManager::makeQuickReply("Detail report", "event_detail")
		];

		CacheManager::clear($fbId);
		CacheManager::storeCommand($fbId, "event_menu");
		ReplyManager::quickReply($fbId, "We are currently in '[current_event]', What do you want to do [first_name]?", $replies);
	}
}