<?php


namespace App\Core\Actions;


use App\Core\ApiManager;
use App\Core\CacheManager;
use App\Core\ReplyManager;

class TransactionMenuAction implements IAction
{
	public function run($fbId)
	{
		// Generate transaction menus
		$replies = [
			ApiManager::makeQuickReply("I have a new transaction", "transaction_create"),
			ApiManager::makeQuickReply("I want to remove a transaction", "transaction_delete")
		];

		CacheManager::clear($fbId);
		CacheManager::storeCommand($fbId, "transaction_menu");
		ReplyManager::quickReply($fbId, "Tell us what you need!", $replies);
	}

}